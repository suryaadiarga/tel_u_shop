@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Detail User</h1>

        <p><strong>ID:</strong> {{ $user->id }}</p>
        <p><strong>Nama:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Role:</strong> {{ $user->role->name ?? '-' }}</p>

        <a href="{{ route('admin.users.index') }}">Kembali ke daftar user</a>
    </div>
@endsection