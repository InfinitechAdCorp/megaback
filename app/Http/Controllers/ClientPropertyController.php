<?php

namespace App\Http\Controllers;

use App\Models\ClientProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientPropertyController extends Controller {
    public function updateStatus(Request $request)
{
    try {
        // ✅ Validate request, ensure ID is present in the request body
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:client_properties,id', // ✅ Ensure ID exists in the database
            'status' => 'required|string|in:Pending,Approved,Rejected' // ✅ Validate status
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // ✅ Find ClientProperty by ID
        $clientProperty = ClientProperty::findOrFail($request->id);

        // ✅ Update status
        $clientProperty->status = $request->status;
        $clientProperty->save();

        return response()->json([
            'message' => 'Property status updated successfully!',
            'client_property' => $clientProperty
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Something went wrong',
            'details' => $e->getMessage()
        ], 500);
    }
}

public function store(Request $request) {
    try {
        // ✅ Ensure unit_type is always an array
        $requestData = $request->all();
        $requestData['unit_type'] = $request->has('unit_type') && is_array($request->unit_type)
            ? $request->unit_type
            : [];

        $validator = Validator::make($requestData, [
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'email' => 'required|email',
            'number' => 'required|digits:11',
            'property_name' => 'required|string',
            'development_type' => 'required|string',
            'unit_type' => 'nullable|array',
            'unit_type.*' => 'string',
            'status' => 'required|string',
            'price' => 'required|string',
            'location' => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // ✅ Convert property_name to a valid folder name
        $folderName = str_replace(' ', '_', strtolower($request->property_name));
        $directory = public_path("clientproperty/{$folderName}");
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // ✅ Store images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move($directory, $filename);
                $imagePaths[] = "/clientproperty/{$folderName}/{$filename}";
            }
        }

        // ✅ Save to database, set `unit_type` to NULL if empty
        $clientProperty = ClientProperty::create([
            'last_name' => $requestData['last_name'],
            'first_name' => $requestData['first_name'],
            'email' => $requestData['email'],
            'number' => $requestData['number'],
            'property_name' => $requestData['property_name'],
            'development_type' => $requestData['development_type'],
            'unit_type' => empty($requestData['unit_type']) ? null : $requestData['unit_type'], // ✅ NULL if empty
            'price' => $requestData['price'],
            'status' => $requestData['status'],
            'location' => $requestData['location'],
            'images' => $imagePaths, // ✅ No JSON encoding needed for `json` column
        ]);

        return response()->json(['message' => 'Client property submitted successfully!', 'client_property' => $clientProperty], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Something went wrong', 'details' => $e->getMessage()], 500);
    }
}



    // ✅ Read (Get all properties)
    public function index() {
        $properties = ClientProperty::all();
        return response()->json(['client_properties' => $properties], 200);
    }

    // ✅ Read (Get single property)
    public function show($id) {
        $property = ClientProperty::find($id);
        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }
        return response()->json(['client_property' => $property], 200);
    }

    // ✅ Update (Modify property details)
    public function update(Request $request, $id) {
        $property = ClientProperty::find($id);
        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'last_name' => 'sometimes|string',
            'first_name' => 'sometimes|string',
            'email' => 'sometimes|email',
            'number' => 'sometimes|digits:11',
            'property_name' => 'sometimes|string',
            'development_type' => 'sometimes|string',
            'unit_type' => 'nullable|array',
            'unit_type.*' => 'string',
            'price' => 'sometimes|numeric|min:0',
            'location' => 'sometimes|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle new image uploads
        $imagePaths = $property->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('uploads/client_properties', 'public');
                $imagePaths[] = $path;
            }
        }

        // Update property details
        $property->update([
            'last_name' => $request->last_name ?? $property->last_name,
            'first_name' => $request->first_name ?? $property->first_name,
            'email' => $request->email ?? $property->email,
            'number' => $request->number ?? $property->number,
            'property_name' => $request->property_name ?? $property->property_name,
            'development_type' => $request->development_type ?? $property->development_type,
            'unit_type' => $request->unit_type ?? $property->unit_type,
            'price' => $request->price ?? $property->price,
            'location' => $request->location ?? $property->location,
            'images' => $imagePaths,
        ]);

        return response()->json(['message' => 'Property updated successfully!', 'client_property' => $property], 200);
    }

    // ✅ Delete (Remove a property)
    public function destroy($id) {
        $property = ClientProperty::find($id);
        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }
        
        // Delete images from storage
        if ($property->images) {
            foreach ($property->images as $image) {
                \Storage::delete('public/' . $image);
            }
        }

        $property->delete();
        return response()->json(['message' => 'Property deleted successfully!'], 200);
    }
}
