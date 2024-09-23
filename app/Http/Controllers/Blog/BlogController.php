<?php

namespace App\Http\Controllers\Blog;
use App\Models\Blog;
use App\Http\Requests\BlogDeleteRequest;
use App\Http\Requests\BlogStoreRequest;
use App\Http\Requests\BlogUpdateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function display(){
        //every one can see the blog
        $blogs = Blog::all();
        return response()->json([
            "status" => true,
            "message" => "Blog fetched successfully",
            "data" => $blogs
        ]);

    }

    public function store(BlogStoreRequest $request)
    {
        $request->validated();
        $filldata = [
            "user_id" => auth()->user()->id,
            "title" => $request->title,
            "description" => $request->description
        ];
        $blog = Blog::create($filldata);
        return response()->json([
            "status" => true,
            "message" => "Blog created successfully",
            "data" => $blog
        ]);
    }

    public function update(BlogUpdateRequest $request)
    {
        $blog_id = $request->blog_id;
        $filldata = [
            "title" => $request->title,
            "description" => $request->description
        ];
        if (!Blog::where("id", $blog_id)->update($filldata)) {
            return response()->json([
                "status" => false,
                "message" => "Blog not found",
                "data" => []
            ]);
        }
        return response()->json([
            "status" => true,
            "message" => "Blog updated successfully",
            "data" => Blog::find($blog_id)
        ]);
    }

    public function destroy(BlogDeleteRequest $request)
    {
        $blog_id = $request->blog_id;
        if (!Blog::where("id", $blog_id)->delete()) {
            return response()->json([
                "status" => false,
                "message" => "Blog not found",
                "data" => []
            ]);
        }
        return response()->json([
            "status" => true,
            "message" => "Blog deleted successfully",
        ]);
    }
}
