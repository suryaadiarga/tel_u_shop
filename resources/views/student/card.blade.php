@extends('layouts.app')

@section('content')
    @include('components.topbar', ['title' => 'Kartu tanda mahasiswa'])

    <div class="max-w-md mx-auto px-5 -mt-10 space-y-5">
        <div class="flex flex-col items-center">
            <div class="w-40 h-40 rounded-full overflow-hidden ring-8 ring-white shadow-soft">
                <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover" alt="">
            </div>
            <div class="text-3xl font-extrabold mt-3">{{ $user->name }}</div>
            <div class="text-sm text-neutral-500">{{ $user->email }}</div>
        </div>

        <div
            class="rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800 shadow-soft p-4">
            <div class="flex items-center gap-3 py-2">
                <div class="w-9 h-9 grid place-items-center rounded-xl bg-black/5 dark:bg-white/10">🪪</div>
                <div class="grow">
                    <div class="text-xs text-neutral-500">NIM</div>
                    <div class="font-semibold">{{ $user->nim }}</div>
                </div>
            </div>
            <div class="border-t border-neutral-100 dark:border-neutral-800 my-2"></div>
            <div class="flex items-center gap-3 py-2">
                <div class="w-9 h-9 grid place-items-center rounded-xl bg-black/5 dark:bg-white/10">🏷️</div>
                <div class="grow">
                    <div class="text-xs text-neutral-500">Kelas</div>
                    <div class="font-semibold">{{ $user->kelas }}</div>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center gap-3">
            <img src="/images/qr-demo.png" class="w-52 h-52 object-contain" alt="QR">
            <a href="{{ route('profile') }}"
                class="w-full text-center rounded-xl border py-3 hover:bg-black/5 dark:border-neutral-800 dark:hover:bg-white/5 transition">Go
                to link</a>
        </div>
    </div>
@endsection