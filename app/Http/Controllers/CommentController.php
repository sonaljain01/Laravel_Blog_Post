<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\CommentStoreRequest;
use App\Models\Blog;
class CommentController extends Controller
{
    public function storeComment(CommentStoreRequest $request)
    {
        $request->validated();

        $user = auth()->user();
        if (!$user) {
            return response()->json([
                "status" => false,
                "message" => "Unauthorized access",
            ], 401); 
        }
        $blog = Blog::find($request->blog_id);
        if (!$blog) {
            return response()->json([
                "status" => false,
                "message" => "Blog not found",
                "data" => []
            ]);
        }
        $comment = Comment::create([
            "user_id" => $user->id,
            "blog_id" => $request->blog_id,
            "comment" => $request->comment
        ]);
        return response()->json([
            "status" => true,
            "message" => "Comment added successfully",
            "data" => $comment,
        ]);
    }

    public function displayComments($slug)
    {
        $blog = Blog::where('slug', $slug)->first();
        if (!$blog) {
            return response()->json(['status' => false, 'message' => 'Blog not found'], 404);
        }

        $comments = $blog->comments()->with('user:id,name')->get();

        return response()->json([
            'status' => true,
            'data' => $comments,
        ]);
    }
}
