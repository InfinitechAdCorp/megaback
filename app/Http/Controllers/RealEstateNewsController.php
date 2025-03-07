<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RealEstateNews;
use Illuminate\Support\Facades\File;

class RealEstateNewsController extends Controller
{
 public function getRealEstateNewsStatistics()
{
    $totalnews = RealEstateNews::count(); // Count all real estate news

    return response()->json([
        'totalNews' => $totalnews, // ✅ Corrected variable name
    ]);
}

    public function index()
    {
        return response()->json(RealEstateNews::all(), 200);
    }

    // Store a new news article
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'date' => 'required|date',
        ]);

        // Handle image upload
        $image = $request->file('image');
        $imageName = time() . '-' . $image->getClientOriginalName();
        $image->move(public_path('real-estate-news'), $imageName);

        // Create the news entry
        $news = RealEstateNews::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => '/real-estate-news/' . $imageName,
            'date' => $request->date,
        ]);

        return response()->json($news, 201);
    }

    // Get a single news article by ID
    public function show($id)
    {
        $news = RealEstateNews::find($id);

        if (!$news) {
            return response()->json(['message' => 'News article not found'], 404);
        }

        $news->image = asset($news->image);

        return response()->json($news);
    }

    // Update a news article
    public function update(Request $request, $id)
    {
        $news = RealEstateNews::find($id);

        if (!$news) {
            return response()->json(['message' => 'News article not found'], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
            'date' => 'required|date',
        ]);

        if ($request->hasFile('image')) {
            $oldImage = public_path($news->image);
            if (File::exists($oldImage)) {
                File::delete($oldImage);
            }

            $image = $request->file('image');
            $imageName = time() . '-' . $image->getClientOriginalName();
            $image->move(public_path('real-estate-news'), $imageName);

            $news->image = '/real-estate-news/' . $imageName;
        }

        $news->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return response()->json($news);
    }

    // Delete a news article
    public function destroy($id)
    {
        $news = RealEstateNews::find($id);

        if (!$news) {
            return response()->json(['message' => 'News article not found'], 404);
        }

        $imagePath = public_path($news->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $news->delete();

        return response()->json(['message' => 'News article deleted successfully'], 200);
    }
}