<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partai; // <-- Pastikan Model Partai di-import
use App\Models\PartaiTanding;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Menghitung langsung dari tabel Partai (Seni)
        $totalSolo = Partai::where('id_kategori', 1)->count(); // Sesuaikan ID Solo

        $totalGanda = Partai::where('id_kategori', 2)->count(); // Sesuaikan ID Ganda

        $totalTunggal = Partai::where('id_kategori', 3)->count(); // Sesuaikan ID Tunggal

        $totalRegu = Partai::where('id_kategori', 4)->count(); // Sesuaikan ID Regu

        // 2. Menghitung total dari tabel Partai Tanding
        $totalTanding = PartaiTanding::count();

        return view('pages.dashboard.index', compact(
            'totalSolo',
            'totalGanda',
            'totalTunggal',
            'totalRegu',
            'totalTanding'
        ));
    }
}