@extends('layouts.app')

@section('title', 'Kartu tanda mahasiswa')

@section('content')
<div class="page">
    <div class="app-title">Kartu tanda mahasiswa</div>

    <div class="page-content">
        @php($u = auth()->user())
        <div class="auth-card" style="max-width:720px">
            <div style="display:flex; gap:20px; align-items:center; margin-bottom:14px">
                <div style="width:84px;height:84px;border-radius:50%;overflow:hidden;background:#ddd;flex:none">
                    {{-- foto user kalau ada --}}
                    @if(!empty($u->avatar_url))
                        <img src="{{ $u->avatar_url }}" alt="avatar" style="width:100%;height:100%;object-fit:cover">
                    @endif
                </div>
                <div>
                    <div style="font-weight:800;font-size:28px;line-height:1">{{ $u->name ?? 'User' }}</div>
                    <div style="color:#cfd3d9">{{ $u->email }}</div>
                </div>
            </div>

            <div
                style="background:#fff;border-radius:14px;padding:16px;color:#111; box-shadow:0 6px 18px rgba(0,0,0,.06)">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div>
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px">NIM</div>
                        <div style="font-weight:700">{{ $u->nim ?? '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size:12px;color:#6b7280;margin-bottom:6px">Kelas</div>
                        <div style="font-weight:700">{{ $u->kelas ?? '-' }}</div>
                    </div>
                </div>

                {{-- QR sederhana/placeholder --}}
                <div style="display:flex;justify-content:center;margin:22px 0">
                    <div style="
            width:180px;height:180px;border-radius:12px;background:
            repeating-linear-gradient(45deg,#0f172a 0 8px,#e2e8f0 8px 16px);
          " aria-label="QR placeholder"></div>
                </div>

                <button class="btn" type="button" onclick="location.href='{{ route('qr') }}'">
                    Go to link
                </button>
            </div>
        </div>
    </div>

    @include('partials.bottom-nav')
</div>
@endsection