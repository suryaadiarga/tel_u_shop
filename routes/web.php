<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentCardController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\CheckoutController;

// ========================
// Guest Routes (Belum login)
// ========================
Route::middleware('guest')->group(function () {
    // --- Login & Register ---
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])
        ->middleware('throttle:5,1')->name('login.post');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:3,1')->name('register.store');

    // --- Lupa Password / Reset Password ---
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');
});

// ========================
// Authenticated Routes (Sudah login)
// ========================
Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // --- Wallet ---
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet');
    Route::post('/wallet/topup', [WalletController::class, 'topup'])->name('wallet.topup');

    // --- Profil & Data Mahasiswa ---
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/student-card', [StudentCardController::class, 'index'])->name('student.card');
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity');

    // --- Keranjang & Checkout ---
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/item/{item}', [CartController::class, 'updateQty'])->name('cart.update');
    Route::delete('/cart/item/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // --- QR / Scan ---
    Route::get('/qr', [QrController::class, 'index'])->name('qr');

    // --- Logout ---
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
