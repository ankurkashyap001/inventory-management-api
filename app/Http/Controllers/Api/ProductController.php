<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['categories', 'images', 'primaryImage'])
            ->where('is_active', true);

        // Optional Category filter
        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        $products = $query->latest()->paginate(10);

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