<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminUserController extends Controller
{
    /**
     * GET /api/admin/users
     */
    public function index(): JsonResponse
    {
        $users = User::withCount('orders')
            ->where('role', 'user')
            ->latest()
            ->get()
            ->map(function ($u) {
                return [
                    'id'           => $u->id,
                    'name'         => $u->name,
                    'phone'        => $u->phone,
                    'email'        => $u->email,
                    'orders_count' => $u->orders_count,
                    'created_at'   => $u->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Customers list fetched',
            'code'    => 1000,
            'data'    => $users,
        ], 200);
    }
}