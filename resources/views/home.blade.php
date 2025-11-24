@extends('layouts.app')
@section('page.title', 'Home')

@section('content')
    {{-- Search & Wallet --}}
    <div class="space-y-4">
        <div class="ui-card">
            <input type="text" placeholder="Search your item" class="w-full rounded-xl border px-4 py-3">
            <div class="mt-3 ui-card bg-brand-50 border border-brand-100">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">💳</span>
                    <div>
                        <div class="text-sm text-gray-600">Tel-U Ewallet</div>
                        <div class="text-lg font-bold">Rp 45.000</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product grid preview --}}
        <div class="grid grid-cols-2 gap-4">
            @foreach([['Kopi susu', 18000, 'https://images.unsplash.com/photo-1541167760496-1628856ab772'], ['Risol', 10000, 'https://akcdn.detik.net.id/community/media/visual/2022/07/21/resep-risoles-sayuran_43.jpeg?w=700&q=90']] as [$title, $price, $img])
                <article class="product-card">
                    <img src="{{ $img }}" alt="">
                    <div class="body">
                        <h3 class="product-title">{{ $title }}</h3>
                        <div class="product-price">Rp {{ number_format($price, 0, ',', '.') }}</div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
@endsection