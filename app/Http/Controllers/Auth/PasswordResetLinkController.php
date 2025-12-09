<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Endpoint untuk meminta reset password (opsional info).
     */
    public function create()
    {
        return response()->json([
            'message' => 'Silakan masukkan email untuk reset password.'
        ]);
    }

    /**
     * Kirim link reset password ke email.
     */
    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Link reset password sudah dikirim ke email.'
            ]);
        }

        return response()->json([
            'error' => 'Gagal mengirim link reset password.'
        ], 400);
    }
}
