<?php

namespace App\Http\Controllers\Admin\Hasil;

use App\Http\Controllers\Controller;

class TunggalController extends Controller
{
    public function index()
    {
        // Logika untuk menampilkan partai yang siap tanding
        return view('pages.hasil.tunggal.index');
    }
}