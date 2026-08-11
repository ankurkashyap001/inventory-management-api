<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::with(['categories', 'images', 'primaryImage'])
            ->latest()
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => ProductResource::collection($products),
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'price'          => 'required|numeric|min:0',
            'sale_price'     => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_ids'   => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'image_url'      => 'required|url',
        ]);

        $product = Product::create([
            'title'          => $validated['title'],
            'slug'           => Str::slug($validated['title'] . '-' . Str::random(4)),
            'description'    => $validated['description'],
            'price'          => $validated['price'],
            'sale_price'     => $validated['sale_price'] ?? null,
            'stock_quantity' => $validated['stock_quantity'],
            'sku'            => 'ANK-' . strtoupper(Str::random(6)),
            'is_active'      => true,
        ]);

        $product->categories()->attach($validated['category_ids']);

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => $validated['image_url'],
            'is_primary' => true,
        ]);

        $product->load(['categories', 'images', 'primaryImage']);

        return response()->json([
            'success' => true,
            'message' => 'Product created',
            'data'    => new ProductResource($product),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'price'          => 'required|numeric|min:0',
            'sale_price'     => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'is_active'      => 'boolean',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated',
            'data'    => new ProductResource($product->load(['categories', 'images', 'primaryImage'])),
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product deleted'], 200);
    }
}