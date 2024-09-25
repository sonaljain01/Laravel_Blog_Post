<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BlogRatingRequest;
use App\Models\Blog;
use App\Models\Rating;
use Auth;
class BlogRatingController extends Controller
{
    public function rate(BlogRatingRequest $request, $slug) {
        $request->validated();

        $blog = Blog::where('slug', $slug)->firstOrFail();

        $existingRating = Rating::where('blog_id', $blog->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingRating) {
            $existingRating->update(['rating' => $request->rating]);
        } else {
            Rating::create([
                'blog_id' => $blog->id,
                'user_id' => Auth::id(),
                'rating' => $request->rating,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Rating submitted successfully',
            'data' => [
                'rating' => $request->rating,
            ],
        ]);
    }

    public function getRating($slug) {
        $blog = Blog::where('slug', $slug)->with('ratings.user')->firstOrFail();
        if(!$blog) {
            return response()->json([
                'status' => false, 
                'message' => 'Blog not found',
            ], 404);
        }

        $rating = $blog->ratings;

        $avgrating = $rating->avg('rating');

        return response()->json([
            'status' => true,
            'message' => 'Rating fetched successfully',
            'data' => [
                'avgrating' => $avgrating,
                'rating' => $rating,
            ],
        ]);
    }
}
