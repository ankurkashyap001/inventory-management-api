<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * GET /api/orders
     * Fetch user's order history.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product.primaryImage'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Order history retrieved',
            'code'    => 1000,
            'data'    => OrderResource::collection($orders),
        ], 200);
    }

    /**
     * GET /api/orders/{id}
     * Fetch single order details by ID or order_number.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $order = $request->user()
            ->orders()
            ->with(['items.product.primaryImage'])
            ->where(function ($query) use ($id) {
                $query->where('id', $id)
                      ->orWhere('order_number', $id);
            })
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
                'code'    => 1005,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order details retrieved',
            'code'    => 1000,
            'data'    => new OrderResource($order),
        ], 200);
    }

    /**
     * POST /api/orders
     * Place an order from active cart.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'address_id'     => 'required|integer|exists:addresses,id',
            'payment_method' => 'required|string|in:COD,Online',
        ]);

        $user = $request->user();

        // 1. Verify Address Ownership
        $address = Address::where('user_id', $user->id)
            ->where('id', $request->address_id)
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shipping address selected',
                'code'    => 1001,
            ], 422);
        }

        // 2. Fetch Active Cart
        $cart = $user->activeCart()->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty. Cannot place order.',
                'code'    => 1002,
            ], 400);
        }

        // 3. Stock Availability Verification
        foreach ($cart->items as $item) {
            $product = $item->product;

            if (!$product || !$product->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => "Product '{$product?->title}' is no longer available.",
                    'code'    => 1003,
                ], 422);
            }

            if ($product->stock_quantity < $item->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock for '{$product->title}'. Only {$product->stock_quantity} remaining.",
                    'code'    => 1004,
                ], 422);
            }
        }

        // 4. Execute Transactional Order Creation
        try {
            $order = DB::transaction(function () use ($user, $cart, $address, $request) {
                
                // Create Order Entry
                $newOrder = Order::create([
                    'user_id'               => $user->id,
                    'order_number'          => 'ANK-' . strtoupper(Str::random(8)),
                    'total_amount'          => $cart->total_amount,
                    'status'                => 'pending',
                    'payment_method'        => $request->payment_method,
                    'payment_status'        => $request->payment_method === 'Online' ? 'paid' : 'pending',
                    'shipping_address_json' => [
                        'full_name'    => $address->full_name,
                        'phone'        => $address->phone,
                        'address_line' => $address->address_line,
                        'city'         => $address->city,
                        'state'        => $address->state,
                        'postal_code'  => $address->postal_code,
                    ],
                ]);

                // Move items from Cart to Order & Decrement Stock
                foreach ($cart->items as $cartItem) {
                    $newOrder->items()->create([
                        'product_id'  => $cartItem->product_id,
                        'quantity'    => $cartItem->quantity,
                        'unit_price'  => $cartItem->price,
                        'total_price' => $cartItem->subtotal,
                    ]);

                    // Deduct stock quantity
                    $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
                }

                // Clear Active Cart items & Reset cart total
                $cart->items()->delete();
                $cart->update(['total_amount' => 0.00]);

                return $newOrder;
            });

            $order->load(['items.product.primaryImage']);

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'code'    => 1000,
                'data'    => new OrderResource($order),
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order. ' . $th->getMessage(),
                'code'    => 500,
            ], 500);
        }
    }
}