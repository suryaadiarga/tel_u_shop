@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Detail Order</h1>

        <p><strong>ID:</strong> {{ $order->id }}</p>
        <p><strong>Pemesan:</strong> {{ $order->user->name ?? '-' }}</p>
        <p><strong>Status:</strong> {{ $order->status }}</p>
        <p><strong>Tanggal:</strong> {{ $order->created_at }}</p>

        <a href="{{ route('admin.orders.index') }}">Kembali ke daftar order</a>
    </div>
@endsection