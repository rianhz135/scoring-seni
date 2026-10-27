<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelanggaranTanding extends Model
{
    protected $table = 'pelanggaran_tanding';
    protected $guarded = ['id'];

    public function partai()
    {
        return $this->belongsTo(PartaiTanding::class, 'id_partai', 'id');
    }
}
