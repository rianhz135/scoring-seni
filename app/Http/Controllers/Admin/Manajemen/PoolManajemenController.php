<?php
namespace App\Http\Controllers\Admin\Manajemen;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\DetailPesertaTanding;
use Illuminate\Http\Request;
use App\Models\Partai;
class PoolManajemenController extends Controller
{
    public function index()
    {
        // 1. Query Khusus Solo: Sudut NULL dan No Urut NOT NULL
        $pool_solo = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'LIKE', '%SOLO%');
            })
            ->whereHas('detail_peserta', function ($q) {
                $q->whereNull('sudut') // Belum ditentukan Merah/Biru
                    ->whereNotNull('no_urut'); // Tapi sudah punya nomor urut tampil
            })
            ->orderBy('no_partai', 'asc')
            ->get();

        // --- Untuk kategori lain, kita gunakan logic standar (atau sesuaikan nanti) ---

        $baseQuery = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('detail_peserta', function ($q) {
                $q->whereNull('sudut');
            })
            ->orderBy('no_partai', 'asc');

        $pool_ganda = (clone $baseQuery)->whereHas('kategori', function ($q) {
            $q->where('nama_kategori', 'LIKE', '%GANDA%');
        })->get();

        $pool_tunggal = (clone $baseQuery)->whereHas('kategori', function ($q) {
            $q->where('nama_kategori', 'LIKE', '%TUNGGAL%');
        })->get();

        $pool_regu = (clone $baseQuery)->whereHas('kategori', function ($q) {
            $q->where('nama_kategori', 'LIKE', '%REGU%');
        })->get();

        return view('pages.manajemen.pool.index', compact(
            'pool_solo',
            'pool_ganda',
            'pool_tunggal',
            'pool_regu'
        ));
    }

    // Di dalam PoolManajemenController.php
    public function mulai($id_partai, $id_detail)
    {
        try {
            DB::beginTransaction();

            // 1. Reset status peserta lain di partai ini
            DetailPesertaTanding::where('id_partai', $id_partai)
                ->where('status_mulai', 1)
                ->update(['status_mulai' => 0]);

            // 2. Set peserta pilihan menjadi aktif
            DetailPesertaTanding::where('id_detail', $id_detail)
                ->update(['status_mulai' => 1]);

            // 3. Reset Status Kontrol Juri (is_on = 1 & status = Proses)
            for ($i = 1; $i <= 6; $i++) {
                DB::table('status_kontrol_juri')->updateOrInsert(
                    ['id_partai' => $id_partai, 'no_juri' => $i],
                    [
                        'status_input' => 'Proses',
                        'is_on' => 1,
                        'updated_at' => now()
                    ]
                );
            }

            // 4. Identifikasi Kategori untuk Broadcast
            $partai = Partai::with('kategori')->find($id_partai);
            $namaKat = strtolower($partai->kategori->nama_kategori ?? '');

            $jenis = 'solo';
            if (str_contains($namaKat, 'ganda'))
                $jenis = 'ganda';
            elseif (str_contains($namaKat, 'regu'))
                $jenis = 'regu';
            elseif (str_contains($namaKat, 'tunggal'))
                $jenis = 'tunggal';

            // 5. Kirim Sinyal Refresh ke Display
            broadcast(new \App\Events\RefreshDisplay($jenis . '.' . $id_partai));

            DB::commit();

            // ==========================================================
            // PINDAH KE HALAMAN DEWAN (Sesuai Permintaan Anda)
            // ==========================================================
            return redirect()->route('dewan.pool.index', ['id_partai' => $id_partai])
                ->with('success', 'Pertandingan Dimulai.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}