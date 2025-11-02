<div class="relative bg-hero text-white">
    <div class="max-w-md mx-auto px-5 pt-4 pb-20 curve-mask">
        <div class="flex items-center gap-3">
            <a href="{{ url()->previous() }}" class="p-2 rounded-xl bg-white/10 hover:bg-white/20 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor">
                    <path stroke-width="2" d="m15 18-6-6 6-6" />
                </svg>
            </a>
            <h1 class="font-semibold">{{ $title ?? 'Title' }}</h1>
        </div>
    </div>
</div>