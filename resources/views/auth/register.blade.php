@extends('layouts.app')

@section('content')
    <div class="page">
        <div class="app-title">Tel-U Shop</div>

        <div class="page-content">
            <div class="auth-wrap">
                <div class="auth-card">
                    <div class="auth-head">
                        <span class="pill">Daftar</span>
                    </div>
                    <h1 class="auth-title">Buat Akun</h1>
                    <p class="auth-sub">Isi data singkat di bawah ini.</p>

                    @include('components.alert')

                    <form method="POST" action="{{ route('register.store') }}" novalidate>
                        @csrf
                        <div class="inline">
                            <div class="form-row" style="flex:1">
                                <label class="label" for="name">Nama</label>
                                <input class="input" id="name" type="text" name="name" value="{{ old('name') }}" required>
                                @error('name') <small class="help-link">{{ $message }}</small> @enderror
                            </div>
                            <div class="form-row" style="flex:1">
                                <label class="label" for="email">Email</label>
                                <input class="input" id="email" type="email" name="email" value="{{ old('email') }}"
                                    required>
                                @error('email') <small class="help-link">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="inline">
                            <div class="form-row" style="flex:1">
                                <label class="label" for="password">Password</label>
                                <input class="input" id="password" type="password" name="password" required>
                                @error('password') <small class="help-link">{{ $message }}</small> @enderror
                            </div>
                            <div class="form-row" style="flex:1">
                                <label class="label" for="password_confirmation">Konfirmasi</label>
                                <input class="input" id="password_confirmation" type="password" name="password_confirmation"
                                    required>
                            </div>
                        </div>

                        <div class="inline">
                            <div class="form-row" style="flex:1">
                                <label class="label" for="nim">NIM (opsional)</label>
                                <input class="input" id="nim" type="text" name="nim" value="{{ old('nim') }}">
                            </div>
                            <div class="form-row" style="flex:1">
                                <label class="label" for="kelas">Kelas (opsional)</label>
                                <input class="input" id="kelas" type="text" name="kelas" value="{{ old('kelas') }}">
                            </div>
                        </div>

                        <button class="btn" type="submit">Buat Akun</button>

                        <div class="auth-foot">
                            <span>Sudah punya akun?</span>
                            <a class="help-link" href="{{ route('login') }}">Masuk</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @include('partials.fab-scan')
        @include('partials.bottom-nav')
    </div>
@endsection