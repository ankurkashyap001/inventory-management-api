<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. User Creation
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Generate Sanctum Auth Token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Response
        return response()->json([
            'status'  => true,
            'message' => 'User registered successfully',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }
    

    public function login(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Check Credentials
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // 3. Get Authenticated User & Generate Sanctum Token
        $user  = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Response
        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'user'    => $user,
            'token'   => $token,
        ], 200);
    }

    //logout
    public function logout(Request $request)
    {
        // User ka current access token revoke / delete karein
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ], 200);
    }
}