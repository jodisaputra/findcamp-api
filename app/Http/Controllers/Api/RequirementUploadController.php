<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequirementUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class RequirementUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'requirement_id' => 'required|exists:requirements,id',
            'file' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $user = Auth::user();
        $existing = RequirementUpload::where('user_id', $user->id)
            ->where('country_id', $request->country_id)
            ->where('requirement_id', $request->requirement_id)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();
        if ($existing) {
            return response()->json(['message' => 'You have already uploaded this requirement and it is pending or accepted.'], 403);
        }

        $path = $request->file('file')->store('requirement_uploads', 'public');
        $upload = RequirementUpload::create([
            'user_id' => $user->id,
            'country_id' => $request->country_id,
            'requirement_id' => $request->requirement_id,
            'file_path' => $path,
            'status' => 'pending',
        ]);
        return response()->json($upload, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $country_id, $requirement_id)
    {
        $user = Auth::user();
        $upload = RequirementUpload::where('user_id', $user->id)
            ->where('country_id', $country_id)
            ->where('requirement_id', $requirement_id)
            ->latest()->first();
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
        $request->validate([
            'status' => 'required|in:accepted,refused',
            'admin_note' => 'nullable|string',
        ]);
        $upload = RequirementUpload::findOrFail($id);
        $upload->status = $request->status;
        $upload->admin_note = $request->admin_note;
        $upload->save();
        return response()->json($upload);
    }
}
