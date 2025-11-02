@extends('layouts.app')

@section('content')
    <div class="relative">
        <div class="h-48 bg-[url('/images/food-bg.jpg')] bg-cover bg-center"></div>
        <div class="absolute inset-x-0 top-36">
            <div class="max-w-md mx-auto px-5">
                <div
                    class="rounded-2xl bg-white dark:bg-neutral-900 shadow-soft border border-neutral-100 dark:border-neutral-800 p-5">
                    <div class="flex items-center gap-4">
                        <img class="w-20 h-20 rounded-full ring-4 ring-white shadow-soft" src="{{ $user->avatar_url }}"
                            alt="">
                        <div class="grow">
                            <div class="text-2xl font-extrabold">{{ $user->name }}</div>
                            <div class="text-sm text-neutral-500">{{ $user->email }}</div>
                        </div>
                    </div>

                    <div class="mt-5 divide-y divide-neutral-100 dark:divide-neutral-800">
                        <div class="py-3 flex items-center gap-3"><span>🆔</span> <b class="w-28">NIM</b>
                            <div class="grow"></div> {{ $user->nim }}
                        </div>
                        <div class="py-3 flex items-center gap-3"><span>📞</span> <b class="w-28">No. Hp</b>
                            <div class="grow"></div> {{ $user->phone ?? '-' }}
                        </div>
                        <a href="{{ route('student.card') }}"
                            class="py-3 flex items-center gap-3 hover:bg-black/5 dark:hover:bg-white/5 rounded-xl px-2">
                            <span>💳</span> <b class="w-28">KTM</b>
                            <div class="grow"></div> <span class="text-brand-600">Lihat</span>
                        </a>
                        <div class="py-3 flex items-center gap-3"><span>⚙️</span> <b class="w-28">Pengaturan</b></div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="mt-5">
                        @csrf
                        <button
                            class="w-full py-3 rounded-xl border border-neutral-200 dark:border-neutral-800 hover:bg-black/5 dark:hover:bg-white/5 transition">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection