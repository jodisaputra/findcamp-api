@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Edit Region</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('regions.update', $region) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $region->name }}" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image</label>
                    <input type="file" class="form-control" id="image" name="image">
                    <img src="{{ asset('storage/' . $region->image_path) }}" class="img-fluid mt-2" alt="{{ $region->name }}">
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
