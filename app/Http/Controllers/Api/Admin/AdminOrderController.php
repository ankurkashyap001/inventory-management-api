<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    /**
     * GET /api/admin/orders
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['user', 'items.product.primaryImage']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Admin orders list retrieved',
            'code'    => 1000,
            'data'    => OrderResource::collection($orders),
            'meta'    => [
                'current_page' => $orders->currentPage(),
                'total_pages'  => $orders->lastPage(),
                'total_orders' => $orders->total(),
            ],
        ], 200);
    }

    /**
     * PATCH /api/admin/orders/{id}/status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:placed,packing,out_for_delivery,delivered,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;

        if ($request->status === 'delivered') {
            $order->payment_status = 'paid';
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => "Order status updated to '{$request->status}'",
            'code'    => 1000,
            'data'    => new OrderResource($order->load('items.product.primaryImage')),
        ], 200);
    }
}