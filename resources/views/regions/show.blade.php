@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ $region->name }}</h5>
        </div>
        <div class="card-body">
            <img src="{{ asset('storage/' . $region->image_path) }}" class="img-fluid mb-3" alt="{{ $region->name }}">
            <a href="{{ route('regions.edit', $region) }}" class="btn btn-warning">Edit</a>
            <form action="{{ route('regions.destroy', $region) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
