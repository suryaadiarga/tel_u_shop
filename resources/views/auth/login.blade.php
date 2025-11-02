@extends('layouts.app')

@section('content')
    <div class="relative">
        <div class="absolute inset-0 h-72 bg-[url('/images/bg-market.jpg')] bg-cover bg-center"></div>
        <div class="relative z-10 pt-10 pb-6 bg-gradient-to-b from-black/20 to-white/0"></div>
    </div>

    <div class="max-w-md mx-auto px-5 -mt-24">
        <div
            class="glass bg-white/80 dark:bg-neutral-900/70 border border-white/40 dark:border-white/10 rounded-2xl shadow-glass p-6">
            <h1 class="text-2xl font-bold mb-2">Login</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-4">Masuk dengan akun kampusmu.</p>

            @if($errors->any())
                <div class="text-sm rounded-xl p-3 bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300 mb-3">
                    Cek kembali email / password.
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-medium">SSO (username)</label>
                    <input type="email" name="email"
                        class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 px-4 py-3 outline-none focus:ring-2 focus:ring-brand-500"
                        required>
                </div>
                <div>
                    <label class="text-sm font-medium">Password</label>
                    <input type="password" name="password"
                        class="mt-1 w-full rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 px-4 py-3 outline-none focus:ring-2 focus:ring-brand-500"
                        required>
                </div>
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="remember"
                            class="rounded border-neutral-300 text-brand-600 focus:ring-brand-500">
                        Ingat saya
                    </label>
                    <a class="text-sm text-brand-600 hover:underline" href="#">Lupa password?</a>
                </div>
                <button
                    class="w-full rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 shadow-soft transition">Log
                    In</button>
            </form>

            <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-4">
                Belum punya akun? <a class="text-brand-600 hover:underline" href="{{ route('register') }}">Daftar</a>
            </p>
        </div>
    </div>
@endsection