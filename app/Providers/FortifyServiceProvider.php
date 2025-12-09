<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bisa dipakai untuk binding custom service terkait auth
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Custom view untuk 2FA challenge
        Fortify::twoFactorChallengeView(fn() => Inertia::render('auth/two-factor-challenge'));

        // Custom view untuk confirm password
        Fortify::confirmPasswordView(fn() => Inertia::render('auth/confirm-password'));

        // Rate limiter untuk 2FA
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // Rate limiter untuk login umum
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->input('email') . $request->ip());
        });
    }
}