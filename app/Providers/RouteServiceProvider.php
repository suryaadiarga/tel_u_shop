<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Setelah login, user akan diarahkan ke sini.
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->routes(function () {
            // API routes (utama untuk React frontend)
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            // Web routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Guest routes
            if (file_exists(base_path('routes/guest.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/guest.php'));
            }

            // Admin routes
            if (file_exists(base_path('routes/admin.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/admin.php'));
            }

            // Merchant routes
            if (file_exists(base_path('routes/merchant.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/merchant.php'));
            }

            // Customer routes
            if (file_exists(base_path('routes/customer.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/customer.php'));
            }
        });
    }
}
