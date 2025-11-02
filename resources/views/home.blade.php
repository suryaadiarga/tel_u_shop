@extends('layouts.app')

@section('content')
    <div class="bg-hero text-white">
        <div class="max-w-md mx-auto px-5 pt-6 pb-20 curve-mask">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full overflow-hidden ring-2 ring-white/30">
                    <img class="w-full h-full object-cover" src="{{ auth()->user()->avatar_url }}" alt="">
                </div>
                <div class="grow">
                    <div class="text-sm opacity-80">Saldo</div>
                    <div class="text-xl font-bold">Rp {{ number_format(auth()->user()->ewallet_balance, 0, ',', '.') }}</div>
                </div>
                <a href="{{ route('wallet') }}" class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20">E-Wallet</a>
            </div>

            <div class="mt-4 flex gap-2">
                <input
                    class="w-full rounded-xl bg-white/10 placeholder-white/70 px-4 py-3 outline-none focus:ring-2 focus:ring-white/60"
                    placeholder="Search your item">
                <a class="p-3 rounded-xl bg-white/10 hover:bg-white/20" href="#">🔔</a>
            </div>
        </div>
    </div>

    <div class="max-w-md mx-auto px-5 -mt-12 space-y-4">
        <h2 class="text-lg font-semibold">Coffee</h2>
        <div class="grid grid-cols-2 gap-4">
            @if($products->isEmpty())
                <div class="grid grid-cols-2 gap-4">
                    @for($i=0;$i<4;$i++)
                    <div class="rounded-2xl overflow-hidden">
                        <div class="h-36 bg-neutral-200 dark:bg-neutral-800 animate-pulse"></div>
                        <div class="p-3 space-y-2">
                        <div class="h-3 bg-neutral-200 dark:bg-neutral-800 animate-pulse rounded"></div>
                        <div class="h-3 w-1/2 bg-neutral-200 dark:bg-neutral-800 animate-pulse rounded"></div>
                        </div>
                    </div>
                    @endfor
                </div>
                @endif
            @foreach($products as $p)
                <div
                    class="group bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800 rounded-2xl shadow-soft overflow-hidden">
                    <div class="relative">
                        <img src="{{ $p->image_url }}" alt="{{ $p->name }}"
                            class="h-36 w-full object-cover group-hover:scale-[1.02] transition">
                        <button
                            class="absolute right-2 bottom-2 w-10 h-10 rounded-full bg-white text-neutral-900 shadow-soft grid place-items-center hover:scale-105 transition">＋</button>
                    </div>
                    <div class="p-3">
                        <div class="font-semibold">{{ $p->name }}</div>
                        <div class="text-amber-600 text-xs mt-0.5">⭐ {{ $p->rating }} ({{ number_format($p->reviews_count) }})
                        </div>
                        <div class="font-bold mt-1">{{ number_format($p->price, 0, ',', '.') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection