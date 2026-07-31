<?php

namespace App\Http\Controllers\Admin\Peserta;

use App\Http\Controllers\Controller;
use App\Models\PartaiTanding;
use App\Models\PesertaTanding;
use Illuminate\Http\Request;
class TandingController extends Controller
{
    public function index()
    {
        // Mengambil data partai tanding beserta data nama peserta merah & biru
        $partais = PartaiTanding::with(['pesertaMerah', 'pesertaBiru'])
            ->orderBy('no_partai', 'asc')
            ->paginate(10);

        return view('pages.peserta.tanding.index', compact('partais'));
    }

    public function create()
    {
        return view('pages.peserta.tanding.create');
    }

    public function store(Request $request)
    {
        // 1. Simpan Data Sudut Biru (Bisa Insert Peserta Baru atau Biarkan Kosong)
        $id_biru = null;
        $tunggu_biru = null;

        if ($request->status_biru === 'atlet') {
            $pesertaBiru = PesertaTanding::create([
                'nama' => $request->biru_nama,
                'kontingen' => $request->biru_kontingen,
            ]);
            $id_biru = $pesertaBiru->id;
        } else {
            $tunggu_biru = $request->menunggu_biru;
        }

        // 2. Simpan Data Sudut Merah
        $id_merah = null;
        $tunggu_merah = null;

        if ($request->status_merah === 'atlet') {
            $pesertaMerah = PesertaTanding::create([
                'nama' => $request->merah_nama,
                'kontingen' => $request->merah_kontingen,
                'jenis_kelamin' => $request->merah_jk,
            ]);
            $id_merah = $pesertaMerah->id;
        } else {
            $tunggu_merah = $request->menunggu_merah;
        }

        // 3. Simpan Partai Tanding
        PartaiTanding::create([
            'no_partai' => $request->no_partai,
            'fase_babak' => $request->fase_babak,
            'kelas' => $request->kelas,
            'id_biru' => $id_biru,
            'id_merah' => $id_merah,
            'menunggu_pemenang_biru_dari' => $tunggu_biru,
            'menunggu_pemenang_merah_dari' => $tunggu_merah,
            'status_pertandingan' => 'Belum'
        ]);

        return redirect()->route('admin.peserta.tanding.index')->with('success', 'Partai berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $partai = PartaiTanding::findOrFail($id);

        // Simpan ID peserta untuk dihapus agar database bersih dari atlet yang tidak punya jadwal
        $id_merah = $partai->id_merah;
        $id_biru = $partai->id_biru;

        // Hapus partai (Otomatis menghapus log juri & poin berkat cascadeOnDelete di migration)
        $partai->delete();

        // (Opsional) Hapus peserta dari tabel master jika mereka tidak terkait partai lain
        if ($id_merah) {
            PesertaTanding::where('id', $id_merah)->delete();
        }
        if ($id_biru) {
            PesertaTanding::where('id', $id_biru)->delete();
        }

        return redirect()->route('admin.peserta.tanding.index')
            ->with('success', 'Partai berhasil dihapus permanen!');
    }

    /**
     * Menampilkan halaman form edit partai
     */
    public function edit($id)
    {
        // Ambil data partai beserta relasi pesertanya
        $partai = PartaiTanding::with(['pesertaMerah', 'pesertaBiru'])->findOrFail($id);

        return view('pages.peserta.tanding.edit', compact('partai'));
    }

    /**
     * Memproses pembaruan data ke database
     */
    public function update(Request $request, $id)
    {
        $partai = PartaiTanding::findOrFail($id);

        // 1. Proses Sudut Biru
        $id_biru = $partai->id_biru;
        $tunggu_biru = null;

        if ($request->status_biru === 'atlet') {
            if ($id_biru) {
                // Jika sebelumnya sudah ada atlet, Update saja
                PesertaTanding::where('id', $id_biru)->update([
                    'nama' => $request->biru_nama,
                    'kontingen' => $request->biru_kontingen,
                ]);
            } else {
                // Jika sebelumnya 'menunggu', lalu diganti jadi 'atlet' (Buat Baru)
                $pesertaBiru = PesertaTanding::create([
                    'nama' => $request->biru_nama,
                    'kontingen' => $request->biru_kontingen,
                ]);
                $id_biru = $pesertaBiru->id;
            }
        } else {
            // Jika statusnya menunggu
            $tunggu_biru = $request->menunggu_biru;
            // Hapus data atlet lama jika Admin mengubahnya menjadi 'menunggu'
            if ($id_biru) {
                PesertaTanding::where('id', $id_biru)->delete();
                $id_biru = null;
            }
        }

        // 2. Proses Sudut Merah
        $id_merah = $partai->id_merah;
        $tunggu_merah = null;

        if ($request->status_merah === 'atlet') {
            if ($id_merah) {
                PesertaTanding::where('id', $id_merah)->update([
                    'nama' => $request->merah_nama,
                    'kontingen' => $request->merah_kontingen,
                ]);
            } else {
                $pesertaMerah = PesertaTanding::create([
                    'nama' => $request->merah_nama,
                    'kontingen' => $request->merah_kontingen,
                ]);
                $id_merah = $pesertaMerah->id;
            }
        } else {
            $tunggu_merah = $request->menunggu_merah;
            if ($id_merah) {
                PesertaTanding::where('id', $id_merah)->delete();
                $id_merah = null;
            }
        }

        // 3. Update tabel jadwal partai
        $partai->update([
            'no_partai' => $request->no_partai,
            'fase_babak' => $request->fase_babak,
            'kelas' => $request->kelas,
            'id_biru' => $id_biru,
            'id_merah' => $id_merah,
            'menunggu_pemenang_biru_dari' => $tunggu_biru,
            'menunggu_pemenang_merah_dari' => $tunggu_merah,
        ]);

        return redirect()->route('admin.peserta.tanding.index')
            ->with('success', 'Data partai #' . $request->no_partai . ' berhasil diperbarui!');
    }
}
