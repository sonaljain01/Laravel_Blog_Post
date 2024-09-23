<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChildCatrgoryRequest;
use App\Http\Requests\ChildCatrgoryUpdateRequest;
use App\Models\ChildCategory;

class ChildCatrgoryController extends Controller
{
    public function store(ChildCatrgoryRequest $request)
    {
        $data = [
            "name" => $request->name,
            "category_id" => $request->category_id,
            "created_by" => auth()->user()->id,
        ];

        $isSave = ChildCategory::create($data);
        if ($isSave) {
            return response()->json([
                "status" => true,
                "message" => "Child category created successfully",
            ], 200);
        }
        return response()->json([
            "status" => false,
            "message" => "Child category creation failed",
        ], 500);
    }


}
