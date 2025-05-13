<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampusController extends Controller
{
    public function index()
    {
        $campuses = Campus::with('country')->get();
        return response()->json(['data' => $campuses]);
    }

    public function show($id)
    {
        $campus = Campus::with('country')->findOrFail($id);
        return response()->json(['data' => $campus]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'logo' => 'required|string',
            'banner' => 'required|string',
            'rating' => 'required|numeric|min:0|max:5',
            'description' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'website' => 'required|url',
            'country_id' => 'required|exists:countries,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $campus = Campus::create($request->all());
        return response()->json(['data' => $campus->load('country')], 201);
    }

    public function update(Request $request, $id)
    {
        $campus = Campus::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'logo' => 'string',
            'banner' => 'string',
            'rating' => 'numeric|min:0|max:5',
            'description' => 'string',
            'phone' => 'string',
            'email' => 'email',
            'website' => 'url',
            'country_id' => 'exists:countries,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $campus->update($request->all());
        return response()->json(['data' => $campus->load('country')]);
    }

    public function destroy($id)
    {
        $campus = Campus::findOrFail($id);
        $campus->delete();
        return response()->json(null, 204);
    }

    public function getByCountry($country)
    {
        // First find the country by name
        $countryModel = Country::where('name', $country)->firstOrFail();
        
        // Then get campuses for that country
        $campuses = Campus::with('country')
            ->where('country_id', $countryModel->id)
            ->get();
            
        return response()->json(['data' => $campuses]);
    }

    public function getByCountryId($id)
    {
        $campuses = Campus::with('country')->where('country_id', $id)->get();
        return response()->json(['data' => $campuses]);
    }
} 