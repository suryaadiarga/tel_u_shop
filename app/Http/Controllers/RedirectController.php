<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    /**
     * Endpoint QR redirect.
     */
    public function qr()
    {
        return response()->json([
            'action' => 'qr_scanner',
            'route' => 'qr.scanner.page',
            'message' => 'Silakan buka halaman QR Scanner.'
        ]);
    }

    /**
     * Endpoint activity redirect.
     */
    public function activity()
    {
        if (Auth::check() && Auth::user()->role_id == 3) {
            return response()->json([
                'action' => 'customer_activity',
                'route' => 'customer.activity',
                'message' => 'Arahkan ke halaman aktivitas customer.'
            ]);
        }

        return response()->json([
            'error' => 'Forbidden',
            'message' => 'Anda tidak memiliki akses ke halaman aktivitas.'
        ], 403);
    }

    /**
     * Endpoint wallet redirect.
     */
    public function wallet()
    {
        if (Auth::check() && Auth::user()->role_id == 3) {
            return response()->json([
                'action' => 'customer_wallet',
                'route' => 'customer.wallet',
                'message' => 'Arahkan ke halaman wallet customer.'
            ]);
        }

        return response()->json([
            'error' => 'Forbidden',
            'message' => 'Anda tidak memiliki akses ke halaman wallet.'
        ], 403);
    }
}