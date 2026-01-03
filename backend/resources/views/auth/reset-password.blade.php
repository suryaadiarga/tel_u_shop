@extends('layouts.app')

@section('title', 'Reset password')

@section('content')
    <div class="page">
        <div class="app-title">Tel-U Shop</div>
        <div class="page-content">
            <div class="auth-card">
                <h1>Reset Password</h1>
                <p class="muted">Buat password barumu.</p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-row">
                        <label class="label">Email</label>
                        <input type="email" name="email" class="input" value="{{ old('email', $email) }}" required>
                        @error('email') <small style="color:#ffb4b4">{{ $message }}</small> @enderror
                    </div>

                    <div class="inline">
                        <div class="form-row" style="flex:1">
                            <label class="label">Password baru</label>
                            <input type="password" name="password" class="input" required>
                            @error('password') <small style="color:#ffb4b4">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-row" style="flex:1">
                            <label class="label">Konfirmasi</label>
                            <input type="password" name="password_confirmation" class="input" required>
                        </div>
                    </div>

                    <button type="submit" class="btn">Simpan Password</button>
                </form>
            </div>
        </div>
        @include('partials.fab-scan')
        @include('partials.bottom-nav')
    </div>
@endsection