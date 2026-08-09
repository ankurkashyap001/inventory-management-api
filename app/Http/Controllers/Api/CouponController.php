<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * POST /api/coupons/apply
     * Validate coupon code and calculate discount.
     */
    public function apply(Request $request): JsonResponse
    {
        $request->validate([
            'code'          => 'required|string',
            'cart_subtotal' => 'required|numeric|min:0',
        ]);

        $code = strtoupper(trim($request->code));
        $subtotal = (float) $request->cart_subtotal;

        // 1. Fetch Coupon
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'valid'   => false,
                'message' => 'Invalid coupon code.',
                'code'    => 1001,
            ], 422);
        }

        // 2. Active Check
        if (!$coupon->is_active) {
            return response()->json([
                'success' => false,
                'valid'   => false,
                'message' => 'This coupon is no longer active.',
                'code'    => 1002,
            ], 422);
        }

        // 3. Expiry Check
        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'valid'   => false,
                'message' => 'This coupon has expired.',
                'code'    => 1003,
            ], 422);
        }

        // 4. Minimum Order Amount Check
        if ($subtotal < (float) $coupon->min_order_amount) {
            return response()->json([
                'success' => false,
                'valid'   => false,
                'message' => "Minimum order amount of ₹" . number_format($coupon->min_order_amount, 0) . " required for this coupon.",
                'code'    => 1004,
            ], 422);
        }

        // 5. Calculate Discount
        $discountAmount = 0.00;

        if ($coupon->type === 'percent') {
            $discountAmount = ($subtotal * (float) $coupon->value) / 100;
        } else {
            $discountAmount = (float) $coupon->value;
        }

        // Discount cannot exceed subtotal
        if ($discountAmount > $subtotal) {
            $discountAmount = $subtotal;
        }

        $discountAmount = round($discountAmount, 2);
        $finalAmount = round($subtotal - $discountAmount, 2);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'code'    => 1000,
            'data'    => [
                'valid'            => true,
                'code'             => $coupon->code,
                'type'             => $coupon->type,
                'value'            => (float) $coupon->value,
                'discount_amount'  => $discountAmount,
                'cart_subtotal'    => $subtotal,
                'final_amount'     => $finalAmount,
            ],
        ], 200);
    }
}