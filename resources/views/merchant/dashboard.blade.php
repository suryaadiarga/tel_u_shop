{{-- resources/views/merchant/dashboard.blade.php --}}

@extends('layouts.app') 

@section('title', 'Dashboard Merchant')

@section('content')
<div class="container">
    <h2 class="mb-4">Dashboard Merchant - Pesanan Aktif</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($orders->isEmpty())
        <div class="alert alert-info">
            Belum ada pesanan yang perlu diproses saat ini.
        </div>
    @else
        <div class="row">
            @foreach($orders as $order)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Pesanan #{{ $order->id }}</h5>
                        <small>Customer: **{{ $order->user->name ?? 'N/A' }}**</small>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            **Waktu Pesan:** {{ $order->placed_at->format('d M H:i') }} | 
                            **Total:** Rp {{ number_format($order->total, 0, ',', '.') }}
                        </p>
                        
                        <h6>Detail Item:</h6>
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($order->items as $item)
                                <li class="list-group-item p-1">{{ $item->qty }}x **{{ $item->product_name }}**</li>
                            @endforeach
                        </ul>

                        <h6>Status Saat Ini: 
                            <span class="badge bg-{{ 
                                $order->status === 'Ready for Pickup' ? 'success' : 
                                ($order->status === 'Preparing' ? 'primary' : 'secondary') 
                            }}">
                                {{ $order->status }}
                            </span>
                        </h6>

                        {{-- Form Perubahan Status --}}
                        <form action="{{ route('merchant.order.update_status', $order) }}" method="POST">
                            @csrf
                            <div class="input-group mt-3">
                                <select name="status" class="form-select form-select-sm">
                                    @foreach($availableStatuses as $status)
                                        <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                    {{-- Opsi Batal juga bisa ditambahkan --}}
                                    <option value="Canceled" class="text-danger">Batalkan Pesanan</option> 
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Ubah Status</button>
                            </div>
                            @error('status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- Link ke Halaman Scan QR --}}
    <div class="text-center mt-5">
        <a href="{{ route('qr.scanner.page') }}" class="btn btn-lg btn-success shadow">
            <i class="fas fa-qrcode"></i> Buka QR Scanner (Pengambilan Barang)
        </a>
    </div>
</div>
@endsection