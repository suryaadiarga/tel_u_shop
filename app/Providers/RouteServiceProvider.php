<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/';


    public function boot(): void
    {
        RateLimiter::for('login', function (Request $r) {
            return [Limit::perMinute(5)->by($r->input('email') ?: $r->ip())];
        });

        RateLimiter::for('register', fn(Request $r) => [
            Limit::perMinute(3)->by($r->ip()),
        ]);

        // REGISTRASI ROUTES
        $this->routes(function () {
            Route::middleware('web')->group(base_path('routes/web.php'));
            Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));
        });
    }
}
