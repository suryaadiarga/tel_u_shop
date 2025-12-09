<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Endpoint profil user (API JSON).
     */
    public function index()
    {
        $user = Auth::user();

        return response()->json([
            'title' => 'Profil Saya',
            'user' => $user ? $user->only(['id', 'name', 'email']) : null
        ]);
    }
}