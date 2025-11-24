@extends('layouts.app')

@section('title', 'Lupa password')

@section('content')
    <div class="page">
        <div class="app-title">Tel-U Shop</div>
        <div class="page-content">
            <div class="auth-card">
                <h1>Lupa Password</h1>
                <p class="muted">Masukkan email kampusmu. Kami kirim link reset password.</p>

                @if (session('status'))
                    <div class="alert" data-autohide="5000">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-row">
                        <label class="label">Email</label>
                        <input type="email" name="email" class="input" required autofocus value="{{ old('email') }}">
                        @error('email') <small style="color:#ffb4b4">{{ $message }}</small> @enderror
                    </div>

                    <button type="submit" class="btn">Kirim Link Reset</button>
                </form>

                <p style="margin-top:12px">
                    <a class="help-link" href="{{ route('login') }}">Kembali ke Login</a>
                </p>
            </div>
        </div>
        @include('partials.fab-scan')
        @include('partials.bottom-nav')
    </div>
@endsection