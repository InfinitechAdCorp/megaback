<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RealEstateTips;
use Illuminate\Support\Facades\File;

class RealEstateTipsController extends Controller
{
      public function getRealEstateTipsStatistics()
{
    $totalTips = RealEstateTips::count(); // Count all seminars

    return response()->json([
        'totalTips' => $totalTips,
    ]);
}
    public function index()
    {
        return response()->json(RealEstateTips::all(), 200);
    }

    // Store a new tip
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
            'date' => 'required|date',
        ]);

        // Handle image upload if provided
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . $image->getClientOriginalName();
            $image->move(public_path('real-estate-tips'), $imageName);
            $imagePath = '/real-estate-tips/' . $imageName;
        }

        // Create the tip
        $tip = RealEstateTips::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'date' => $request->date,
        ]);

        return response()->json($tip, 201);
    }

    // Get a single tip by ID
    public function show($id)
    {
        $tip = RealEstateTips::find($id);

        if (!$tip) {
            return response()->json(['message' => 'Tip not found'], 404);
        }

        $tip->image = asset($tip->image);

        return response()->json($tip);
    }

    // Update a tip
    public function update(Request $request, $id)
    {
        $tip = RealEstateTips::find($id);

        if (!$tip) {
            return response()->json(['message' => 'Tip not found'], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
            'date' => 'required|date',
        ]);

        // Handle new image upload
        if ($request->hasFile('image')) {
            $oldImage = public_path($tip->image);
            if (File::exists($oldImage)) {
                File::delete($oldImage);
            }

            $image = $request->file('image');
            $imageName = time() . '-' . $image->getClientOriginalName();
            $image->move(public_path('real-estate-tips'), $imageName);

            $tip->image = '/real-estate-tips/' . $imageName;
        }

        $tip->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return response()->json($tip);
    }

    // Delete a tip
    public function destroy($id)
    {
        $tip = RealEstateTips::find($id);

        if (!$tip) {
            return response()->json(['message' => 'Tip not found'], 404);
        }

        $imagePath = public_path($tip->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $tip->delete();

        return response()->json(['message' => 'Tip deleted successfully'], 200);
    }
}
