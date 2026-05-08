<?php

use Illuminate\Support\Facades\Route;
// Import Controller Utama
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;

// Import Controller Peserta (Dipisah per Kategori)
use App\Http\Controllers\Admin\Peserta\SoloController;
use App\Http\Controllers\Admin\Peserta\GandaController;
use App\Http\Controllers\Admin\Peserta\TunggalController;
use App\Http\Controllers\Admin\Peserta\ReguController;
use App\Http\Controllers\Admin\Manajemen\KelasController;
use App\Http\Controllers\Admin\Manajemen\MatchGandaController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});
Route::get('/dewan', function () {
    return view('pages.dewan.index');
});
Route::prefix('admin')->name('admin.')->group(function () {

    // --- 1. DASHBOARD & PROFILE ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    // Route untuk cek tampilan Dewan Solo/Ganda secara langsung
    // Route::get('/dewan/solo', function () {
    //     return view('pages.dewan.solo');
    // })->name('admin.dewan.solo.test');

    Route::get('/juri/solo', function () {
        return view('pages.juri.solo');
    })->name('juri.solo.test');

    // ROUTE UNTUK LAYAR DISPLAY PROYEKTOR / PENONTON
    Route::get('/dewan/display', function () {
        // Layar akan otomatis mencari partai yang SEDANG BERTANDING (status_mulai = 1)
        $partai = \App\Models\Partai::with(['kelas', 'detail_peserta.peserta'])
            ->whereHas('detail_peserta', function ($q) {
                $q->where('status_mulai', 1);
            })
            ->first(); // Mengambil 1 partai aktif

        return view('pages.dewan.display', compact('partai'));
    })->name('dewan.display');
    Route::post('/dewan/solo/{id}/selesai/{sudut}', [App\Http\Controllers\Admin\Manajemen\SoloController::class, 'selesai'])->name('dewan.solo.selesai');
    Route::post('/dewan/ganda/{id}/selesai/{sudut}', [App\Http\Controllers\Admin\Manajemen\MatchGandaController::class, 'selesai'])->name('dewan.ganda.selesai');
    Route::post('/dewan/refresh-display', [App\Http\Controllers\Admin\Manajemen\SoloController::class, 'triggerRefreshDisplay'])->name('dewan.refresh.display');


    // --- 2. DATA MASTER PESERTA (PER KATEGORI) ---
    Route::prefix('peserta')->name('peserta.')->group(function () {

        // Solo
        Route::prefix('solo')->name('solo.')->group(function () {
            Route::get('/', [SoloController::class, 'index'])->name('index');
            Route::get('/create', [SoloController::class, 'create'])->name('create');
            Route::post('/store', [SoloController::class, 'store'])->name('store');

            // Menampilkan Form (Method: GET)
            Route::get('/{id}/edit', [SoloController::class, 'edit'])->name('edit');

            // Menyimpan Perubahan (Method: PUT/PATCH)
            // URL-nya jangan pakai /edit lagi, cukup /{id} saja
            Route::put('/{id}', [SoloController::class, 'update'])->name('update');

            Route::delete('/{id}', [SoloController::class, 'destroy'])->name('destroy');

        });
        Route::prefix('pool')->name('pool.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\Peserta\PoolController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\Peserta\PoolController::class, 'create'])->name('create');
            Route::post('/store', [App\Http\Controllers\Admin\Peserta\PoolController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [App\Http\Controllers\Admin\Peserta\PoolController::class, 'edit'])->name('edit');
            Route::put('/{id}', [App\Http\Controllers\Admin\Peserta\PoolController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Admin\Peserta\PoolController::class, 'destroy'])->name('destroy');
        });

        // Ganda (Disederhanakan agar tidak double prefix)
        Route::prefix('ganda')->name('ganda.')->group(function () {
            Route::get('/', [GandaController::class, 'index'])->name('index');
            Route::get('/create', [GandaController::class, 'create'])->name('create');
            Route::post('/store', [GandaController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [GandaController::class, 'edit'])->name('edit');
            Route::put('/{id}', [GandaController::class, 'update'])->name('update');
            Route::delete('/{id}', [GandaController::class, 'destroy'])->name('destroy');
        });

        // Tunggal (Saran: samakan strukturnya agar rapi)
        Route::prefix('tunggal')->name('tunggal.')->group(function () {
            Route::get('/', [TunggalController::class, 'index'])->name('index');
            Route::get('/create', [TunggalController::class, 'create'])->name('create');
            Route::get('/{id}/edit', [TunggalController::class, 'edit'])->name('edit');
            Route::put('/{id}', [TunggalController::class, 'update'])->name('update');
            Route::post('/store', [TunggalController::class, 'store'])->name('store');
            Route::delete('/{id}', [TunggalController::class, 'destroy'])->name('destroy');
        });

        // Regu
        Route::prefix('regu')->name('regu.')->group(function () {
            Route::get('/', [ReguController::class, 'index'])->name('index');
            Route::get('/create', [ReguController::class, 'create'])->name('create');
            Route::post('/store', [ReguController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ReguController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ReguController::class, 'update'])->name('update');
            Route::delete('/{id}', [ReguController::class, 'destroy'])->name('destroy');
        });
    });

    // --- MANAJEMEN PERTANDINGAN (OPERASIONAL) ---
    Route::prefix('manajemen')->name('manajemen.')->group(function () {
        Route::get('/solo', [App\Http\Controllers\Admin\Manajemen\SoloController::class, 'index'])->name('solo.index');
        Route::get('/solo/mulai/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\SoloController::class, 'mulai'])->name('solo.mulai');
        Route::post('/solo/hukuman/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\SoloController::class, 'updateHukuman'])->name('solo.hukuman');
        Route::post('/solo/diskualifikasi/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\SoloController::class, 'diskualifikasi'])->name('solo.diskualifikasi');
        Route::post('/solo/update-timer/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\SoloController::class, 'updateTimer'])->name('solo.update-timer');
        Route::get('/solo/get-status/{id}/{sudut}', [\App\Http\Controllers\Admin\Manajemen\SoloController::class, 'getStatus']);
        Route::get('/solo/get-juri-scores/{id}/{sudut}', [\App\Http\Controllers\Admin\Manajemen\SoloController::class, 'getJuriScores']);
        Route::post('/solo/toggle-juri/{id_partai}/{no_juri}', [\App\Http\Controllers\Admin\Manajemen\SoloController::class, 'toggleStatusJuri']);
        Route::post('/solo/toggle-status-input/{id_partai}/{no_juri}', [\App\Http\Controllers\Admin\Manajemen\SoloController::class, 'toggleStatusInput']);

        Route::get('/ganda', [App\Http\Controllers\Admin\Manajemen\MatchGandaController::class, 'index'])->name('ganda.index');
        Route::get('/ganda/mulai/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\MatchGandaController::class, 'mulai'])->name('ganda.mulai');
        Route::post('/ganda/hukuman/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\MatchGandaController::class, 'updateHukuman'])->name('ganda.hukuman');
        Route::post('/ganda/diskualifikasi/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\MatchGandaController::class, 'diskualifikasi'])->name('ganda.diskualifikasi');
        Route::post('/ganda/update-timer/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\MatchGandaController::class, 'updateTimer'])->name('ganda.update-timer');
        Route::get('/ganda/get-status/{id}/{sudut}', [\App\Http\Controllers\Admin\Manajemen\MatchGandaController::class, 'getStatus']);
        Route::get('/ganda/get-juri-scores/{id}/{sudut}', [\App\Http\Controllers\Admin\Manajemen\MatchGandaController::class, 'getJuriScores']);
        Route::post('/ganda/toggle-juri/{id_partai}/{no_juri}', [\App\Http\Controllers\Admin\Manajemen\MatchGandaController::class, 'toggleStatusJuri']);
        Route::post('/ganda/toggle-status-input/{id_partai}/{no_juri}', [\App\Http\Controllers\Admin\Manajemen\MatchGandaController::class, 'toggleStatusInput']);

        Route::get('/tunggal', [App\Http\Controllers\Admin\Manajemen\TunggalController::class, 'index'])->name('tunggal.index');
        Route::get('/regu', [App\Http\Controllers\Admin\Manajemen\ReguController::class, 'index'])->name('regu.index');
        Route::resource('kelas', KelasController::class);
        Route::delete('/kelas/{id}', [KelasController::class, 'destroy'])->name('admin.manajemen.kelas.destroy');
        Route::put('/kelas/{id}', [KelasController::class, 'update'])->name('admin.manajemen.kelas.update');
        Route::get('/pool', [App\Http\Controllers\Admin\Manajemen\PoolManajemenController::class, 'index'])->name('pool.index');
    });



    // --- HASIL PERTANDINGAN (LAPORAN) ---
    Route::prefix('hasil')->name('hasil.')->group(function () {
        Route::get('/solo', [App\Http\Controllers\Admin\Hasil\SoloController::class, 'index'])->name('solo.index');
        Route::get('/solo/export-pdf', [App\Http\Controllers\Admin\Hasil\SoloController::class, 'exportPdf'])->name('solo.export-pdf');
        Route::post('/solo/{id}/tetapkan-pemenang', [App\Http\Controllers\Admin\Hasil\SoloController::class, 'tetapkanPemenang'])->name('solo.tetapkan-pemenang');
        Route::post('/solo/{id}/tutup-display', [App\Http\Controllers\Admin\Hasil\SoloController::class, 'tutupDisplay'])->name('solo.tutup-display');
        Route::get('/ganda', [App\Http\Controllers\Admin\Hasil\GandaController::class, 'index'])->name('ganda.index');
        Route::get('/tunggal', [App\Http\Controllers\Admin\Hasil\TunggalController::class, 'index'])->name('tunggal.index');
        Route::get('/regu', [App\Http\Controllers\Admin\Hasil\ReguController::class, 'index'])->name('regu.index');
    });

});

// ==============================================
// ROUTE JURI (TABLET) - AUTO DETECT PARTAI AKTIF
// ==============================================

// 1. Route API untuk mengecek apakah ada partai yang baru dimulai
Route::get('/api/cek-partai-aktif', function () {
    $aktif = \App\Models\DetailPesertaTanding::where('status_mulai', 1)->first();
    return response()->json([
        'id_detail' => $aktif ? $aktif->id_detail : null
    ]);
});

// 2. Route Halaman Utama Juri (Hanya butuh nomor Juri, misal: /juri/solo/1)
Route::get('/juri/solo/{no_juri}', function ($no_juri) {

    // Cari partai yang detailnya SEDANG BERTANDING (status_mulai = 1)
    $partai = \App\Models\Partai::with(['kelas', 'detail_peserta.peserta'])
        ->whereHas('detail_peserta', function ($q) {
            $q->where('status_mulai', 1);
        })->first();

    $aktif = null;
    $nilai = null;
    $sudut = null;

    if ($partai) {
        $aktif = $partai->detail_peserta->where('status_mulai', 1)->first();
        $sudut = strtolower($aktif->sudut);

        // Tarik nilai juri saat ini dari database
        $nilai = \Illuminate\Support\Facades\DB::table('input_nilai_juri')
            ->where('id_detail', $aktif->id_detail)
            ->where('no_juri', $no_juri)
            ->first();
    }
    $kontrol = null;
    if ($partai) {
        $aktif = $partai->detail_peserta->where('status_mulai', 1)->first();

        // Ambil data kontrol juri spesifik untuk juri ini
        $kontrol = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
            ->where('id_partai', $partai->id_partai)
            ->where('no_juri', $no_juri)
            ->first();
    }

    // Jika tidak ada partai, kirim $partai = null (akan memicu Mode Standby di Blade)
    return view('pages.juri.solo', compact('partai', 'aktif', 'sudut', 'no_juri', 'nilai', 'kontrol'));

})->name('juri.solo');

// 2. Route Halaman Utama Juri Ganda (Hanya butuh nomor Juri, misal: /juri/ganda/1)
Route::get('/juri/ganda/{no_juri}', function ($no_juri) {

    // Cari partai yang detailnya SEDANG BERTANDING (status_mulai = 1)
    $partai = \App\Models\Partai::with(['kelas', 'detail_peserta.peserta'])
        ->whereHas('detail_peserta', function ($q) {
            $q->where('status_mulai', 1);
        })->first();

    $aktif = null;
    $nilai = null;
    $sudut = null;

    if ($partai) {
        $aktif = $partai->detail_peserta->where('status_mulai', 1)->first();
        $sudut = strtolower($aktif->sudut);

        // Tarik nilai juri saat ini dari database
        $nilai = \Illuminate\Support\Facades\DB::table('input_nilai_juri')
            ->where('id_detail', $aktif->id_detail)
            ->where('no_juri', $no_juri)
            ->first();
    }
    $kontrol = null;
    if ($partai) {
        $aktif = $partai->detail_peserta->where('status_mulai', 1)->first();

        // Ambil data kontrol juri spesifik untuk juri ini
        $kontrol = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
            ->where('id_partai', $partai->id_partai)
            ->where('no_juri', $no_juri)
            ->first();
    }

    // Jika tidak ada partai, kirim $partai = null (akan memicu Mode Standby di Blade)
    return view('pages.juri.ganda', compact('partai', 'aktif', 'sudut', 'no_juri', 'nilai', 'kontrol'));

})->name('juri.ganda');

// Route Simpan Nilai (Tetap)
Route::post('/juri/solo/simpan/{id_detail}/{no_juri}', [\App\Http\Controllers\Admin\Manajemen\SoloController::class, 'simpanNilaiJuri'])->name('juri.solo.simpan');
Route::post('/juri/ganda/simpan/{id_detail}/{no_juri}', [\App\Http\Controllers\Admin\Manajemen\MatchGandaController::class, 'simpanNilaiJuri'])->name('juri.ganda.simpan');


// Route baru untuk Kontrol Dewan Solo
Route::get('/dewan/solo/{id}/{sudut}', function ($id, $sudut) {
    // Mengambil data partai untuk ditampilkan di kontrol dewan
    $partai = \App\Models\Partai::with(['kelas', 'detail_peserta.peserta'])->findOrFail($id);
    // --- TAMBAHAN LOGIKA AUTO-GENERATE JURI ---
    $sudutDb = ucfirst($sudut);
    $aktif = $partai->detail_peserta->where('sudut', $sudutDb)->first();

    if ($aktif) {
        // Cek apakah data juri untuk id_detail ini sudah ada?
        $jumlahJuri = \Illuminate\Support\Facades\DB::table('input_nilai_juri')
            ->where('id_detail', $aktif->id_detail)
            ->count();

        // Jika belum ada 6 juri, buatkan defaultnya
        if ($jumlahJuri < 6) {
            $dataJuri = [];
            for ($i = 1; $i <= 6; $i++) {
                $dataJuri[] = [
                    'id_detail' => $aktif->id_detail,
                    'no_juri' => $i,
                    'nilai_a' => 0.00,
                    'nilai_b' => 0.00,
                    'nilai_c' => 0.00,
                    'skor_akhir_juri' => 9.10, // Nilai default solo
                ];
            }
            \Illuminate\Support\Facades\DB::table('input_nilai_juri')->insert($dataJuri);
        }

        // --- LOGIKA 2: Auto-Generate Kontrol Juri ---
        $jumlahKontrol = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')->where('id_partai', $id)->count();
        if ($jumlahKontrol < 6) {
            $dataKontrol = [];
            for ($i = 1; $i <= 6; $i++) {
                $dataKontrol[] = [
                    'id_partai' => $id, // Menggunakan id_partai sesuai skema Anda
                    'no_juri' => $i,
                    'is_on' => 0, // 0 = OFF (Merah) defaultnya
                    'status_input' => 'Proses', // Default Proses (Oranye)
                ];
            }
            \Illuminate\Support\Facades\DB::table('status_kontrol_juri')->insert($dataKontrol);
        }
    }
    return view('pages.dewan.solo', compact('partai', 'sudut'));
})->name('dewan.solo');


//route untuk kontrol dewan ganda
Route::get('/dewan/ganda/{id}/{sudut}', function ($id, $sudut) {
    // 1. Ambil data partai dan peserta (Eager Loading)
    $partai = \App\Models\Partai::with(['kelas', 'detail_peserta.peserta'])->findOrFail($id);

    $sudutDb = ucfirst($sudut);
    // Untuk Ganda, kita ambil id_detail pertama dari pasangan tersebut sebagai anchor nilai
    $aktif = $partai->detail_peserta->where('sudut', $sudutDb)->first();

    if ($aktif) {
        // --- LOGIKA 1: Auto-Generate Input Nilai Juri (6 Juri) ---
        $jumlahJuri = \Illuminate\Support\Facades\DB::table('input_nilai_juri')
            ->where('id_detail', $aktif->id_detail)
            ->count();

        if ($jumlahJuri < 6) {
            $dataJuri = [];
            for ($i = 1; $i <= 6; $i++) {
                $dataJuri[] = [
                    'id_detail' => $aktif->id_detail,
                    'no_juri' => $i,
                    'nilai_a' => 0.00,
                    'nilai_b' => 0.00,
                    'nilai_c' => 0.00,
                    'skor_akhir_juri' => 9.10, // Default Ganda (sama dengan Solo)
                ];
            }
            \Illuminate\Support\Facades\DB::table('input_nilai_juri')->insert($dataJuri);
        }

        // --- LOGIKA 2: Auto-Generate Kontrol Juri (6 Juri) ---
        // Kontrol juri biasanya per ID Partai agar Dewan bisa melihat status ke-6 juri secara global
        $jumlahKontrol = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
            ->where('id_partai', $id)
            ->count();

        if ($jumlahKontrol < 6) {
            $dataKontrol = [];
            for ($i = 1; $i <= 6; $i++) {
                $dataKontrol[] = [
                    'id_partai' => $id,
                    'no_juri' => $i,
                    'is_on' => 0,
                    'status_input' => 'Proses',
                ];
            }
            \Illuminate\Support\Facades\DB::table('status_kontrol_juri')->insert($dataKontrol);
        }
    }

    // Mengarahkan ke view dewan ganda
    return view('pages.dewan.ganda', compact('partai', 'sudut'));
})->name('dewan.ganda');