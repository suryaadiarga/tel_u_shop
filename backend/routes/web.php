<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect to API documentation or welcome page
Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to Tel-U Shop API',
        'version' => '1.0.0',
        'documentation' => '/docs'
    ]);
});
