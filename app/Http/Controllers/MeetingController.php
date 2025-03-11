<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;
use Illuminate\Support\Facades\File;  // Import File Facade for file operations

class MeetingController extends Controller
{
       public function getMeetingStatistics()
{
    $totalMeetings = Meeting::count(); // Count all seminars

    return response()->json([
        'totalMeetings' => $totalMeetings,
    ]);
}
    public function index()
    {
        return response()->json(Meeting::all(), 200);
    }

    // Store a new meeting
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',  // Validate image file
            'date' => 'required|date',
        ]);

        // Handle image upload manually and store it in the public/meeting folder
        $image = $request->file('image');
        $imageName = time() . '-' . $image->getClientOriginalName();  // Generate a unique file name
        $image->move(public_path('meeting'), $imageName);  // Move the file to public/meeting

        // Create the meeting
        $meeting = Meeting::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => '/meeting/' . $imageName,  // Store the path of the image relative to the public directory, with '/'
            'date' => $request->date,
        ]);

        return response()->json($meeting, 201);
    }

    // Get a single meeting by ID
public function show($id)
{
    $meeting = Meeting::find($id);

    if (!$meeting) {
        return response()->json(['message' => 'Meeting not found'], 404);
    }

    return response()->json($meeting);
}

    public function update(Request $request, $id)
    {
        $meeting = Meeting::find($id);

        if (!$meeting) {
            return response()->json(['message' => 'Meeting not found'], 404);
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
            $oldImage = public_path($meeting->image);
            if (File::exists($oldImage)) {
                File::delete($oldImage);
            }

            // Handle new image upload
            $image = $request->file('image');
            $imageName = time() . '-' . $image->getClientOriginalName();  // Generate a unique file name
            $image->move(public_path('meeting'), $imageName);  // Move the file to public/meeting

            $meeting->image = '/meeting/' . $imageName;  // Update the path in the database, with '/'
        }

        // Update other fields
        $meeting->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return response()->json($meeting);
    }

    // Delete a meeting
    public function destroy($id)
    {
        $meeting = Meeting::find($id);

        if (!$meeting) {
            return response()->json(['message' => 'Meeting not found'], 404);
        }

        // Delete the meeting's image from the public/meeting folder
        $imagePath = public_path($meeting->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $meeting->delete();

        return response()->json(['message' => 'Meeting deleted successfully'], 200);
    }
}
