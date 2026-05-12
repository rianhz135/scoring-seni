<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partai; // <-- Pastikan Model Partai di-import

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Menghitung langsung dari tabel Partai
        // Karena 1 baris di tabel Partai = 1 pertandingan, tidak perlu pakai distinct lagi.

        $totalSolo = Partai::where('id_kategori', 1)->count(); // Sesuaikan ID Solo

        $totalGanda = Partai::where('id_kategori', 2)->count(); // Sesuaikan ID Ganda

        $totalTunggal = Partai::where('id_kategori', 3)->count(); // Sesuaikan ID Tunggal

        $totalRegu = Partai::where('id_kategori', 4)->count(); // Sesuaikan ID Regu

        return view('pages.dashboard.index', compact(
            'totalSolo',
            'totalGanda',
            'totalTunggal',
            'totalRegu'
        ));
    }
}