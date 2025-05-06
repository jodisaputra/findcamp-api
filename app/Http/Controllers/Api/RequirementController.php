<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequirementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requirements = Requirement::all();
        return response()->json($requirements);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'requirement_name' => 'required|string|max:255',
            'status' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $requirement = Requirement::create($request->all());
        return response()->json($requirement, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Requirement $requirement)
    {
        return response()->json($requirement);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Requirement $requirement)
    {
        $validator = Validator::make($request->all(), [
            'requirement_name' => 'string|max:255',
            'status' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $requirement->update($request->all());
        return response()->json($requirement);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Requirement $requirement)
    {
        $requirement->delete();
        return response()->json(null, 204);
    }

    public function attachToCountry(Request $request, Requirement $requirement)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'is_required' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $requirement->countries()->attach($request->country_id, [
            'is_required' => $request->is_required ?? true
        ]);

        return response()->json(['message' => 'Requirement attached to country successfully']);
    }

    public function detachFromCountry(Request $request, Requirement $requirement)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $requirement->countries()->detach($request->country_id);
        return response()->json(['message' => 'Requirement detached from country successfully']);
    }
}
