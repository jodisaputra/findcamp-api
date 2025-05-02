<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::with('region')->get();
        return view('countries.index', compact('countries'));
    }

    public function create()
    {
        $regions = Region::all();
        return view('countries.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'flag' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'rating' => 'required|numeric|min:0|max:5',
            'description' => 'nullable|string',
        ]);

        $flagPath = $request->file('flag')->store('flags', 'public');

        Country::create([
            'name' => $request->name,
            'region_id' => $request->region_id,
            'flag_path' => $flagPath,
            'rating' => $request->rating,
            'description' => $request->description,
        ]);

        return redirect()->route('countries.index')->with('success', 'Country created successfully.');
    }

    public function show(Country $country)
    {
        return view('countries.show', compact('country'));
    }

    public function edit(Country $country)
    {
        $regions = Region::all();
        return view('countries.edit', compact('country', 'regions'));
    }

    public function update(Request $request, Country $country)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'flag' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'rating' => 'required|numeric|min:0|max:5',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('flag')) {
            Storage::disk('public')->delete($country->flag_path);
            $country->flag_path = $request->file('flag')->store('flags', 'public');
        }

        $country->name = $request->name;
        $country->region_id = $request->region_id;
        $country->rating = $request->rating;
        $country->description = $request->description;
        $country->save();

        return redirect()->route('countries.index')->with('success', 'Country updated successfully.');
    }

    public function destroy(Country $country)
    {
        if ($country->flag_path) {
            Storage::disk('public')->delete($country->flag_path);
        }
        $country->delete();
        return redirect()->route('countries.index')->with('success', 'Country deleted successfully.');
    }
}
