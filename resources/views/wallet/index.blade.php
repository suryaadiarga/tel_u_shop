@extends('layouts.app')

@section('content')
    @include('components.topbar', ['title' => 'Tel-U Ewallet'])

    <div class="max-w-md mx-auto px-5 -mt-10 space-y-4">
        <div class="flex items-center gap-3">
            <img class="w-14 h-14 rounded-full ring-2 ring-white/60 shadow-soft" src="{{ $user->avatar_url }}" alt="">
            <div>
                <div class="text-xl font-extrabold">{{ $user->name }}</div>
                <div class="text-sm text-neutral-500 dark:text-neutral-400">{{ $user->email }}</div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div
                class="rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800 shadow-soft p-4">
                <div class="text-sm text-neutral-500">Tel-U Ewallet</div>
                <div class="text-xl font-bold">Rp {{ number_format($user->ewallet_balance, 0, ',', '.') }}</div>
            </div>
            <button
                class="rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800 shadow-soft p-4 text-left hover:ring-2 hover:ring-brand-500 transition">
                <div class="text-sm text-neutral-500">➕</div>
                <div class="text-lg font-semibold text-brand-600">Isi saldo</div>
            </button>
        </div>

        <div class="rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800 shadow-soft">
            <div class="p-4 font-semibold">Riwayat Transaksi</div>
            <ul>
                @foreach($txs as $t)
                    <li class="px-4 py-3 flex items-center gap-3 border-t border-neutral-100 dark:border-neutral-800">
                        <div class="w-10 h-10 rounded-full bg-brand-600/10 text-brand-700 grid place-items-center">🏪</div>
                        <div class="grow">
                            <div class="font-medium">{{ $t->title }}</div>
                            <div class="text-xs text-neutral-500">{{ $t->created_at->format('d M Y, H:i') }}</div>
                        </div>
                        <div class="font-semibold {{ $t->amount < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                            {{ $t->amount < 0 ? '' : '+' }}{{ number_format($t->amount, 0, ',', '.') }}
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection