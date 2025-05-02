@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ $country->name }}</h5>
        </div>
        <div class="card-body">
            <img src="{{ asset('storage/' . $country->flag_path) }}" class="img-fluid mb-3" alt="{{ $country->name }}">
            <p><strong>Region:</strong> {{ $country->region->name }}</p>
            <p><strong>Rating:</strong> {{ $country->rating }}</p>
            <p><strong>Description:</strong> {{ $country->description }}</p>
            <a href="{{ route('countries.edit', $country) }}" class="btn btn-warning">Edit</a>
            <form action="{{ route('countries.destroy', $country) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
