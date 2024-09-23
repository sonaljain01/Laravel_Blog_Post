<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParentCatrgoryRequest;
use App\Models\ParentCategory;
use App\Models\ChildCategory;

class CatrgoryController extends Controller
{
    public function store(ParentCatrgoryRequest $request)
    {
        $isSave = ParentCategory::create($request->all());
        if ($isSave) {
            return response()->json([
                "status" => true,
                "message" => "Category created successfully",
            ], 200);
        }
        return response()->json([
            "status" => false,
            "message" => "Unable to create category",
        ], 500);
    }

}
