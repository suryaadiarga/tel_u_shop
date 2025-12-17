<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\WalletController;
use App\Http\Controllers\Customer\ActivityController;
use App\Http\Controllers\Merchant\ProductController as MerchantProductController;
use App\Http\Controllers\Merchant\OrderController as MerchantOrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('api')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Auth Routes (Public)
    |--------------------------------------------------------------------------
    */
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    /*
    |--------------------------------------------------------------------------
    | Authenticated Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Customer - Cart
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart/add/{product}', [CartController::class, 'add']);
        Route::put('/cart/update/{item}', [CartController::class, 'updateQty']);
        Route::delete('/cart/remove/{item}', [CartController::class, 'remove']);
        Route::delete('/cart/clear', [CartController::class, 'clear']);

        // Customer - Checkout
        Route::post('/checkout', [CheckoutController::class, 'store']);

        // Customer - Wallet
        Route::get('/wallet/balance', [WalletController::class, 'balance']);
        Route::get('/wallet/transactions', [WalletController::class, 'transactions']);
        Route::post('/wallet/topup', [WalletController::class, 'topup']);

        // Customer - Activity
        Route::get('/activities', [ActivityController::class, 'index']);
        Route::get('/activities/{id}', [ActivityController::class, 'show']);
        Route::get('/activities/{id}/track', [ActivityController::class, 'track']);

        /*
        |--------------------------------------------------------------------------
        | Merchant Routes
        |--------------------------------------------------------------------------
        */
        Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':merchant')->group(function () {
            // Products
            Route::get('/merchant/products', [MerchantProductController::class, 'index']);
            Route::post('/merchant/products', [MerchantProductController::class, 'store']);
            Route::put('/merchant/products/{id}', [MerchantProductController::class, 'update']);
            Route::delete('/merchant/products/{id}', [MerchantProductController::class, 'destroy']);

            // Orders
            Route::get('/merchant/orders', [MerchantOrderController::class, 'index']);
            Route::put('/merchant/orders/{id}/status', [MerchantOrderController::class, 'updateStatus']);
        });

        /*
        |--------------------------------------------------------------------------
        | Admin Routes
        |--------------------------------------------------------------------------
        */
        Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin')->group(function () {
            // Orders
            Route::get('/admin/orders', [AdminOrderController::class, 'index']);
            Route::get('/admin/orders/{id}', [AdminOrderController::class, 'show']);
            Route::put('/admin/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);

            // Users
            Route::get('/admin/users', [AdminUserController::class, 'index']);
            Route::get('/admin/users/{id}', [AdminUserController::class, 'show']);
            Route::put('/admin/users/{id}/role', [AdminUserController::class, 'updateRole']);
            Route::put('/admin/users/{id}/deactivate', [AdminUserController::class, 'deactivate']);
            Route::put('/admin/users/{id}/activate', [AdminUserController::class, 'activate']);
        });
    });
});
