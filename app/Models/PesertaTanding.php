<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaTanding extends Model
{
    // Beri tahu Laravel nama tabel aslinya
    protected $table = 'peserta_tanding';

    // Izinkan semua kolom untuk diisi (Mass Assignment)
    protected $guarded = ['id'];

    // (Opsional) Relasi balik ke Partai
    public function partaiMerah()
    {
        return $this->hasMany(PartaiTanding::class, 'id_merah', 'id');
    }

    public function partaiBiru()
    {
        return $this->hasMany(PartaiTanding::class, 'id_biru', 'id');
    }

    public function partaiMenang()
    {
        return $this->hasMany(PartaiTanding::class, 'id_pemenang', 'id');
    }
}