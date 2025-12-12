<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Mahasiswa;


class MahasiswaAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nim' => 'required|string|max:20|unique:mahasiswa,nim',
            'email' => 'required|string|max:50|unique',
            'kelas' => 'required|string|max:10',
            'prodi' => 'required|string|max:10',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $mahasiswa = Mahasiswa::create([
            'nama' => $request->nama,
            'nim' => $request->nim,
            'email' => $request->email,
            'kelas' => $request->kelas,
            'prodi' => $request->prodi,
            'password' => Hash::make($request->password),
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('mahasiswa')->attempt([
            'nim' => $request->nim,
            'password' => $request->password
        ])) {
            $mahasiswa = Auth::guard('mahasiswa')->user();

            return response()->json([
                'message' => 'Login berhasil',
                'data' => $mahasiswa
            ], 200);
        }

        return response()->json([
            'message' => 'NIM atau password salah'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::guard('mahasiswa')->logout();
        return response()->json(['message' => 'Logout berhasil']);
    }
}