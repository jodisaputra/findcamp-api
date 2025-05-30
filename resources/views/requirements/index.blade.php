@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Requirements</h5>
                    <div>
                        <a href="{{ route('requirements.manage-country') }}" class="btn btn-info me-2">Manage Country Requirements</a>
                        <a href="{{ route('requirements.create') }}" class="btn btn-primary">Add New Requirement</a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Notes</th>
                                    <th>Status</th>
                                    <th>Payment Required</th>
                                    <th>Countries</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requirements as $requirement)
                                    <tr>
                                        <td>{{ $requirement->id }}</td>
                                        <td>{{ $requirement->requirement_name }}</td>
                                        <td>
                                            @if($requirement->notes)
                                                <span class="text-muted">{{ Str::limit($requirement->notes, 50) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $requirement->status ? 'bg-success' : 'bg-danger' }}">
                                                {{ $requirement->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $requirement->requires_payment ? 'bg-warning' : 'bg-secondary' }}">
                                                {{ $requirement->requires_payment ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td>
                                            @foreach($requirement->countries as $country)
                                                <span class="badge bg-info me-1">{{ $country->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="{{ route('requirements.edit', $requirement) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('requirements.destroy', $requirement) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 