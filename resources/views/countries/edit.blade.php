@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Edit Country</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('countries.update', $country) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $country->name }}" required>
                </div>
                <div class="mb-3">
                    <label for="region_id" class="form-label">Region</label>
                    <select class="form-control" id="region_id" name="region_id" required>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ $country->region_id == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="flag" class="form-label">Flag</label>
                    <input type="file" class="form-control" id="flag" name="flag">
                    <img src="{{ asset('storage/' . $country->flag_path) }}" class="img-fluid mt-2" alt="{{ $country->name }}">
                </div>
                <div class="mb-3">
                    <label for="rating" class="form-label">Rating</label>
                    <input type="number" class="form-control" id="rating" name="rating" min="0" max="5" step="0.1" value="{{ $country->rating }}" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description">{{ $country->description }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
