<?php

namespace App\Http\Controllers;

use App\Models\Blog;

class AdminController extends Controller
{
    public function display()
    {
        //admin can see blog of every user
        $blogs = Blog::all()
            ->with(["users:id,name", "deletedBy:id,name", "parentCategory:id,name", "childCategory:id,name"])
            ->paginate(20);
        return response()->json([
            "status" => true,
            "message" => "Blog fetched successfully",
            "data" => $blogs
        ]);
    }

    public function destroy(string $id)
    {

        $isBlogExist = Blog::where("id", $id)->with("deletedBy:id,name")->get()[0];

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

        $deletedBy = $isBlogExist->deletedBy->name;

        if ($isUpdate)
            return response()->json([
                "status" => true,
                "message" => "Blog deleted successfully",
                "deletedBy" => "Blog is deleted by admin $deletedBy"
            ]);
    }
}
