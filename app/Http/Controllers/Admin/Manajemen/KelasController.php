<?php

namespace App\Http\Controllers\Admin\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\RefKelas; // Pastikan Model ini sudah dibuat
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Tampilkan halaman index dengan data kelas
     */
    public function index()
    {
        // Mengambil semua data dari tabel ref_kelas, urutkan dari yang terbaru
        $data_kelas = \App\Models\RefKelas::orderBy('id_kelas', 'desc')->get();

        // Kirim data ke view
        return view('pages.manajemen.kelas.index', compact('data_kelas'));
    }

    /**
     * Simpan data kelas baru dari modal tambah
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'label_kelas' => 'required|string|max:50|unique:ref_kelas,label_kelas',
        ], [
            // Pesan error kustom
            'label_kelas.unique' => 'Gagal! Nama kelas "' . strtoupper($request->label_kelas) . '" sudah terdaftar di sistem.',
            'label_kelas.required' => 'Nama kelas tidak boleh kosong.'
        ]);

        \App\Models\RefKelas::create([
            'label_kelas' => strtoupper($request->label_kelas)
        ]);

        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan!');
    }

    /**
     * Update data kelas dari modal edit
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            // unique:table,column,except,id_column
            'label_kelas' => 'required|string|max:50|unique:ref_kelas,label_kelas,' . $id . ',id_kelas',
        ], [
            'label_kelas.unique' => 'Gagal! Nama kelas sudah digunakan oleh data lain.',
        ]);

        $kelas = \App\Models\RefKelas::findOrFail($id);
        $kelas->update([
            'label_kelas' => strtoupper($request->label_kelas)
        ]);

        return redirect()->back()->with('success', 'Kelas berhasil diperbarui!');
    }

    /**
     * Hapus data kelas
     */
    public function destroy($id)
    {
        try {
            $kelas = \App\Models\RefKelas::findOrFail($id);
            $kelas->delete();

            return redirect()->back()->with('success', 'Kelas berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data. Data mungkin masih digunakan di tabel lain.');
        }
    }
}