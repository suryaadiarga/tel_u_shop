<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\WalletController;
use App\Http\Controllers\Customer\ActivityController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Customer\LoyaltyController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Merchant\ProductController as MerchantProductController;
use App\Http\Controllers\Merchant\OrderController as MerchantOrderController;
use App\Http\Controllers\Merchant\AnalyticsController as MerchantAnalyticsController;
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
    Route::middleware(['auth:sanctum', 'not-banned'])->group(function () {
        // Auth
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':customer')->group(function () {
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

            // Customer - Wishlist
            Route::get('/wishlist', [WishlistController::class, 'index']);
            Route::post('/wishlist/add/{product}', [WishlistController::class, 'add']);
            Route::delete('/wishlist/remove/{product}', [WishlistController::class, 'remove']);
            Route::get('/wishlist/check/{product}', [WishlistController::class, 'check']);

            // Customer - Products
            Route::get('/products', [CustomerProductController::class, 'index']);
            Route::get('/products/{id}', [CustomerProductController::class, 'show']);
            Route::get('/products/categories', [CustomerProductController::class, 'categories']);

            // Customer - Reviews
            Route::get('/products/{product}/reviews', [ReviewController::class, 'index']);
            Route::post('/products/{product}/reviews', [ReviewController::class, 'store']);
            Route::get('/reviews/{review}', [ReviewController::class, 'show']);
            Route::put('/reviews/{review}', [ReviewController::class, 'update']);
            Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
            Route::get('/my-reviews', [ReviewController::class, 'myReviews']);

            // Customer - Loyalty Points
            Route::get('/loyalty/balance', [LoyaltyController::class, 'balance']);
            Route::get('/loyalty/history', [LoyaltyController::class, 'history']);
            Route::post('/loyalty/redeem', [LoyaltyController::class, 'redeem']);
            Route::get('/loyalty/rewards', [LoyaltyController::class, 'rewards']);

            // Customer - Notifications
            Route::get('/notifications', [NotificationController::class, 'index']);
            Route::get('/notifications/stats', [NotificationController::class, 'stats']);
            Route::get('/notifications/{id}', [NotificationController::class, 'show']);
            Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
            Route::put('/notifications/mark-read', [NotificationController::class, 'markMultipleAsRead']);
            Route::put('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
            Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
        });

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

            // Analytics
            Route::get('/merchant/analytics/dashboard', [MerchantAnalyticsController::class, 'dashboard']);
            Route::get('/merchant/analytics/sales', [MerchantAnalyticsController::class, 'salesAnalytics']);
            Route::get('/merchant/analytics/products', [MerchantAnalyticsController::class, 'productPerformance']);
            Route::get('/merchant/analytics/customers', [MerchantAnalyticsController::class, 'customerAnalytics']);
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
            Route::put('/admin/merchants/{id}/approve', [AdminUserController::class, 'approveMerchant']);
            Route::put('/admin/users/{id}/ban', [AdminUserController::class, 'ban']);
            Route::put('/admin/users/{id}/unban', [AdminUserController::class, 'unban']);
        });
    });
});
