@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Edit Campus</h2>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('campuses.update', $campus) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="name">Campus Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $campus->name) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="country">Country</label>
                            <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $campus->country) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="logo">Logo URL</label>
                            <input type="text" class="form-control" id="logo" name="logo" value="{{ old('logo', $campus->logo) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="banner">Banner URL</label>
                            <input type="text" class="form-control" id="banner" name="banner" value="{{ old('banner', $campus->banner) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="rating">Rating (0-5)</label>
                            <input type="number" class="form-control" id="rating" name="rating" step="0.1" min="0" max="5" value="{{ old('rating', $campus->rating) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description', $campus->description) }}</textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $campus->phone) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $campus->email) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="website">Website</label>
                            <input type="url" class="form-control" id="website" name="website" value="{{ old('website', $campus->website) }}" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('campuses.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 