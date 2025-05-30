@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Add New Requirement</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('requirements.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="requirement_name" class="form-label">Requirement Name</label>
                            <input type="text" class="form-control @error('requirement_name') is-invalid @enderror" 
                                id="requirement_name" name="requirement_name" value="{{ old('requirement_name') }}" required>
                            @error('requirement_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional notes about this requirement.</div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input @error('status') is-invalid @enderror" 
                                    id="status" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">Active</label>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input @error('requires_payment') is-invalid @enderror" 
                                    id="requires_payment" name="requires_payment" value="1" {{ old('requires_payment') ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_payment">Requires Payment</label>
                                @error('requires_payment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Countries</label>
                            <div class="row">
                                @foreach($countries as $country)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" 
                                                name="countries[]" value="{{ $country->id }}" 
                                                id="country_{{ $country->id }}"
                                                {{ in_array($country->id, old('countries', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="country_{{ $country->id }}">
                                                {{ $country->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('countries')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('requirements.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Requirement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 