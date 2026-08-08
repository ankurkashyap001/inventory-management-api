<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * GET /api/addresses
     * Fetch user's saved addresses.
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Addresses retrieved successfully',
            'code'    => 1000,
            'data'    => AddressResource::collection($addresses),
        ], 200);
    }

    /**
     * POST /api/addresses
     * Save a new address.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
            'address_line' => 'required|string',
            'city'         => 'required|string|max:100',
            'state'        => 'required|string|max:100',
            'postal_code'  => 'required|string|max:20',
            'is_default'   => 'nullable|boolean',
        ]);

        $user = $request->user();

        // Handle default address flag logic
        if (!empty($validated['is_default']) && $validated['is_default'] === true) {
            $user->addresses()->update(['is_default' => false]);
        }

        // Make first address default automatically if none exists
        if ($user->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }

        $address = $user->addresses()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address saved successfully',
            'code'    => 1000,
            'data'    => new AddressResource($address),
        ], 201);
    }

    /**
     * DELETE /api/addresses/{id}
     * Delete a saved address for the authenticated user.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $address = $request->user()->addresses()->where('id', $id)->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found or does not belong to you',
                'code'    => 1001,
            ], 404);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        // If deleted address was default, set the latest remaining address as default
        if ($wasDefault) {
            $nextDefault = $request->user()->addresses()->latest()->first();
            if ($nextDefault) {
                $nextDefault->update(['is_default' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully',
            'code'    => 1000,
        ], 200);
    }
}