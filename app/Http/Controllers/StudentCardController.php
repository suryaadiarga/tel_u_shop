<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentCardController extends Controller
{
    /**
     * Endpoint kartu mahasiswa digital (API JSON).
     */
    public function index()
    {
        $user = Auth::user();

        // TODO: bisa tambahkan logika generate QR code untuk student_id
        // Contoh: $qrCode = QrCode::size(200)->generate($user->student_id);

        return response()->json([
            'title' => 'Kartu Mahasiswa',
            'user' => $user ? $user->only(['id', 'name', 'email', 'student_id']) : null,
            'qrCode' => $user ? $user->student_id : null // placeholder, FE bisa generate QR sendiri
        ]);
    }
}
