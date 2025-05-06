@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Manage Country Requirements</h5>
                    <a href="{{ route('requirements.index') }}" class="btn btn-secondary">Back to Requirements</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            <div class="list-group">
                                @foreach($countries as $country)
                                    <a href="#" class="list-group-item list-group-item-action country-selector" 
                                       data-country-id="{{ $country->id }}"
                                       data-country-name="{{ $country->name }}">
                                        {{ $country->name }}
                                        <span class="badge bg-primary float-end">
                                            {{ $country->requirements->count() }} requirements
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-8">
                            <form id="requirementsForm" action="{{ route('requirements.update-country') }}" method="POST">
                                @csrf
                                <input type="hidden" name="country_id" id="country_id">
                                
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Requirements for <span id="selectedCountry">Select a country</span></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Requirement</th>
                                                        <th>Required</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($requirements as $requirement)
                                                        <tr>
                                                            <td>{{ $requirement->requirement_name }}</td>
                                                            <td>
                                                                <div class="form-check">
                                                                    <input class="form-check-input requirement-checkbox" 
                                                                           type="checkbox" 
                                                                           name="requirements[]" 
                                                                           value="{{ $requirement->id }}"
                                                                           data-requirement-id="{{ $requirement->id }}">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-primary" id="saveButton" disabled>Save Changes</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countrySelectors = document.querySelectorAll('.country-selector');
    const requirementsForm = document.getElementById('requirementsForm');
    const countryIdInput = document.getElementById('country_id');
    const selectedCountrySpan = document.getElementById('selectedCountry');
    const saveButton = document.getElementById('saveButton');
    const requirementCheckboxes = document.querySelectorAll('.requirement-checkbox');

    countrySelectors.forEach(selector => {
        selector.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all selectors
            countrySelectors.forEach(s => s.classList.remove('active'));
            // Add active class to clicked selector
            this.classList.add('active');

            const countryId = this.dataset.countryId;
            const countryName = this.dataset.countryName;

            // Update form
            countryIdInput.value = countryId;
            selectedCountrySpan.textContent = countryName;
            saveButton.disabled = false;

            // Reset all checkboxes
            requirementCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            // Fetch country requirements
            fetch(`/api/countries/${countryId}/requirements`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(req => {
                        const checkbox = document.querySelector(`input[data-requirement-id="${req.id}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });
                });
        });
    });

    // Enable save button only if a country is selected
    requirementsForm.addEventListener('submit', function(e) {
        // No need to preventDefault, let the form submit normally
    });
});
</script>
@endpush

@push('styles')
<style>
.country-selector.active {
    background-color: #e9ecef;
    border-color: #dee2e6;
}
</style>
@endpush
@endsection 