<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PhoneAuthController extends Controller
{
    /**
     * POST /api/auth/send-otp
     * Generate & send mock OTP to user's phone.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
        ], [
            'phone.regex' => 'The phone number must be exactly 10 digits.',
        ]);

        $phone = $request->phone;
        $mockOtp = '1234'; // Development Mock OTP

        // Save or Update OTP record in database with 10 mins expiry
        Otp::updateOrCreate(
            ['phone' => $phone],
            [
                'otp'        => $mockOtp,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        $userExists = User::where('phone', $phone)->exists();

        return response()->json([
            'success'     => true,
            'message'     => 'OTP sent successfully',
            'code'        => 1000,
            'is_new_user' => !$userExists,
            'otp'         => $mockOtp, // Sent in response for Dev testing
        ], 200);
    }

    /**
     * POST /api/auth/verify-otp
     * Verify OTP and issue Sanctum token.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'otp'   => ['required', 'string', 'digits:4'],
            'name'  => ['nullable', 'string', 'max:255'],
        ]);

        $phone = $request->phone;
        $otpCode = $request->otp;

        // Verify against OTP records
        $otpRecord = Otp::where('phone', $phone)
            ->where('otp', $otpCode)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP',
                'code'    => 1001,
            ], 422);
        }

        // Find or Create User
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            $user = User::create([
                'phone' => $phone,
                'name'  => $request->name ?? 'User ' . substr($phone, -4),
            ]);
        } else if ($request->filled('name')) {
            $user->update(['name' => $request->name]);
        }

        // Revoke old tokens & create fresh Sanctum Auth Token
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Clear used OTP record
        $otpRecord->delete();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'code'    => 1000,
            'data'    => [
                'token' => $token,
                'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                ],
            ],
        ], 200);
    }
}