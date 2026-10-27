<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifikasiDewan extends Model
{
    protected $table = 'verifikasi_dewan';
    protected $guarded = ['id'];

    public function partai()
    {
        return $this->belongsTo(PartaiTanding::class, 'id_partai', 'id');
    }

    public function jawabanJuri()
    {
        return $this->hasMany(JawabanVerifikasiJuri::class, 'id_verifikasi', 'id');
    }
}
