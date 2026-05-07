<?php

namespace App\Http\Controllers\Admin\Hasil;

use App\Http\Controllers\Controller;
use App\Models\Partai; // Pastikan Model Partai dipanggil
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan ini ditambahkan di atas



class SoloController extends Controller
{
    public function index()
    {
        $partais = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'LIKE', '%SOLO%');
            })
            ->whereHas('detail_peserta', function ($q) {
                // Syarat 1: Jangan tampilkan sistem pool (no_urut harus kosong/null)
                $q->whereNull('no_urut')
                    // Syarat 2: Pastikan sudah diplot ke sudut
                    ->whereNotNull('sudut')
                    // Syarat 3: Hanya tampilkan yang sudah selesai (status_mulai = 2)
                    ->where('status_mulai', 2);
            })
            ->orderBy('no_partai', 'asc')
            ->paginate(10); // Sesuaikan jumlah baris per halaman

        // Sesuaikan nama string view dengan struktur folder resources/views Anda
        // Misalnya: 'admin.hasil.solo' atau 'pages.admin.hasil.solo'
        return view('pages.hasil.solo.index', compact('partais'));
    }

    public function exportPdf()
    {
        // Mengambil semua data yang selesai tanpa limit paginasi
        $partais = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'LIKE', '%SOLO%');
            })
            ->whereHas('detail_peserta', function ($q) {
                $q->whereNull('no_urut')
                    ->whereNotNull('sudut')
                    ->where('status_mulai', 2);
            })
            ->orderBy('no_partai', 'asc')
            ->get();

        // Pastikan Anda membuat file blade baru khusus untuk desain cetak
        // (misalnya: resources/views/pages/hasil/solo/pdf.blade.php)
        $pdf = Pdf::loadView('pages.hasil.solo.pdf', compact('partais'))
            ->setPaper('a4', 'landscape'); // Format lanskap agar tabel muat

        // File langsung terbuka di tab baru browser
        return $pdf->stream('Rekapitulasi_Solo_' . date('Ymd_His') . '.pdf');
    }
}