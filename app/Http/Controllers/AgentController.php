<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agent;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log; // Import logging

class AgentController extends Controller
{
    public function getAgentStatistics()
    {
        $totalAgents = Agent::count();
        $agentRoleCounts = Agent::groupBy('role')->selectRaw('role, COUNT(*) as count')->pluck('count', 'role');

        return response()->json([
            'totalAgents' => $totalAgents,
            'agentRoleCounts' => $agentRoleCounts
        ]);
    }
    // Get all agents
    public function index()
    {
        return response()->json(Agent::all(), 200);
    }

    // Store a new agent
public function store(Request $request)
{
    // ✅ Debug: Log incoming request
    Log::info('Received Agent Data:', $request->all());

    $request->validate([
        'name' => 'required|string|max:255',
        'role' => 'required|string|max:255',
        'description' => 'required|string',
        'email' => 'required|email',
        'phone' => 'required|string',
        'facebook' => 'nullable|string',
        'instagram' => 'nullable|string',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        'certificates' => 'nullable|array',
        'certificates.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        'gallery' => 'nullable|array',
        'gallery.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
    ]);

    // ✅ Handle main image upload
    $image = $request->file('image');
    $imageName = time() . '-' . $image->getClientOriginalName();
    $image->move(public_path('agent'), $imageName);
    $imagePath = '/agent/' . $imageName;

    // ✅ Handle certificates upload
    $certificates = [];
    if ($request->hasFile('certificates')) {
        foreach ($request->file('certificates') as $certificate) {
            $certName = time() . '-' . $certificate->getClientOriginalName();
            $certificate->move(public_path('agent/certificates'), $certName);
            $certificates[] = '/agent/certificates/' . $certName;
        }
    }

    // ✅ Handle gallery upload
    $gallery = [];
    if ($request->hasFile('gallery')) {
        foreach ($request->file('gallery') as $photo) {
            $photoName = time() . '-' . $photo->getClientOriginalName();
            $photo->move(public_path('agent/gallery'), $photoName);
            $gallery[] = '/agent/gallery/' . $photoName;
        }
    }

    // ✅ Debug: Log file paths
    Log::info('Uploaded Files:', [
        'image' => $imagePath,
        'certificates' => $certificates,
        'gallery' => $gallery
    ]);

    // ✅ Create agent in database
    $agent = Agent::create([
        'name' => $request->name,
        'role' => $request->role,
        'description' => $request->description,
        'email' => $request->email,
        'phone' => $request->phone,
        'facebook' => $request->facebook,  // ✅ Added Facebook
        'instagram' => $request->instagram,  // ✅ Added Instagram
        'image' => $imagePath,
        'certificates' => json_encode($certificates),
        'gallery' => json_encode($gallery),
    ]);

    return response()->json($agent, 201);
}

    // Get a single agent by ID
    public function show($id)
    {
        $agent = Agent::find($id);
        if (!$agent) {
            return response()->json(['message' => 'Agent not found'], 404);
        }

        // ✅ Convert JSON fields to arrays
        $agent->certificates = json_decode($agent->certificates, true);
        $agent->gallery = json_decode($agent->gallery, true);

        return response()->json($agent);
    }

public function update(Request $request, $id)
{
    $agent = Agent::find($id);
    if (!$agent) {
        return response()->json(['message' => 'Agent not found'], 404);
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'role' => 'required|string|max:255',
        'description' => 'required|string',
        'email' => 'required|email',
        'phone' => 'required|string',
        'facebook' => 'nullable|string',
        'instagram' => 'nullable|string',
        'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
        'certificates' => 'nullable|array',
        'certificates.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        'gallery' => 'nullable|array',
        'gallery.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
    ]);

    // ✅ Handle new image upload
    if ($request->hasFile('image')) {
        if (File::exists(public_path($agent->image))) {
            File::delete(public_path($agent->image));
        }
        $image = $request->file('image');
        $imageName = time() . '-' . $image->getClientOriginalName();
        $image->move(public_path('agent'), $imageName);
        $agent->image = '/agent/' . $imageName;
    }

    // ✅ Handle certificates upload
    $certificates = json_decode($agent->certificates, true) ?? [];
    if ($request->hasFile('certificates')) {
        foreach ($request->file('certificates') as $certificate) {
            $certName = time() . '-' . $certificate->getClientOriginalName();
            $certificate->move(public_path('agent/certificates'), $certName);
            $certificates[] = '/agent/certificates/' . $certName;
        }
    }

    // ✅ Handle gallery upload
    $gallery = json_decode($agent->gallery, true) ?? [];
    if ($request->hasFile('gallery')) {
        foreach ($request->file('gallery') as $photo) {
            $photoName = time() . '-' . $photo->getClientOriginalName();
            $photo->move(public_path('agent/gallery'), $photoName);
            $gallery[] = '/agent/gallery/' . $photoName;
        }
    }

    // ✅ Update agent details
    $agent->update([
        'name' => $request->name,
        'role' => $request->role,
        'description' => $request->description,
        'email' => $request->email,
        'phone' => $request->phone,
        'facebook' => $request->facebook,
        'instagram' => $request->instagram,
        'certificates' => json_encode($certificates),
        'gallery' => json_encode($gallery),
    ]);

    return response()->json($agent);
}

    // Delete an agent
    public function destroy($id)
    {
        $agent = Agent::find($id);
        if (!$agent) {
            return response()->json(['message' => 'Agent not found'], 404);
        }

        // Delete image
        if (File::exists(public_path($agent->image))) {
            File::delete(public_path($agent->image));
        }

        // Delete certificates
        $certificates = json_decode($agent->certificates, true);
        foreach ($certificates as $certPath) {
            if (File::exists(public_path($certPath))) {
                File::delete(public_path($certPath));
            }
        }

        // Delete gallery images
        $gallery = json_decode($agent->gallery, true);
        foreach ($gallery as $photoPath) {
            if (File::exists(public_path($photoPath))) {
                File::delete(public_path($photoPath));
            }
        }

        $agent->delete();

        return response()->json(['message' => 'Agent deleted successfully'], 200);
    }
}
