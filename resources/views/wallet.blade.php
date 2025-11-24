@extends('layouts.app')

{{-- Judul di header + tombol back (opsional) --}}
@section('page.title', 'Tel-U Ewallet')
@section('page.back', route('profile'))

@section('content')
    <div class="max-w-md mx-auto px-5 space-y-4 pb-28"> {{-- pb-28: ruang aman untuk FAB + bottom-nav --}}
        {{-- Header kartu user --}}
        <div class="flex items-center gap-3">
            <img class="w-14 h-14 rounded-full ring-2 ring-neutral-200 dark:ring-neutral-700 shadow-md object-cover"
                src="{{ $user->avatar_url ?? '/images/avatar-default.png' }}" alt="Avatar">
            <div>
                <div class="text-xl font-extrabold text-neutral-900 dark:text-neutral-100">
                    {{ $user->name }}
                </div>
                <div class="text-sm text-neutral-500 dark:text-neutral-400">
                    {{ $user->email }}
                </div>
            </div>
        </div>

        {{-- Ringkasan & Topup --}}
        <div class="grid grid-cols-2 gap-3">
            {{-- Saldo --}}
            <div
                class="rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800 shadow-md p-4">
                <div class="text-sm text-neutral-500 dark:text-neutral-400">Tel-U Ewallet</div>
                <div class="mt-1 text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                    Rp {{ number_format($user->ewallet_balance ?? 0, 0, ',', '.') }}
                </div>
            </div>

            {{-- Form Topup --}}
            <div
                class="rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800 shadow-md p-4">
                <form method="POST" action="{{ route('wallet.topup') }}" class="space-y-2">
                    @csrf
                    <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400">Jumlah topup</label>
                    <input type="number" name="amount" min="1000" step="1000" placeholder="Minimal 1.000" required
                        class="w-full rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 px-3 py-2 focus:outline-none focus:border-[var(--brand)] focus:ring-2 focus:ring-[color:var(--ring)]" />

                    <button class="btn w-full" type="submit" data-ripple>Topup</button>
                </form>
            </div>
        </div>

        {{-- Riwayat Transaksi --}}
        <div
            class="rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800 shadow-md overflow-hidden">
            <div class="p-4 font-semibold text-neutral-900 dark:text-neutral-100">Riwayat Transaksi</div>

            @if($txs->count() === 0)
                <div class="px-4 pb-5 text-sm text-neutral-500 dark:text-neutral-400">
                    Belum ada transaksi.
                </div>
            @else
                <ul class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @foreach($txs as $t)
                        <li class="px-4 py-3 flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-[color:var(--brand)]/10 text-[color:var(--brand)] grid place-items-center text-sm">
                                🏪</div>

                            <div class="grow min-w-0">
                                <div class="font-medium text-neutral-900 dark:text-neutral-100 truncate">{{ $t->title }}</div>
                                <div class="text-xs text-neutral-500">{{ $t->created_at->format('d M Y, H:i') }}</div>
                            </div>

                            <div class="ml-3 shrink-0 font-semibold {{ $t->amount < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                {{ $t->amount < 0 ? '' : '+' }}{{ number_format($t->amount, 0, ',', '.') }}
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection