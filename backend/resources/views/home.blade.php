@extends('layouts.app')
@section('page.title', 'Home')

@section('content')
    <div class="space-y-4 pb-24">
        {{-- Search + kartu saldo ewallet --}}
        <div class="rounded-2xl bg-[color:var(--clr-card)] border border-[color:var(--clr-line)] shadow-md p-4">
            <input type="text" placeholder="Cari makanan & minuman…"
                class="w-full rounded-xl border border-[color:var(--clr-line)] bg-transparent px-4 py-2 text-sm
                                           focus:outline-none focus:border-[var(--brand)] focus:ring-2 focus:ring-[color:var(--ring)]">

            <div
                class="mt-3 rounded-2xl bg-[color:var(--brand)]/5 border border-[color:var(--brand)]/30 px-4 py-3 flex items-center gap-3">
                <span class="text-2xl">💳</span>
                <div>
                    <div class="text-xs text-[color:var(--clr-muted)]">Tel-U Ewallet</div>
                    <div class="text-lg font-bold text-[color:var(--clr-text-primary)]">
                        Rp {{ number_format(auth()->user()->ewallet_balance ?? 0, 0, ',', '.') }}
                    </div>
                    {{-- ✅ Fix: gunakan route customer.wallet --}}
                    <a href="{{ route('customer.wallet') }}" class="text-xs text-[color:var(--brand)] font-semibold">
                        Lihat detail &rsaquo;
                    </a>
                </div>
            </div>
        </div>

        {{-- Grid produk --}}
        <div class="grid grid-cols-2 gap-4">
            @forelse($products as $product)
                <article
                    class="rounded-2xl bg-[color:var(--clr-card)] border border-[color:var(--clr-line)] shadow-md overflow-hidden flex flex-col">
                    <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=600' }}"
                        alt="{{ $product->name }}" class="w-full h-28 object-cover">
                    <div class="p-3 flex-1 flex flex-col gap-1">
                        <h3 class="text-sm font-semibold text-[color:var(--clr-text-primary)] line-clamp-2">
                            {{ $product->name }}
                        </h3>
                        <div class="text-sm font-bold text-[color:var(--brand)]">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </div>

                        {{-- Form tambah ke keranjang --}}
                        {{-- ✅ Fix: gunakan route customer.cart.add --}}
                        <form action="{{ route('customer.cart.add', $product) }}" method="POST" class="mt-2">
                            @csrf
                            <input type="hidden" name="qty" value="1">
                            <button type="submit" class="btn w-full !rounded-xl !py-2 text-sm" data-ripple>
                                + Tambah ke keranjang
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <p class="col-span-2 text-sm text-center text-[color:var(--clr-muted)]">
                    Belum ada produk yang tersedia.
                </p>
            @endforelse
        </div>
    </div>
@endsection