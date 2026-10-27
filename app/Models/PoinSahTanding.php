<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoinSahTanding extends Model
{
    protected $table = 'poin_sah_tanding';
    protected $guarded = ['id'];

    public function partai()
    {
        return $this->belongsTo(PartaiTanding::class, 'id_partai', 'id');
    }
}
