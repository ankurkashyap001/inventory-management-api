<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    /**
     * GET /api/admin/categories
     * Fetch all categories.
     */
    public function index(): JsonResponse
    {
        $categories = Category::latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully',
            'code'    => 1000,
            'data'    => CategoryResource::collection($categories),
        ], 200);
    }

    /**
     * POST /api/admin/categories
     * Create category with direct image upload (.avif supported) or image URL.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255', 'unique:categories,name'],
            'slug'      => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'is_active' => ['nullable'],
            'image'     => ['nullable'],
        ]);

        $imagePath = null;

        // 1. Handle direct file upload (including AVIF format)
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => ['file', 'mimes:jpeg,png,jpg,webp,svg,avif', 'max:2048'],
            ]);
            $imagePath = $request->file('image')->store('categories', 'public');
        } 
        // 2. Handle image URL string fallback
        elseif ($request->filled('image') && is_string($request->input('image'))) {
            $imagePath = $request->input('image');
        }

        $category = Category::create([
            'name'      => $validated['name'],
            'slug'      => !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']),
            'image'     => $imagePath,
            'is_active' => filter_var($request->input('is_active', true), FILTER_VALIDATE_BOOLEAN),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'code'    => 1000,
            'data'    => new CategoryResource($category),
        ], 201);
    }

    /**
     * POST/PUT /api/admin/categories/{id}
     * Update category with optional image replacement (.avif supported).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255', 'unique:categories,name,' . $id],
            'slug'      => ['nullable', 'string', 'max:255', 'unique:categories,slug,' . $id],
            'is_active' => ['nullable'],
            'image'     => ['nullable'],
        ]);

        // Handle file replacement
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => ['file', 'mimes:jpeg,png,jpg,webp,svg,avif', 'max:2048'],
            ]);

            // Delete old physical file if it exists on the local storage disk
            if ($category->image && !str_starts_with($category->image, 'http') && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $category->image = $request->file('image')->store('categories', 'public');
        } elseif ($request->filled('image') && is_string($request->input('image'))) {
            $category->image = $request->input('image');
        }

        $category->name = $validated['name'];
        $category->slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        if ($request->has('is_active')) {
            $category->is_active = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);
        }

        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'code'    => 1000,
            'data'    => new CategoryResource($category),
        ], 200);
    }

    /**
     * DELETE /api/admin/categories/{id}
     * Delete category and cleanup associated stored image.
     */
    public function destroy(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        // Delete physical file from storage if stored locally
        if ($category->image && !str_starts_with($category->image, 'http') && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
            'code'    => 1000,
        ], 200);
    }
}