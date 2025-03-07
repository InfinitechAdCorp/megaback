<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Career;

class CareerController extends Controller
{
    // Get all careers
    public function index()
    {
        return response()->json(Career::all(), 200);
    }

    // Store a new career
    public function store(Request $request)
    {
        $validated = $request->validate([
            'roleName' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
        ]);

        $career = Career::create($validated);

        return response()->json($career, 201);
    }

    // Get a single career by ID
    public function show($id)
    {
        $career = Career::find($id);
        if (!$career) {
            return response()->json(['message' => 'Career not found'], 404);
        }
        return response()->json($career);
    }

    // Update a career
    public function update(Request $request, $id)
    {
        $career = Career::find($id);
        if (!$career) {
            return response()->json(['message' => 'Career not found'], 404);
        }

        $validated = $request->validate([
            'roleName' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
        ]);

        $career->update($validated);

        return response()->json($career);
    }

    // Delete a career
    public function destroy($id)
    {
        $career = Career::find($id);
        if (!$career) {
            return response()->json(['message' => 'Career not found'], 404);
        }

        $career->delete();

        return response()->json(['message' => 'Career deleted successfully'], 200);
    }
}
