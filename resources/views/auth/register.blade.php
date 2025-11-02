@extends('layouts.app')
@section('content')
    @include('components.topbar', ['title' => 'Daftar'])

    <div class="max-w-md mx-auto px-5 -mt-10">
        <div
            class="rounded-2xl border border-neutral-100 dark:border-neutral-800 bg-white dark:bg-neutral-900 shadow-soft p-6">
            <form method="POST" action="{{ route('register.store') }}" class="grid grid-cols-1 gap-4">
                @csrf
                <div>
                    <label class="text-sm font-medium">Nama</label>
                    <input name="name" required class="mt-1 input" />
                </div>
                <div>
                    <label class="text-sm font-medium">Email</label>
                    <input type="email" name="email" required class="mt-1 input" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium">Password</label>
                        <input type="password" name="password" value="password" required class="mt-1 input" />
                    </div>
                    <div>
                        <label class="text-sm font-medium">Konfirmasi</label>
                        <input type="password" name="password_confirmation" value="password" required class="mt-1 input" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium">NIM</label>
                        <input name="nim" class="mt-1 input" />
                    </div>
                    <div>
                        <label class="text-sm font-medium">Kelas</label>
                        <input name="kelas" class="mt-1 input" />
                    </div>
                </div>

                <button
                    class="mt-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 shadow-soft transition">Buat
                    Akun</button>
            </form>
        </div>
    </div>

    @push('styles')
        <style>
            .input {
                width: 100%;
                border-radius: 12px;
                border: 1px solid #e5e7eb;
                background: #fff;
                padding: .75rem 1rem;
                outline: none
            }

            .dark .input {
                border-color: #262626;
                background: #0a0a0a
            }
        </style>
    @endpush
@endsection