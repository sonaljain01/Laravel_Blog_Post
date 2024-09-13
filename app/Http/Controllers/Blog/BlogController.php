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
        $id = $request->blog_id;
        // $request->validated();
        // $blog = Blog::find($id);
        // $blog->update($request->all());
        // return response()->json([
        //     "status" => true,
        //     "message" => "Blog updated successfully",
        //     "data" => $blog
        // ]);

        // only that authorized user can update blog where the id matches
        $blog = Blog::find($blog_id);
        $user_id = auth()->user()->id;
        if($blog->blog_id == $user_id){
            $blog->update($request->all());
            return response()->json([
                "status" => true,
                "message" => "Blog updated successfully",
                "data" => $blog
            ]);
        }
    }

    public function destroy(BlogDeleteRequest $request)
    {
        $id = $request->blog_id;
        // $request->validated();
        // $blog = Blog::find($id);
        // $blog->delete();
        // return response()->json([
        //     "status" => true,
        //     "message" => "Blog deleted successfully",
        //     "data" => $blog
        // ]);

        // only that authorized user can delete blog where the id matches
        $blog = Blog::find($blog_id);
        $user_id = auth()->user()->id;
        if($blog->blog_id == $user_id){
            $blog->delete();
            return response()->json([
                "status" => true,
                "message" => "Blog deleted successfully",
                "data" => $blog
            ]);
        }
    }
}
