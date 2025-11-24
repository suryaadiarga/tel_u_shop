@extends('layouts.app')

@section('content')
    <div class="page">
        <div class="app-title">Tel-U Shop</div>

        <div class="page-content">
            <div class="auth-wrap">
                <div class="auth-card">
                    <div class="auth-head">
                        <span class="pill">Login</span>
                    </div>
                    <h1 class="auth-title">Masuk</h1>
                    <p class="auth-sub">Masuk dengan akun kampusmu.</p>

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
                            <input class="input" id="password" type="password" name="password" placeholder="••••••••"
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

                        <button class="btn" type="submit">
                            Log In
                        </button>

                        <div class="auth-foot">
                            <span>Belum punya akun?</span>
                            <a class="help-link" href="{{ route('register') }}">Daftar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @include('partials.fab-scan')
        @include('partials.bottom-nav')
    </div>
@endsection