@extends('layouts.app')

@section('content')
    <div class="auth-page">
        <div class="auth-shell">
            <section class="auth-panel">
                <div class="auth-panel-top">
                    <div class="auth-logo">TS</div>
                    <div>
                        <p class="auth-brand">Tel-U Shop</p>
                        <p class="auth-brand-sub">Admin Console</p>
                    </div>
                </div>
                <div class="auth-panel-body">
                    <h2>Kelola toko kamu dengan dashboard premium.</h2>
                    <p>
                        Pantau pesanan, produk, dan pelanggan dari satu tempat
                        dengan tampilan yang rapi dan modern.
                    </p>
                    <span class="auth-chip">Secure access</span>
                </div>
            </section>

            <section class="auth-card-shell">
                <div class="auth-card auth-card--panel">
                    <div class="auth-head">
                        <span class="pill">Login</span>
                    </div>
                    <h1 class="auth-title">Welcome back</h1>
                    <p class="auth-sub">Masuk untuk melanjutkan ke Tel-U Shop.</p>

                    @include('components.alert')

                    <form method="POST" action="{{ route('login.post') }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <label class="label" for="email">SSO (Email)</label>
                            <input class="input" id="email" type="email" name="email" value="{{ old('email') }}"
                                placeholder="nama@student.telkomuniversity.ac.id" required autofocus>
                            @error('email') <small class="help-link">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-row">
                            <label class="label" for="password">Password</label>
                            <input class="input" id="password" type="password" name="password" placeholder="********"
                                required>
                            @error('password') <small class="help-link">{{ $message }}</small> @enderror
                        </div>

                        <div class="checkbox-row">
                            <input id="remember" type="checkbox" name="remember">
                            <label for="remember">Ingat saya</label>
                            <div style="margin-left:auto">
                                <a class="help-link" href="{{ route('password.request') }}">Lupa password?</a>
                            </div>
                        </div>

                        <button class="btn btn-full" type="submit">
                            Log In
                        </button>

                        <div class="auth-foot">
                            <span>Belum punya akun?</span>
                            <a class="help-link" href="{{ route('register') }}">Daftar</a>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection
