<?php

namespace App\Http\Controllers;

use App\Models\Blog;

class AdminController extends Controller
{
    public function display()
    {
        //admin can see blog of every user
        $blogs = Blog::with("users:id,name")->paginate(20);
        return response()->json([
            "status" => true,
            "message" => "Blog fetched successfully",
            "data" => $blogs
        ]);
    }
}
