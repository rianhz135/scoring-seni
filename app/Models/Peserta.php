<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $table = 'peserta';
    protected $primaryKey = 'id_peserta';
    
    protected $fillable = ['nama_peserta', 'kontingen'];

    // Relasi ke Detail Tanding
    public function detail_tanding()
    {
        return $this->hasMany(DetailPesertaTanding::class, 'id_peserta', 'id_peserta');
    }
}