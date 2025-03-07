<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;

class StatusController extends Controller
{
    // Get all statuses
    public function index()
    {
        return response()->json(Status::all(), 200);
    }

    // Store a new status
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $status = Status::create([
            'name' => $request->name,
        ]);

        return response()->json($status, 201);
    }

    // Get a single status by ID
    public function show($id)
    {
        $status = Status::find($id);

        if (!$status) {
            return response()->json(['message' => 'Status not found'], 404);
        }

        return response()->json($status);
    }

    // Update a status
    public function update(Request $request, $id)
    {
        $status = Status::find($id);

        if (!$status) {
            return response()->json(['message' => 'Status not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $status->update([
            'name' => $request->name,
        ]);

        return response()->json($status);
    }

    // Delete a status
    public function destroy($id)
    {
        $status = Status::find($id);

        if (!$status) {
            return response()->json(['message' => 'Status not found'], 404);
        }

        $status->delete();

        return response()->json(['message' => 'Status deleted successfully'], 200);
    }
}
