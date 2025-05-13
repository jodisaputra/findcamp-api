<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CampusController extends Controller
{
    public function index()
    {
        $campuses = Campus::with('country')->latest()->paginate(10);
        return view('campuses.index', compact('campuses'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        return view('campuses.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'banner' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'rating' => 'required|numeric|min:0|max:5',
            'description' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'website' => 'required|url',
            'country_id' => 'required|exists:countries,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('campuses/logos', 'public');
            $data['logo'] = $logoPath;
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('campuses/banners', 'public');
            $data['banner'] = $bannerPath;
        }

        Campus::create($data);
        return redirect()->route('campuses.index')
            ->with('success', 'Campus created successfully.');
    }

    public function edit(Campus $campus)
    {
        $countries = Country::orderBy('name')->get();
        return view('campuses.edit', compact('campus', 'countries'));
    }

    public function update(Request $request, Campus $campus)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'rating' => 'required|numeric|min:0|max:5',
            'description' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'website' => 'required|url',
            'country_id' => 'required|exists:countries,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($campus->logo) {
                Storage::disk('public')->delete($campus->logo);
            }
            $logoPath = $request->file('logo')->store('campuses/logos', 'public');
            $data['logo'] = $logoPath;
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            // Delete old banner
            if ($campus->banner) {
                Storage::disk('public')->delete($campus->banner);
            }
            $bannerPath = $request->file('banner')->store('campuses/banners', 'public');
            $data['banner'] = $bannerPath;
        }

        $campus->update($data);
        return redirect()->route('campuses.index')
            ->with('success', 'Campus updated successfully.');
    }

    public function destroy(Campus $campus)
    {
        // Delete associated files
        if ($campus->logo) {
            Storage::disk('public')->delete($campus->logo);
        }
        if ($campus->banner) {
            Storage::disk('public')->delete($campus->banner);
        }

        $campus->delete();
        return redirect()->route('campuses.index')
            ->with('success', 'Campus deleted successfully.');
    }

    public function getByCountryId($id)
    {
        $campuses = Campus::with('country')->where('country_id', $id)->get();
        return response()->json(['data' => $campuses]);
    }
} 