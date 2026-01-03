<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
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
        $simpleView = static fn(string $label) => response($label, 200);

        Fortify::loginView(fn() => $simpleView('login'));
        Fortify::registerView(fn() => $simpleView('register'));
        Fortify::requestPasswordResetLinkView(fn() => $simpleView('password.request'));
        Fortify::resetPasswordView(fn(Request $request) => $simpleView('password.reset'));
        Fortify::verifyEmailView(fn() => $simpleView('verification.notice'));
        Fortify::confirmPasswordView(fn() => $simpleView('password.confirm'));
        Fortify::twoFactorChallengeView(fn() => $simpleView('two-factor.login'));

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
