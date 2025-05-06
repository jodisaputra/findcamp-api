@extends('layouts.app')

@section('content')
<div class="container">
    <div class="alert alert-info">
        <strong>Note:</strong> Admins can only validate user uploads. Uploading is only available to users.
    </div>
    <h3>Requirement Upload #{{ $upload->id }}</h3>
    <div class="card mb-4">
        <div class="card-body">
            <p><strong>User:</strong> {{ $upload->user->name ?? '-' }}</p>
            <p><strong>Country:</strong> {{ $upload->country->name ?? '-' }}</p>
            <p><strong>Requirement:</strong> {{ $upload->requirement->requirement_name ?? '-' }}</p>
            <p><strong>Status:</strong> <span class="badge bg-{{ $upload->status == 'accepted' ? 'success' : ($upload->status == 'refused' ? 'danger' : 'warning') }}">{{ ucfirst($upload->status) }}</span></p>
            <p><strong>Admin Note:</strong> {{ $upload->admin_note ?? '-' }}</p>
            <p><strong>Uploaded At:</strong> {{ $upload->created_at->format('Y-m-d H:i') }}</p>
            <a href="{{ route('requirement-uploads.file', $upload->id) }}" class="btn btn-info" target="_blank">Download File</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h5>Validate Upload</h5>
            @if($upload->status == 'pending')
            <form method="POST" action="{{ route('requirement-uploads.validate', $upload->id) }}">
                @csrf
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="accepted" {{ $upload->status == 'accepted' ? 'selected' : '' }}>Accept</option>
                        <option value="refused" {{ $upload->status == 'refused' ? 'selected' : '' }}>Refuse</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="admin_note" class="form-label">Admin Note (optional)</label>
                    <textarea name="admin_note" id="admin_note" class="form-control" rows="3">{{ $upload->admin_note }}</textarea>
                </div>
                <button type="submit" class="btn btn-success">Update Status</button>
                <a href="{{ route('requirement-uploads.index') }}" class="btn btn-secondary">Back</a>
            </form>
            @else
            <div class="alert alert-secondary">
                This upload has already been {{ $upload->status }}. No further changes are allowed.
            </div>
            <a href="{{ route('requirement-uploads.index') }}" class="btn btn-secondary">Back</a>
            @endif
        </div>
    </div>
</div>
@endsection 