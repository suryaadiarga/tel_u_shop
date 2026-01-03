@extends('layouts.app')
@section('content')
    <div class="max-w-md mx-auto px-5 mt-24 text-center">
        <div class="text-7xl">🙈</div>
        <h1 class="text-2xl font-bold mt-3">Halaman tidak ditemukan</h1>
        <p class="text-neutral-500 mt-1">Cek kembali URL atau kembali ke beranda.</p>
        <a href="{{ route('home') }}" class="inline-block mt-5 px-4 py-2 rounded-xl bg-brand-600 text-white">Ke Beranda</a>
    </div>
@endsection