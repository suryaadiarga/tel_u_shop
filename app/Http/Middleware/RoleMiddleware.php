<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleMiddleware
{
    /**
     * Cek role user berdasarkan parameter middleware.
     *
     * Bisa dipakai:
     * - role:1                    (by id)
     * - role:admin               (by name)
     * - role:admin,merchant      (multiple, name)
     * - role:1,2                 (multiple, id)
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Ambil role name & id yang dimiliki user saat ini
        $currentRoleId   = $user->role_id;
        $currentRoleName = optional($user->role)->name; // pastikan relasi role() ada

        // Normalisasi argumen (pisah by koma jika single string)
        if (count($roles) === 1 && Str::contains($roles[0], ',')) {
            $roles = array_map('trim', explode(',', $roles[0]));
        }

        // Cek match oleh name atau id
        $authorized = collect($roles)->contains(function ($r) use ($currentRoleId, $currentRoleName) {
            // Jika numeric → bandingkan role_id
            if (is_numeric($r)) {
                return (int)$r === (int)$currentRoleId;
            }
            // Jika string → bandingkan name (lowercase)
            return Str::lower($r) === Str::lower((string)$currentRoleName);
        });

        if (!$authorized) {
            return response()->json([
                'error'   => 'Forbidden',
                'message' => 'Anda tidak memiliki role yang sesuai.'
            ], 403);
        }

        return $next($request);
    }
}
