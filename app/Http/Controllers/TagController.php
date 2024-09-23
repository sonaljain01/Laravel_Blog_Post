<?php

namespace App\Http\Controllers;
use App\Models\Tags;
use App\Http\Requests\TagRequest;

class TagController extends Controller
{
    public function display()
    {
        $tags = Tags::all();
        return response()->json([
            "status" => true,
            "data" => $tags
        ], 200);
    }
    public function store(TagRequest $request)
    {
        $data = [
            "name" => $request->name,
            "created_by" => auth()->user()->id
        ];
        $isSave = Tags::create($data);
        if ($isSave) {
            return response()->json([
                "status" => true,
                "message" => "Tag created successfully"
            ], 200);
        }
        return response()->json([
            "status" => false,
            "message" => "Unable to create tag"
        ], 500);
    }

}
