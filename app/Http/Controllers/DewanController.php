<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partai;
use Illuminate\Http\Request;

class DewanController extends Controller
{
    public function index($id_partai)
    {
        // 1. Ambil data partai beserta relasi kategori dan pesertanya
        $partai = Partai::with(['kategori', 'detail_peserta.peserta', 'kelas'])
                        ->findOrFail($id_partai);

        // 2. Ambil nama kategori dan ubah ke huruf kapital untuk pengecekan
        $namaKategori = strtoupper($partai->kategori->nama_kategori);

        // 3. Logika percabangan view
        if (str_contains($namaKategori, 'SOLO') || str_contains($namaKategori, 'GANDA')) {
            // Jika Solo atau Ganda, arahkan ke solo.blade.php (Default 9.10)
            return view('pages.dewan.solo', compact('partai'));
        } 
        
        // Default: Arahkan ke index.blade.php (Tunggal & Regu - Default 9.90)
        return view('pages.dewan.index', compact('partai'));
    }
}