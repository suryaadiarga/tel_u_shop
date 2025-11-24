<?php

namespace App\Http\Controllers;

class StudentCardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('card', compact('user'));
    }
}
