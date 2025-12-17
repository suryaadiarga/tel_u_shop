<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentCardController;
use App\Http\Controllers\RedirectController;

// Home
//Route::get('/', [HomeController::class, 'index'])->name('home');

// QR Scanner
Route::get('/qr-scanner', [QrController::class, 'index'])->name('qr.scanner.page');
Route::post('/qr-scanner', [QrController::class, 'process'])->name('qr.process');

// Aliases kompatibilitas (Blade lama tetap aman)
Route::get('/qr', [RedirectController::class, 'qr'])->name('qr');
Route::get('/activity', [RedirectController::class, 'activity'])->name('activity');
Route::get('/wallet', [RedirectController::class, 'wallet'])->name('wallet');

// Auth umum
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/student-card', [StudentCardController::class, 'index'])->name('student.card');
});

// Modular routes
require __DIR__ . '/admin.php';
require __DIR__ . '/merchant.php';
require __DIR__ . '/customer.php';
require __DIR__ . '/guest.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/settings.php';
