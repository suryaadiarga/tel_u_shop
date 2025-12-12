<?php

use App\Http\Controllers\Api\MahasiswaAuthController;

Route::post('/mahasiswa/register', [MahasiswaAuthController::class, 'register']);
Route::post('/mahasiswa/login', [MahasiswaAuthController::class, 'login']);
Route::post('/mahasiswa/logout', [MahasiswaAuthController::class, 'logout']);