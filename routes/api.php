<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\CatalogController;

Route::prefix('v1')->group(function () {
    // katalog tel u shop
    Route::get('/catalog', [CatalogController::class, 'index']);

    // pesan langsung tanpa masuk keranjang
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // menandai apabila sudah bayar
    Route::post('/orders/{id}/pay', [OrderController::class, 'pay']);
});