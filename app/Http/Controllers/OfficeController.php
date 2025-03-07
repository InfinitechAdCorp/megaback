<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Office;
use Illuminate\Support\Facades\File;

class OfficeController extends Controller
{
    public function getOfficeStatistics()
    {
        // ✅ Count total offices
        $totalOffices = Office::count();

        // ✅ Count offices based on status
        $statusCounts = Office::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json([
            'totalOffices' => $totalOffices,
            'statusCounts' => $statusCounts,
        ]);
    }
    public function index()
    {
        return response()->json(Office::all(), 200);
    }

    public function store(Request $request)
{
    \Log::info("🔹 Raw Request Data:", $request->all());  // ✅ Debugging
    \Log::info("🔹 Uploaded File:", [$request->file('image')]);

    // Validate request
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        'location' => 'required|string',
        'status' => 'required|string|in:For Lease,For Sale,For Rent',
        'price' => 'required|string',
        'lotArea' => 'required|string',
        'amenities' => 'nullable|string',  // ✅ Accept string (JSON)
    ]);

    // Ensure directory exists
    if (!File::exists(public_path('offices'))) {
        File::makeDirectory(public_path('offices'), 0755, true);
    }

    // Handle image upload
    if (!$request->hasFile('image')) {
        return response()->json(['error' => 'Image file is required'], 400);
    }
    
    $image = $request->file('image');
    $imageName = time() . '-' . $image->getClientOriginalName();
    $image->move(public_path('offices'), $imageName);
    $imagePath = '/offices/' . $imageName;

    // ✅ Convert amenities JSON string to array before storing
    $amenities = json_decode($request->input('amenities', '[]'), true);

    // ✅ Create Office
    $office = Office::create([
        'name' => $request->name,
        'description' => $request->description,
        'image' => $imagePath,
        'location' => $request->location,
        'status' => $request->status,
        'price' => $request->price,
        'lotArea' => $request->lotArea,
        'amenities' => json_encode($amenities),
    ]);

    return response()->json($office, 201);
}


    // Get a single office by ID
    public function show($id)
    {
        $office = Office::find($id);

        if (!$office) {
            return response()->json(['message' => 'Office not found'], 404);
        }

        // Convert amenities to an array and return full image URL
        $office->image = asset($office->image);
        $office->amenities = json_decode($office->amenities, true);

        return response()->json($office);
    }
public function update(Request $request, $id)
{
    $office = Office::find($id);

    if (!$office) {
        return response()->json(['message' => 'Office not found'], 404);
    }

    \Log::info('Received Update Request:', $request->all());

    // Validate request
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'location' => 'required|string',
        'status' => 'required|string|in:For Lease,For Sale,For Rent',
        'price' => 'required|string',
        'lotArea' => 'required|string',
        'amenities' => 'nullable',
    ]);

    // ✅ Fix Amenities Handling
    $office->amenities = json_encode(is_array($request->amenities) ? $request->amenities : json_decode($request->amenities, true));

    // ✅ Handle Image Upload
    if ($request->hasFile('image')) {
        if (!empty($office->image) && File::exists(public_path($office->image))) {
            File::delete(public_path($office->image));
        }

        $image = $request->file('image');
        $imageName = time() . '-' . $image->getClientOriginalName();
        $imagePath = public_path('offices');

        if (!File::exists($imagePath)) {
            File::makeDirectory($imagePath, 0755, true);
        }

        $image->move($imagePath, $imageName);
        $office->image = '/offices/' . $imageName;
    }

    // ✅ Assign Updated Values
    $office->name = $request->name;
    $office->description = $request->description;
    $office->location = $request->location;
    $office->status = $request->status;
    $office->price = $request->price;
    $office->lotArea = $request->lotArea;

    $office->save(); // ✅ Manually saving the office

    \Log::info('Updated Office:', ['id' => $office->id, 'data' => $office]);

    return response()->json($office);
}


    // Delete an office
    public function destroy($id)
    {
        $office = Office::find($id);

        if (!$office) {
            return response()->json(['message' => 'Office not found'], 404);
        }

        // Delete the office's image
        $imagePath = public_path($office->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $office->delete();

        return response()->json(['message' => 'Office deleted successfully'], 200);
    }
}
