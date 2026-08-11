<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    /**
     * GET /api/admin/dashboard-stats
     */
    public function stats(): JsonResponse
    {
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $totalOrders = Order::count();
        $totalCustomers = User::where('role', 'user')->count();
        $pendingOrders = Order::where('status', 'placed')->count();
        $lowStockProducts = Product::where('stock_quantity', '<=', 5)->count();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard metrics fetched successfully',
            'code'    => 1000,
            'data'    => [
                'total_revenue'       => (float) round($totalRevenue, 2),
                'total_orders'        => $totalOrders,
                'total_customers'     => $totalCustomers,
                'pending_orders'      => $pendingOrders,
                'low_stock_products'  => $lowStockProducts,
            ],
        ], 200);
    }
}