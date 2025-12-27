<?php

namespace App\Http\Controllers;

use App\Models\Testimonials;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonials::all();
        return response()->json(['data' => $testimonials], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_profession' => 'nullable|string|max:255',
            'message' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $testimonial = Testimonials::create($validated);

        return response()->json(['message' => 'Testimonial created successfully', 'data' => $testimonial], 201);
    }

    public function delete($id)
    {
        $testimonial = Testimonials::find($id);

        if (!$testimonial) {
            return response()->json(['message' => 'Testimonial not found'], 404);
        }

        $testimonial->delete();

        return response()->json(['message' => 'Testimonial deleted successfully'], 200);
    }
}
