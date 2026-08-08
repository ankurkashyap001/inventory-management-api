<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * GET /api/user
     * Fetch authenticated user details.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'User profile retrieved successfully',
            'code'    => 1000,
            'data'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone ?? null,
                'created_at' => $user->created_at->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * PUT /api/user/profile
     * Update user's name and phone number.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'], // 10 digit Indian phone validation
        ], [
            'phone.regex' => 'The phone number must be exactly 10 digits.',
        ]);

        $user = $request->user();
        $user->update([
            'name'  => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'code'    => 1000,
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ], 200);
    }
}