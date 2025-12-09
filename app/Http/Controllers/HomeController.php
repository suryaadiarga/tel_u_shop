<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Halaman utama aplikasi (API JSON).
     */
    public function index()
    {
        // Ambil produk terbaru (opsional)
        $products = Product::latest()->take(8)->get();

        return response()->json([
            'title' => 'Tel-U Shop',
            'products' => $products
        ]);
    }
}