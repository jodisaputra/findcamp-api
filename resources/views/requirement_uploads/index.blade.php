@extends('layouts.app')

@section('content')
<div class="container">
    <div class="alert alert-info">
        <strong>Note:</strong> Admins can only validate user uploads. Uploading is only available to users.
    </div>
    <h3 class="mb-4">Requirement Uploads</h3>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Country</th>
                    <th>Requirement</th>
                    <th>Status</th>
                    <th>Payment Required</th>
                    <th>Payment Status</th>
                    <th>Uploaded At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($uploads as $upload)
                <tr>
                    <td>{{ $upload->id }}</td>
                    <td>{{ $upload->user->name ?? '-' }}</td>
                    <td>{{ $upload->country->name ?? '-' }}</td>
                    <td>{{ $upload->requirement->requirement_name ?? '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $upload->status == 'accepted' ? 'success' : ($upload->status == 'refused' ? 'danger' : 'warning') }}">
                            {{ ucfirst($upload->status) }}
                        </span>
                    </td>
                    <td>{{ $upload->requirement->requires_payment ? 'Yes' : 'No' }}</td>
                    <td>
                        @if($upload->requirement->requires_payment)
                            @if($upload->payment_path)
                                <span class="badge bg-{{ $upload->payment_status == 'accepted' ? 'success' : ($upload->payment_status == 'refused' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($upload->payment_status) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Not Uploaded</span>
                            @endif
                        @else
                            <span class="badge bg-secondary">N/A</span>
                        @endif
                    </td>
                    <td>{{ $upload->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('requirement-uploads.show', $upload->id) }}" class="btn btn-sm btn-primary">View/Validate</a>
                        <a href="{{ route('requirement-uploads.file', $upload->id) }}" class="btn btn-sm btn-info" target="_blank">Download</a>
                        @if($upload->requirement->requires_payment && $upload->payment_path)
                            <a href="{{ route('requirement-uploads.payment-file', $upload->id) }}" class="btn btn-sm btn-info" target="_blank">Payment</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $uploads->links() }}
    </div>
</div>
@endsection 