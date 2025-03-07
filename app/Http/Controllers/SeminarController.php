<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seminar;
use Illuminate\Support\Facades\File;  // Import File Facade for file operations

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seminar;
use Illuminate\Support\Facades\File;  // Import File Facade for file operations

class SeminarController extends Controller
{
    public function getSeminarStatistics()
{
    $totalSeminars = Seminar::count(); // Count all seminars

    return response()->json([
        'totalSeminars' => $totalSeminars,
    ]);
}

    public function index()
    {
        return response()->json(Seminar::all(), 200);
    }

    // Store a new seminar
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',  // Validate image file
            'date' => 'required|date',
        ]);

        // Handle image upload manually and store it in the public/seminar folder
        $image = $request->file('image');
        $imageName = time() . '-' . $image->getClientOriginalName();  // Generate a unique file name
        $image->move(public_path('seminar'), $imageName);  // Move the file to public/seminar

        // Create the seminar
        $seminar = Seminar::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => '/seminar/' . $imageName,  // Store the path of the image relative to the public directory, with '/'
            'date' => $request->date,
        ]);

        return response()->json($seminar, 201);
    }

    // Get a single seminar by ID
    public function show($id)
    {
        $seminar = Seminar::find($id);

        if (!$seminar) {
            return response()->json(['message' => 'Seminar not found'], 404);
        }

        // Return the full URL of the image (public access)
        $seminar->image = asset($seminar->image);

        return response()->json($seminar);
    }

    public function update(Request $request, $id)
{
    $seminar = Seminar::find($id);

    if (!$seminar) {
        return response()->json(['message' => 'Seminar not found'], 404);
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',  // Validate image file (only if it's being updated)
        'date' => 'required|date',
    ]);

    // If there's a new image, handle the upload
    if ($request->hasFile('image')) {
        // Delete the old image if it exists
        $oldImage = public_path($seminar->image);
        if (File::exists($oldImage)) {
            File::delete($oldImage);
        }

        // Handle new image upload
        $image = $request->file('image');
        $imageName = time() . '-' . $image->getClientOriginalName();  // Generate a unique file name
        $image->move(public_path('seminar'), $imageName);  // Move the file to public/seminar

        $seminar->image = '/seminar/' . $imageName;  // Update the path in the database, with '/'
    }

    // Update other fields
    $seminar->update([
        'title' => $request->title,
        'description' => $request->description,
        'date' => $request->date,
    ]);

    return response()->json($seminar);
}


    // Delete a seminar
    public function destroy($id)
    {
        $seminar = Seminar::find($id);

        if (!$seminar) {
            return response()->json(['message' => 'Seminar not found'], 404);
        }

        // Delete the seminar's image from the public/seminar folder
        $imagePath = public_path($seminar->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $seminar->delete();

        return response()->json(['message' => 'Seminar deleted successfully'], 200);
    }
}

