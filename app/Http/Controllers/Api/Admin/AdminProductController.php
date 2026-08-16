<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    /**
     * GET /api/admin/products
     * Fetch paginated products list.
     */
    public function index(Request $request): JsonResponse
    {
        $products = Product::with(['categories', 'images', 'primaryImage'])
            ->latest()
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Products fetched successfully',
            'code'    => 1000,
            'data'    => ProductResource::collection($products),
            'meta'    => [
                'current_page' => $products->currentPage(),
                'total_pages'  => $products->lastPage(),
                'total_items'  => $products->total(),
            ],
        ], 200);
    }

    /**
     * POST /api/admin/products
     * Create product with flexible file upload ('image') OR direct URL string ('image_url').
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'slug'           => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'description'    => ['nullable', 'string'],
            'price'          => ['required', 'numeric', 'min:0'],
            'sale_price'     => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'category_id'    => ['nullable', 'exists:categories,id'],
            'category_ids'   => ['nullable', 'array'],
            'category_ids.*' => ['exists:categories,id'],
            'is_active'      => ['nullable'],
            'image'          => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,svg,avif', 'max:3072', 'required_without:image_url'],
            'image_url'      => ['nullable', 'string', 'url', 'required_without:image'],
        ]);

        $product = DB::transaction(function () use ($request, $validated) {
            $slug = $request->filled('slug')
                ? Str::slug($request->slug)
                : Str::slug($request->title . '-' . Str::random(4));

            $newProduct = Product::create([
                'title'          => $validated['title'],
                'slug'           => $slug,
                'description'    => $request->input('description', ''),
                'price'          => $validated['price'],
                'sale_price'     => $validated['sale_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'],
                'sku'            => 'ANK-' . strtoupper(Str::random(6)),
                'is_active'      => filter_var($request->input('is_active', true), FILTER_VALIDATE_BOOLEAN),
            ]);

            // Sync categories (handles single category_id or array category_ids)
            $categories = [];
            if ($request->filled('category_id')) {
                $categories[] = $request->input('category_id');
            }
            if ($request->filled('category_ids') && is_array($request->input('category_ids'))) {
                $categories = array_unique(array_merge($categories, $request->input('category_ids')));
            }
            if (!empty($categories)) {
                $newProduct->categories()->sync($categories);
            }

            // Determine image storage path
            $finalImagePath = null;
            if ($request->hasFile('image')) {
                $finalImagePath = $request->file('image')->store('products', 'public');
            } elseif ($request->filled('image_url')) {
                $finalImagePath = $request->input('image_url');
            }

            if ($finalImagePath) {
                ProductImage::create([
                    'product_id' => $newProduct->id,
                    'image_path' => $finalImagePath,
                    'is_primary' => true,
                ]);
            }

            return $newProduct;
        });

        $product->load(['categories', 'images', 'primaryImage']);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'code'    => 1000,
            'data'    => new ProductResource($product),
        ], 201);
    }

    /**
     * POST/PUT /api/admin/products/{id}
     * Update product with optional file replacement OR new URL string.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'slug'           => ['nullable', 'string', 'max:255', 'unique:products,slug,' . $id],
            'description'    => ['nullable', 'string'],
            'price'          => ['required', 'numeric', 'min:0'],
            'sale_price'     => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'category_id'    => ['nullable', 'exists:categories,id'],
            'category_ids'   => ['nullable', 'array'],
            'category_ids.*' => ['exists:categories,id'],
            'is_active'      => ['nullable'],
            'image'          => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,svg,avif', 'max:3072'],
            'image_url'      => ['nullable', 'string', 'url'],
        ]);

        DB::transaction(function () use ($request, $product, $validated) {
            $product->update([
                'title'          => $validated['title'],
                'slug'           => $request->filled('slug') ? Str::slug($request->slug) : $product->slug,
                'description'    => $request->input('description', $product->description),
                'price'          => $validated['price'],
                'sale_price'     => $validated['sale_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'],
                'is_active'      => $request->has('is_active') 
                                    ? filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN) 
                                    : $product->is_active,
            ]);

            // Sync categories
            $categories = [];
            if ($request->filled('category_id')) {
                $categories[] = $request->input('category_id');
            }
            if ($request->filled('category_ids') && is_array($request->input('category_ids'))) {
                $categories = array_unique(array_merge($categories, $request->input('category_ids')));
            }
            if (!empty($categories)) {
                $product->categories()->sync($categories);
            }

            // Process image updates
            $newImagePath = null;
            if ($request->hasFile('image')) {
                $newImagePath = $request->file('image')->store('products', 'public');
            } elseif ($request->filled('image_url')) {
                $newImagePath = $request->input('image_url');
            }

            if ($newImagePath) {
                $primaryImage = $product->primaryImage;

                // Delete previous file from storage if it was a local upload
                if ($primaryImage && !str_starts_with($primaryImage->image_path, 'http') && Storage::disk('public')->exists($primaryImage->image_path)) {
                    Storage::disk('public')->delete($primaryImage->image_path);
                }

                if ($primaryImage) {
                    $primaryImage->update(['image_path' => $newImagePath]);
                } else {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $newImagePath,
                        'is_primary' => true,
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'code'    => 1000,
            'data'    => new ProductResource($product->fresh(['categories', 'images', 'primaryImage'])),
        ], 200);
    }

    /**
     * DELETE /api/admin/products/{id}
     * Delete product along with its physical image files.
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::with('images')->findOrFail($id);

        foreach ($product->images as $img) {
            if (!str_starts_with($img->image_path, 'http') && Storage::disk('public')->exists($img->image_path)) {
                Storage::disk('public')->delete($img->image_path);
            }
            $img->delete();
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product and associated images deleted successfully',
            'code'    => 1000,
        ], 200);
    }
}