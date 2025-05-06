<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryCollection;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $query = Country::with('region');

        // Filter by region if provided
        if ($request->has('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        // Search by name if provided
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('region', function($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $countries = $query->get();

        return new CountryCollection($countries);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'flag' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'rating' => 'required|numeric|min:0|max:5',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $flagPath = $request->file('flag')->store('flags', 'public');

        $country = Country::create([
            'name' => $request->name,
            'region_id' => $request->region_id,
            'flag_path' => $flagPath,
            'rating' => $request->rating,
            'description' => $request->description,
        ]);

        return new CountryResource($country);
    }

    public function show(Country $country)
    {
        $country->load('region');
        return new CountryResource($country);
    }

    public function update(Request $request, Country $country)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'region_id' => 'sometimes|exists:regions,id',
            'flag' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'rating' => 'sometimes|numeric|min:0|max:5',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('name')) {
            $country->name = $request->name;
        }

        if ($request->has('region_id')) {
            $country->region_id = $request->region_id;
        }

        if ($request->hasFile('flag')) {
            // Delete old flag
            Storage::disk('public')->delete($country->flag_path);

            // Upload new flag
            $country->flag_path = $request->file('flag')->store('flags', 'public');
        }

        if ($request->has('rating')) {
            $country->rating = $request->rating;
        }

        if ($request->has('description')) {
            $country->description = $request->description;
        }

        $country->save();

        return new CountryResource($country);
    }

    public function destroy(Country $country)
    {
        // Delete flag
        if ($country->flag_path) {
            Storage::disk('public')->delete($country->flag_path);
        }

        $country->delete();

        return response()->json(['message' => 'Country deleted successfully']);
    }

    public function getRequirements(Country $country)
    {
        return response()->json($country->requirements);
    }
}
