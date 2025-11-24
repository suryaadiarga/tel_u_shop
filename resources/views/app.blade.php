<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tel-U Shop')</title>
    <link rel="stylesheet" href="/app.css">
</head>

<body>
    <div class="page">
        {{-- Title Bar sederhana (opsional), atau pakai @include('partials.topbar') di halaman --}}
        @yield('topbar')

        <div class="page-content">
            @yield('content')
        </div>

        @include('partials.bottom-nav')
        @include('partials.fab-scan')
    </div>

    <script src="/app.js"></script>
</body>

</html>