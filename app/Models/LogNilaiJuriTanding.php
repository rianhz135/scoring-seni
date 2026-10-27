<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogNilaiJuriTanding extends Model
{
    protected $table = 'log_nilai_juri_tanding';
    protected $guarded = ['id'];

    public function partai()
    {
        return $this->belongsTo(PartaiTanding::class, 'id_partai', 'id');
    }

    public function poinSah()
    {
        return $this->belongsTo(PoinSahTanding::class, 'id_poin_sah', 'id');
    }
}
