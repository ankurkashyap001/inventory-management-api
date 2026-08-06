<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Get or create active cart for authenticated user.
     */
    private function getOrCreateActiveCart($user): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            ['total_amount' => 0.00]
        );
    }

    /**
     * GET /api/cart
     * View active cart with items.
     */
    public function index(Request $request): JsonResponse
    {
        $cart = $request->user()->activeCart()->with(['items.product.primaryImage', 'items.product.images'])->first();

        if (!$cart) {
            $cart = $this->getOrCreateActiveCart($request->user());
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart retrieved successfully',
            'code'    => 1000,
            'data'    => new CartResource($cart),
        ], 200);
    }

    /**
     * POST /api/cart/add
     * Add or update quantity of product in cart with stock validation.
     */
    public function add(AddToCartRequest $request): JsonResponse
    {
        $user = $request->user();
        $product = Product::findOrFail($request->product_id);

        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Product is currently unavailable',
                'code'    => 1001,
            ], 400);
        }

        $cart = $this->getOrCreateActiveCart($user);
        $existingItem = $cart->items()->where('product_id', $product->id)->first();
        
        $newQuantity = $request->quantity;
        if ($existingItem) {
            $newQuantity += $existingItem->quantity;
        }

        // Stock Validation Check
        if ($product->stock_quantity < $newQuantity) {
            return response()->json([
                'success' => false,
                'message' => "Only {$product->stock_quantity} units available in stock.",
                'code'    => 1002,
            ], 422);
        }

        // Effective selling price (sale price if available, else standard price)
        $unitPrice = $product->sale_price ?? $product->price;

        DB::transaction(function () use ($cart, $product, $existingItem, $newQuantity, $unitPrice) {
            if ($existingItem) {
                $existingItem->update([
                    'quantity' => $newQuantity,
                    'price'    => $unitPrice,
                    'subtotal' => $unitPrice * $newQuantity,
                ]);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $newQuantity,
                    'price'      => $unitPrice,
                    'subtotal'   => $unitPrice * $newQuantity,
                ]);
            }

            $cart->recalculateTotal();
        });

        $cart->load(['items.product.primaryImage', 'items.product.images']);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'code'    => 1000,
            'data'    => new CartResource($cart),
        ], 200);
    }

    /**
     * PUT /api/cart/items/{id}
     * Update cart item quantity directly.
     */
    public function updateItem(UpdateCartItemRequest $request, int $itemId): JsonResponse
    {
        $cart = $request->user()->activeCart;

        if (!$cart) {
            return response()->json(['success' => false, 'message' => 'Cart not found'], 404);
        }

        $cartItem = $cart->items()->where('id', $itemId)->first();

        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Cart item not found'], 404);
        }

        $product = $cartItem->product;

        // Stock Validation
        if ($product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => "Stock limit exceeded. Only {$product->stock_quantity} available.",
                'code'    => 1002,
            ], 422);
        }

        DB::transaction(function () use ($cart, $cartItem, $request) {
            $cartItem->update([
                'quantity' => $request->quantity,
                'subtotal' => $cartItem->price * $request->quantity,
            ]);

            $cart->recalculateTotal();
        });

        $cart->load(['items.product.primaryImage', 'items.product.images']);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'code'    => 1000,
            'data'    => new CartResource($cart),
        ], 200);
    }

    /**
     * DELETE /api/cart/items/{id}
     * Remove single item from cart.
     */
    public function removeItem(Request $request, int $itemId): JsonResponse
    {
        $cart = $request->user()->activeCart;

        if (!$cart) {
            return response()->json(['success' => false, 'message' => 'Cart not found'], 404);
        }

        $cartItem = $cart->items()->where('id', $itemId)->first();

        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Item not found in cart'], 404);
        }

        DB::transaction(function () use ($cart, $cartItem) {
            $cartItem->delete();
            $cart->recalculateTotal();
        });

        $cart->load(['items.product.primaryImage', 'items.product.images']);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'code'    => 1000,
            'data'    => new CartResource($cart),
        ], 200);
    }

    /**
     * DELETE /api/cart/clear
     * Empty all items from active cart.
     */
    public function clear(Request $request): JsonResponse
    {
        $cart = $request->user()->activeCart;

        if ($cart) {
            DB::transaction(function () use ($cart) {
                $cart->items()->delete();
                $cart->recalculateTotal();
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'code'    => 1000,
            'data'    => [
                'cart_id'      => $cart?->id,
                'total_amount' => 0.00,
                'total_items'  => 0,
                'items'        => [],
            ],
        ], 200);
    }
}