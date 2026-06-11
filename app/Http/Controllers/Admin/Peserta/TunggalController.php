<?php

namespace App\Http\Controllers\Admin\Peserta;
use App\Models\Partai;
use App\Models\DetailPesertaTanding;
use App\Models\Peserta;
use App\Models\RefKelas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TunggalController extends Controller
{
    public function index()
    {
        $partai_tunggal = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'LIKE', '%TUNGGAL%');
            })
            // KUNCI UTAMA: Hanya ambil yang detail_pesertanya punya 'sudut'
            ->whereHas('detail_peserta', function ($q) {
                $q->whereNotNull('sudut');
            })
            ->orderBy('no_partai', 'asc')
            ->paginate(50);

        return view('pages.peserta.tunggal.index', compact('partai_tunggal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_partai' => 'required|string|max:100',
            'id_kelas' => 'required|exists:ref_kelas,id_kelas',
            'biru_nama' => 'required|string|max:255',
            'biru_kontingen' => 'required|string|max:255',
            'merah_nama' => 'required|string|max:255',
            'merah_kontingen' => 'required|string|max:255',
        ]);

        // Gunakan Transaction agar jika salah satu gagal, semua dibatalkan (aman)
        DB::transaction(function () use ($request) {
            // 1. Simpan Atlet Biru
            $atletBiru = Peserta::create([
                'nama_peserta' => strtoupper($request->biru_nama),
                'kontingen' => strtoupper($request->biru_kontingen),
            ]);

            // 2. Simpan Atlet Merah
            $atletMerah = Peserta::create([
                'nama_peserta' => strtoupper($request->merah_nama),
                'kontingen' => strtoupper($request->merah_kontingen),
            ]);

            // 3. Simpan Partai
            $partai = Partai::create([
                'id_kategori' => 3, // TUNGGAL category ID
                'id_kelas' => $request->id_kelas,
                'no_partai' => $request->no_partai,
                'babak' => 'Penyisihan', // Default
                'gelanggang' => 'A',     // Default
                'status_pertandingan' => 'Waiting',
            ]);

            // 4. Hubungkan ke Detail (Sudut Biru)
            DetailPesertaTanding::create([
                'id_partai' => $partai->id_partai,
                'id_peserta' => $atletBiru->id_peserta,
                'sudut' => 'Biru',
            ]);

            // 5. Hubungkan ke Detail (Sudut Merah)
            DetailPesertaTanding::create([
                'id_partai' => $partai->id_partai,
                'id_peserta' => $atletMerah->id_peserta,
                'sudut' => 'Merah',
            ]);
        });

        return redirect()->route('admin.peserta.tunggal.index')->with('success', 'Data Peserta Tunggal berhasil ditambahkan!');
    }

    public function create()
    {
        // Ambil semua kelas untuk dropdown
        $data_kelas = RefKelas::orderBy('label_kelas', 'asc')->get();

        return view('pages.peserta.tunggal.create', compact('data_kelas'));
    }
    public function edit($id)
    {
        // Ambil data partai beserta relasinya
        $partai = Partai::with(['detail_peserta.peserta', 'kategori', 'kelas'])->findOrFail($id);

        // Ambil data kelas untuk dropdown
        $data_kelas = RefKelas::orderBy('label_kelas', 'asc')->get();

        // Pisahkan atlet berdasarkan sudut
        $atletBiru = $partai->detail_peserta->where('sudut', 'Biru')->first();
        $atletMerah = $partai->detail_peserta->where('sudut', 'Merah')->first();

        return view('pages.peserta.tunggal.edit', compact('partai', 'data_kelas', 'atletBiru', 'atletMerah'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'no_partai' => 'required|string|max:100',
            'id_kelas' => 'required|exists:ref_kelas,id_kelas',
            'biru_nama' => 'required|string|max:255',
            'biru_kontingen' => 'required|string|max:255',
            'merah_nama' => 'required|string|max:255',
            'merah_kontingen' => 'required|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                // 1. Ambil data partai dengan relasi
                $partai = Partai::with(['detail_peserta.peserta'])->findOrFail($id);

                // 2. Update data partai
                $partai->update([
                    'no_partai' => $request->no_partai,
                    'id_kelas' => $request->id_kelas,
                ]);

                // 3. Update atlet biru
                $biruDetail = $partai->detail_peserta->where('sudut', 'Biru')->first();
                if ($biruDetail && $biruDetail->peserta) {
                    $biruDetail->peserta->update([
                        'nama_peserta' => strtoupper($request->biru_nama),
                        'kontingen' => strtoupper($request->biru_kontingen),
                    ]);
                }

                // 4. Update atlet merah
                $merahDetail = $partai->detail_peserta->where('sudut', 'Merah')->first();
                if ($merahDetail && $merahDetail->peserta) {
                    $merahDetail->peserta->update([
                        'nama_peserta' => strtoupper($request->merah_nama),
                        'kontingen' => strtoupper($request->merah_kontingen),
                    ]);
                }
            });

            return redirect()->route('admin.peserta.tunggal.index')->with('success', 'Data Peserta Tunggal berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // 1. Cari partai
                $partai = Partai::findOrFail($id);

                // 2. Ambil ID peserta yang terkait
                $pesertaIds = $partai->detail_peserta->pluck('id_peserta');

                // 3. Hapus detail peserta
                $partai->detail_peserta()->delete();

                // 4. Hapus data peserta
                Peserta::whereIn('id_peserta', $pesertaIds)->delete();

                // 5. Hapus partai
                $partai->delete();
            });

            return redirect()->route('admin.peserta.tunggal.index')->with('success', 'Data Peserta Tunggal berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
