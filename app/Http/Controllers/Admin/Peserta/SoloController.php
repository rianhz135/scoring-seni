<?php

namespace App\Http\Controllers\Admin\Peserta;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class SoloController extends Controller
{
    public function index()
    {
        $partai_solo = \App\Models\Partai::with(['kelas', 'detail_peserta.peserta'])
            ->whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'LIKE', '%SOLO%');
            })
            ->whereHas('detail_peserta', function ($q) {
                $q->whereNotNull('sudut');
            })
            ->orderBy('no_partai', 'asc')
            ->paginate(6); // Menggunakan paginate bukan get

        return view('pages.peserta.solo.index', compact('partai_solo'));
    }

    public function create()
    {
        // Ambil semua kelas untuk dropdown
        $data_kelas = \App\Models\RefKelas::orderBy('label_kelas', 'asc')->get();

        return view('pages.peserta.solo.create', compact('data_kelas'));
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
        \DB::transaction(function () use ($request) {
            // 1. Simpan Atlet Biru
            $atletBiru = \App\Models\Peserta::create([
                'nama_peserta' => strtoupper($request->biru_nama),
                'kontingen' => strtoupper($request->biru_kontingen),
            ]);

            // 2. Simpan Atlet Merah
            $atletMerah = \App\Models\Peserta::create([
                'nama_peserta' => strtoupper($request->merah_nama),
                'kontingen' => strtoupper($request->merah_kontingen),
            ]);

            // 3. Simpan Partai
            $partai = \App\Models\Partai::create([
                'id_kategori' => 1, // Pastikan ID 1 adalah SOLO di table ref_kategori
                'id_kelas' => $request->id_kelas,
                'no_partai' => $request->no_partai,
                'babak' => 'Penyisihan', // Default
                'gelanggang' => 'A',     // Default
                'status_pertandingan' => 'Waiting',
            ]);

            // 4. Hubungkan ke Detail (Sudut Biru)
            \App\Models\DetailPesertaTanding::create([
                'id_partai' => $partai->id_partai,
                'id_peserta' => $atletBiru->id_peserta,
                'sudut' => 'Biru',
            ]);

            // 5. Hubungkan ke Detail (Sudut Merah)
            \App\Models\DetailPesertaTanding::create([
                'id_partai' => $partai->id_partai,
                'id_peserta' => $atletMerah->id_peserta,
                'sudut' => 'Merah',
            ]);
        });

        return redirect()->route('admin.peserta.solo.index')->with('success', 'Data Peserta Solo berhasil ditambahkan!');
    }
    public function edit($id)
    {
        // Ambil data partai beserta relasi detail dan pesertanya
        $partai = \App\Models\Partai::with(['detail_peserta.peserta'])->findOrFail($id);
        $data_kelas = \App\Models\RefKelas::orderBy('label_kelas', 'asc')->get();

        // Pisahkan data biru dan merah agar mudah dipanggil di view
        $biru = $partai->detail_peserta->where('sudut', 'Biru')->first();
        $merah = $partai->detail_peserta->where('sudut', 'Merah')->first();

        return view('pages.peserta.solo.edit', compact('partai', 'data_kelas', 'biru', 'merah'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'no_partai' => 'required|string|max:100',
            'id_kelas' => 'required|exists:ref_kelas,id_kelas',
            'biru_nama' => 'required|string',
            'biru_kontingen' => 'required|string',
            'merah_nama' => 'required|string',
            'merah_kontingen' => 'required|string',
        ]);

        \DB::transaction(function () use ($request, $id) {
            $partai = \App\Models\Partai::findOrFail($id);

            // 1. Update Data Partai
            $partai->update([
                'no_partai' => $request->no_partai,
                'id_kelas' => $request->id_kelas,
            ]);

            // 2. Update Peserta Biru
            $biru = $partai->detail_peserta->where('sudut', 'Biru')->first();
            $biru->peserta->update([
                'nama_peserta' => strtoupper($request->biru_nama),
                'kontingen' => strtoupper($request->biru_kontingen),
            ]);

            // 3. Update Peserta Merah
            $merah = $partai->detail_peserta->where('sudut', 'Merah')->first();
            $merah->peserta->update([
                'nama_peserta' => strtoupper($request->merah_nama),
                'kontingen' => strtoupper($request->merah_kontingen),
            ]);
        });

        return redirect()->route('admin.peserta.solo.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        try {
            \DB::transaction(function () use ($id) {
                $partai = \App\Models\Partai::findOrFail($id);

                // Hapus detail peserta (jembatan ke atlet)
                $partai->detail_peserta()->delete();

                // Hapus partai itu sendiri
                $partai->delete();
            });

            return redirect()->route('admin.peserta.solo.index')->with('success', 'Partai berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('admin.peserta.solo.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}

