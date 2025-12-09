<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Merchant\DashboardController;
use App\Http\Controllers\Merchant\OrderController;
use App\Http\Controllers\Merchant\ProductController;

Route::middleware(['auth', 'role:2'])->prefix('merchant')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('merchant.dashboard');

    // Order management
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('merchant.orders.update-status');
    Route::get('/orders/history', [OrderController::class, 'history'])->name('merchant.orders.history');

    // Product management
    Route::resource('/products', ProductController::class);
});