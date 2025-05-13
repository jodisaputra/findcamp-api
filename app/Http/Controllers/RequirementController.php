<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\Country;
use Illuminate\Http\Request;

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
            'status' => 'boolean',
            'requires_payment' => 'sometimes|boolean',
            'countries' => 'array',
            'countries.*' => 'exists:countries,id'
        ]);

        $requirement = Requirement::create([
            'requirement_name' => $validated['requirement_name'],
            'status' => $validated['status'] ?? true,
            'requires_payment' => $request->has('requires_payment'),
        ]);

        if (isset($validated['countries'])) {
            $requirement->countries()->attach($validated['countries']);
        }

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
            'status' => 'boolean',
            'requires_payment' => 'sometimes|boolean',
            'countries' => 'array',
            'countries.*' => 'exists:countries,id'
        ]);

        $requirement->update([
            'requirement_name' => $validated['requirement_name'],
            'status' => $validated['status'] ?? true,
            'requires_payment' => $request->has('requires_payment'),
        ]);

        if (isset($validated['countries'])) {
            $requirement->countries()->sync($validated['countries']);
        }

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
