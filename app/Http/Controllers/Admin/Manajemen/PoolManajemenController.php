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
            broadcast(new \App\Events\RefreshDisplay('pool.A'));

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

    public function triggerRefreshDisplay()
    {
        // Sesuaikan 'A' dengan gelanggang Anda jika sistem multi-gelanggang
        broadcast(new \App\Events\RefreshDisplay('pool.A'));

        return response()->json([
            'status' => 'success',
            'message' => 'Sinyal refresh berhasil dikirim ke Layar Display!'
        ]);
    }

    public function inputNilaiJuriTunggalRegu(Request $request, $id_partai, $no_juri, $no_urut)
    {
        try {
            $nilaiA = (float) ($request->nilai_a ?? 0);
            $nilaiB = (float) ($request->nilai_b ?? 0);

            // 1. CARI PESERTA BERDASARKAN ID PARTAI & NO URUT DI POOL TERSEBUT
            $detail = DB::table('detail_peserta_tanding')
                ->where('id_partai', $id_partai)
                ->where('no_urut', $no_urut)
                ->first();

            if ($detail) {
                // ==========================================
                // DETEKSI KATEGORI TUNGGAL ATAU REGU
                // ==========================================
                $partai = \App\Models\Partai::with('kategori')->find($id_partai);
                $namaKatLower = strtolower($partai->kategori->nama_kategori ?? '');
                $jenis = str_contains($namaKatLower, 'regu') ? 'regu' : 'tunggal';
                // ==========================================

                // 2. RUMUS TUNGGAL / REGU (Baseline: 9.90)
                $skorAkhirJuri = 9.90 - $nilaiA + $nilaiB;

                // 3. SIMPAN NILAI JURI KE DATABASE
                DB::table('input_nilai_juri')
                    ->where('id_detail', $detail->id_detail)
                    ->where('no_juri', $no_juri)
                    ->update([
                        'nilai_a' => $nilaiA,
                        'nilai_b' => $nilaiB,
                        'skor_akhir_juri' => $skorAkhirJuri,
                        'updated_at' => now()
                    ]);

                // 4. AMBIL DATA JURI YANG SEDANG "ON" UNTUK KALKULASI MEDIAN & DEVIASI
                $kontrolScores = DB::table('status_kontrol_juri')
                    ->where('id_partai', $id_partai)
                    ->where('is_on', 1)
                    ->get();

                $juriScores = DB::table('input_nilai_juri')
                    ->where('id_detail', $detail->id_detail)
                    ->get();

                $activeScores = [];
                foreach ($kontrolScores as $kontrol) {
                    $scoreRow = $juriScores->where('no_juri', $kontrol->no_juri)->first();
                    if ($scoreRow) {
                        $activeScores[] = (float) $scoreRow->skor_akhir_juri;
                    }
                }

                // 5. HITUNG MEDIAN & STANDAR DEVIASI
                $median = 0;
                $deviasi = 0;
                $count = count($activeScores);

                if ($count > 0) {
                    // Hitung Median
                    sort($activeScores);
                    $mid = floor($count / 2);
                    $median = ($count % 2 == 0) ? ($activeScores[$mid - 1] + $activeScores[$mid]) / 2 : $activeScores[$mid];

                    // Hitung Standar Deviasi
                    $mean = array_sum($activeScores) / $count;
                    $variance = 0;
                    foreach ($activeScores as $score) {
                        $variance += pow($score - $mean, 2);
                    }
                    $deviasi = sqrt($variance / $count);
                }

                $hukuman = (float) ($detail->hukuman_ketua ?? 0);
                $totalNilai = $median - $hukuman; // Total akhir = Median - Hukuman

                // 6. UPDATE TABEL DETAIL PESERTA DENGAN NILAI TERBARU
                DB::table('detail_peserta_tanding')
                    ->where('id_detail', $detail->id_detail)
                    ->update([
                        'nilai_median' => $median,
                        'nilai_deviasi' => $deviasi,
                        'total_nilai' => $totalNilai
                    ]);

                // 7. BROADCAST DATA KE LAYAR DEWAN & DISPLAY
                broadcast(new \App\Events\UpdateNilaiJuriPool(
                    $id_partai,            // 1. partaiId
                    (int) $no_juri,        // 2. noJuri
                    (float) $nilaiA,       // 3. nilaiA
                    (float) $nilaiB,       // 4. nilaiB
                    0,                     // 5. nilaiC (Kirim 0 untuk Tunggal/Regu)
                    (float) $skorAkhirJuri,// 6. skorAkhir
                    (float) $median,       // 7. median
                    (float) $deviasi,      // 8. deviasi
                    (float) $totalNilai,   // 9. totalNilai
                    $jenis,                // 10. jenis (Dinamis: 'tunggal' atau 'regu')
                    (int) $no_urut         // 11. noUrut (Posisi paling akhir)
                ));

                return response()->json(['status' => 'success']);
            }

            return response()->json(['status' => 'error', 'message' => 'Peserta dengan Nomor Urut tersebut tidak ditemukan di Pool ini'], 404);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function simpanNilaiJuriTunggalRegu(Request $request, $id_partai, $no_juri, $no_urut)
    {
        try {
            // 1. Cari Data Peserta berdasarkan nomor urut di partai ini
            $detail = \Illuminate\Support\Facades\DB::table('detail_peserta_tanding')
                ->where('id_partai', $id_partai)
                ->where('no_urut', $no_urut)
                ->first();

            if (!$detail) {
                return response()->json(['status' => 'error', 'message' => 'Data peserta tidak ditemukan']);
            }

            // ==========================================
            // TAMBAHAN: DETEKSI KATEGORI TUNGGAL ATAU REGU
            // ==========================================
            $partai = \App\Models\Partai::with('kategori')->find($id_partai);
            $namaKatLower = strtolower($partai->kategori->nama_kategori ?? '');
            $jenis = str_contains($namaKatLower, 'regu') ? 'regu' : 'tunggal';
            // ==========================================

            // 2. Update status juri menjadi 'Selesai' di tabel kontrol juri
            \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                ->where('id_partai', $id_partai)
                ->where('no_juri', $no_juri)
                ->update(['status_input' => 'Selesai']);

            // 3. Ambil status is_on untuk dikirim via broadcast
            $kontrolJuriIni = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                ->where('id_partai', $id_partai)
                ->where('no_juri', $no_juri)
                ->first();

            // 4. BROADCAST STATUS 'SELESAI' KE DEWAN
            broadcast(new \App\Events\ToggleKontrolJuriPool(
                $id_partai,                  // 1. partaiId
                $no_juri,                    // 2. noJuri
                $kontrolJuriIni->is_on ?? 1, // 3. isOn
                'Selesai',                   // 4. statusInput (Kunci untuk dewan)
                null,                        // 5. median
                null,                        // 6. deviasi
                null,                        // 7. totalNilai
                null,                        // 8. sudut
                0,                           // 9. hukuman
                0,                           // 10. isDisq
                $jenis,                      // 11. jenis (Dinamis: 'tunggal' atau 'regu')
                (int) $no_urut               // 12. noUrut (Posisi Paling Akhir)
            ));

            return response()->json([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}