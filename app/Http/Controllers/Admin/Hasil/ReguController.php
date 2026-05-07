<?php

namespace App\Http\Controllers\Admin\Hasil;

use App\Http\Controllers\Controller;

class ReguController extends Controller
{
    public function index()
    {
        // Logika untuk menampilkan partai yang siap tanding
        return view('pages.hasil.regu.index');
    }
}