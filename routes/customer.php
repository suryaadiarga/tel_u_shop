<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\WalletController;
use App\Http\Controllers\Customer\ActivityController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;

Route::middleware(['auth', 'role:3'])->prefix('customer')->group(function () {
    // Wallet
    Route::get('/wallet', [WalletController::class, 'index'])->name('customer.wallet');
    Route::post('/wallet/topup', [WalletController::class, 'topup'])->name('customer.wallet.topup');

    // Activity
    Route::get('/activity', [ActivityController::class, 'index'])->name('customer.activity');

    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('customer.cart.index');
        Route::post('/add/{product}', [CartController::class, 'add'])->name('customer.cart.add');
        Route::patch('/item/{item}', [CartController::class, 'updateQty'])->name('customer.cart.update');
        Route::delete('/item/{item}', [CartController::class, 'remove'])->name('customer.cart.remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('customer.cart.clear');
    });

    // Checkout
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('customer.checkout.store');
});