@extends('layouts.app')
@section('page.title', 'Welcome')

@section('content')
  <section class="space-y-4">
    <article class="rounded-2xl border p-6"
      style="background:var(--clr-card);box-shadow:var(--shadow-card);border-color:var(--clr-line);">
      <h1 class="text-2xl font-bold" style="color:var(--clr-text-primary);">
        Halo, Tel-U Shop 🎉
      </h1>
      <p class="mt-2 text-sm" style="color:var(--clr-text-secondary);">
        Ini halaman demo: Blade + Tailwind CDN + <code>app.css</code> + <code>app.js</code> custom,
        tanpa Vite. Tapi tampilan tetap premium & responsif.
      </p>

      <div class="mt-5 flex flex-wrap items-center gap-3">
        <a href="https://tailwindcss.com/docs" target="_blank" class="btn text-sm" data-ripple>
          Tailwind Docs
        </a>
        <a href="https://laravel.com/docs" target="_blank" class="btn text-sm" style="background:#111;" data-ripple>
          Laravel Docs
        </a>
      </div>
    </article>

    <article class="rounded-2xl border p-6"
      style="background:var(--clr-card);box-shadow:var(--shadow-card);border-color:var(--clr-line);">
      <h2 class="text-xl font-semibold" style="color:var(--clr-text-primary);">
        Catatan Setup
      </h2>
      <ul class="mt-3 text-sm space-y-1" style="color:var(--clr-text-secondary);">
        <li>• Tailwind aktif dari CDN (instant, tanpa build).</li>
        <li>• Semua custom design di <code>public/app.css</code>.</li>
        <li>• Interaksi UI (theme toggle, ripple, FAB, dsb.) di <code>public/app.js</code>.</li>
      </ul>
    </article>
  </section>
@endsection