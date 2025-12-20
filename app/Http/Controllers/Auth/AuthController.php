<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class AuthController extends Controller
{
    /**
     * REGISTER
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'nim'      => 'required|string|max:255',
            'kelas'    => 'required|string|max:255',
            'phone'    => 'required|string|max:255',
            'role'     => 'required|in:2,3',
        ]);

        DB::beginTransaction();
        try {
            $merchantStatus = $request->role == 2 ? 'pending' : 'approved';
            $user = User::create([
                'name'     => $request->name,
                'username' => $request->username,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'nim'      => $request->nim,
                'kelas'    => $request->kelas,
                'phone'    => $request->phone,
                'role_id'  => $request->role,
                'merchant_status' => $merchantStatus,
            ]);

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
                'message' => 'Gagal mendaftar.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kredensial tidak valid.'
            ], 401);
        }

        if ($user->isBanned()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akun diblokir.'
            ], 403);
        }

        // hapus token lama
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil.',
            'data'    => [
                'user'         => $user->only(['id', 'name', 'email', 'role_id']),
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ]
        ]);
    }

    /**
     * USER LOGIN INFO
     */
    public function me(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data'   => $request->user()->load('role')
        ]);
    }

    /**
     * UPDATE PROFILE
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
            if ($user->avatar_url) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            $user->avatar_url = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update([
            'name'  => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil berhasil diperbarui.',
            'data'    => $user
        ]);
    }

    /**
     * CHANGE PASSWORD
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required|string',
            'new_password'          => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kata sandi saat ini salah.'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Kata sandi berhasil diubah.'
        ]);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout berhasil.'
        ]);
    }
}
