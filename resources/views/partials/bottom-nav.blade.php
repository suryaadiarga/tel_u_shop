<nav class="bottom-nav" role="navigation" aria-label="Bottom navigation">
    <div class="bar">
        <a class="item {{ request()->is('/') ? 'active' : '' }}" href="{{ route('home') }}">
            <span>🏠</span><small>Home</small>
        </a>
        <a class="item {{ request()->is('cart') ? 'active' : '' }}" href="{{ route('cart') }}">
            <span>🛒</span><small>Cart</small>
        </a>
        <a class="item {{ request()->is('qr') ? 'active' : '' }}" href="{{ route('qr') }}">
            <span>📷</span><small>ScanPay</small>
        </a>
        <a class="item {{ request()->is('activity') ? 'active' : '' }}" href="{{ route('activity') }}">
            <span>⏱️</span><small>Activity</small>
        </a>
        <a class="item {{ request()->is('profile') ? 'active' : '' }}" href="{{ route('profile') }}">
            <span>👤</span><small>Profile</small>
        </a>
    </div>
</nav>