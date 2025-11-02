<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class StudentCardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('student.card', compact('user'));
    }
}
