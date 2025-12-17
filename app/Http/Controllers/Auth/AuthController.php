<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    /**
     * Menangani pendaftaran pengguna baru (Customer/Merchant).
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nim'      => 'required|string|max:255',
            'kelas'    => 'required|string|max:255',
            'phone'    => 'required|string|max:255',
            'role'     => 'required|in:1,2,3', // 1:Admin, 2:Merchant, 3:Customer
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name'      => $request->input('name'),
                'username'  => $request->input('username'),
                'email'     => $request->input('email'),
                'password'  => $request->input('password'),
                'nim'       => $request->input('nim'),
                'kelas'     => $request->input('kelas'),
                'phone'     => $request->input('phone'),
                'role_id'   => $request->input('role'),
            ]);

            // Inisialisasi saldo awal atau profil tambahan jika diperlukan
            if ($user->role_id == 3) {
                // Contoh: $user->wallet()->create(['balance' => 0]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Pendaftaran berhasil.',
                'data'    => [
                    'user'         => $user,
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                ]
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat pendaftaran.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menangani log masuk pengguna.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kredensial tidak valid.'
            ], 401);
        }

        // Keamanan: Hapus token lama agar hanya ada satu sesi aktif
        // Menggunakan delete() method yang tersedia dari HasApiTokens trait
        $user->tokens->each(function ($token) {
            $token->delete();
        });

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil.',
            'data'    => [
                'user'         => $user->only(['id', 'name', 'email', 'role', 'avatar']),
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ]
        ], 200);
    }

    /**
     * Mengambil data profil pengguna yang sedang login.
     */
    public function me(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data'   => $request->user()
        ]);
    }

    /**
     * Memperbarui profil pengguna termasuk unggah avatar.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name'   => 'sometimes|string|max:255',
            'email'  => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil berhasil diperbarui.',
            'data'    => $user
        ]);
    }

    /**
     * Mengganti kata sandi pengguna.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kata sandi saat ini tidak cocok.'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->input('new_password'))
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Kata sandi berhasil diubah.'
        ]);
    }

    /**
     * Menangani logout (menghapus token).
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Logout berhasil.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal melakukan logout.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
