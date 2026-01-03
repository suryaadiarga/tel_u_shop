<div class="topbar">
    <div class="topbar-left">
        @isset($back)
            <a href="{{ $back }}" class="back-btn" aria-label="Kembali">←</a>
        @endisset
        <h1 class="topbar-title">{{ $title ?? 'Tel-U Shop' }}</h1>
    </div>
</div>