<?php

namespace App\Http\Controllers\Admin\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partai;
use App\Models\RefKategori;
use App\Models\DetailPesertaTanding;
use App\Models\RefKelas;
use App\Models\Peserta;
use Illuminate\Support\Facades\DB;

class ReguController extends Controller
{
    public function index()
    {
        $partais = Partai::with(['kategori', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'Regu');
            })
            ->whereHas('detail_peserta', function ($q) {
                $q->whereNotNull('sudut');
            })
            ->paginate(10);

        return view('pages.peserta.regu.index', compact('partais'));
    }

    public function create()
    {
        // Mengambil semua data dari tabel ref_kelas
        $kelas = \App\Models\RefKelas::all();

        // Mengambil kategori regu
        $kategori = RefKategori::where('nama_kategori', 'Regu')->get();

        return view('pages.peserta.regu.create', compact('kelas', 'kategori'));
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            // 1. Create Partai
            $partai = Partai::create([
                'no_partai' => $request->no_partai,
                'id_kategori' => $request->id_kategori,
                'id_kelas' => $request->id_kelas,
                'status_pertandingan' => 'Waiting',
            ]);

            // 2. Create athletes for Biru
            foreach ($request->biru_nama as $nama) {
                $peserta = Peserta::create([
                    'nama_peserta' => strtoupper($nama),
                    'kontingen' => strtoupper($request->biru_kontingen)
                ]);

                DetailPesertaTanding::create([
                    'id_partai' => $partai->id_partai,
                    'id_peserta' => $peserta->id_peserta,
                    'sudut' => 'Biru',
                ]);
            }

            // 3. Create athletes for Merah
            foreach ($request->merah_nama as $nama) {
                $peserta = Peserta::create([
                    'nama_peserta' => strtoupper($nama),
                    'kontingen' => strtoupper($request->merah_kontingen)
                ]);

                DetailPesertaTanding::create([
                    'id_partai' => $partai->id_partai,
                    'id_peserta' => $peserta->id_peserta,
                    'sudut' => 'Merah',
                ]);
            }
        });

        return redirect()->route('admin.peserta.regu.index')->with('success', 'Data Peserta Regu berhasil ditambahkan!');
    }

    public function edit($id)
    {
        // Ambil data partai beserta relasinya
        $partai = Partai::with(['detail_peserta.peserta', 'kategori', 'kelas'])->findOrFail($id);

        // Ambil data pendukung untuk dropdown
        $kelas = RefKelas::all();
        $kategori = RefKategori::where('nama_kategori', 'like', '%Regu%')->get();

        // Pisahkan atlet berdasarkan sudut untuk memudahkan di View
        $atletBiru = $partai->detail_peserta->where('sudut', 'Biru');
        $atletMerah = $partai->detail_peserta->where('sudut', 'Merah');

        return view('pages.peserta.regu.edit', compact('partai', 'kelas', 'kategori', 'atletBiru', 'atletMerah'));
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

            return redirect()->route('admin.peserta.regu.index')->with('success', 'Data Regu berhasil diperbarui!');

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

            return redirect()->route('admin.peserta.regu.index')->with('success', 'Data Regu dan Atlet berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
