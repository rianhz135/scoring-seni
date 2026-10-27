<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JatuhanTanding extends Model
{
    protected $table = 'jatuhan_tanding';
    protected $guarded = ['id'];

    public function partai()
    {
        return $this->belongsTo(PartaiTanding::class, 'id_partai', 'id');
    }
}
