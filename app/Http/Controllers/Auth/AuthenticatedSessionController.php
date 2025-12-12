<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Attempt to authenticate using the `mahasiswa` provider and `nim` credential
        // $credentials = $request->only('nim', 'password');


        // if (Features::enabled(Features::twoFactorAuthentication())) {
            // If you're using two-factor tied to the user model, retrieve user first
            // $user = \App\Models\Mahasiswa::where('nim', $request->nim)->first();
            // if ($user && $user->hasEnabledTwoFactorAuthentication()) {
                // $request->session()->put([
                    // 'login.id' => $user->getKey(),
                    // 'login.remember' => $request->boolean('remember'),
                // ]);

                // return to_route('two-factor.login');
            // }
        // }

        // if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
        //     $user = Auth::guard('web')->user();
        //     $request->session()->regenerate();

        //     return redirect()->intended(route('dashboard', absolute: false));
        // }

        // If authentication failed, throw validation exception consistent with Fortify
        // return back()->withErrors(['nim' => trans('auth.failed')])->onlyInput('nim');

        $loginField = $request->input('login'); // bisa NIM atau Nama
        $password   = $request->input('password');
        
        // Cari mahasiswa berdasarkan NIM atau Nama
        $user = \App\Models\Mahasiswa::where('nim', $loginField)
            ->orWhere('nama', $loginField)
            ->first();
            
        if ($user && \Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            Auth::guard('web')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            
        return redirect()->intended(route('dashboard', absolute: false));
    }
    
    return back()->withErrors([
        'login' => trans('auth.failed')
        ])->onlyInput('login');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
