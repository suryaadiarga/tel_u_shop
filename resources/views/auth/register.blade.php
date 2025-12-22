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
                    <h2>Buat akun dan mulai kelola bisnismu.</h2>
                    <p>
                        Dapatkan akses ke dashboard modern untuk pesanan,
                        produk, dan pelanggan kamu.
                    </p>
                    <span class="auth-chip">Create account</span>
                </div>
            </section>

            <section class="auth-card-shell">
                <div class="auth-card auth-card--panel">
                    <div class="auth-head">
                        <span class="pill">Daftar</span>
                    </div>
                    <h1 class="auth-title">Create your account</h1>
                    <p class="auth-sub">Isi data singkat untuk mulai memakai Tel-U Shop.</p>

                    @include('components.alert')

                    <form method="POST" action="{{ route('register.store') }}" novalidate>
                        @csrf
                        <div class="form-row">
                            <label class="label" for="name">Nama</label>
                            <input class="input" id="name" type="text" name="name" value="{{ old('name') }}" required>
                            @error('name') <small class="help-link">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-row">
                            <label class="label" for="email">Email</label>
                            <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" required>
                            @error('email') <small class="help-link">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-row">
                            <label class="label" for="password">Password</label>
                            <input class="input" id="password" type="password" name="password" required>
                            @error('password') <small class="help-link">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-row">
                            <label class="label" for="password_confirmation">Konfirmasi</label>
                            <input class="input" id="password_confirmation" type="password" name="password_confirmation" required>
                        </div>
                        <div class="form-row">
                            <label class="label" for="nim">NIM (opsional)</label>
                            <input class="input" id="nim" type="text" name="nim" value="{{ old('nim') }}">
                        </div>
                        <div class="form-row">
                            <label class="label" for="kelas">Kelas (opsional)</label>
                            <input class="input" id="kelas" type="text" name="kelas" value="{{ old('kelas') }}">
                        </div>

                        <button class="btn btn-full" type="submit">Buat Akun</button>

                        <div class="auth-foot">
                            <span>Sudah punya akun?</span>
                            <a class="help-link" href="{{ route('login') }}">Masuk</a>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection
