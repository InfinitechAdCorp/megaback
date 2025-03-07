<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    // Get all locations
    public function index()
    {
        return response()->json(Location::all(), 200);
    }

    // Store a new location
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $location = Location::create([
            'name' => $request->name,
        ]);

        return response()->json($location, 201);
    }

    // Get a single location by ID
    public function show($id)
    {
        $location = Location::find($id);

        if (!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }

        return response()->json($location);
    }

    // Update a location
    public function update(Request $request, $id)
    {
        $location = Location::find($id);

        if (!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $location->update([
            'name' => $request->name,
        ]);

        return response()->json($location);
    }

    // Delete a location
    public function destroy($id)
    {
        $location = Location::find($id);

        if (!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }

        $location->delete();

        return response()->json(['message' => 'Location deleted successfully'], 200);
    }
}
