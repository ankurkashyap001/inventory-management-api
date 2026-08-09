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
use Carbon\Carbon;

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

    /**
     * GET /api/orders/{id}/track
     * Live Order Tracking with status timeline & delivery partner details.
     */
    public function track(Request $request, $id): JsonResponse
    {
        $order = $request->user()
            ->orders()
            ->with(['items.product.primaryImage', 'items.product.images'])
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

        // Default status fallback if new status values are not set
        $currentStatus = in_array($order->status, ['placed', 'packing', 'out_for_delivery', 'delivered', 'cancelled'])
            ? $order->status
            : 'out_for_delivery';

        // Estimated delivery calculation (Default: 10 mins from order creation if not set)
        $estimatedTime = $order->estimated_delivery_time ?? $order->created_at->addMinutes(10);
        $diffInMins = (int) ceil(Carbon::now()->diffInMinutes($estimatedTime, false));
        $estimatedMins = max($diffInMins, 0);

        // Mock Delivery Partner details if DB fields are empty
        $deliveryPartner = [
            'name'   => $order->delivery_partner_name ?? 'Rahul Sharma',
            'phone'  => $order->delivery_partner_phone ?? '+91 98765 43210',
            'avatar' => 'https://i.pravatar.cc/150?img=12',
        ];

        // Status Timeline with completion status for UI stepper
        $timeline = [
            [
                'stage'       => 'placed',
                'title'       => 'Order Placed',
                'completed'   => true,
                'time'        => $order->created_at->format('h:i A'),
            ],
            [
                'stage'       => 'packing',
                'title'       => 'Items Packed',
                'completed'   => in_array($currentStatus, ['packing', 'out_for_delivery', 'delivered']),
                'time'        => $order->created_at->addMinutes(2)->format('h:i A'),
            ],
            [
                'stage'       => 'out_for_delivery',
                'title'       => 'Out for Delivery',
                'completed'   => in_array($currentStatus, ['out_for_delivery', 'delivered']),
                'time'        => $order->created_at->addMinutes(4)->format('h:i A'),
            ],
            [
                'stage'       => 'delivered',
                'title'       => 'Delivered',
                'completed'   => $currentStatus === 'delivered',
                'time'        => $estimatedTime->format('h:i A'),
            ],
        ];

        // Format order items
        $formattedItems = $order->items->map(function ($item) {
            return [
                'id'          => $item->id,
                'product_id'  => $item->product_id,
                'title'       => $item->product?->title ?? 'Item',
                'quantity'    => $item->quantity,
                'unit_price'  => (float) $item->unit_price,
                'total_price' => (float) $item->total_price,
                'image_url'   => $item->product?->primaryImage?->image_path,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Live tracking data retrieved successfully',
            'code'    => 1000,
            'data'    => [
                'id'               => $order->id,
                'order_number'     => $order->order_number,
                'status'           => $currentStatus,
                'estimated_mins'   => $estimatedMins,
                'delivery_partner' => $deliveryPartner,
                'timeline'         => $timeline,
                'delivery_address' => $order->shipping_address_json,
                'total_amount'     => (float) $order->total_amount,
                'items'            => $formattedItems,
                'created_at'       => $order->created_at->toIso8601String(),
            ],
        ], 200);
    }
}