<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if (count($roles) === 1 && Str::contains($roles[0], ',')) {
            $roles = array_map('trim', explode(',', $roles[0]));
        }

        $currentRoleId   = (int) ($user->role_id ?? 0);
        $currentRoleName = Str::lower((string) optional($user->role)->name);

        $authorized = collect($roles)->contains(function ($requiredRole) use ($currentRoleId, $currentRoleName) {
            $requiredRole = trim($requiredRole);

            if (is_numeric($requiredRole)) {
                return (int) $requiredRole === $currentRoleId;
            }

            return Str::lower($requiredRole) === $currentRoleName;
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
