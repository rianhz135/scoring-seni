<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusKontrolJuriTanding extends Model
{
    protected $table = 'status_kontrol_juri_tanding';
    protected $guarded = ['id'];

    public function partai()
    {
        return $this->belongsTo(PartaiTanding::class, 'id_partai', 'id');
    }
}
