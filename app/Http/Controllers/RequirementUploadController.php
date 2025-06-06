<?php

namespace App\Http\Controllers;

use App\Models\RequirementUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequirementUploadController extends Controller
{
    // List all uploads (with filters for admin)
    public function index(Request $request)
    {
        $uploads = RequirementUpload::with(['user', 'country', 'requirement'])
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('requirement_uploads.index', compact('uploads'));
    }

    // Show a single upload (file preview/download)
    public function show($id)
    {
        $upload = RequirementUpload::with(['user', 'country', 'requirement'])->findOrFail($id);
        return view('requirement_uploads.show', compact('upload'));
    }

    // Download the file
    public function file($id)
    {
        $upload = RequirementUpload::findOrFail($id);
        return Storage::disk('public')->download($upload->file_path);
    }

    // Download the payment file
    public function paymentFile($id)
    {
        $upload = RequirementUpload::findOrFail($id);
        return Storage::disk('public')->download($upload->payment_path);
    }

    // Validate (accept/refuse) an upload
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
        return redirect()->route('requirement-uploads.index')->with('success', 'Status updated!');
    }

    // Validate (accept/refuse) a payment
    public function validatePayment(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:accepted,refused',
            'payment_note' => 'nullable|string',
        ]);
        $upload = RequirementUpload::findOrFail($id);
        $upload->payment_status = $request->payment_status;
        $upload->payment_note = $request->payment_note;
        $upload->save();
        return redirect()->route('requirement-uploads.index')->with('success', 'Payment status updated!');
    }

    // Upload admin document
    public function uploadAdminDocument(Request $request, $id)
    {
        $upload = RequirementUpload::findOrFail($id);
        $request->validate([
            'admin_document' => 'required|file|mimes:pdf|max:10240',
        ]);
        if ($request->hasFile('admin_document')) {
            // Hapus file lama jika ada
            if ($upload->admin_document_path) {
                \Storage::disk('public')->delete($upload->admin_document_path);
            }
            $path = $request->file('admin_document')->store('admin_documents', 'public');
            $upload->admin_document_path = $path;
            $upload->save();
        }
        return back()->with('success', 'Admin document uploaded!');
    }
}
