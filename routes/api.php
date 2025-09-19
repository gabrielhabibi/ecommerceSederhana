<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\PaymentController;

Route::prefix('auth')->group(function () {

    // Registrasi
    Route::post('register', [AuthController::class, 'register']);

    // Login
    Route::post('login', [AuthController::class, 'login']);

    // Logout (pakai token, misal Sanctum)
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Verifikasi email
    Route::get('verify-email/{token}', [VerificationController::class, 'verify']);

    // Kirim ulang verifikasi email
    Route::post('resend-verification', [VerificationController::class, 'resend']);

    // Lupa password → request kode
    Route::post('forgot-password', [PasswordResetController::class, 'requestReset']);

    // Reset password
    Route::post('reset-password', [PasswordResetController::class, 'reset']);
});

// Untuk menampilkan semua produk dan filtering by category
Route::get('/products', [ProductController::class, 'index']);      // list + optional filter via query
Route::get('/products/{id}', [ProductController::class, 'show']);  // detail produk

// Cart
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart', [CartController::class, 'update']);
    Route::put('/cart/{id}', [CartController::class, 'updateSingle']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);
    Route::delete('/cart/bulk-destroy', [CartController::class, 'bulkDestroy']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::get('/cart/summary', [CartController::class, 'summary']);
});

// Checkout
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/checkout', [CheckoutController::class, 'checkout']);
});

//Midtrans
Route::post('/midtrans/notification', [PaymentController::class, 'handleNotification']);