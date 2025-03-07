<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Testimonial;

class TestimonialController extends Controller
{
    // Get all testimonials
    public function index()
    {
        return response()->json(Testimonial::all(), 200);
    }

    // Store a new testimonial
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $testimonial = Testimonial::create($validated);

        return response()->json($testimonial, 201);
    }

    // Get a single testimonial by ID
    public function show($id)
    {
        $testimonial = Testimonial::find($id);
        if (!$testimonial) {
            return response()->json(['message' => 'Testimonial not found'], 404);
        }
        return response()->json($testimonial);
    }

    // Update a testimonial
    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::find($id);
        if (!$testimonial) {
            return response()->json(['message' => 'Testimonial not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $testimonial->update($validated);

        return response()->json($testimonial);
    }

    // Delete a testimonial
    public function destroy($id)
    {
        $testimonial = Testimonial::find($id);
        if (!$testimonial) {
            return response()->json(['message' => 'Testimonial not found'], 404);
        }

        $testimonial->delete();

        return response()->json(['message' => 'Testimonial deleted successfully'], 200);
    }
}