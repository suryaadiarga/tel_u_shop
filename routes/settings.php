<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('settings')->group(function () {
    Route::get('/', function () {
        return view('settings.index', ['title' => 'Pengaturan']);
    })->name('settings.index');
});