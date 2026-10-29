<?php

namespace App\Http\Controllers\Admin\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\PartaiTanding;
use App\Models\PesertaTanding;
use App\Models\StatusKontrolJuriTanding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TandingController extends Controller
{
    /**
     * Menampilkan daftar partai dan peserta tanding di halaman manajemen operasional.
     */
    public function index(Request $request)
    {
        // Query untuk daftar partai tanding
        $query = PartaiTanding::with(['pesertaMerah', 'pesertaBiru', 'pemenang']);

        // Filter Pencarian (No Partai, Kelas, Nama Atlet, atau Kontingen)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('no_partai', 'LIKE', "%{$search}%")
                    ->orWhere('kelas', 'LIKE', "%{$search}%")
                    ->orWhere('fase_babak', 'LIKE', "%{$search}%")
                    ->orWhereHas('pesertaMerah', function ($sub) use ($search) {
                        $sub->where('nama', 'LIKE', "%{$search}%")
                            ->orWhere('kontingen', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('pesertaBiru', function ($sub) use ($search) {
                        $sub->where('nama', 'LIKE', "%{$search}%")
                            ->orWhere('kontingen', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Filter Status Pertandingan
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status_pertandingan', $request->status);
        }

        // Filter Fase Babak
        if ($request->filled('fase_babak') && $request->fase_babak !== 'all') {
            $query->where('fase_babak', $request->fase_babak);
        }

        // Sorting & Pagination
        $partais = $query->orderBy('no_partai', 'asc')->paginate(10)->withQueryString();

        // Ringkasan Statistik
        $stats = [
            'total' => PartaiTanding::count(),
            'belum' => PartaiTanding::where('status_pertandingan', 'Belum')->count(),
            'berlangsung' => PartaiTanding::where('status_pertandingan', 'Berlangsung')->count(),
            'selesai' => PartaiTanding::where('status_pertandingan', 'Selesai')->count(),
            'total_atlet' => PesertaTanding::count(),
        ];

        // Query untuk Tab Daftar Atlet Tanding
        $atletQuery = PesertaTanding::with(['partaiMerah', 'partaiBiru', 'partaiMenang']);
        if ($request->filled('search_atlet')) {
            $searchAtlet = trim($request->search_atlet);
            $atletQuery->where(function ($q) use ($searchAtlet) {
                $q->where('nama', 'LIKE', "%{$searchAtlet}%")
                    ->orWhere('kontingen', 'LIKE', "%{$searchAtlet}%")
                    ->orWhere('kategori_umur', 'LIKE', "%{$searchAtlet}%");
            });
        }
        $atlets = $atletQuery->orderBy('nama', 'asc')->paginate(15, ['*'], 'atlet_page')->withQueryString();

        return view('pages.manajemen.tanding.index', compact('partais', 'stats', 'atlets'));
    }

    /**
     * Memulai pertandingan suatu partai (status: Berlangsung).
     */
    public function mulai($id)
    {
        $partai = PartaiTanding::findOrFail($id);

        // Validasi ketersediaan peserta
        if (!$partai->id_merah && !$partai->id_biru) {
            return redirect()->back()->with('error', 'Partai #' . $partai->no_partai . ' belum memiliki atlet di kedua sudut!');
        }

        $partai->update([
            'status_pertandingan' => 'Berlangsung',
            'status_timer' => 'running',
            'babak_aktif' => $partai->babak_aktif ?: 1,
        ]);

        // Inisialisasi status kontrol juri (1, 2, 3) jika tabel siap
        if (Schema::hasTable('status_kontrol_juri_tanding')) {
            for ($noJuri = 1; $noJuri <= 3; $noJuri++) {
                StatusKontrolJuriTanding::firstOrCreate(
                    ['id_partai' => $partai->id, 'no_juri' => $noJuri],
                    ['is_active' => true, 'status' => 'Active']
                );
            }
        }

        // Broadcast refresh layar display agar otomatis membuka partai tanding ini
        try {
            broadcast(new \App\Events\RefreshDisplay('tanding.A'));
        } catch (\Throwable $e) {
            // Silently continue if broadcast server offline
        }

        // Pindah otomatis ke halaman kontrol dewan tanding
        return redirect()->route('admin.dewan.tanding.index', $partai->id)
            ->with('success', 'Pertandingan Partai #' . $partai->no_partai . ' dimulai! Selamat bertanding.');
    }

    /**
     * Memperbarui babak aktif (Babak 1, 2, atau 3).
     */
    public function updateBabak(Request $request, $id)
    {
        $request->validate([
            'babak_aktif' => 'required|integer|in:1,2,3',
        ]);

        $partai = PartaiTanding::findOrFail($id);
        $partai->update([
            'babak_aktif' => $request->babak_aktif,
        ]);

        return redirect()->back()->with('success', 'Babak aktif Partai #' . $partai->no_partai . ' diubah ke Babak ' . $request->babak_aktif . '!');
    }

    /**
     * Menetapkan pemenang partai tanding dan otomatis memperbarui bagan babak lanjutan jika ada.
     */
    public function setPemenang(Request $request, $id)
    {
        $request->validate([
            'id_pemenang' => 'required|exists:peserta_tanding,id',
        ]);

        $partai = PartaiTanding::findOrFail($id);

        if ($partai->id_merah != $request->id_pemenang && $partai->id_biru != $request->id_pemenang) {
            return redirect()->back()->with('error', 'Pemenang harus merupakan atlet dari sudut merah atau biru pada partai ini!');
        }

        $partai->update([
            'id_pemenang' => $request->id_pemenang,
            'status_pertandingan' => 'Selesai',
            'status_timer' => 'stop',
        ]);

        // Otomatis isi pemenang ke partai berikutnya jika ada sistem bagan yang menunggu partai ini
        PartaiTanding::where('menunggu_pemenang_biru_dari', $partai->no_partai)
            ->update(['id_biru' => $request->id_pemenang]);

        PartaiTanding::where('menunggu_pemenang_merah_dari', $partai->no_partai)
            ->update(['id_merah' => $request->id_pemenang]);

        $pemenang = PesertaTanding::find($request->id_pemenang);
        $namaPemenang = $pemenang ? $pemenang->nama : 'Atlet';

        return redirect()->back()->with('success', "Partai #{$partai->no_partai} selesai! Pemenang: {$namaPemenang}. Bagan lanjutan telah diperbarui secara otomatis.");
    }

    /**
     * Menandai status partai selesai tanpa menetapkan pemenang secara spesifik.
     */
    public function selesai($id)
    {
        $partai = PartaiTanding::findOrFail($id);
        $partai->update([
            'status_pertandingan' => 'Selesai',
            'status_timer' => 'stop',
        ]);

        return redirect()->back()->with('success', 'Partai #' . $partai->no_partai . ' ditandai Selesai.');
    }

    /**
     * Me-reset status pertandingan partai kembali ke status 'Belum'.
     */
    public function resetPertandingan($id)
    {
        $partai = PartaiTanding::findOrFail($id);
        $partai->update([
            'status_pertandingan' => 'Belum',
            'babak_aktif' => 1,
            'status_timer' => 'stop',
            'id_pemenang' => null,
        ]);

        return redirect()->back()->with('success', 'Status Partai #' . $partai->no_partai . ' berhasil di-reset kembali ke Belum.');
    }
}

