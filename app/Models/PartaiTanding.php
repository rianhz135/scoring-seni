<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartaiTanding extends Model
{
    // Beri tahu Laravel nama tabel aslinya
    protected $table = 'partai_tanding';

    // Izinkan semua kolom untuk diisi
    protected $guarded = ['id'];

    public function pesertaBiru()
    {
        return $this->belongsTo(PesertaTanding::class, 'id_biru', 'id');
    }

    public function pesertaMerah()
    {
        return $this->belongsTo(PesertaTanding::class, 'id_merah', 'id');
    }
}