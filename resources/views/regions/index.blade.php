@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Regions</h1>
    <a href="{{ route('regions.create') }}" class="btn btn-primary mb-3">Create New Region</a>
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Regions Table</h5>
        </div>
        <div class="card-body">
            <table id="regionsTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($regions as $region)
                        <tr>
                            <td>{{ $region->id }}</td>
                            <td>{{ $region->name }}</td>
                            <td><img src="{{ asset('storage/' . $region->image_path) }}" width="50" alt="{{ $region->name }}"></td>
                            <td>
                                <a href="{{ route('regions.show', $region) }}" class="btn btn-info">View</a>
                                <a href="{{ route('regions.edit', $region) }}" class="btn btn-warning">Edit</a>
                                <form action="{{ route('regions.destroy', $region) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#regionsTable').DataTable();
    });
</script>
@endpush
@endsection
