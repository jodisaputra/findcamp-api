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
            <p><strong>Payment Required:</strong> {{ $upload->requirement->requires_payment ? 'Yes' : 'No' }}</p>
            <p><strong>Status:</strong> <span class="badge bg-{{ $upload->status == 'accepted' ? 'success' : ($upload->status == 'refused' ? 'danger' : 'warning') }}">{{ ucfirst($upload->status) }}</span></p>
            <p><strong>Admin Note:</strong> {{ $upload->admin_note ?? '-' }}</p>
            <p><strong>Uploaded At:</strong> {{ $upload->created_at->format('Y-m-d H:i') }}</p>
            <a href="{{ route('requirement-uploads.file', $upload->id) }}" class="btn btn-info" target="_blank">Download File</a>
        </div>
    </div>

    @if($upload->requirement->requires_payment)
    <div class="card mb-4">
        <div class="card-body">
            <h5>Payment Information</h5>
            <p><strong>Payment Status:</strong> 
                <span class="badge bg-{{ $upload->payment_status == 'accepted' ? 'success' : ($upload->payment_status == 'refused' ? 'danger' : 'warning') }}">
                    {{ ucfirst($upload->payment_status ?? 'Not Uploaded') }}
                </span>
            </p>
            <p><strong>Payment Note:</strong> {{ $upload->payment_note ?? '-' }}</p>
            @if($upload->payment_path)
                <a href="{{ route('requirement-uploads.payment-file', $upload->id) }}" class="btn btn-info" target="_blank">Download Payment File</a>
            @else
                <p class="text-muted">No payment file uploaded yet.</p>
            @endif
        </div>
    </div>

    @if($upload->payment_path && $upload->payment_status == 'pending')
    <div class="card mb-4">
        <div class="card-body">
            <h5>Validate Payment</h5>
            <form method="POST" action="{{ route('requirement-uploads.validate-payment', $upload->id) }}">
                @csrf
                <div class="mb-3">
                    <label for="payment_status" class="form-label">Payment Status</label>
                    <select name="payment_status" id="payment_status" class="form-select" required>
                        <option value="accepted">Accept</option>
                        <option value="refused">Refuse</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="payment_note" class="form-label">Payment Note (required if refused)</label>
                    <textarea name="payment_note" id="payment_note" class="form-control" rows="3">{{ $upload->payment_note }}</textarea>
                </div>
                <button type="submit" class="btn btn-success">Update Payment Status</button>
            </form>
        </div>
    </div>
    @endif
    @endif

    @if($upload->admin_document_path)
        <a href="{{ asset('storage/'.$upload->admin_document_path) }}" target="_blank" class="btn btn-success mb-2">Download Admin Document</a>
    @endif
    <form method="POST" action="{{ route('requirement-uploads.upload-admin-document', $upload->id) }}" enctype="multipart/form-data" class="mb-3">
        @csrf
        <div class="input-group">
            <input type="file" name="admin_document" accept="application/pdf" class="form-control" required>
            <button type="submit" class="btn btn-primary">Upload Admin Document</button>
        </div>
        @error('admin_document')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </form>

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
                    <label for="admin_note" class="form-label">Admin Note (required if refused)</label>
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