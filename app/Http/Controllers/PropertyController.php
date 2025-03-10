<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;  // Assuming you have a Property model
use Illuminate\Support\Facades\File;

class PropertyController extends Controller
{
    public function searchProperties(Request $request)
    {
        // Validate incoming request parameters
        $request->validate([
            'location' => 'nullable|string',
            'minPrice' => 'nullable|numeric',
            'maxPrice' => 'nullable|numeric',
            'type' => 'nullable|string',
        ]);

        // Start with the Property model
        $query = Property::query();

     
        if ($request->has('location') && $request->location != '') {
            $query->where('location', $request->location);
        }

        if ($request->has('minPrice') && $request->minPrice != '') {
            // The priceRange is stored as a string with a format like "15000 - 16000"
            $query->whereRaw("CAST(SUBSTRING_INDEX(priceRange, ' - ', 1) AS UNSIGNED) >= ?", [$request->minPrice]);
        }

        if ($request->has('maxPrice') && $request->maxPrice != '') {
            // The priceRange is stored as a string with a format like "15000 - 16000"
            $query->whereRaw("CAST(SUBSTRING_INDEX(priceRange, ' - ', -1) AS UNSIGNED) <= ?", [$request->maxPrice]);
        }

        if ($request->has('type') && $request->type != '') {
            $query->where('developmentType', $request->type);
        }

        // Fetch the filtered properties
        $properties = $query->get();

        return response()->json($properties);
    }
  public function getPropertyStatistics()
{
    $totalProperties = Property::count(); // ✅ Count all properties

    // ✅ Count properties by status
    $statusCounts = Property::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status'); // Returns an associative array: ['New' => 10, 'Pre-Selling' => 5, ...]

    return response()->json([
        'totalProperties' => $totalProperties,
        'statusCounts' => $statusCounts,
    ]);
}

    public function index()
    {
        return response()->json(Property::all(), 200);
    }
public function store(Request $request)
{
    \Log::info("🔹 Raw Request Data:", $request->all());

    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        'location' => 'required|string',
        'status' => 'required|string',
        'priceRange' => 'required|string',
        'specificLocation' => 'required|string',  // Ensure specificLocation is validated
        'lotArea' => 'required|string',
        'units' => 'required|string',
        'floors' => 'required|integer',
        'parkingLots' => 'required|integer',
        'masterPlan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'amenities' => 'nullable|array',
        'amenities.*.name' => 'required|string',
        'amenities.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'features' => 'nullable|array',
        'features.*.name' => 'required|string',
    ]);

    // Ensure directory exists for storing images
    if (!File::exists(public_path('properties'))) {
        File::makeDirectory(public_path('properties'), 0755, true);
    }

    // Handle main image
    $imagePath = null;
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '-' . $image->getClientOriginalName();
        $image->move(public_path('properties'), $imageName);
        $imagePath = '/properties/' . $imageName;
    }

    // Handle master plan
    $masterPlanPath = null;
    if ($request->hasFile('masterPlan')) {
        $masterPlan = $request->file('masterPlan');
        $masterPlanName = time() . '-' . $masterPlan->getClientOriginalName();
        $masterPlan->move(public_path('properties'), $masterPlanName);
        $masterPlanPath = '/properties/' . $masterPlanName;
    }

    // Handle amenities
    $amenities = [];
    if ($request->has('amenities')) {
        foreach ($request->amenities as $amenity) {
            $amenityImagePath = null;
            if (isset($amenity['image']) && $amenity['image'] instanceof \Illuminate\Http\UploadedFile) {
                $imageFile = $amenity['image'];
                $amenityImageName = time() . '-' . $imageFile->getClientOriginalName();
                $imageFile->move(public_path('properties'), $amenityImageName);
                $amenityImagePath = '/properties/' . $amenityImageName;
            }
            $amenities[] = [
                'name' => $amenity['name'],
                'image' => $amenityImagePath,
            ];
        }
    }

    // Handle features
    $features = [];
    if ($request->has('features')) {
        foreach ($request->features as $feature) {
            $features[] = ['name' => $feature['name']];
        }
    }

    // Decode the units JSON
    $units = json_decode($request->units, true);

    // Save the property
    $property = Property::create([
        'name' => $request->name,
        'description' => $request->description,
        'image' => $imagePath,
        'location' => $request->location,
        'specificLocation' => $request->specificLocation,  // Save the specificLocation
        'status' => $request->status,
        'priceRange' => $request->priceRange,
        'lotArea' => $request->lotArea,
        'developmentType' => $request->developmentType,
        'units' => json_encode($units),
        'amenities' => json_encode($amenities),
        'features' => json_encode($features),
        'masterPlan' => $masterPlanPath,
        'floors' => $request->floors,
        'parkingLots' => $request->parkingLots,
    ]);

    return response()->json($property, 201);
}


    // Get a single property by ID
    public function show($id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        // Convert amenities to an array and return full image URL
        $property->image = asset($property->image);
        $property->amenities = json_decode($property->amenities, true);

        return response()->json($property);
    }
public function updateFeatures(Request $request, $id)
{
    \Log::info('Received Data:', $request->all());

    $property = Property::find($id);
    
    if (!$property) {
        return response()->json(['message' => 'Property not found'], 404);
    }

    // Decode JSON string to an array
    $features = json_decode($request->features, true);

    if (!is_array($features)) {
        return response()->json(['message' => 'Invalid features format'], 400);
    }

    // Keep only feature names
    $cleanedFeatures = array_map(fn($feature) => ['name' => $feature['name']], $features);

    // Validate feature names
    $validator = \Validator::make(['features' => $cleanedFeatures], [
        'features' => 'required|array',
        'features.*.name' => 'required|string'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Save the cleaned features
    $property->features = json_encode($cleanedFeatures);
    $property->save();

    return response()->json(['message' => 'Features updated successfully', 'features' => json_decode($property->features, true)]);
}
public function updateAmenities(Request $request, $id)
{
    \Log::info('Received Amenities Data:', $request->all());

    $property = Property::find($id);

    if (!$property) {
        return response()->json(['message' => 'Property not found'], 404);
    }

    $amenities = [];

    if ($request->has('amenities')) {
        foreach ($request->input('amenities') as $index => $amenity) {
            $imagePath = $amenity['originalImage'] ?? null; // ✅ Default to old image

            // ✅ Check if a new image file was uploaded
            if ($request->hasFile("amenities.{$index}.image")) {
                $imageFile = $request->file("amenities.{$index}.image");
                $imageName = time() . '-' . $imageFile->getClientOriginalName();
                $imageFile->move(public_path('properties'), $imageName);
                $imagePath = '/properties/' . $imageName; // ✅ Use new image path
            }

            $amenities[] = [
                'name' => $amenity['name'],
                'image' => $imagePath, // ✅ Keeps old image if no new one is uploaded
            ];
        }
    }

    $property->amenities = json_encode($amenities);
    $property->save();

    return response()->json(['message' => 'Amenities updated successfully', 'amenities' => json_decode($property->amenities, true)]);
}




public function updateProperty(Request $request, $id)
{
    $property = Property::find($id);

    if (!$property) {
        return response()->json(['message' => 'Property not found'], 404);
    }

    \Log::info('Received Update Request:', $request->all());

    // Validate request
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'specificLocation' => 'required|string',
        'units' => 'required|string', // Expect JSON string
        'priceRange' => 'required|string',
        'lotArea' => 'required|string',
        'location' => 'required|string',
        'status' => 'required|string|in:New,Ready for occupancy,Pre-selling,Sold-out,Under Construction',
        'developmentType' => 'required|string',
        'floors' => 'nullable|integer',
        'parkingLots' => 'nullable|integer',
        'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'masterPlan' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
    ]);

    // 🔹 Fix `units` (Ensure it is stored as an array)
    $property->units = is_string($request->units) ? json_decode($request->units, true) : $request->units;

    // 🔹 Handle Image Upload Properly
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '-' . $image->getClientOriginalName();
        $image->move(public_path('properties'), $imageName);
        $property->image = '/properties/' . $imageName;
    }

    if ($request->hasFile('masterPlan')) {
        $masterPlan = $request->file('masterPlan');
        $masterPlanName = time() . '-' . $masterPlan->getClientOriginalName();
        $masterPlan->move(public_path('properties'), $masterPlanName);
        $property->masterPlan = '/properties/' . $masterPlanName;
    }

    // 🔹 Assign Other Fields
    $property->name = $request->name;
    $property->description = $request->description;
    $property->specificLocation = $request->specificLocation;
    $property->priceRange = $request->priceRange;
    $property->lotArea = $request->lotArea;
    $property->location = $request->location;
    $property->status = $request->status;
    $property->developmentType = $request->developmentType;
    $property->floors = $request->floors ?? 0;
    $property->parkingLots = $request->parkingLots ?? 0;

    // 🔹 Ensure the Data is Saved
    $property->save();

    \Log::info('Updated Property:', ['id' => $property->id, 'data' => $property]);

    return response()->json($property);
}



    // Delete a property
    public function destroy($id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        // Delete the property's image
        $imagePath = public_path($property->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $property->delete();

        return response()->json(['message' => 'Property deleted successfully'], 200);
    }
}
