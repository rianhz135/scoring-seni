<?php

namespace App\Http\Controllers\Admin\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Partai;
use App\Models\Peserta;
use App\Models\DetailPesertaTanding;
use App\Models\RefKelas;
use App\Models\RefKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PoolController extends Controller
{
    public function index()
    {
        $data_pool = Partai::with(['detail_peserta.peserta', 'kategori', 'kelas'])
            ->whereHas('detail_peserta', function ($q) {
                $q->whereNull('sudut');
            })
            ->orderBy('created_at', 'asc')
            ->paginate(5); // Menampilkan 10 partai per halaman

        return view('pages.peserta.pool.index', compact('data_pool'));
    }

    public function create()
    {
        $data_kelas = RefKelas::all();
        $data_kategori = RefKategori::all(); // Tampil: SOLO, TUNGGAL, GANDA, REGU

        return view('pages.peserta.pool.create', compact('data_kelas', 'data_kategori'));
    }

    public function edit($id)
    {
        $partai = Partai::with(['detail_peserta.peserta', 'kategori', 'kelas'])->findOrFail($id);
        $data_kelas = RefKelas::all();
        $data_kategori = RefKategori::all();

        return view('pages.peserta.pool.edit', compact('partai', 'data_kelas', 'data_kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_partai' => 'required|string',
            'id_kategori' => 'required',
            'id_kelas' => 'required',
            'peserta.*.nama' => 'required|string',
            'peserta.*.kontingen' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $partai = Partai::create([
                'no_partai' => $request->no_partai,
                'id_kategori' => $request->id_kategori,
                'id_kelas' => $request->id_kelas,
                'gelanggang' => $request->gelanggang ?? 'A',
                'status_pertandingan' => 'Waiting',
                'babak' => 'Final'
            ]);

            foreach ($request->peserta as $data) {
                $atlet = Peserta::create([
                    'nama_peserta' => strtoupper($data['nama']),
                    'kontingen' => strtoupper($data['kontingen']),
                ]);

                DetailPesertaTanding::create([
                    'id_partai' => $partai->id_partai,
                    'id_peserta' => $atlet->id_peserta,
                    'no_urut' => $data['no_urut'],
                    'sudut' => null,
                    'total_nilai' => 0
                ]);
            }
        });

        return redirect()->route('admin.peserta.pool.index')->with('success', 'Data Pool Berhasil Ditambahkan');
    }

    public function destroy($id)
    {
        $partai = Partai::findOrFail($id);

        try {
            DB::transaction(function () use ($partai) {
                // 1. Ambil ID semua peserta yang terhubung dengan partai ini
                $participantIds = $partai->detail_peserta->pluck('id_peserta');

                // 2. Hapus detail_peserta_tanding (relasi)
                $partai->detail_peserta()->delete();

                // 3. Hapus data peserta/atlet dari tabel peserta
                Peserta::whereIn('id_peserta', $participantIds)->delete();

                // 4. Terakhir hapus partainya
                $partai->delete();
            });

            return redirect()->route('admin.peserta.pool.index')->with('success', 'Data Pool dan seluruh pesertanya berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $partai = Partai::findOrFail($id);

        DB::transaction(function () use ($request, $partai) {
            // 1. Update Header Partai
            $partai->update([
                'no_partai' => $request->no_partai,
                'id_kategori' => $request->id_kategori,
                'id_kelas' => $request->id_kelas,
            ]);

            // 2. Ambil ID peserta lama untuk dibersihkan
            $oldParticipantIds = $partai->detail_peserta->pluck('id_peserta');

            // 3. Hapus detail lama (relasi)
            $partai->detail_peserta()->delete();

            // 4. Hapus data atlet lama (optional: jika atlet tidak dipakai di partai lain)
            Peserta::whereIn('id_peserta', $oldParticipantIds)->delete();

            // 5. Masukkan data baru dari form
            foreach ($request->peserta as $index => $data) {
                $atlet = Peserta::create([
                    'nama_peserta' => strtoupper($data['nama']),
                    'kontingen' => strtoupper($data['kontingen']),
                ]);

                DetailPesertaTanding::create([
                    'id_partai' => $partai->id_partai,
                    'id_peserta' => $atlet->id_peserta,
                    'no_urut' => $index + 1, // Reset no urut agar rapi
                    'sudut' => null,
                    'total_nilai' => $data['total_nilai'] ?? 0
                ]);
            }
        });

        return redirect()->route('admin.peserta.pool.index')->with('success', 'Data Pool berhasil diperbarui');
    }
}
