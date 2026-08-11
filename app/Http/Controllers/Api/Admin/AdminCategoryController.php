<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::latest()->get();
        return response()->json([
            'success' => true,
            'data'    => CategoryResource::collection($categories),
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255|unique:categories,name',
            'image' => 'nullable|string',
        ]);

        $category = Category::create([
            'name'      => $validated['name'],
            'slug'      => Str::slug($validated['name']),
            'image'     => $validated['image'] ?? null,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created',
            'data'    => new CategoryResource($category),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:categories,name,' . $id,
            'image'     => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'name'      => $validated['name'],
            'slug'      => Str::slug($validated['name']),
            'image'     => $validated['image'] ?? $category->image,
            'is_active' => $validated['is_active'] ?? $category->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated',
            'data'    => new CategoryResource($category),
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted'], 200);
    }
}