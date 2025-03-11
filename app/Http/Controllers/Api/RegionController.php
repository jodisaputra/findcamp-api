<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RegionResource;
use App\Http\Resources\RegionCollection;
use App\Http\Resources\CountryResource;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::all();
        return new RegionCollection($regions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:regions',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = $request->file('image')->store('regions', 'public');

        $region = Region::create([
            'name' => $request->name,
            'image_path' => $imagePath,
        ]);

        return new RegionResource($region);
    }

    public function show(Region $region)
    {
        return new RegionResource($region);
    }

    public function update(Request $request, Region $region)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:regions,name,' . $region->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('name')) {
            $region->name = $request->name;
        }

        if ($request->hasFile('image')) {
            // Delete old image
            Storage::disk('public')->delete($region->image_path);

            // Upload new image
            $region->image_path = $request->file('image')->store('regions', 'public');
        }

        $region->save();

        return new RegionResource($region);
    }

    public function destroy(Region $region)
    {
        // Delete image
        if ($region->image_path) {
            Storage::disk('public')->delete($region->image_path);
        }

        $region->delete();

        return response()->json(['message' => 'Region deleted successfully']);
    }

    public function getCountries(Region $region)
    {
        $countries = $region->countries;
        return CountryResource::collection($countries);
    }
}
