<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    /**
     * Show login info (optional endpoint used by web clients).
     */
    public function show()
    {
        return response()->json(['message' => 'Silakan login dengan email dan password.']);
    }

    /**
     * Authenticate (kept for compatibility; AuthController handles API login).
     */
    public function authenticate(Request $request)
    {
        $key = 'login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json(['error' => 'Terlalu banyak percobaan login.'], 429);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return response()->json(['message' => 'Login berhasil', 'user' => Auth::user()]);
        }

        RateLimiter::hit($key);
        return response()->json(['error' => 'Email atau password salah.'], 401);
    }

    /**
     * Logout (web compatibility).
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Logout berhasil']);
    }
}
