<!doctype html>
<html x-data="{ dark: localStorage.theme === 'dark' }" x-bind:class="dark ? 'dark' : ''" lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Tel-U Shop' }}</title>

    {{-- Tailwind CDN (tanpa Vite) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#fff1f1', 100: '#ffd7d7', 200: '#ffb3b3', 300: '#ff8a8a', 400: '#f25c5c', 500: '#c1121f', 600: '#9b1111', 700: '#7c0e0e', 800: '#5f0b0b', 900: '#420808' },
                    },
                    boxShadow: {
                        soft: '0 10px 30px rgba(0,0,0,.12)',
                        glass: '0 8px 30px rgba(0,0,0,.18)',
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js untuk interaksi ringan --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Google Font (Inter) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        html,
        body {
            font-family: Inter, ui-sans-serif, system-ui, Arial
        }

        .bg-hero {
            background: radial-gradient(120% 120% at 50% -10%, rgba(193, 18, 31, .9) 0%, rgba(155, 17, 17, .95) 55%, rgba(155, 17, 17, 0) 56%), #fff;
        }

        .curve-mask {
            mask: radial-gradient(140% 100% at 50% 0, #000 0 65%, transparent 66% 100%);
        }

        .glass {
            backdrop-filter: blur(10px);
        }
    </style>

    @stack('styles')
</head>

<body class="bg-white dark:bg-neutral-950 dark:text-neutral-100">

    {{-- Top gradient halo --}}
    <div class="fixed inset-x-0 -top-24 h-64 blur-3xl opacity-30 dark:opacity-50 pointer-events-none"
        style="background: radial-gradient(600px 200px at 50% 40%, #c1121f55, transparent 70%);"></div>

    <main class="min-h-[100dvh] pb-24">
        @yield('content')
    </main>

    {{-- Bottom Nav (glass) --}}
    <nav class="fixed bottom-3 inset-x-3 z-50">
        <div
            class="glass shadow-glass rounded-2xl px-4 py-2 bg-white/80 dark:bg-neutral-900/70 backdrop-saturate-150 border border-white/40 dark:border-white/10">
            <ul class="flex items-center justify-between text-sm">
                <li><a href="{{ route('home') }}"
                        class="px-3 py-2 rounded-xl {{ request()->routeIs('home') ? 'bg-brand-600 text-white' : 'text-neutral-600 dark:text-neutral-300' }}">Home</a>
                </li>
                <li><a href="{{ route('cart') }}"
                        class="px-3 py-2 rounded-xl {{ request()->routeIs('cart') ? 'bg-brand-600 text-white' : 'text-neutral-600 dark:text-neutral-300' }}">Cart</a>
                </li>
                <li>
                    <a href="{{ route('qr') }}"
                        class="inline-flex items-center justify-center w-12 h-12 -mt-8 rounded-full bg-brand-600 text-white shadow-soft hover:scale-105 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path stroke-width="2"
                                d="M3 3h6v6H3V3zm12 0h6v6h-6V3zM3 15h6v6H3v-6zm12 4h2v-2h2v4h-4v-2zM15 13h4v2h-4v-2z" />
                        </svg>
                    </a>
                </li>
                <li><a href="{{ route('activity') }}"
                        class="px-3 py-2 rounded-xl {{ request()->routeIs('activity') ? 'bg-brand-600 text-white' : 'text-neutral-600 dark:text-neutral-300' }}">Activity</a>
                </li>
                <li class="flex items-center gap-2">
                    <a href="{{ route('profile') }}"
                        class="px-3 py-2 rounded-xl {{ request()->routeIs('profile') ? 'bg-brand-600 text-white' : 'text-neutral-600 dark:text-neutral-300' }}">Profile</a>
                    {{-- Dark mode toggle --}}
                    <button class="ml-2 p-2 rounded-xl hover:bg-black/5 dark:hover:bg-white/10"
                        x-on:click="dark=!dark; dark?localStorage.theme='dark':localStorage.removeItem('theme')"
                        title="Toggle theme">
                        <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-neutral-600"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 18a6 6 0 1 1 0-12v12Z" />
                        </svg>
                        <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-300"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.657 16.243A8 8 0 0 1 7.757 6.343 8 8 0 1 0 17.657 16.243Z" />
                        </svg>
                    </button>
                </li>
            </ul>
        </div>
    </nav>

    @if(session('toast'))
        <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,3000)"
            class="fixed top-4 inset-x-0 flex justify-center z-[60]">
            <div class="px-4 py-2 rounded-xl bg-emerald-600 text-white shadow-soft">{{ session('toast') }}</div>
        </div>
    @endif


    @stack('scripts')
</body>

</html>