<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * GET /api/wishlist
     * Fetch all wishlisted products for logged-in user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Retrieve wishlisted items along with product relations
        $wishlists = Wishlist::where('user_id', $user->id)
            ->with(['product.categories', 'product.images', 'product.primaryImage'])
            ->latest()
            ->get();

        $products = $wishlists->pluck('product')->filter();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist items retrieved successfully',
            'code'    => 1000,
            'data'    => ProductResource::collection($products),
        ], 200);
    }

    /**
     * POST /api/wishlist/toggle
     * Toggle item in wishlist (Add if absent, Remove if present).
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $user = $request->user();
        $productId = $request->product_id;

        $existingItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existingItem) {
            $existingItem->delete();
            $wishlisted = false;
            $message = 'Product removed from wishlist';
        } else {
            Wishlist::create([
                'user_id'    => $user->id,
                'product_id' => $productId,
            ]);
            $wishlisted = true;
            $message = 'Product added to wishlist';
        }

        $totalCount = Wishlist::where('user_id', $user->id)->count();

        return response()->json([
            'success'    => true,
            'message'    => $message,
            'code'       => 1000,
            'data'       => [
                'product_id' => (int) $productId,
                'wishlisted' => $wishlisted,
                'count'      => $totalCount,
            ],
        ], 200);
    }

    /**
     * GET /api/wishlist/ids
     * Get array of wishlisted product IDs for fast state sync on UI.
     */
    public function getIds(Request $request): JsonResponse
    {
        $wishlistedIds = $request->user()
            ->wishlists()
            ->pluck('product_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist IDs retrieved successfully',
            'code'    => 1000,
            'data'    => $wishlistedIds,
        ], 200);
    }
}