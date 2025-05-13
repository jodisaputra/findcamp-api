@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Campuses</h2>
                    <a href="{{ route('campuses.create') }}" class="btn btn-primary">Add New Campus</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Logo</th>
                                    <th>Name</th>
                                    <th>Country</th>
                                    <th>Rating</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($campuses as $campus)
                                    <tr>
                                        <td>
                                            @if($campus->logo)
                                                <img src="{{ asset('storage/' . $campus->logo) }}" alt="{{ $campus->name }}" class="img-thumbnail" style="max-height: 50px;">
                                            @else
                                                No Logo
                                            @endif
                                        </td>
                                        <td>{{ $campus->name }}</td>
                                        <td>{{ $campus->country->name }}</td>
                                        <td>{{ number_format($campus->rating, 1) }}/5.0</td>
                                        <td>{{ $campus->phone }}</td>
                                        <td>{{ $campus->email }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('campuses.edit', $campus) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form action="{{ route('campuses.destroy', $campus) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this campus?')">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $campuses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 