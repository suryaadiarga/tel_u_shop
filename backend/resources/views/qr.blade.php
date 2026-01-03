@extends('layouts.app')
@section('page.title', 'QRIS')

@section('content')
    <section class="flex flex-col items-center justify-center gap-4" style="min-height:calc(100dvh - 56px - 88px);">
        <button class="btn btn-lg" id="btnScan" type="button" data-ripple style="max-width:220px;">
            Mulai Scan QR
        </button>
        <p class="text-sm text-center" style="color:var(--clr-muted);max-width:260px;">
            Simulasi: tombol ini bisa kamu hubungkan ke modul scanner / kamera di langkah berikutnya.
        </p>
        
    </section>
@endsection