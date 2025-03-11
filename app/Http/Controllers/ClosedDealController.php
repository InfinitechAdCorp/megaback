<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClosedDeal;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
class ClosedDealController extends Controller
{
      public function getClosedDealStatistics()
{
    $totalcloseddeal = ClosedDeal::count(); // Count all seminars

    return response()->json([
        'totalcloseddeal' => $totalcloseddeal,
    ]);
}
    public function index()
    {
        return response()->json(ClosedDeal::all(), 200);
    }
public function store(Request $request)
{
    // Log the incoming request data
    Log::info("🔹 Incoming Closed Deal Data:", $request->all());

    // Validate input
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        'date' => 'required|date',
    ]);

    // Check if an image is uploaded
    if (!$request->hasFile('image')) {
        Log::error("❌ No image file uploaded.");
        return response()->json(["error" => "No image file uploaded."], 400);
    }

    $image = $request->file('image');

    // Log details about the uploaded file
    Log::info("🖼️ Image File Details:", [
        'original_name' => $image->getClientOriginalName(),
        'mime_type' => $image->getMimeType(),
        'size' => $image->getSize(),
        'temporary_path' => $image->getPathname(),
    ]);

    // Generate a unique filename
    $imageName = time() . '-' . $image->getClientOriginalName();
    $destinationPath = public_path('closed-deals');

    // Attempt to move the file
    if (!$image->move($destinationPath, $imageName)) {
        Log::error("❌ Failed to move uploaded image to destination.");
        return response()->json(["error" => "Image upload failed."], 500);
    }

    Log::info("✅ Image successfully moved:", ['path' => "/closed-deals/{$imageName}"]);

    // Create the closed deal record
    $closedDeal = ClosedDeal::create([
        'title' => $request->title,
        'description' => $request->description,
        'image' => '/closed-deals/' . $imageName,
        'date' => $request->date,
    ]);

    // Log success message
    Log::info("✅ Closed Deal Created Successfully:", [
        'id' => $closedDeal->id,
        'title' => $closedDeal->title,
        'image_url' => $closedDeal->image,
    ]);

    return response()->json($closedDeal, 201);
}
// Get a single closed deal by ID
public function show($id)
{
    $closedDeal = ClosedDeal::find($id);

    if (!$closedDeal) {
        return response()->json(['message' => 'Closed deal not found'], 404);
    }

    return response()->json($closedDeal);
}

    // Update a closed deal
    public function update(Request $request, $id)
    {
        $closedDeal = ClosedDeal::find($id);

        if (!$closedDeal) {
            return response()->json(['message' => 'Closed deal not found'], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
            'date' => 'required|date',
        ]);

        if ($request->hasFile('image')) {
            $oldImage = public_path($closedDeal->image);
            if (File::exists($oldImage)) {
                File::delete($oldImage);
            }

            $image = $request->file('image');
            $imageName = time() . '-' . $image->getClientOriginalName();
            $image->move(public_path('closed-deals'), $imageName);

            $closedDeal->image = '/closed-deals/' . $imageName;
        }

        $closedDeal->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return response()->json($closedDeal);
    }

    // Delete a closed deal
    public function destroy($id)
    {
        $closedDeal = ClosedDeal::find($id);

        if (!$closedDeal) {
            return response()->json(['message' => 'Closed deal not found'], 404);
        }

        $imagePath = public_path($closedDeal->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $closedDeal->delete();

        return response()->json(['message' => 'Closed deal deleted successfully'], 200);
    }
}