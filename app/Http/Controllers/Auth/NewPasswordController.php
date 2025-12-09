<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class NewPasswordController extends Controller
{
    /**
     * Endpoint untuk menampilkan token reset (opsional).
     */
    public function create($token)
    {
        return response()->json([
            'message' => 'Gunakan token ini untuk reset password.',
            'token' => $token
        ]);
    }

    /**
     * Proses reset password.
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password berhasil direset.'
            ]);
        }

        return response()->json([
            'error' => 'Gagal reset password.'
        ], 400);
    }
}
