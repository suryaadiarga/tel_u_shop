<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'error'   => 'Unauthenticated',
                'message' => 'Silakan login terlebih dahulu.'
            ], 401);
        }

        $activities = $user->activities()
            ->orderByDesc('occurred_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $activities,
            'meta' => [
                'count' => $activities->count(),
                'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
            ]
        ]);
    }
}
