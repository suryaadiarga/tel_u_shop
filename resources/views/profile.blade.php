@extends('layouts.app')
@section('page.title', 'Profil')

@section('content')
@php($u = auth()->user())

<section class="space-y-3">
    <div class="app-title">Profil</div>

    <article class="profile-card">
        <div class="profile-header"></div>

        <div class="profile-avatar">
            <img src="{{ $u->avatar_url ?? '/images/avatar-default.png' }}" alt="Avatar">
        </div>

        <div class="profile-info">
            <h2>{{ ucwords($u->name) }}</h2>
            <p class="email">{{ $u->email }}</p>

            <div class="profile-field">
                <span class="icon">🆔</span>
                <div>
                    <label>NIM</label>
                    <p>{{ $u->nim ?? '-' }}</p>
                </div>
            </div>

            <div class="profile-field">
                <span class="icon">📞</span>
                <div>
                    <label>No. HP</label>
                    <p>{{ $u->phone ?? '-' }}</p>
                </div>
            </div>

            <div class="profile-field">
                <span class="icon">💳</span>
                <div>
                    <label>Tel-U Ewallet</label>
                    <p>
                        <a class="lihat" href="{{ route('customer.wallet') }}">Lihat saldo</a>
                    </p>
                </div>
            </div>

            <div class="profile-field">
                <span class="icon">🎓</span>
                <div>
                    <label>Kartu tanda mahasiswa</label>
                    <p>
                        <a class="lihat" href="{{ route('student.card') }}">Lihat</a>
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button class="logout-btn" type="submit" data-ripple>Logout</button>
            </form>
        </div>
    </article>
</section>
@endsection