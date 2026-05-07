<?php

namespace App\Http\Controllers\Admin\Manajemen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partai;

class PoolManajemenController extends Controller
{
    public function index()
    {
        $data_pool = Partai::with(['kategori', 'kelas', 'detail_peserta.peserta'])
            ->whereHas('detail_peserta', function ($q) {
                $q->whereNull('sudut');
            })
            ->orderBy('no_partai', 'asc')
            ->paginate(10);

        return view('pages.manajemen.pool.index', compact('data_pool'));
    }
}
