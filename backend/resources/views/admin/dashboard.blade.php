@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Selamat datang, {{ auth()->user()->name }}!</p>

        <div class="links">
            <a href="{{ route('admin.users.index') }}">Manajemen User</a> |
            <a href="{{ route('admin.orders.index') }}">Monitoring Order</a>
        </div>
    </div>
@endsection