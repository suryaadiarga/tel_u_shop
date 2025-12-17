<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Merchant Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:merchant'])->group(function () {
    // Merchant dashboard or routes here
});
