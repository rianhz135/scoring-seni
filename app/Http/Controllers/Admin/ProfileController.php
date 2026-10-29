<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanDisplay;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user() ?? User::first();
        $pengaturan = PengaturanDisplay::getSetting();

        return view('pages.admin.profile', compact('user', 'pengaturan'));
    }

    /**
     * Memperbarui pengaturan informasi kejuaraan, gelanggang, dan logo pada display.
     */
    public function updateDisplay(Request $request)
    {
        $request->validate([
            'nama_kejuaraan' => 'required|string|max:255',
            'gelanggang' => 'required|string|max:100',
            'logo_kiri' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:3072',
            'logo_kanan' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:3072',
            'hapus_logo_kanan' => 'nullable|boolean',
        ]);

        $pengaturan = PengaturanDisplay::getSetting();

        $data = [
            'nama_kejuaraan' => $request->nama_kejuaraan,
            'gelanggang' => $request->gelanggang,
        ];

        $uploadDir = public_path('uploads/display');
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        // Upload Logo 1 (Kiri)
        if ($request->hasFile('logo_kiri')) {
            $fileKiri = $request->file('logo_kiri');
            $extKiri = $fileKiri->getClientOriginalExtension();
            $filenameKiri = 'logo_kiri_' . time() . '.' . $extKiri;
            $fileKiri->move($uploadDir, $filenameKiri);
            $data['logo_kiri'] = 'uploads/display/' . $filenameKiri;
        }

        // Upload Logo 2 (Kanan)
        if ($request->hasFile('logo_kanan')) {
            $fileKanan = $request->file('logo_kanan');
            $extKanan = $fileKanan->getClientOriginalExtension();
            $filenameKanan = 'logo_kanan_' . time() . '.' . $extKanan;
            $fileKanan->move($uploadDir, $filenameKanan);
            $data['logo_kanan'] = 'uploads/display/' . $filenameKanan;
        } elseif ($request->has('hapus_logo_kanan') && $request->hapus_logo_kanan) {
            $data['logo_kanan'] = null;
        }

        $pengaturan->update($data);

        // Broadcast Refresh ke seluruh channel display gelanggang
        try {
            $channels = ['tanding.A', 'solo.A', 'ganda.A', 'tunggal.A', 'regu.A', 'pool.A'];
            foreach ($channels as $ch) {
                broadcast(new \App\Events\RefreshDisplay($ch));
            }
        } catch (\Throwable $e) {}

        return redirect()->back()->with('success', 'Pengaturan Display (Gelanggang, Kejuaraan, Logo) berhasil diperbarui dan disinkronkan ke layar!');
    }

    /**
     * Memperbarui profil personal admin.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user() ?? User::first();
        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->back()->with('success', 'Informasi profil personal berhasil disimpan.');
    }

    /**
     * Memperbarui password akun admin.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user() ?? User::first();
        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password berhasil diperbarui.');
    }
}
