@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>{{ isset($campus) ? 'Edit Campus' : 'Add New Campus' }}</h2>
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

                    <form action="{{ isset($campus) ? route('campuses.update', $campus) : route('campuses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($campus))
                            @method('PUT')
                        @endif

                        <div class="form-group mb-3">
                            <label for="name">Campus Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $campus->name ?? '') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="country_id">Country</label>
                            <select class="form-control" id="country_id" name="country_id" required>
                                <option value="">Select a country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ (old('country_id', $campus->country_id ?? '') == $country->id) ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="logo">Logo</label>
                            @if(isset($campus) && $campus->logo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $campus->logo) }}" alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*" {{ !isset($campus) ? 'required' : '' }}>
                            <small class="form-text text-muted">Upload a logo image (PNG, JPG, JPEG)</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="banner">Banner</label>
                            @if(isset($campus) && $campus->banner)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $campus->banner) }}" alt="Current Banner" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                            <input type="file" class="form-control" id="banner" name="banner" accept="image/*" {{ !isset($campus) ? 'required' : '' }}>
                            <small class="form-text text-muted">Upload a banner image (PNG, JPG, JPEG)</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="rating">Rating (0-5)</label>
                            <input type="number" class="form-control" id="rating" name="rating" step="0.1" min="0" max="5" value="{{ old('rating', $campus->rating ?? '') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description', $campus->description ?? '') }}</textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $campus->phone ?? '') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $campus->email ?? '') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="website">Website</label>
                            <input type="url" class="form-control" id="website" name="website" value="{{ old('website', $campus->website ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ isset($campus) ? 'Update' : 'Create' }}</button>
                            <a href="{{ route('campuses.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 