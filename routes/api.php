<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ProviderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReviewController;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::middleware('throttle:6,1')->group(function () {
    Route::post('/auth/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
});

Route::get('/categories', [CustomerController::class, 'getCategories']);
Route::get('/categories/{category}/services', [CustomerController::class, 'getServices']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Customer Routes
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/service-requests', [CustomerController::class, 'createRequest']);
    });
    
    Route::get('/customer/orders', [CustomerController::class, 'getOrders']);
    
    Route::post('/service-requests/{id}/cancel', [CustomerController::class, 'cancelRequest']);
    Route::get('/service-requests/{id}/track', [CustomerController::class, 'trackRequest']);
    Route::post('/reviews', [ReviewController::class, 'store']);

    // Provider Routes
    Route::get('/provider/stats', [ProviderController::class, 'getStats']);
    Route::get('/provider/requests', [ProviderController::class, 'getRequests']);
    Route::post('/provider/profile', [ProviderController::class, 'updateProfile']);
    Route::middleware('throttle:5,1')->post('/provider/kyc', [ProviderController::class, 'uploadKyc']);
    Route::post('/provider/toggle-availability', [ProviderController::class, 'toggleAvailability']);
    Route::middleware('throttle:30,1')->post('/provider/location', [ProviderController::class, 'updateLocation']);
    Route::post('/provider/requests/{id}/accept', [ProviderController::class, 'acceptRequest']);
    Route::post('/provider/requests/{id}/complete', [ProviderController::class, 'completeRequest']);

    // Payment Routes (Moyasar)
    Route::post('/payments/authorize', [PaymentController::class, 'authorizePayment']);
    Route::post('/payments/capture', [PaymentController::class, 'capturePayment']);
    Route::post('/payments/void', [PaymentController::class, 'voidPayment']);
});
