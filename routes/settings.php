<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Exceptions\Handler;

/*
|--------------------------------------------------------------------------
| Settings Routes
|--------------------------------------------------------------------------
*/

Route::get('/settings', function () {
    return response()->json([
        'message' => 'Settings endpoint'
    ]);
});
