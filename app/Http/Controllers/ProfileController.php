<?php

namespace App\Http\Controllers;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profile', compact('user'));
    }
}
