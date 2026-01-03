@extends('layouts.app')

@section('title', 'Riwayat Pesanan Merchant')

@section('content')
    <div class="container">
        <h2 class="mb-4">Riwayat Pesanan (Selesai & Dibatalkan)</h2>
        <p class="text-muted">Ini adalah daftar pesanan yang sudah diselesaikan (diambil Customer) atau dibatalkan.</p>

        @if ($orders->isEmpty())
            <div class="alert alert-info">
                Belum ada riwayat pesanan yang selesai atau dibatalkan.
            </div>
        @else
            <div class="row">
                @foreach($orders as $order)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Pesanan #{{ $order->id }}</h5>
                                <small>Customer: **{{ $order->user->name ?? 'N/A' }}**</small>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    **Waktu Selesai:** {{ $order->updated_at->format('d M H:i') }}
                                </p>

                                <h6>Detail Item:</h6>
                                <ul class="list-group list-group-flush mb-3">
                                    @foreach($order->items as $item)
                                        <li class="list-group-item p-1">{{ $item->qty }}x **{{ $item->product_name }}**</li>
                                    @endforeach
                                </ul>

                                <h6>Status Akhir:
                                    <span class="badge bg-{{ 
                                            $order->status === 'Completed' ? 'success' : 'danger'
                                        }}">
                                        {{ $order->status }}
                                    </span>
                                </h6>
                                <p class="fw-bold mt-2">Total Transaksi: Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('merchant.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Pesanan Aktif
            </a>
        </div>
    </div>
@endsection