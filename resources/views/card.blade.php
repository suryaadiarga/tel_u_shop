@extends('layouts.app')

@section('page.title', 'Kartu tanda mahasiswa')

@section('content')
@php($u = auth()->user())

<section class="max-w-md mx-auto space-y-4">
    <div class="app-title">Kartu Tanda Mahasiswa</div>

    <article class="profile-card" style="margin-top:0;">
        {{-- header merah cuma sedikit biar beda dengan halaman profil --}}
        <div class="profile-header" style="height:80px;"></div>

        <div class="profile-avatar">
            @if (!empty($u->avatar_url))
                <img src="{{ $u->avatar_url }}" alt="Avatar">
            @else
                <img src="/images/avatar-default.png" alt="Avatar">
            @endif
        </div>

        <div class="profile-info">
            <h2>{{ $u->name ?? 'User' }}</h2>
            <p class="email">{{ $u->email }}</p>

            <div class="profile-field">
                <span class="icon">🆔</span>
                <div>
                    <label>NIM</label>
                    <p>{{ $u->nim ?? '-' }}</p>
                </div>
            </div>

            <div class="profile-field">
                <span class="icon">🏫</span>
                <div>
                    <label>Kelas</label>
                    <p>{{ $u->kelas ?? '-' }}</p>
                </div>
            </div>

            {{-- QR placeholder kartu --}}
            <div class="mt-4 flex justify-center">
                <div aria-label="QR Kartu Mahasiswa" style="
                        width:180px;height:180px;border-radius:14px;
                        background:
                            repeating-linear-gradient(45deg,#0f172a 0 8px,#e2e8f0 8px 16px);
                    "></div>
            </div>

            <button class="btn mt-5" type="button" onclick="location.href='{{ route('qr') }}'" data-ripple>
                Tampilkan QR Aktif
            </button>
        </div>
    </article>
</section>
@endsection