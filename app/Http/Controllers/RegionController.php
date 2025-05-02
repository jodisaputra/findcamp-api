<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::all();
        return view('regions.index', compact('regions'));
    }

    public function create()
    {
        return view('regions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:regions',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $imagePath = $request->file('image')->store('regions', 'public');

        Region::create([
            'name' => $request->name,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('regions.index')->with('success', 'Region created successfully.');
    }

    public function show(Region $region)
    {
        return view('regions.show', compact('region'));
    }

    public function edit(Region $region)
    {
        return view('regions.edit', compact('region'));
    }

    public function update(Request $request, Region $region)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:regions,name,' . $region->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($region->image_path);
            $region->image_path = $request->file('image')->store('regions', 'public');
        }

        $region->name = $request->name;
        $region->save();

        return redirect()->route('regions.index')->with('success', 'Region updated successfully.');
    }

    public function destroy(Region $region)
    {
        if ($region->image_path) {
            Storage::disk('public')->delete($region->image_path);
        }
        $region->delete();
        return redirect()->route('regions.index')->with('success', 'Region deleted successfully.');
    }
}
