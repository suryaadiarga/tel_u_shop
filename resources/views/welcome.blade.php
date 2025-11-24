@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
    <div class="space-y-6">
      <div class="p-6 rounded-xl bg-white shadow">
        <h1 class="text-2xl font-bold">Halo, Blade + Tailwind CDN + app.css & app.js sederhana 🎉</h1>
        <p class="mt-2 text-gray-600">Semua tanpa Vite. Gak pake ribet.</p>

        <div class="mt-6 flex items-center gap-3">
          <a href="https://tailwindcss.com/docs" target="_blank" class="btn">Tailwind Docs</a>
          <a href="https://laravel.com/docs" target="_blank" class="btn btn-dark">Laravel Docs</a>
        </div>
      </div>

      <div class="p-6 rounded-xl bg-white shadow">
        <h2 class="text-xl font-semibold">Catatan</h2>
        <ul class="mt-3 list-disc pl-6 text-gray-700">
          <li>Tailwind aktif dari CDN (tanpa build).</li>
          <li>Semua custom CSS di <code>public/app.css</code>.</li>
          <li>Semua JS di <code>public/app.js</code>.</li>
        </ul>
      </div>
    </div>
@endsection
