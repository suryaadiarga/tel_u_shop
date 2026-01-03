@extends('layouts.app')
@section('page.title', 'Dashboard')

@section('content')
    <section class="max-w-md mx-auto space-y-4">
        <div class="app-title">Dashboard</div>

        <article class="rounded-2xl border p-4"
            style="background:var(--clr-card);box-shadow:var(--shadow-card);border-color:var(--clr-line);">
            <p class="mb-3" style="color:var(--clr-text-secondary);">
                Kamu berhasil login ke Tel-U Shop.
            </p>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn" type="submit" data-ripple>Logout</button>
            </form>
        </article>
    </section>
@endsection