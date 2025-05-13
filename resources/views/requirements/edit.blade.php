@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Requirement</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('requirements.update', $requirement) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="requirement_name" class="form-label">Requirement Name</label>
                            <input type="text" class="form-control @error('requirement_name') is-invalid @enderror" 
                                id="requirement_name" name="requirement_name" 
                                value="{{ old('requirement_name', $requirement->requirement_name) }}" required>
                            @error('requirement_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="status" name="status" value="1" 
                                    {{ old('status', $requirement->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="requires_payment" name="requires_payment" value="1" 
                                    {{ old('requires_payment', $requirement->requires_payment) ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_payment">Requires Payment</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Countries</label>
                            <div class="row">
                                @foreach($countries as $country)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" 
                                                id="country_{{ $country->id }}" 
                                                name="countries[]" 
                                                value="{{ $country->id }}"
                                                {{ in_array($country->id, old('countries', $requirement->countries->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="country_{{ $country->id }}">
                                                {{ $country->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('requirements.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Requirement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 