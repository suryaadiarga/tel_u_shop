@extends('layouts.app')

@section('content')
    @include('components.topbar', ['title' => 'Activity'])

    <div class="max-w-md mx-auto px-5 -mt-10 space-y-3">
        @foreach($orders as $o)
            <div
                class="rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800 shadow-soft p-3 flex gap-3">
                <img src="{{ $o->items[0]->thumb ?? '/images/food-bg.jpg' }}" class="w-16 h-16 rounded-xl object-cover">
                <div class="grow">
                    <div class="font-semibold">{{ $o->items[0]->product_name ?? 'Pesanan' }}</div>
                    <div class="text-xs text-neutral-500">{{ $o->placed_at?->format('d M Y, H:i') }}</div>
                    <div class="mt-1">
                        <span
                            class="px-2 py-0.5 rounded-full text-xs {{ strtolower($o->status) === 'batal' ? 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' }}">{{ $o->status }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs bg-neutral-100 dark:bg-neutral-800 rounded-full inline-block px-2 py-0.5 mb-1">
                        {{ $o->items[0]->qty ?? 1 }} item
                    </div>
                    <div class="font-semibold">Rp {{ number_format($o->total, 0, ',', '.') }}</div>
                    <div class="flex gap-2 mt-1">
                        <button class="px-3 py-1 rounded-full bg-neutral-100 dark:bg-neutral-800">Ulasan</button>
                        <button class="px-3 py-1 rounded-full border border-neutral-200 dark:border-neutral-700">Beli
                            Lagi</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection