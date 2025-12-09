<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController;

Route::middleware(['auth', 'role:1'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // User management
    Route::resource('/users', UserController::class);

    // Order management
    Route::resource('/orders', OrderController::class);
});