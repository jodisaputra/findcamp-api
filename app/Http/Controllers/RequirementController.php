<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequirementController extends Controller
{
    public function index()
    {
        $requirements = Requirement::with('countries')->get();
        return view('requirements.index', compact('requirements'));
    }

    public function create()
    {
        $countries = Country::all();
        return view('requirements.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'requirement_name' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'boolean',
            'requires_payment' => 'boolean',
            'countries' => 'required|array',
            'countries.*' => 'exists:countries,id'
        ]);

        $requirement = Requirement::create($validated);

        $requirement->countries()->sync($request->countries);

        return redirect()->route('requirements.index')
            ->with('success', 'Requirement created successfully.');
    }

    public function edit(Requirement $requirement)
    {
        $countries = Country::all();
        return view('requirements.edit', compact('requirement', 'countries'));
    }

    public function update(Request $request, Requirement $requirement)
    {
        $validated = $request->validate([
            'requirement_name' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'boolean',
            'requires_payment' => 'boolean',
            'countries' => 'required|array',
            'countries.*' => 'exists:countries,id'
        ]);

        $requirement->update($validated);
        $requirement->countries()->sync($request->countries);

        return redirect()->route('requirements.index')
            ->with('success', 'Requirement updated successfully.');
    }

    public function destroy(Requirement $requirement)
    {
        $requirement->delete();
        return redirect()->route('requirements.index')
            ->with('success', 'Requirement deleted successfully.');
    }

    public function manageCountryRequirements()
    {
        $countries = Country::with('requirements')->get();
        $requirements = Requirement::where('status', true)->get();
        return view('requirements.manage_country', compact('countries', 'requirements'));
    }

    public function updateCountryRequirements(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'requirements' => 'array',
            'requirements.*' => 'exists:requirements,id'
        ]);

        $country = Country::findOrFail($validated['country_id']);
        $requirements = $validated['requirements'] ?? [];
        $country->requirements()->sync($requirements);

        return redirect()->route('requirements.manage-country')
            ->with('success', 'Country requirements updated successfully.');
    }
}
