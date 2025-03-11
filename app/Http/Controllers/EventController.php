<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\File;  // Import File Facade for file operations
use Illuminate\Support\Facades\Log; 
class EventController extends Controller
{
        public function getEventStatistics()
{
    $totalEvents = Event::count(); // Count all seminars

    return response()->json([
        'totalEvents' => $totalEvents,
    ]);
}
    public function index()
    {
        return response()->json(Event::all(), 200);
    }

public function store(Request $request)
{
    Log::info('Store method called', ['request_data' => $request->all()]);

    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'media_type' => 'required|in:image,video', // Media type (either image or video)
        'date' => 'required|date',
        'image' => 'required_if:media_type,image|image|mimes:jpeg,png,jpg,gif|max:5120', // Validate image if media type is image
        'file' => 'required_if:media_type,video|mimes:mp4,mov,avi|max:10240', // Validate video file if media type is video
    ]);

    Log::info('Validation passed for event creation', ['data' => $request->only(['title', 'description', 'media_type', 'date'])]);

    // Handle file uploads
    $filePath = null;

    // Handling image upload if media type is 'image'
    if ($request->media_type == 'image') {
        $image = $request->file('image');
        $imageName = time() . '-' . $image->getClientOriginalName();  // Generate a unique file name
        // Move the file to the public/event/images folder
        $image->move(public_path('event/images'), $imageName);  
        $filePath = '/event/images/' . $imageName;

        Log::info('Image uploaded', ['image_path' => $filePath]);
    }

    // Handling video upload if media type is 'video'
    if ($request->media_type == 'video') {
        if ($request->hasFile('file')) {
            $video = $request->file('file');
            $videoName = time() . '-' . $video->getClientOriginalName();  // Generate a unique file name
            // Move the file to the public/event/videos folder
            $video->move(public_path('event/videos'), $videoName);  
            $filePath = '/event/videos/' . $videoName;

            Log::info('Video uploaded', ['video_path' => $filePath]);
        } else {
            Log::warning('No video file uploaded, but media type is video');
        }
    }

    // Create the event
    try {
        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $request->media_type == 'image' ? $filePath : null,  // Save the image path if media type is image
            'file' => $request->media_type == 'video' ? $filePath : null,  // Save the video path if media type is video
            'media_type' => $request->media_type,  // Store media type (image or video)
            'date' => $request->date,
        ]);

        Log::info('Event created successfully', ['event_id' => $event->id, 'title' => $event->title]);

        return response()->json($event, 201);
    } catch (\Exception $e) {
        Log::error('Error creating event', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Error creating event'], 500);
    }
}
public function update(Request $request, $id)
{
    $event = Event::find($id);

    if (!$event) {
        return response()->json(['message' => 'Event not found'], 404);
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'media_type' => 'required|in:image,video',  // Media type (either image or video)
        'date' => 'required|date',
        'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',  // Validate image if it's being updated
        'file' => 'sometimes|mimes:mp4,mov,avi|max:10240',  // Validate video file if it's being updated
    ]);

    Log::info('Update method called', ['request_data' => $request->all()]);

    // Handle file uploads
    $filePath = null;

    // Handling image upload if media type is 'image'
    if ($request->media_type == 'image' && $request->hasFile('image')) {
        // Delete the old image if it exists
        $oldImage = public_path($event->image);
        if (File::exists($oldImage)) {
            File::delete($oldImage);
        }

        $image = $request->file('image');
        $imageName = time() . '-' . $image->getClientOriginalName();  // Generate a unique file name
        // Move the file to the public/event/images folder
        $image->move(public_path('event/images'), $imageName);  
        $filePath = '/event/images/' . $imageName;

        Log::info('Image uploaded', ['image_path' => $filePath]);
    }

    // Handling video upload if media type is 'video'
    if ($request->media_type == 'video' && $request->hasFile('file')) {
        // Delete the old video if it exists
        $oldFile = public_path($event->file); // Use the correct property here (file, not image)
        if (File::exists($oldFile)) {
            File::delete($oldFile);
        }

        $video = $request->file('file');
        $videoName = time() . '-' . $video->getClientOriginalName();  // Generate a unique file name
        // Move the file to the public/event/videos folder
        $video->move(public_path('event/videos'), $videoName);  
        $filePath = '/event/videos/' . $videoName;

        Log::info('Video uploaded', ['video_path' => $filePath]);
    }

    // Update the event with the new data
    try {
        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'media_type' => $request->media_type,  // Ensure the media type is updated
            'image' => $request->media_type == 'image' ? $filePath : $event->image, // Only update the image if media type is image
            'file' => $request->media_type == 'video' ? $filePath : $event->file,  // Only update the file if media type is video
        ]);

        Log::info('Event updated successfully', ['event_id' => $event->id, 'title' => $event->title]);

        return response()->json($event, 200);
    } catch (\Exception $e) {
        Log::error('Error updating event', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Error updating event'], 500);
    }
}


    // Get a single event by ID
    public function show($id)
{
    $event = Event::find($id);

    if (!$event) {
        return response()->json(['message' => 'Event not found'], 404);
    }

    return response()->json($event);
}


    // Update an event
    

    // Delete an event
    public function destroy($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Delete the event's image or video from the appropriate folder
        $mediaPath = public_path($event->image);
        if (File::exists($mediaPath)) {
            File::delete($mediaPath);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully'], 200);
    }
}
