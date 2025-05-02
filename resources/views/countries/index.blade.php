@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Countries</h1>
    <a href="{{ route('countries.create') }}" class="btn btn-primary mb-3">Create New Country</a>
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Countries Table</h5>
        </div>
        <div class="card-body">
            <table id="countriesTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Region</th>
                        <th>Flag</th>
                        <th>Rating</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($countries as $country)
                        <tr>
                            <td>{{ $country->id }}</td>
                            <td>{{ $country->name }}</td>
                            <td>{{ $country->region->name }}</td>
                            <td><img src="{{ asset('storage/' . $country->flag_path) }}" width="50" alt="{{ $country->name }}"></td>
                            <td>{{ $country->rating }}</td>
                            <td>
                                <a href="{{ route('countries.show', $country) }}" class="btn btn-info">View</a>
                                <a href="{{ route('countries.edit', $country) }}" class="btn btn-warning">Edit</a>
                                <form action="{{ route('countries.destroy', $country) }}" method="POST" style="display:inline;">
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
        $('#countriesTable').DataTable();
    });
</script>
@endpush
@endsection
