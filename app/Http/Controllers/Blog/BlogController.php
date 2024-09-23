<?php

namespace App\Http\Controllers\Blog;
use App\Models\Blog;
use App\Http\Requests\BlogDeleteRequest;
use App\Http\Requests\BlogStoreRequest;
use App\Http\Requests\BlogUpdateRequest;
use App\Http\Controllers\Controller;
use Http;
use Illuminate\Http\Request;
use Storage;

class BlogController extends Controller
{
    public function display()
    {
        //only authenticate user can see their blog
        if (!auth()->check()) {
            return response()->json([
                "status" => false,
                "message" => "Please login first",
            ], 401);
        }
        $id = auth()->user()->id;
        $blogs = Blog::where("user_id", $id)
            ->where("isdeleted", true)
            ->with(["users:id,name", "deletedBy:id,name", "parentCategory:id,name", "childCategory:id,name"])
            ->paginate(20);

        $returnData = [];
        
        foreach ($blogs as $blog) {
            $returnData[] = [
                "id" => $blog->id,
                "title" => $blog->title,
                "description" => $blog->description,
                "photo" => $blog->photo,
                "category" => $blog->parentCategory->name ?? "",
                "sub_category" => $blog->childCategory->name ?? "",
                "tag" => $blog->tag ?? "",
                "created_at" => $blog->created_at,
                "created_by" => $blog->users->name,
                "is_deleted" => $blog->isdeleted,
                "seo" => [
                    "meta.name" => $blog->title,
                    "meta.desc" => $blog->description,
                    "meta.robots" => "noindex, nofollow"
                ]

            ];
        }
        return response()->json([
            "status" => true,
            "message" => "Blog fetched successfully",
            "data" => $returnData
        ], 200);

    }

    public function store(BlogStoreRequest $request)
    {
        $filldata = [];
        if ($request->file('image')) {
            $image = $this->uploadImage($request->file('image'));
            $filldata = [
                "user_id" => auth()->user()->id,
                "title" => $request->title,
                "description" => $request->description,
                "photo" => $image,
                "parent_category" => $request->category,
                "tag" => $request->tag,
                "child_category" => $request->sub_category
            ];
        } else {
            $filldata = [
                "user_id" => auth()->user()->id,
                "title" => $request->title,
                "description" => $request->description,
                "parent_category" => $request->category,
                "tag" => $request->tag,
                "child_category" => $request->sub_category
            ];
        }

        $sendData = [
            "subject" => "New blog created",
            "title" => $request->title,
            "description" => $request->description
        ];
        $blog = Blog::create($filldata);
        // Http::post("https://connect.pabbly.com/workflow/sendwebhookdata/IjU3NjYwNTZkMDYzNTA0MzI1MjZlNTUzMDUxMzQi_pc", $sendData);
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
        $sendData = [
            "subject" => "Blog with id." . $blog_id . " updated",
            "title" => $request->title,
            "description" => $request->description
        ];
        $isBlogExist = Blog::find($blog_id);
        if (!$isBlogExist) {
            return response()->json([
                "status" => false,
                "message" => "Blog not found",
            ]);
        }

        $isUpdate = $isBlogExist->update($filldata);
        if (!$isUpdate) {
            return response()->json([
                "status" => false,
                "message" => "Unable to update blog",
            ]);
        }

        // Http::post("https://connect.pabbly.com/workflow/sendwebhookdata/IjU3NjYwNTZkMDYzNTA0MzI1MjZlNTUzMDUxMzQi_pc", $sendData);
        return response()->json([
            "status" => true,
            "message" => "Blog updated successfully",
            "data" => Blog::find($blog_id)
        ]);
    }

    public function destroy(BlogDeleteRequest $request)
    {
        $blog_id = $request->blog_id;
        $isBlogExist = Blog::find($blog_id);

        if (!$isBlogExist) {
            return response()->json([
                "status" => false,
                "message" => "Blog not found",

            ]);
        }

        if ($isBlogExist->isDeleted) {
            return response()->json([
                "status" => false,
                "message" => "Blog already deleted",

            ]);
        }

        $isUpdate = $isBlogExist->update([
            "isDeleted" => true,
            "deleted_by" => auth()->user()->id
        ]);


        if ($isUpdate)
            return response()->json([
                "status" => true,
                "message" => "Blog deleted successfully",
                "deletedBy" => "Blog is deleted by You."
            ]);
    }

    protected function uploadImage($file)
    {
        $uploadFolder = 'blog-image';
        $image = $file;
        $image_uploaded_path = $image->store($uploadFolder, 'public');
        $uploadedImageUrl = Storage::disk('public')->url($image_uploaded_path);

        return $uploadedImageUrl;
    }
}
