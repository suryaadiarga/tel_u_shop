<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentCardController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\QrController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');

    // 5 request per 1 menit per IP/email
    Route::post('/login', [LoginController::class, 'authenticate'])
        ->middleware('throttle:5,1')
        ->name('login.post');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');

    // 3 request per 1 menit per IP
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:3,1')
        ->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/student-card', [StudentCardController::class, 'index'])->name('student.card');
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity');
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::get('/qr', [QrController::class, 'index'])->name('qr');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
