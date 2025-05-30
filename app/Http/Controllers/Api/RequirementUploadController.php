<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequirementUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RequirementUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $uploads = RequirementUpload::with(['country', 'requirement'])
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $uploads
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'requirement_id' => 'required|exists:requirements,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user already has an upload for this requirement
        $existingUpload = RequirementUpload::where('user_id', $request->user()->id)
            ->where('requirement_id', $request->requirement_id)
            ->where('country_id', $request->country_id)
            ->latest()
            ->first();

        if ($existingUpload && $existingUpload->status !== 'refused') {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already uploaded a document for this requirement'
            ], 400);
        }

        $file = $request->file('file');
        $path = $file->store('requirement-uploads', 'public');

        $upload = RequirementUpload::create([
            'user_id' => $request->user()->id,
            'country_id' => $request->country_id,
            'requirement_id' => $request->requirement_id,
            'file_path' => $path,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Document uploaded successfully',
            'data' => $upload
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $country_id, $requirement_id)
    {
        $user = Auth::user();
        \Log::info("[RequirementUpload] Fetch attempt: user_id=" . ($user ? $user->id : 'null') . ", country_id=$country_id, requirement_id=$requirement_id");

        $upload = RequirementUpload::with('requirement')
            ->where('user_id', $user->id)
            ->where('country_id', $country_id)
            ->where('requirement_id', $requirement_id)
            ->latest()->first();

        if ($upload) {
            \Log::info("[RequirementUpload] Upload found: id={$upload->id}, file_path={$upload->file_path}, status={$upload->status}");
        } else {
            \Log::info("[RequirementUpload] No upload found for user_id={$user->id}, country_id=$country_id, requirement_id=$requirement_id");
        }

        if (!$upload) {
            return response()->json(['message' => 'No upload found.'], 404);
        }
        return response()->json($upload);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // Download/view the uploaded file
    public function file($id)
    {
        $upload = RequirementUpload::findOrFail($id);
        return Storage::disk('public')->download($upload->file_path);
    }

    // Admin: validate (accept/refuse) an upload
    public function validateUpload(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accepted,refused',
            'admin_note' => 'required_if:status,refused|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $upload = RequirementUpload::findOrFail($id);
        $upload->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Document validation updated successfully',
            'data' => $upload
        ]);
    }

    public function uploadPayment(Request $request, $id)
    {
        $upload = RequirementUpload::with('requirement')->findOrFail($id);

        if (!$upload->requirement->requires_payment) {
            return response()->json([
                'status' => 'error',
                'message' => 'This requirement does not require payment'
            ], 400);
        }

        if ($upload->payment_path && $upload->payment_status !== 'refused') {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment document already uploaded'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'payment_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('payment_file');
        $path = $file->store('payment-uploads', 'public');

        $upload->update([
            'payment_path' => $path,
            'payment_status' => 'pending'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment document uploaded successfully',
            'data' => $upload
        ]);
    }

    public function downloadFile($id)
    {
        \Log::info("[RequirementUpload] Download attempt for ID: $id");
        \Log::info("[RequirementUpload] Auth check - User: " . json_encode(Auth::user()));
        \Log::info("[RequirementUpload] Auth check - Token: " . request()->bearerToken());
        
        try {
            $upload = RequirementUpload::findOrFail($id);
            \Log::info("[RequirementUpload] Found upload: id={$upload->id}, user_id={$upload->user_id}, file_path={$upload->file_path}");
            
            // Check if the user has access to this file
            $currentUserId = Auth::user()->id;
            \Log::info("[RequirementUpload] Auth check - Current user ID: {$currentUserId}, Upload user ID: {$upload->user_id}");
            
            if ($upload->user_id != $currentUserId) {
                \Log::warning("[RequirementUpload] Unauthorized access attempt: requested_user_id={$currentUserId}, file_user_id={$upload->user_id}");
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 403);
            }
            
            if (!$upload->file_path) {
                \Log::error("[RequirementUpload] File path not found for upload ID: $id");
                return response()->json([
                    'status' => 'error',
                    'message' => 'File not found'
                ], 404);
            }

            // Set the appropriate content type based on file extension
            $extension = pathinfo($upload->file_path, PATHINFO_EXTENSION);
            $contentType = match($extension) {
                'pdf' => 'application/pdf',
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                default => 'application/octet-stream',
            };
            \Log::info("[RequirementUpload] Content type set to: $contentType for extension: $extension");

            // Get the file contents
            \Log::info("[RequirementUpload] Attempting to read file from storage: {$upload->file_path}");
            $file = Storage::disk('public')->get($upload->file_path);
            \Log::info("[RequirementUpload] File read successfully, size: " . strlen($file) . " bytes");
            
            // Return the file with proper headers
            \Log::info("[RequirementUpload] Sending file response with headers");
            return response($file, 200, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline; filename="' . basename($upload->file_path) . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        } catch (\Exception $e) {
            \Log::error("[RequirementUpload] Error downloading file: " . $e->getMessage());
            \Log::error("[RequirementUpload] Stack trace: " . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Error downloading file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadPaymentFile($id)
    {
        \Log::info("[RequirementUpload] Download payment file attempt for ID: $id");
        
        try {
            $upload = RequirementUpload::findOrFail($id);
            \Log::info("[RequirementUpload] Found upload: id={$upload->id}, user_id={$upload->user_id}, payment_path={$upload->payment_path}");
            
            // Allow if user is owner or admin
            $user = Auth::user();
            if (!($user && ($upload->user_id == $user->id || ($user->is_admin ?? false)))) {
                \Log::warning("[RequirementUpload] Unauthorized access attempt: requested_user_id=" . ($user ? $user->id : 'null') . ", file_user_id={$upload->user_id}");
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 403);
            }
            
            if (!$upload->payment_path) {
                \Log::error("[RequirementUpload] Payment file path not found for upload ID: $id");
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment file not found'
                ], 404);
            }

            // Set the appropriate content type based on file extension
            $extension = pathinfo($upload->payment_path, PATHINFO_EXTENSION);
            $contentType = match($extension) {
                'pdf' => 'application/pdf',
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                default => 'application/octet-stream',
            };
            \Log::info("[RequirementUpload] Content type set to: $contentType for extension: $extension");

            // Get the file contents
            \Log::info("[RequirementUpload] Attempting to read payment file from storage: {$upload->payment_path}");
            $file = Storage::disk('public')->get($upload->payment_path);
            \Log::info("[RequirementUpload] Payment file read successfully, size: " . strlen($file) . " bytes");
            
            // Return the file with proper headers
            \Log::info("[RequirementUpload] Sending payment file response with headers");
            return response($file, 200, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline; filename="' . basename($upload->payment_path) . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        } catch (\Exception $e) {
            \Log::error("[RequirementUpload] Error downloading payment file: " . $e->getMessage());
            \Log::error("[RequirementUpload] Stack trace: " . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Error downloading payment file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validatePayment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'payment_status' => 'required|in:accepted,refused',
            'payment_note' => 'required_if:payment_status,refused|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $upload = RequirementUpload::findOrFail($id);
        $upload->update([
            'payment_status' => $request->payment_status,
            'payment_note' => $request->payment_note,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment validation updated successfully',
            'data' => $upload
        ]);
    }
}
