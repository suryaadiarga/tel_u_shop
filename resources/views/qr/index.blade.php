@extends('layouts.app')

@section('content')
    @include('components.topbar', ['title' => 'QRIS'])

    <div class="max-w-md mx-auto px-5 -mt-10 space-y-4">
        <div
            class="rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800 shadow-soft p-6 text-center">
            <div id="result" class="text-sm text-neutral-500">Scan Kode QR di sini</div>
            <button x-data @click="$dispatch('open-scan')"
                class="mt-4 w-full rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 shadow-soft transition">
                Scan
            </button>
        </div>
    </div>

    {{-- Modal Scanner --}}
    <div x-data="{open:false}" x-on:open-scan.window="open=true" x-show="open" style="display:none"
        class="fixed inset-0 z-50 grid place-items-center bg-black/60 p-5">
        <div class="w-full max-w-md rounded-2xl bg-white dark:bg-neutral-900 p-4">
            <div class="flex items-center justify-between">
                <div class="font-semibold">Scan QR</div>
                <button class="p-2 rounded-xl hover:bg-black/5 dark:hover:bg-white/5" @click="open=false">✕</button>
            </div>
            <div id="reader" class="mt-3 rounded-xl overflow-hidden bg-black h-72"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let qrcode;
        document.addEventListener('open-scan', async () => {
            if (!qrcode) qrcode = new Html5Qrcode("reader");
            try {
                await qrcode.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 },
                    (text) => { document.getElementById('result').textContent = 'Scanned: ' + text; qrcode.stop(); document.querySelector('[x-data]{open:true}')?.__x.$data.open = false; },
                    () => { }
                );
            } catch (e) { console.log(e); }
        });
    </script>
@endpush