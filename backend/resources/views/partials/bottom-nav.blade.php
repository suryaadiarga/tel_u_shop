<nav class="bottom-nav" role="navigation" aria-label="Bottom navigation">
    <div class="bar">
        <a class="item {{ request()->is('/') ? 'active' : '' }}" href="{{ route('home') }}">
            <span>🏠</span><small>Home</small>
        </a>

        {{-- Cart sesuai role --}}
        @if(auth()->check() && auth()->user()->role_id == 3)
            <a class="item {{ request()->is('customer/cart') ? 'active' : '' }}" href="{{ route('customer.cart.index') }}">
                <span>🛒</span><small>Cart</small>
            </a>
        @elseif(auth()->check() && auth()->user()->role_id == 2)
            <a class="item {{ request()->is('merchant/cart') ? 'active' : '' }}" href="{{ route('merchant.cart') }}">
                <span>🛒</span><small>Cart</small>
            </a>
        @endif

        {{-- ScanPay --}}
        <a class="item {{ request()->is('qr-scanner') ? 'active' : '' }}" href="{{ route('qr.scanner.page') }}">
            <span>📷</span><small>ScanPay</small>
        </a>

        {{-- Activity hanya untuk customer --}}
        @if(auth()->check() && auth()->user()->role_id == 3)s
            <a class="item {{ request()->is('customer/activity') ? 'active' : '' }}"
                href="{{ route('customer.activity') }}">
                <span>⏱️</span><small>Activity</small>
            </a>
        @endif

        {{-- Profile / Login --}}
        @if(auth()->check())
            <a class="item {{ request()->is('profile') ? 'active' : '' }}" href="{{ route('profile') }}">
                <span>👤</span><small>Profile</small>
            </a>
        @else
            <a class="item {{ request()->is('login') ? 'active' : '' }}" href="{{ route('login') }}">
                <span>🔑</span><small>Login</small>
            </a>
        @endif
    </div>
</nav>