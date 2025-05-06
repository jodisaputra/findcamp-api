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
}
