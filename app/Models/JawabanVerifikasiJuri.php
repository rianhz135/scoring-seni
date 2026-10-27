<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanVerifikasiJuri extends Model
{
    protected $table = 'jawaban_verifikasi_juri';
    protected $guarded = ['id'];

    public function verifikasi()
    {
        return $this->belongsTo(VerifikasiDewan::class, 'id_verifikasi', 'id');
    }
}
