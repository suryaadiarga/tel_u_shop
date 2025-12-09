@extends('layouts.app')

@section('page.title', 'Tel-U Ewallet')
@section('page.back', route('profile'))

@section('content')
@php($u = $user)

<section class="max-w-md mx-auto space-y-4 pb-28">
    {{-- Header kartu user --}}
    <div class="flex items-center gap-3">
        <img class="w-14 h-14 rounded-full object-cover flex-none"
            style="border:2px solid rgba(255,255,255,.6);box-shadow:var(--shadow-card);"
            src="{{ $u->avatar_url ?? '/images/avatar-default.png' }}" alt="Avatar">
        <div>
            <div class="text-xl font-extrabold" style="color:var(--clr-text-primary);">
                {{ $u->name }}
            </div>
            <div class="text-sm" style="color:var(--clr-muted);">
                {{ $u->email }}
            </div>
        </div>
    </div>

    {{-- Ringkasan & Topup --}}
    <div class="grid grid-cols-2 gap-3">
        {{-- Saldo --}}
        <article class="rounded-2xl border p-4"
            style="background:var(--clr-card);box-shadow:var(--shadow-card);border-color:var(--clr-line);">
            <div class="text-xs mb-1" style="color:var(--clr-muted);">Tel-U Ewallet</div>
            <div class="text-2xl font-bold" style="color:var(--clr-text-primary);">
                Rp {{ number_format($u->ewallet_balance ?? 0, 0, ',', '.') }}
            </div>
        </article>

        {{-- Form Topup --}}
        <article class="rounded-2xl border p-4"
            style="background:var(--clr-card);box-shadow:var(--shadow-card);border-color:var(--clr-line);">
            <form method="POST" action="{{ route('customer.wallet.topup') }}" class="space-y-2">
                @csrf
                <label class="block text-xs font-semibold" style="color:var(--clr-muted);">
                    Jumlah topup
                </label>
                <input type="number" name="amount" min="1000" step="1000" placeholder="Minimal 1.000" required
                    class="w-full rounded-xl px-3 py-2 text-sm"
                    style="border:1px solid var(--clr-line);background:var(--clr-background);color:var(--clr-text-primary);outline:none;">

                <button class="btn w-full" type="submit" data-ripple>Topup</button>
            </form>
        </article>
    </div>

    {{-- Riwayat Transaksi --}}
    <article class="rounded-2xl border overflow-hidden"
        style="background:var(--clr-card);box-shadow:var(--shadow-card);border-color:var(--clr-line);">
        <div class="p-4 font-semibold" style="color:var(--clr-text-primary);">
            Riwayat Transaksi
        </div>

        @if ($txs->count() === 0)
            <div class="px-4 pb-5 text-sm" style="color:var(--clr-muted);">
                Belum ada transaksi.
            </div>
        @else
            <ul>
                @foreach ($txs as $t)
                    <li class="px-4 py-3 flex items-center gap-3" style="border-top:1px solid var(--clr-line);">
                        <div class="w-10 h-10 rounded-full grid place-items-center text-sm flex-none"
                            style="background:rgba(183,28,28,.08);color:var(--brand);">
                            🏪
                        </div>

                        <div class="grow min-w-0">
                            <div class="font-medium truncate" style="color:var(--clr-text-primary);">
                                {{ $t->title }}
                            </div>
                            <div class="text-xs" style="color:var(--clr-muted);">
                                {{ $t->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>

                        <div class="ml-3 shrink-0 font-semibold
                                                                {{ $t->amount < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                            {{ $t->amount < 0 ? '' : '+' }}{{ number_format($t->amount, 0, ',', '.') }}
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </article>
</section>
@endsection