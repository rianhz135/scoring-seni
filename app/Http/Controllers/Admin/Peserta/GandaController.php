<?php

namespace App\Http\Controllers\Admin\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // <--- TAMBAHKAN BARIS INI
use App\Models\Partai;
use App\Models\Peserta;
use App\Models\DetailPesertaTanding;
use App\Models\RefKelas;
use App\Models\RefKategori;
use Illuminate\Support\Facades\DB; // Tambahkan juga ini untuk transaksi database

class GandaController extends Controller
{
    public function index()
    {
        $data_ganda = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'LIKE', '%GANDA%');
            })
            // KUNCI UTAMA: Hanya ambil yang detail_pesertanya punya 'sudut'
            ->whereHas('detail_peserta', function ($q) {
                $q->whereNotNull('sudut');
            })
            ->orderBy('no_partai', 'asc')
            ->paginate(5);

        return view('pages.peserta.ganda.index', compact('data_ganda'));
    }


    public function create()
    {
        // Mengambil semua data dari tabel ref_kelas
        $kelas = RefKelas::all();

        // Mengambil kategori ganda
        $kategori = RefKategori::where('nama_kategori', 'like', '%Ganda%')->get();

        return view('pages.peserta.ganda.create', compact('kelas', 'kategori'));
    }
    public function edit($id)
    {
        // Ambil data partai beserta relasinya
        $partai = Partai::with(['detail_peserta.peserta', 'kategori', 'kelas'])->findOrFail($id);

        // Ambil data pendukung untuk dropdown
        $kelas = RefKelas::all();
        $kategori = RefKategori::where('nama_kategori', 'like', '%Ganda%')->get();

        // Pisahkan atlet berdasarkan sudut untuk memudahkan di View
        $atletBiru = $partai->detail_peserta->where('sudut', 'Biru');
        $atletMerah = $partai->detail_peserta->where('sudut', 'Merah');

        return view('pages.peserta.ganda.edit', compact('partai', 'kelas', 'kategori', 'atletBiru', 'atletMerah'));
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            // 1. Buat Partainya dulu
            $partai = Partai::create([
                'no_partai' => $request->no_partai,
                'id_kategori' => $request->id_kategori,
                'id_kelas' => $request->id_kelas,
                'status_pertandingan' => 'Waiting',
            ]);

            // 2. Simpan Atlet Biru (Looping 2 Nama)
            foreach ($request->biru_nama as $nama) {
                $peserta = Peserta::create([
                    'nama_peserta' => strtoupper($nama),
                    'kontingen' => strtoupper($request->biru_kontingen)
                ]);

                DetailPesertaTanding::create([
                    'id_partai' => $partai->id_partai,
                    'id_peserta' => $peserta->id_peserta,
                    'sudut' => 'Biru',
                    'no_urut' => 1
                ]);
            }

            // 3. Simpan Atlet Merah (Looping 2 Nama)
            foreach ($request->merah_nama as $nama) {
                $peserta = Peserta::create([
                    'nama_peserta' => strtoupper($nama),
                    'kontingen' => strtoupper($request->merah_kontingen)
                ]);

                DetailPesertaTanding::create([
                    'id_partai' => $partai->id_partai,
                    'id_peserta' => $peserta->id_peserta,
                    'sudut' => 'Merah',
                    'no_urut' => 2
                ]);
            }
        });

        return redirect()->route('admin.peserta.ganda.index')->with('success', 'Pasangan Ganda berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                // 1. Ambil data partai
                $partai = Partai::findOrFail($id);

                // 2. Update Nomor Partai & Kelas
                $partai->update([
                    'no_partai' => $request->no_partai,
                    'id_kelas' => $request->id_kelas,
                ]);

                // 3. Update Atlet Sudut Biru
                if ($request->has('biru_nama')) {
                    foreach ($request->biru_nama as $index => $nama) {
                        $detailId = $request->biru_detail_id[$index];
                        $detail = DetailPesertaTanding::with('peserta')->find($detailId);

                        if ($detail && $detail->peserta) {
                            // UPDATE TABEL PESERTA DISINI
                            $detail->peserta->update([
                                'nama_peserta' => strtoupper($nama),
                                'kontingen' => strtoupper($request->biru_kontingen)
                            ]);
                        }
                    }
                }

                // 4. Update Atlet Sudut Merah
                if ($request->has('merah_nama')) {
                    foreach ($request->merah_nama as $index => $nama) {
                        $detailId = $request->merah_detail_id[$index];
                        $detail = DetailPesertaTanding::with('peserta')->find($detailId);

                        if ($detail && $detail->peserta) {
                            // UPDATE TABEL PESERTA DISINI
                            $detail->peserta->update([
                                'nama_peserta' => strtoupper($nama),
                                'kontingen' => strtoupper($request->merah_kontingen)
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('admin.peserta.ganda.index')->with('success', 'Data Ganda berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // 1. Cari Partainya
                $partai = Partai::findOrFail($id);

                // 2. Ambil semua ID peserta yang terkait dengan partai ini
                $pesertaIds = $partai->detail_peserta->pluck('id_peserta');

                // 3. Hapus Detail (detail_peserta_tanding) otomatis terhapus jika ada cascade, 
                // tapi kita hapus manual untuk keamanan
                $partai->detail_peserta()->delete();

                // 4. Hapus data Peserta (atlet)
                Peserta::whereIn('id_peserta', $pesertaIds)->delete();

                // 5. Terakhir hapus Partainya
                $partai->delete();
            });

            return redirect()->route('admin.peserta.ganda.index')->with('success', 'Data Ganda dan Atlet berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}

