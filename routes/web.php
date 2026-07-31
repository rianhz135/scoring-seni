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
use App\Http\Controllers\Admin\Peserta\TandingController;
use App\Http\Controllers\Admin\Manajemen\KelasController;


use App\Http\Controllers\Auth\LoginController;

Route::get('/login', [LoginController::class, 'index'])->name('login');

Route::post('/login', [LoginController::class, 'authenticate'])->name('login.process');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/dewan/display', function () {

    $partai = \App\Models\Partai::with(['kelas', 'detail_peserta.peserta'])
        ->whereHas('detail_peserta', function ($q) {
            $q->where('status_mulai', 1);
        })
        ->first();

    return view('pages.dewan.display', compact('partai'));

})->name('dewan.display');

Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/dewan', function () {
    return view('pages.dewan.index');
});
Route::prefix('admin')
    ->name('admin.')->group(function () {

        // --- 1. DASHBOARD & PROFILE ---
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');


        // ROUTE UNTUK LAYAR DISPLAY PROYEKTOR / PENONTON
        // Route::get('/dewan/display', function () {
        //     // Layar akan otomatis mencari partai yang SEDANG BERTANDING (status_mulai = 1)
        //     $partai = \App\Models\Partai::with(['kelas', 'detail_peserta.peserta'])
        //         ->whereHas('detail_peserta', function ($q) {
        //             $q->where('status_mulai', 1);
        //         })
        //         ->first(); // Mengambil 1 partai aktif
    
        //     return view('pages.dewan.display', compact('partai'));
        // })->name('dewan.display');
    
        Route::post('/dewan/solo/{id}/selesai/{sudut}', [App\Http\Controllers\Admin\Manajemen\SoloController::class, 'selesai'])->name('dewan.solo.selesai');
        Route::post('/dewan/ganda/{id}/selesai/{sudut}', [App\Http\Controllers\Admin\Manajemen\MatchGandaController::class, 'selesai'])->name('dewan.ganda.selesai');
        Route::post('/dewan/tunggal/{id}/selesai/{sudut}', [App\Http\Controllers\Admin\Manajemen\TunggalController::class, 'selesai'])->name('dewan.tunggal.selesai');
        Route::post('/dewan/regu/{id}/selesai/{sudut}', [App\Http\Controllers\Admin\Manajemen\ReguController::class, 'selesai'])->name('dewan.regu.selesai');
        Route::post('/dewan/pool/{id}/selesai/{id_detail}', [App\Http\Controllers\Admin\Dewan\PoolController::class, 'selesai'])->name('dewan.pool.selesai');
        // Hapus route refresh yang lama, ganti dengan 3 route ini:
        Route::post('/dewan/solo/refresh-display', [App\Http\Controllers\Admin\Manajemen\SoloController::class, 'triggerRefreshDisplay'])->name('dewan.solo.refresh');
        Route::post('/dewan/ganda/refresh-display', [App\Http\Controllers\Admin\Manajemen\MatchGandaController::class, 'triggerRefreshDisplay'])->name('dewan.ganda.refresh');
        Route::post('/dewan/tunggal/refresh-display', [App\Http\Controllers\Admin\Manajemen\TunggalController::class, 'triggerRefreshDisplay'])->name('dewan.tunggal.refresh');
        Route::post('/dewan/regu/refresh-display', [App\Http\Controllers\Admin\Manajemen\ReguController::class, 'triggerRefreshDisplay'])->name('dewan.regu.refresh');
        Route::post('/dewan/pool/refresh', [App\Http\Controllers\Admin\Dewan\PoolController::class, 'triggerRefreshDisplay'])->name('dewan.pool.refresh');

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

            Route::prefix('tanding')->name('tanding.')->group(function () {
                Route::get('/', [TandingController::class, 'index'])->name('index');
                Route::get('/create', [TandingController::class, 'create'])->name('create');
                Route::post('/store', [TandingController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [TandingController::class, 'edit'])->name('edit');
                Route::put('/{id}', [TandingController::class, 'update'])->name('update');
                Route::delete('/{id}', [TandingController::class, 'destroy'])->name('destroy');
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
            Route::get('/tunggal/mulai/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\TunggalController::class, 'mulai'])->name('tunggal.mulai');
            Route::post('/tunggal/hukuman/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\TunggalController::class, 'updateHukuman'])->name('tunggal.hukuman');
            Route::post('/tunggal/diskualifikasi/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\TunggalController::class, 'diskualifikasi'])->name('tunggal.diskualifikasi');
            Route::post('/tunggal/update-timer/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\TunggalController::class, 'updateTimer'])->name('tunggal.update-timer');
            Route::get('/tunggal/get-status/{id}/{sudut}', [\App\Http\Controllers\Admin\Manajemen\TunggalController::class, 'getStatus']);
            Route::get('/tunggal/get-juri-scores/{id}/{sudut}', [\App\Http\Controllers\Admin\Manajemen\TunggalController::class, 'getJuriScores']);
            Route::post('/tunggal/toggle-juri/{id_partai}/{no_juri}', [\App\Http\Controllers\Admin\Manajemen\TunggalController::class, 'toggleStatusJuriTunggal']);
            Route::post('/tunggal/toggle-status-input/{id_partai}/{no_juri}', [\App\Http\Controllers\Admin\Manajemen\TunggalController::class, 'toggleStatusInput']);

            Route::get('/regu', [App\Http\Controllers\Admin\Manajemen\ReguController::class, 'index'])->name('regu.index');
            Route::get('/regu/mulai/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\ReguController::class, 'mulai'])->name('regu.mulai');
            Route::post('/regu/hukuman/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\ReguController::class, 'updateHukuman'])->name('regu.hukuman');
            Route::post('/regu/diskualifikasi/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\ReguController::class, 'diskualifikasi'])->name('regu.diskualifikasi');
            Route::post('/regu/update-timer/{id}/{sudut}', [App\Http\Controllers\Admin\Manajemen\ReguController::class, 'updateTimer'])->name('regu.update-timer');
            Route::get('/regu/get-status/{id}/{sudut}', [\App\Http\Controllers\Admin\Manajemen\ReguController::class, 'getStatus']);
            Route::get('/regu/get-juri-scores/{id}/{sudut}', [\App\Http\Controllers\Admin\Manajemen\ReguController::class, 'getJuriScores']);
            Route::post('/regu/toggle-juri/{id_partai}/{no_juri}', [\App\Http\Controllers\Admin\Manajemen\ReguController::class, 'toggleStatusJuriRegu']);
            Route::post('/regu/toggle-status-input/{id_partai}/{no_juri}', [\App\Http\Controllers\Admin\Manajemen\ReguController::class, 'toggleStatusInput']);

            // Route untuk menjalankan partai pool dari halaman manajemen
            Route::post('/pool/{id_partai}/mulai/{id_detail}', [App\Http\Controllers\Admin\Manajemen\PoolManajemenController::class, 'mulai'])->name('pool.mulai');

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
            Route::get('/ganda/export-pdf', [App\Http\Controllers\Admin\Hasil\GandaController::class, 'exportPdf'])->name('ganda.export-pdf');
            Route::post('/ganda/{id}/tetapkan-pemenang', [App\Http\Controllers\Admin\Hasil\GandaController::class, 'tetapkanPemenang'])->name('ganda.tetapkan-pemenang');
            Route::post('/ganda/{id}/tutup-display', [App\Http\Controllers\Admin\Hasil\GandaController::class, 'tutupDisplay'])->name('ganda.tutup-display');

            Route::get('/tunggal', [App\Http\Controllers\Admin\Hasil\TunggalController::class, 'index'])->name('tunggal.index');
            Route::get('/tunggal/export-pdf', [App\Http\Controllers\Admin\Hasil\TunggalController::class, 'exportPdf'])->name('tunggal.export-pdf');
            Route::post('/tunggal/{id}/tetapkan-pemenang', [App\Http\Controllers\Admin\Hasil\TunggalController::class, 'tetapkanPemenang'])->name('tunggal.tetapkan-pemenang');
            Route::post('/tunggal/{id}/tutup-display', [App\Http\Controllers\Admin\Hasil\TunggalController::class, 'tutupDisplay'])->name('tunggal.tutup-display');


            Route::get('/regu', [App\Http\Controllers\Admin\Hasil\ReguController::class, 'index'])->name('regu.index');
            Route::get('/regu/export-pdf', [App\Http\Controllers\Admin\Hasil\ReguController::class, 'exportPdf'])->name('regu.export-pdf');
            Route::post('/regu/{id}/tetapkan-pemenang', [App\Http\Controllers\Admin\Hasil\ReguController::class, 'tetapkanPemenang'])->name('regu.tetapkan-pemenang');
            Route::post('/regu/{id}/tutup-display', [App\Http\Controllers\Admin\Hasil\ReguController::class, 'tutupDisplay'])->name('regu.tutup-display');

            Route::get('/pool', [App\Http\Controllers\Admin\Hasil\HasilPoolController::class, 'index'])->name('pool.index');
            Route::post('/pool/{id}/tetapkan-pemenang', [App\Http\Controllers\Admin\Hasil\HasilPoolController::class, 'tetapkanPemenang'])->name('pool.tetapkan');
            Route::post('/pool/{id}/tutup-display', [App\Http\Controllers\Admin\Hasil\HasilPoolController::class, 'tutupDisplay'])->name('pool.tutup');
            Route::get('/pool/export-pdf', [App\Http\Controllers\Admin\Hasil\HasilPoolController::class, 'exportPdf'])->name('pool.export-pdf');
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


Route::get('/juri/dashboard', function () {

    return view('pages.juri.dashboard');

})->name('juri.dashboard');


Route::post('/admin/reset/semua', [\App\Http\Controllers\Admin\ResetController::class, 'resetSemua'])->name('admin.reset.semua');
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

Route::get('/juri/tunggal/{no_juri}', function ($no_juri) {

    // 1. Cari partai TUNGGAL yang detailnya SEDANG BERTANDING (status_mulai = 1)
    $partai = \App\Models\Partai::with(['kelas', 'kategori', 'detail_peserta.peserta'])
        ->whereHas('kategori', function ($q) {
            $q->where('nama_kategori', 'LIKE', '%TUNGGAL%');
        })
        ->whereHas('detail_peserta', function ($q) {
            $q->where('status_mulai', 1);
        })->first();

    $aktif = null;
    $inputJuri = null; // Menggunakan nama inputJuri agar sesuai dengan file blade tunggal
    $sudut = null;
    $kontrol = null;

    if ($partai) {
        // 2. Ambil detail peserta yang sedang tampil
        $aktif = $partai->detail_peserta->where('status_mulai', 1)->first();
        $sudut = strtolower($aktif->sudut ?? '');

        // 3. Tarik nilai juri (Nilai A & Nilai B) dari database
        $inputJuri = \Illuminate\Support\Facades\DB::table('input_nilai_juri')
            ->where('id_detail', $aktif->id_detail)
            ->where('no_juri', $no_juri)
            ->first();

        // 4. Ambil data kontrol juri (ON/OFF) spesifik untuk juri ini
        $kontrol = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
            ->where('id_partai', $partai->id_partai)
            ->where('no_juri', $no_juri)
            ->first();
    }

    // Mengarah ke view tunggal yang baru saja didesain
    return view('pages.juri.tunggal', compact(
        'partai',
        'aktif',
        'sudut',
        'no_juri',
        'inputJuri',
        'kontrol'
    ));

})->name('juri.tunggal');

Route::get('/juri/regu/{no_juri}', function ($no_juri) {

    // 1. Cari partai TUNGGAL yang detailnya SEDANG BERTANDING (status_mulai = 1)
    $partai = \App\Models\Partai::with(['kelas', 'kategori', 'detail_peserta.peserta'])
        ->whereHas('kategori', function ($q) {
            $q->where('nama_kategori', 'LIKE', '%REGU%');
        })
        ->whereHas('detail_peserta', function ($q) {
            $q->where('status_mulai', 1);
        })->first();

    $aktif = null;
    $inputJuri = null; // Menggunakan nama inputJuri agar sesuai dengan file blade tunggal
    $sudut = null;
    $kontrol = null;

    if ($partai) {
        // 2. Ambil detail peserta yang sedang tampil
        $aktif = $partai->detail_peserta->where('status_mulai', 1)->first();
        $sudut = strtolower($aktif->sudut ?? '');

        // 3. Tarik nilai juri (Nilai A & Nilai B) dari database
        $inputJuri = \Illuminate\Support\Facades\DB::table('input_nilai_juri')
            ->where('id_detail', $aktif->id_detail)
            ->where('no_juri', $no_juri)
            ->first();

        // 4. Ambil data kontrol juri (ON/OFF) spesifik untuk juri ini
        $kontrol = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
            ->where('id_partai', $partai->id_partai)
            ->where('no_juri', $no_juri)
            ->first();
    }

    // Mengarah ke view tunggal yang baru saja didesain
    return view('pages.juri.regu', compact(
        'partai',
        'aktif',
        'sudut',
        'no_juri',
        'inputJuri',
        'kontrol'
    ));

})->name('juri.regu');

Route::get('/juri/solo-ganda/{no_juri}', function ($no_juri) {

    // 1. Cari partai SOLO atau GANDA yang detailnya SEDANG BERTANDING (status_mulai = 1)
    $partai = \App\Models\Partai::with(['kelas', 'kategori', 'detail_peserta.peserta'])
        ->whereHas('kategori', function ($q) {
            $q->where('nama_kategori', 'LIKE', '%SOLO%')
                ->orWhere('nama_kategori', 'LIKE', '%GANDA%');
        })
        ->whereHas('detail_peserta', function ($q) {
            $q->where('status_mulai', 1);
        })->first();

    $aktif = null;
    $nilai = null; // Variabel nilai untuk menampung skor dari database
    $sudut = null;
    $kontrol = null;

    if ($partai) {
        // 2. Ambil detail peserta yang sedang tampil (status_mulai = 1)
        $aktif = $partai->detail_peserta->where('status_mulai', 1)->first();

        // Meskipun sistem pool/netral, variabel sudut tetap diambil untuk kebutuhan identifikasi di DB jika ada
        $sudut = strtolower($aktif->sudut ?? '');

        // 3. Tarik nilai juri (Nilai A, B, C) dari database berdasarkan id_detail peserta yang aktif
        $nilai = \Illuminate\Support\Facades\DB::table('input_nilai_juri')
            ->where('id_detail', $aktif->id_detail)
            ->where('no_juri', $no_juri)
            ->first();

        // 4. Ambil data kontrol juri (ON/OFF & Status Input) spesifik untuk juri ini
        $kontrol = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
            ->where('id_partai', $partai->id_partai)
            ->where('no_juri', $no_juri)
            ->first();
    }

    // Mengarah ke view solo_ganda (Netral/Tanpa Warna Sudut Dominan)
    return view('pages.juri.solo_ganda', compact(
        'partai',
        'aktif',
        'sudut',
        'no_juri',
        'nilai',
        'kontrol'
    ));

})->name('juri.solo-ganda');
Route::get('/juri/tunggal-regu/{no_juri}', function ($no_juri) {

    // 1. Cari partai TUNGGAL atau REGU yang detailnya SEDANG BERTANDING (status_mulai = 1)
    $partai = \App\Models\Partai::with(['kelas', 'kategori', 'detail_peserta.peserta'])
        ->whereHas('kategori', function ($q) {
            $q->where('nama_kategori', 'LIKE', '%TUNGGAL%')
                ->orWhere('nama_kategori', 'LIKE', '%REGU%');
        })
        ->whereHas('detail_peserta', function ($q) {
            $q->where('status_mulai', 1);
        })->first();

    $aktif = null;
    $inputJuri = null; // Diubah menjadi inputJuri agar sinkron dengan file Blade tunggal_regu
    $sudut = null;
    $kontrol = null;

    if ($partai) {
        // 2. Ambil detail peserta yang sedang tampil (status_mulai = 1)
        $aktif = $partai->detail_peserta->where('status_mulai', 1)->first();

        // Meskipun sistem pool/netral, variabel sudut tetap diambil untuk kebutuhan DB
        $sudut = strtolower($aktif->sudut ?? '');

        // 3. Tarik nilai juri (Nilai A dan B) dari database berdasarkan id_detail peserta yang aktif
        $inputJuri = \Illuminate\Support\Facades\DB::table('input_nilai_juri')
            ->where('id_detail', $aktif->id_detail)
            ->where('no_juri', $no_juri)
            ->first();

        // 4. Ambil data kontrol juri (ON/OFF & Status Input) spesifik untuk juri ini
        $kontrol = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
            ->where('id_partai', $partai->id_partai)
            ->where('no_juri', $no_juri)
            ->first();
    }

    // Mengarah ke view tunggal_regu (Netral/Tanpa Warna Sudut Dominan)
    return view('pages.juri.tunggal_regu', compact(
        'partai',
        'aktif',
        'sudut',
        'no_juri',
        'inputJuri',
        'kontrol'
    ));

})->name('juri.tunggal-regu');

// Route Simpan Nilai (Tetap)
Route::post('/juri/solo/simpan/{id_detail}/{no_juri}', [\App\Http\Controllers\Admin\Manajemen\SoloController::class, 'simpanNilaiJuri'])->name('juri.solo.simpan');
Route::post('/juri/ganda/simpan/{id_detail}/{no_juri}', [\App\Http\Controllers\Admin\Manajemen\MatchGandaController::class, 'simpanNilaiJuri'])->name('juri.ganda.simpan');
Route::post('/juri/solo-ganda/simpan/{id_detail}/{no_juri}', [\App\Http\Controllers\Admin\Dewan\PoolController::class, 'simpanNilaiPool'])
    ->name('juri.solo-ganda.simpan');
Route::post('/juri/pool/tunggal-regu/input-nilai/{id_partai}/{no_juri}/{no_urut}', [App\Http\Controllers\Admin\Manajemen\PoolManajemenController::class, 'inputNilaiJuriTunggalRegu'])->name('juri.pool.tunggal-regu.input-nilai');
Route::post('/juri/pool/tunggal-regu/simpan-nilai/{id_partai}/{no_juri}/{no_urut}', [App\Http\Controllers\Admin\Manajemen\PoolManajemenController::class, 'simpanNilaiJuriTunggalRegu'])->name('juri.pool.tunggal-regu.simpan-nilai-final');

Route::post('/juri/tunggal/input-nilai/{id_partai}/{no_juri}/{sudut}', [App\Http\Controllers\Admin\Manajemen\TunggalController::class, 'inputNilaiJuri'])->name('juri.tunggal.input-nilai');
Route::post('/juri/regu/input-nilai/{id_partai}/{no_juri}/{sudut}', [App\Http\Controllers\Admin\Manajemen\ReguController::class, 'inputNilaiJuri'])->name('juri.regu.input-nilai');

Route::post('/juri/tunggal/simpan-nilai/{id_detail}/{no_juri}', [App\Http\Controllers\Admin\Manajemen\TunggalController::class, 'simpanNilaiJuri'])->name('juri.tunggal.simpan-nilai-final');
Route::post('/juri/tunggal/selesai/{id_partai}/{sudut}', [TunggalController::class, 'selesai'])->name('juri.tunggal.selesai');
Route::post('/juri/regu/simpan-nilai/{id_detail}/{no_juri}', [\App\Http\Controllers\Admin\Manajemen\ReguController::class, 'simpanNilaiJuri'])->name('juri.regu.simpan-nilai-final');

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

Route::get('/dewan/tunggal/{id}/{sudut}', function ($id, $sudut) {
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
                    'skor_akhir_juri' => 9.90, // Nilai default solo
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
    return view('pages.dewan.tunggal', compact('partai', 'sudut'));
})->name('dewan.tunggal');

Route::get('/dewan/regu/{id}/{sudut}', function ($id, $sudut) {
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
                    'skor_akhir_juri' => 9.90, // Nilai default solo
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
    return view('pages.dewan.regu', compact('partai', 'sudut'));
})->name('dewan.regu');

Route::prefix('dewan')->name('dewan.')->group(function () {
    // Rute Pool Baru (Tanpa parameter {sudut})
    Route::get('/pool/{id_partai}', [\App\Http\Controllers\Admin\Dewan\PoolController::class, 'index'])->name('pool.index');
    // 2. Rute Diskualifikasi (Tambahkan ini)
    Route::post('/pool/{id}/diskualifikasi/{id_detail}', [\App\Http\Controllers\Admin\Dewan\PoolController::class, 'diskualifikasi'])->name('pool.diskualifikasi');

    // 3. Rute Selesai (Tambahkan ini)
    Route::post('/pool/{id}/selesai/{id_detail}', [\App\Http\Controllers\Admin\Dewan\PoolController::class, 'selesai'])->name('pool.selesai');

    Route::post('/pool/update-hukuman', [\App\Http\Controllers\Admin\Dewan\PoolController::class, 'updateHukuman'])->name('pool.update-hukuman');
    Route::post('/pool/{id}/diskualifikasi/{id_detail}', [App\Http\Controllers\Admin\Dewan\PoolController::class, 'diskualifikasi'])
        ->name('pool.diskualifikasi');
    Route::post('/pool/update-timer/{id}/{id_detail}', [\App\Http\Controllers\Admin\Dewan\PoolController::class, 'updateTimer'])->name('pool.update-timer');
    Route::post('/pool/hukuman/{id}/{id_detail}', [App\Http\Controllers\Admin\Dewan\PoolController::class, 'updateHukuman'])->name('pool.hukuman');
    Route::post('/pool/toggle-juri/{id_partai}/{no_juri}', [\App\Http\Controllers\Admin\Dewan\PoolController::class, 'toggleJuri'])
        ->name('pool.toggle-juri');
    Route::post('/pool/toggle-status-input/{id_partai}/{no_juri}', [\App\Http\Controllers\Admin\Dewan\PoolController::class, 'toggleStatusInput'])
        ->name('pool.toggle-status-input');
});

