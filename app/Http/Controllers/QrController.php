<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class QrController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Link demo ke profil/ID
        $qrLink = route('student.card');
        return view('qr.index', compact('user', 'qrLink'));
    }
}
