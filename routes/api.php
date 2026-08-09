<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\AuthController; // Ensure exact path here
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PhoneAuthController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\CouponController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public Phone Auth Routes
Route::prefix('auth')->group(function () {
    Route::post('/send-otp', [PhoneAuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [PhoneAuthController::class, 'verifyOtp']);
});

Route::middleware('auth:sanctum')->group(function () {
    
    // Cart Routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/items/{id}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{id}', [CartController::class, 'removeItem']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);

    // User Profile
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);

    // Address
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);

    // Orders & Tracking Endpoints
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/orders/{id}/track', [OrderController::class, 'track']); // Live Tracking
    Route::post('/orders', [OrderController::class, 'store']);

    // Wishlist Endpoints
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle']);
    Route::get('/wishlist/ids', [WishlistController::class, 'getIds']);

    // Coupon Endpoint (Public or Protected)
    Route::post('/coupons/apply', [CouponController::class, 'apply']);

    //logout
    Route::post('/logout', [AuthController::class, 'logout']);

});

Route::middleware('auth:sanctum')->get('/test-token', function (Request $request) {
    return response()->json([
        'message' => 'Token working fine!',
        'user' => $request->user()
    ]);
});

Route::get('/test', [TestController::class, 'getData']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
