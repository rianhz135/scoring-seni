<?php

namespace App\Http\Controllers\Admin\Manajemen;

use App\Http\Controllers\Controller;

class MatchGandaController extends Controller
{
    public function index()
    {
        // Logika untuk menampilkan partai yang siap tanding
        return view('pages.manajemen.ganda.index');
    }
}
