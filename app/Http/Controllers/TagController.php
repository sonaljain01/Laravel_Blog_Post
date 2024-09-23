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

}
