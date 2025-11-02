<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // validasi dasar
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            // field opsional (boleh ada di form, tapi tidak wajib ada di DB)
            'nim' => ['nullable', 'string', 'max:50'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        // siapkan payload minimal (pasti ada kolomnya)
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ];

        // hanya set kolom jika memang ADA di tabel users (hindari "Unknown column")
        foreach (['nim', 'kelas', 'phone', 'avatar_url', 'ewallet_balance'] as $col) {
            if (Schema::hasColumn('users', $col) && array_key_exists($col, $data)) {
                $payload[$col] = $data[$col];
            }
        }

        // default avatar/saldo jika kolomnya ada
        if (Schema::hasColumn('users', 'avatar_url') && empty($payload['avatar_url'])) {
            $payload['avatar_url'] = '/images/avatar-default.png';
        }
        if (Schema::hasColumn('users', 'ewallet_balance') && empty($payload['ewallet_balance'])) {
            $payload['ewallet_balance'] = 0;
        }

        $user = User::create($payload);

        Auth::login($user);

        return redirect()->route('home')->with('toast', 'Akun berhasil dibuat. Selamat datang!');
    }
}
