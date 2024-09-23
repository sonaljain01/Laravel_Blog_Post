<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\CategoryUpdateRequest;
class Categorycontroller extends Controller
{
    public function index()
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return response()->json($categories);
    }

    public function store(CategoryRequest $request)
    {
        $request->validated();
        $category = Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category created successfully',
            'data' => $category,
        ]);
    }

    public function update(CategoryUpdateRequest $request)
    {
        $category = Category::find($request->id);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
                'data' => [],
            ]);
        }
        $request->validated();
        $category->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
                'data' => [],
            ]);
        }
        $category->delete();
        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully',
            'data' => [],
        ]);
    }
}
