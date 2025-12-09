@extends('layouts.app')
@section('page.title', 'Keranjang')

@section('content')
    @if (session('error'))
        <div class="mb-3 px-3 py-2 rounded-lg border"
             style="background:#fef2f2;color:#b91c1c;border-color:#fecaca;">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="mb-3 px-3 py-2 rounded-lg border"
             style="background:#ecfdf5;color:#047857;border-color:#bbf7d0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-3">
        @forelse ($items as $it)
            <article class="rounded-2xl border p-3"
                     style="background:var(--clr-card);box-shadow:var(--shadow-card);border-color:var(--clr-line);">
                <div class="flex items-start gap-3">
                    <img src="{{ $it->product->image_url ?? '/images/noimg.png' }}"
                         class="w-16 h-16 rounded-xl object-cover flex-none" alt="Gambar produk">

                    <div class="flex-1 min-w-0">
                        <div class="font-semibold" style="color:var(--clr-text-primary);">
                            {{ $it->product->name }}
                        </div>
                        <div class="text-sm mt-0.5" style="color:var(--clr-muted);">
                            Rp {{ number_format($it->product->price, 0, ',', '.') }}
                        </div>

                        <div class="mt-2 flex items-center gap-3">
                            {{-- Form update qty --}}
                            <form action="{{ route('cart.update', $it->id) }}" method="POST"
                                  class="flex items-center gap-1">
                                @csrf
                                @method('PATCH')
                                <div class="inline-flex items-center rounded-lg border"
                                     style="border-color:var(--clr-line);">
                                    <button type="submit" name="qty" value="{{ max(1, $it->qty - 1) }}"
                                            class="px-2 py-1 text-base"
                                            style="color:var(--clr-text-secondary);">
                                        −
                                    </button>
                                    <input type="number" name="qty" min="1" value="{{ $it->qty }}"
                                           class="w-12 text-center bg-transparent px-2 py-1 text-sm"
                                           style="border-left:1px solid var(--clr-line);border-right:1px solid var(--clr-line);color:var(--clr-text-primary);">
                                    <button type="submit" name="qty" value="{{ $it->qty + 1 }}"
                                            class="px-2 py-1 text-base"
                                            style="color:var(--clr-text-secondary);">
                                        +
                                    </button>
                                </div>
                            </form>

                            {{-- Form hapus --}}
                            <form action="{{ route('cart.remove', $it->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus item?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs font-semibold"
                                        style="color:#b91c1c;">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="p-6 rounded-2xl border border-dashed text-center"
                 style="background:var(--clr-card);border-color:var(--clr-line);color:var(--clr-muted);">
                Keranjang masih kosong.
            </div>
        @endforelse
    </div>

    @if ($items->isNotEmpty())
        <section class="mt-4 rounded-2xl border p-4"
                 style="background:var(--clr-card);box-shadow:var(--shadow-card);border-color:var(--clr-line);">
            <div class="flex items-center justify-between text-sm"
                 style="color:var(--clr-text-secondary);">
                <span>Subtotal</span>
                <span>Rp {{ number_format($summary['subtotal'], 0, ',', '.') }}</span>
            </div>

            <hr class="my-3" style="border-color:var(--clr-line);">

            <form action="{{ route('checkout.store') }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <label class="flex items-center gap-2 rounded-xl px-3 py-2 cursor-pointer border"
                           style="border-color:var(--clr-line);color:var(--clr-text-secondary);">
                        <input type="radio" name="payment_method" value="cash" checked>
                        <span>Cash</span>
                    </label>

                    <label class="flex items-center gap-2 rounded-xl px-3 py-2 cursor-pointer border"
                           style="border-color:var(--clr-line);color:var(--clr-text-secondary);">
                        <input type="radio" name="payment_method" value="ewallet">
                        <span>Tel-U e-Wallet (Saldo:
                            Rp {{ number_format(auth()->user()->ewallet_balance, 0, ',', '.') }})</span>
                    </label>
                </div>

                @if (auth()->user()->ewallet_balance < $summary['subtotal'])
                    <p class="text-xs mt-1" style="color:#b91c1c;">
                        Saldo e-wallet kurang untuk total ini.
                    </p>
                @endif

                <div class="flex items-center gap-2 mt-2">
                    <a href="{{ route('cart.clear') }}"
                       onclick="event.preventDefault(); if(confirm('Kosongkan cart?')) document.getElementById('clearCart').submit();"
                       class="px-4 py-2 rounded-xl border text-sm font-medium"
                       style="border-color:var(--clr-line);color:var(--clr-text-secondary);">
                        Kosongkan
                    </a>

                    <button class="btn flex-1" type="submit" data-ripple>
                        Bayar Rp {{ number_format($summary['subtotal'], 0, ',', '.') }}
                    </button>
                </div>
            </form>

            <form id="clearCart" action="{{ route('cart.clear') }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </section>
    @endif
@endsection
