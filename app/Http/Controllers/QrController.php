<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Endroid QR v6 imports
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class QrController extends Controller
{
    /**
     * Info endpoint QR Scanner.
     * Route: GET /qr-scanner (name: qr.scanner.page)
     */
    public function index()
    {
        return response()->json([
            'title'     => 'QR Scanner',
            'message'   => 'Gunakan endpoint /qr untuk generate QR PNG, dan /qr/process untuk memproses kode.',
            'endpoints' => [
                'GET /qr?data=...' => 'Generate QR PNG berdasarkan parameter "data"',
                'POST /qr/process' => 'Proses kode QR (body: { "code": "..." })',
            ],
        ]);
    }

    /**
     * Generate QR PNG (v6 style).
     * Route: GET /qr?data=...
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|string'
        ]);

        // Builder pattern v6 (tanpa Builder::create)
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $validated['data'],
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 250,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            // (opsional) logo / label bisa ditambahkan jika perlu
            // logoPath: storage_path('app/public/logo.png'),
            // labelText: 'Tel-U Shop',
        );

        $result = $builder->build();

        return response($result->getString(), 200)
            ->header('Content-Type', $result->getMimeType());
    }

    /**
     * Proses kode QR yang dikirimkan dari client.
     * Route: POST /qr/process
     * Body: { "code": "..." }
     */
    public function process(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        return response()->json([
            'message' => "QR Code {$request->code} berhasil diproses.",
            'code'    => $request->code,
            'action'  => 'redirect_to_activity'
        ]);
    }
}
