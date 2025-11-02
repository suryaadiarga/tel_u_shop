@extends('layouts.app')

@section('content')
    <h1 style="margin:0 0 12px 0;">Dashboard</h1>
    <p class="muted">Kamu berhasil login.</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn">Logout</button>
    </form>
@endsection