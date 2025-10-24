<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" {{-- pakai helper @class untuk toggle dark mode --}}
    @class(['dark' => ($appearance ?? 'system') === 'dark'])>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Deteksi dark-mode sistem sedini mungkin (tanpa FOUC) --}}
    <script>
        (function () {
            const appearance = @json($appearance ?? 'system');
            if (appearance === 'system') {
                try {
                    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                        document.documentElement.classList.add('dark');
                    }
                } catch (_) { }
            }
        })();
    </script>

    {{-- Fallback warna latar (supaya tidak flashing) --}}
    <style>
        html {
            background-color: oklch(1 0 0);
        }

        html.dark {
            background-color: oklch(0.145 0 0);
        }

        @supports not (background-color: oklch(1 0 0)) {
            html {
                background-color: #ffffff;
            }

            html.dark {
                background-color: #0b0b0b;
            }
        }
    </style>

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    {{-- Ziggy: expose helper route() ke frontend --}}
    @routes

    {{-- Vite + React + Inertia entry point (cukup SATU entry) --}}
    @viteReactRefresh
    @vite('resources/js/app.tsx')

    {{-- Inertia head tags --}}
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>