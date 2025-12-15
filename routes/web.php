<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::get('/history', function() {
    return Inertia::render('history');
})->name('history');

Route::get('/cart', function() {
    return Inertia::render('cart/shoppingcart');
})->name('shoppingcart');

Route::get('/cart/wishlist', function() {
    return Inertia::render('cart/wishlist');
})->name('wishlist');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
