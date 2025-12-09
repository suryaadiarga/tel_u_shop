<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class IdeHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Daftarkan hanya di local/dev environment
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Tidak perlu bootstrap tambahan untuk IDE Helper
    }
}