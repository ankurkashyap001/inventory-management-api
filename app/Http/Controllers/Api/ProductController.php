<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products with filtering & pagination.
     */
    public function index(Request $request)
    {
        $query = Product::with(['categories', 'images', 'primaryImage'])
            ->where('is_active', true);

        // 1. Filter by Category ID
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // 2. Filter by Category Slug (Blinkit style URL filtering: /products?category_slug=milk)
        if ($request->filled('category_slug')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.slug', $request->category_slug);
            });
        }

        // Dynamic page limit (Default: 20 products per page for UI grids)
        $perPage = $request->get('per_page', 20);

        $products = $query->latest()->paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * Display the specified product.
     */
    public function show(string $id)
    {
        $product = Product::with(['categories', 'images', 'primaryImage'])
            ->where('is_active', true)
            ->findOrFail($id);

        return new ProductResource($product);
    }
}