@extends('layouts.app')
@section('title', 'Profil')

@section('content')
<div class="app-title">Tel-U Shop</div>

@php($u = auth()->user())
<div class="profile-card">
    <div class="profile-header"></div>
    <div class="profile-avatar">
        <img src="{{ $u->avatar_url ?? '/images/avatar-default.png' }}" alt="Avatar">
    </div>

    <div class="profile-info">
        <h2>{{ ucwords($u->name) }}</h2>
        <p class="email">{{ $u->email }}</p>

        <div class="profile-field">
            <span class="icon">🆔</span>
            <div><label>NIM</label>
                <p>{{ $u->nim ?? '-' }}</p>
            </div>
        </div>

        <div class="profile-field">
            <span class="icon">📞</span>
            <div><label>No. Hp</label>
                <p>{{ $u->phone ?? '-' }}</p>
            </div>
        </div>

        <div class="profile-field">
            <span class="icon">💳</span>
            <div><label>Tel-U Ewallet</label>
                <p><a class="lihat" href="{{ route('wallet') }}">Lihat saldo</a></p>
            </div>
        </div>

        <div class="profile-field">
            <span class="icon">🎓</span>
            <div><label>Kartu tanda mahasiswa</label>
                <p><a class="lihat" href="{{ route('student.card') }}">Lihat</a></p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout-btn" type="submit">Logout</button>
        </form>
    </div>
</div>
@endsection