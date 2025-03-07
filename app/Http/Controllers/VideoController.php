<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\File;

class VideoController extends Controller
{
    // Fetch all videos
    public function index()
    {
        return response()->json(Video::all(), 200);
    }

    // Store a new video
   public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'url' => 'nullable|url',
        'file' => 'nullable|mimes:mp4,avi,mkv|max:50000',
        'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'location' => 'nullable|string|max:255',
        'date' => 'nullable|date',
        'views' => 'nullable|integer', // ✅ Ensure views is accepted
    ]);

    $filePath = null;
    $thumbnailPath = null;

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $fileName = time() . '-' . $file->getClientOriginalName();
        $file->move(public_path('videos'), $fileName);
        $filePath = '/videos/' . $fileName;
    }

    if ($request->hasFile('thumbnail')) {
        $thumbnail = $request->file('thumbnail');
        $thumbnailName = time() . '-' . $thumbnail->getClientOriginalName();
        $thumbnail->move(public_path('videos/thumbnails'), $thumbnailName);
        $thumbnailPath = '/videos/thumbnails/' . $thumbnailName;
    }

    // ✅ Include views from the request
    $video = Video::create([
        'title' => $request->title,
        'url' => $request->url,
        'file_path' => $filePath,
        'thumbnail' => $thumbnailPath,
        'location' => $request->location,
        'date' => $request->date,
        'views' => $request->views ?? 0, // ✅ Store views correctly
    ]);

    return response()->json($video, 201);
}


    // Show a single video
    public function show($id)
    {
        $video = Video::find($id);
        if (!$video) {
            return response()->json(['message' => 'Video not found'], 404);
        }
        return response()->json($video);
    }

    // Update a video
   public function update(Request $request, $id)
{
    $video = Video::find($id);
    if (!$video) {
        return response()->json(['message' => 'Video not found'], 404);
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'url' => 'nullable|url',
        'file' => 'nullable|mimes:mp4,avi,mkv|max:50000',
        'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'location' => 'nullable|string|max:255',
        'date' => 'nullable|date',
        'views' => 'nullable|integer',  // ✅ Ensure views can be updated
    ]);

    // Handle new video file upload
    if ($request->hasFile('file')) {
        $oldFilePath = public_path($video->file_path);
        if (File::exists($oldFilePath)) {
            File::delete($oldFilePath);
        }

        $file = $request->file('file');
        $fileName = time() . '-' . $file->getClientOriginalName();
        $file->move(public_path('videos'), $fileName);
        $video->file_path = '/videos/' . $fileName;
    }

    // Handle new thumbnail upload
    if ($request->hasFile('thumbnail')) {
        $oldThumbnailPath = public_path($video->thumbnail);
        if (File::exists($oldThumbnailPath)) {
            File::delete($oldThumbnailPath);
        }

        $thumbnail = $request->file('thumbnail');
        $thumbnailName = time() . '-' . $thumbnail->getClientOriginalName();
        $thumbnail->move(public_path('videos/thumbnails'), $thumbnailName);
        $video->thumbnail = '/videos/thumbnails/' . $thumbnailName;
    }

    // ✅ Ensure views is updated
    $video->update($request->only(['title', 'url', 'location', 'date', 'views']));

    return response()->json($video);
}


    // Delete a video
    public function destroy($id)
    {
        $video = Video::find($id);
        if (!$video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        if ($video->file_path) {
            File::delete(public_path($video->file_path));
        }
        if ($video->thumbnail) {
            File::delete(public_path($video->thumbnail));
        }

        $video->delete();
        return response()->json(['message' => 'Video deleted successfully'], 200);
    }
}
