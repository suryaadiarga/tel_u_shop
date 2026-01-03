<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', trim($__env->yieldContent('page.title', 'Tel-U Shop')))</title>

    {{-- Tailwind CDN (opsional, util tambahan) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Theme boot awal agar tidak ada flicker (FOUC) --}}
    <script>
        try {
            const key = 'telu-theme'; // SAMA dengan THEME_KEY di app.js
            const saved = localStorage.getItem(key);
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = saved || (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        } catch (e) { }
    </script>

    {{-- CSS utama --}}
    <link rel="stylesheet" href="{{ asset('app.css') }}?v=20251102">
</head>

<body class="min-h-dvh">
    {{-- Header --}}
    <header class="app-header">
        <div class="mx-auto max-w-md px-4 py-3 flex items-center gap-3">
            @hasSection('page.back')
                <a href="@yield('page.back')" class="header-back" aria-label="Back" data-ripple>
                    {{-- Icon panah kiri --}}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd"
                            d="M15.78 3.97a.75.75 0 010 1.06L9.81 11l5.97 5.97a.75.75 0 11-1.06 1.06l-6.5-6.5a.75.75 0 010-1.06l6.5-6.5a.75.75 0 011.06 0z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <div class="w-10"></div>
            @endif

            <h1 class="header-title flex-1">
                @yield('page.title', 'Tel-U Shop')
            </h1>

            {{-- Theme toggle kecil kanan --}}
            <button type="button" class="theme-toggle" data-theme-toggle data-theme-label="1" aria-label="Toggle theme"
                data-ripple>
                🌙
            </button>
        </div>
    </header>

    {{-- Page content --}}
    <main class="mx-auto max-w-md px-4 pt-4 scroll-page page-content">
        @yield('content')
    </main>

    {{-- FAB Scan (tengah) --}}
    <button class="fab" data-fab-scan data-to="{{ route('qr.scanner.page') }}" aria-label="ScanPay" data-ripple>
        {{-- QR icon --}}
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 8V6a2 2 0 012-2h2M20 8V6a2 2 0 00-2-2h-2M4 16v2a2 2 0 002 2h2M20 16v2a2 2 0 01-2 2h-2M7 7h3v3H7V7zm7 0h3v3h-3V7zm-7 7h3v3H7v-3zm7 0h3v3h-3v-3z" />
        </svg>
    </button>

    {{-- Bottom Nav --}}
    <nav class="bottom-nav safe-bottom">
        <div class="bottom-nav-inner">
            <ul class="mx-auto max-w-md">
                <li>
                    <a data-nav-link href="{{ route('home') }}">
                        {{-- home --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l9-7 9 7v8a2 2 0 01-2 2h-4a2 2 0 01-2-2V13H9v7a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    {{-- Cart: sesuaikan dengan role --}}
                    @if(auth()->check() && auth()->user()->role_id == 3)
                        <a data-nav-link href="{{ route('customer.cart.index') }}">
                            {{-- cart customer --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l3-7H6.4M7 13L5.4 5M7 13l-2 9m12-9l2 9M10 21h4" />
                            </svg>
                            <span>Cart</span>
                        </a>
                    @elseif(auth()->check() && auth()->user()->role_id == 2)
                        <a data-nav-link href="{{ route('merchant.cart') }}">
                            {{-- cart merchant --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l3-7H6.4M7 13L5.4 5M7 13l-2 9m12-9l2 9M10 21h4" />
                            </svg>
                            <span>Cart</span>
                        </a>
                    @endif
                </li>
                <li>
                    <a data-nav-link href="{{ route('qr.scanner.page') }}">
                        {{-- scanpay --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M3 7V5a2 2 0 012-2h2v2H5v2H3zm12-4h2a2 2 0 012 2v2h-2V5h-2V3zM3 17h2v2h2v2H5a2 2 0 01-2-2v-2zm16 0h2v2a2 2 0 01-2 2h-2v-2h2v-2z" />
                            <path d="M7 7h3v3H7zM14 7h3v3h-3zM7 14h3v3H7zM14 14h3v3h-3z" />
                        </svg>
                        <span>ScanPay</span>
                    </a>
                </li>
                <li>
                    {{-- Activity hanya untuk customer --}}
                    @if(auth()->check() && auth()->user()->role_id == 3)
                        <a data-nav-link href="{{ route('customer.activity') }}">
                            {{-- activity --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Activity</span>
                        </a>
                    @endif
                </li>
                <li>
                    @if(auth()->check())
                        <a data-nav-link href="{{ route('profile') }}">
                            {{-- profile --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A7 7 0 0112 15a7 7 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Profile</span>
                        </a>
                    @else
                        <a data-nav-link href="{{ route('login') }}">
                            {{-- login (guest) --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12H3m6-6l-6 6 6 6m12-6v6a2 2 0 01-2 2h-4a2 2 0 01-2-2V6a2 2 0 012-2h4a2 2 0 012 2v6z" />
                            </svg>
                            <span>Login</span>
                        </a>
                    @endif
                </li>
            </ul>
        </div>
    </nav>

    @stack('modals')
    @stack('scripts')

    {{-- JS utama --}}
    <script src="{{ asset('app.js') }}?v=20251102" defer></script>
</body>

</html>