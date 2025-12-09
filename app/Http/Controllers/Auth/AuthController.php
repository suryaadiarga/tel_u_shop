<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    /**
     * Register a new user (JSON API).
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users', 'regex:/@student\.telkomuniversity\.ac\.id$/'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        // Langsung set role_id = 3 (customer)
        $user = DB::transaction(function () use ($data) {
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'], // Mutator akan hash otomatis
                'role_id' => 3, // Default customer
            ]);
        });

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login and issue token.
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    /**
     * Logout (revoke current token or all tokens).
     */

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $current = $user->currentAccessToken();
            if ($current) {
                $current->delete();
            } else {
                $user->tokens()->delete();
            }
        }

        return response()->json(['message' => 'Logout berhasil'], 200);
    }
}
