<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bisa dipakai untuk binding interface ke implementation
        // Contoh:
        // $this->app->bind(\App\Contracts\PaymentGateway::class, \App\Services\MidtransPaymentGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix default string length untuk MySQL lama (utf8mb4)
        Schema::defaultStringLength(191);

        // Force HTTPS di production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Bisa juga daftarkan macro/helper global di sini
    }
}