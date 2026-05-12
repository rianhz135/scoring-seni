<?php
namespace App\Http\Controllers\Admin\Manajemen;
use App\Http\Controllers\Controller;
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
}