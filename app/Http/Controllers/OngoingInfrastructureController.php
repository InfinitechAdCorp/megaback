<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OngoingInfrastructure;
use Illuminate\Support\Facades\File;

class OngoingInfrastructureController extends Controller
{
       public function getOngoingInfrastructureStatistics()
{
    $totalOngoing = OngoingInfrastructure::count(); // Count all seminars

    return response()->json([
        'totalOngoing' => $totalOngoing,
    ]);
}

    public function index()
    {
        return response()->json(OngoingInfrastructure::all(), 200);
    }

    // Store a new infrastructure
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
            $image->move(public_path('ongoing-infrastructure'), $imageName);
            $imagePath = '/ongoing-infrastructure/' . $imageName;
        }

        // Create the infrastructure record
        $infrastructure = OngoingInfrastructure::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'date' => $request->date,
        ]);

        return response()->json($infrastructure, 201);
    }

    // Get a single infrastructure by ID
    public function show($id)
    {
        $infrastructure = OngoingInfrastructure::find($id);

        if (!$infrastructure) {
            return response()->json(['message' => 'Infrastructure not found'], 404);
        }

        $infrastructure->image = asset($infrastructure->image);

        return response()->json($infrastructure);
    }

    // Update an infrastructure record
    public function update(Request $request, $id)
    {
        $infrastructure = OngoingInfrastructure::find($id);

        if (!$infrastructure) {
            return response()->json(['message' => 'Infrastructure not found'], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
            'date' => 'required|date',
        ]);

        // Handle new image upload
        if ($request->hasFile('image')) {
            $oldImage = public_path($infrastructure->image);
            if (File::exists($oldImage)) {
                File::delete($oldImage);
            }

            $image = $request->file('image');
            $imageName = time() . '-' . $image->getClientOriginalName();
            $image->move(public_path('ongoing-infrastructure'), $imageName);

            $infrastructure->image = '/ongoing-infrastructure/' . $imageName;
        }

        $infrastructure->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return response()->json($infrastructure);
    }

    // Delete an infrastructure record
    public function destroy($id)
    {
        $infrastructure = OngoingInfrastructure::find($id);

        if (!$infrastructure) {
            return response()->json(['message' => 'Infrastructure not found'], 404);
        }

        $imagePath = public_path($infrastructure->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $infrastructure->delete();

        return response()->json(['message' => 'Infrastructure deleted successfully'], 200);
    }
}
