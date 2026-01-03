@extends('layouts.app')

@section('title', 'QR Scanner Kasir')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center mb-4">Pindai Pesanan (Kasir)</h2>
                <p class="text-center text-muted">Arahkan kamera ke QR Code yang ditampilkan Mahasiswa.</p>

                {{-- Tempat kamera akan ditampilkan --}}
                <div id="qr-reader" style="width: 100%;"></div>

                {{-- Area untuk menampilkan hasil atau feedback --}}
                <div id="qr-reader-results" class="mt-3">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <div id="result-message" class="alert d-none"></div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- 1. CDN Library html5-qrcode --}}
    <script src="https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const qrCodeRegionId = "qr-reader";
            const resultMessageElement = document.getElementById('result-message');
            let html5QrcodeScanner = null;

            // Fungsi yang dipanggil ketika QR Code berhasil di-scan
            function onScanSuccess(decodedText, decodedResult) {
                // Hentikan scanner setelah berhasil mendapatkan hasilnya
                html5QrcodeScanner.pause();

                // Tampilkan pesan loading
                showMessage('Memproses Pesanan...', 'alert-info');

                // 1. Ambil URL Scan dari hasil decodedText
                // decodedText adalah URL lengkap: http://127.0.0.1:8000/scan/{orderId}

                // 2. Kirim permintaan (AJAX/Fetch) ke backend Laravel
                fetch(decodedText, {
                    method: 'GET', // Metode yang sesuai dengan rute qr.scan
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Penting untuk POST, tapi tetap jaga
                    }
                })
                    .then(response => response.json().then(data => ({
                        status: response.status,
                        body: data
                    })))
                    .then(res => {
                        if (res.status === 200) {
                            // Sukses: Pesanan Selesai
                            showMessage(`✅ Selesai! Pesanan #${res.body.order_details.status} Mahasiswa ${res.body.order_details.customer} berhasil diselesaikan.`, 'alert-success');
                        } else {
                            // Gagal: Status bukan 200 (misalnya 404, 400, 403)
                            showMessage(`❌ Gagal: ${res.body.message}`, 'alert-danger');
                        }

                        // Jeda sebentar lalu lanjutkan scanning
                        setTimeout(() => {
                            html5QrcodeScanner.resume();
                        }, 3000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showMessage('❌ Terjadi kesalahan jaringan atau server.', 'alert-danger');
                        setTimeout(() => {
                            html5QrcodeScanner.resume();
                        }, 3000);
                    });
            }

            function onScanFailure(error) {
                // Kita bisa mengabaikan error untuk menghindari spam pesan
                // console.warn(`Code scan error = ${error}`);
            }

            function showMessage(message, className) {
                // Reset dan tampilkan pesan baru
                resultMessageElement.className = `alert mt-3 ${className}`;
                resultMessageElement.textContent = message;
                resultMessageElement.classList.remove('d-none');
            }

            // Inisialisasi Scanner
            html5QrcodeScanner = new Html5QrcodeScanner(
                qrCodeRegionId,
                { fps: 10, qrbox: { width: 250, height: 250 } },
                /* verbose= */ false
            );
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);

        });
    </script>
@endpush