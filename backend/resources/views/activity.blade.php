@extends('layouts.app')
@section('page.title', 'Activity')

@section('content')
    <div class="space-y-4">
        {{-- Menggunakan $orders yang dikirim dari ActivityController --}}
        @forelse ($orders as $order)
            @php
                // Ambil daftar nama produk, maksimum 2 untuk ditampilkan sebagai judul
                $productNames = $order->items->pluck('product.name')->all();
                $title = count($productNames) > 2
                    ? implode(', ', array_slice($productNames, 0, 2)) . '...'
                    : implode(', ', $productNames);

                $itemCount = $order->items->sum('qty');
                // Ambil gambar produk pertama sebagai thumbnail
                $firstProductImage = $order->items->first()->product->image_url ?? '/images/noimg.png';
            @endphp
            <article class="flex gap-4 items-center rounded-2xl border px-4 py-3"
                style="background:var(--clr-card);box-shadow:var(--shadow-card);border-color:var(--clr-line);">
                <img src="{{ $firstProductImage }}" class="w-16 h-16 rounded-xl object-cover flex-none" alt="Foto produk">

                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-base" style="color:var(--clr-text-primary);">
                        {{ $title }}
                    </div>
                    <div class="text-xs mt-0.5" style="color:var(--clr-muted);">
                        {{ $order->created_at->format('d M Y, H:i') }} • {{ $itemCount }} item
                    </div>

                    {{-- Logic Status Pesanan & Aksi --}}
                    <div class="mt-2 flex items-center gap-2 text-xs">
                        @if ($order->status === 'Ready for Pickup')
                            {{-- Status: Siap Diambil --}}
                            <span class="px-2 py-1 rounded-full"
                                style="background:rgba(234,179,8,.1);color:#a16207;font-weight:600;">
                                Siap Diambil
                            </span>

                            {{-- Tampilkan QR Code yang sudah dibuat di Controller --}}
                            @isset($qrcodes[$order->id])
                                <div class="p-2 border rounded-xl" style="border-color:var(--clr-line);">
                                    {!! $qrcodes[$order->id] !!} {{-- Tampilkan QR Code (SVG) --}}
                                </div>
                            @endisset
                        @elseif ($order->status === 'Completed')
                            {{-- Status: Selesai --}}
                            <span class="px-2 py-1 rounded-full"
                                style="background:rgba(16,185,129,.1);color:#047857;font-weight:600;">
                                Selesai
                            </span>
                            {{-- Tombol Ulasan (Simulasi) --}}
                            <button type="button" class="btn"
                                style="background:transparent;color:var(--brand);border:1px solid var(--brand);padding-inline:10px;font-size:13px;"
                                data-ripple>
                                Ulasan
                            </button>
                        @elseif ($order->status === 'Canceled')
                            {{-- Status: Dibatalkan --}}
                            <span class="px-2 py-1 rounded-full"
                                style="background:rgba(185,24,4,.1);color:#991b1b;font-weight:600;">
                                Dibatalkan
                            </span>
                        @else
                            {{-- Status Lainnya --}}
                            <span class="px-2 py-1 rounded-full"
                                style="background:rgba(128,128,128,.1);color:#555;font-weight:600;">
                                {{ $order->status }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="text-right text-sm flex flex-col items-end gap-1">
                    {{-- Tampilkan jumlah item di pesanan --}}
                    <span class="text-xs px-2 py-1 rounded-full"
                        style="background:var(--clr-background);color:var(--clr-text-secondary);">
                        {{ $itemCount }} item
                    </span>
                    {{-- Tampilkan total harga --}}
                    <span style="color:var(--clr-text-primary);font-weight:bold;">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </span>

                    {{-- Tombol Beli Lagi (Aksi Tambahan) --}}
                    <a href="{{ route('home') }}" class="btn btn-light mt-2 inline-flex items-center justify-center"
                        data-ripple>
                        Beli Lagi
                    </a>
                </div>
            </article>
        @empty
            <p class="text-center text-[color:var(--clr-muted)] py-10">
                Belum ada riwayat pesanan.
            </p>
        @endforelse
    </div>
@endsection