<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefKelas extends Model
{
    // Nama tabel di database
    protected $table = 'ref_kelas';

    // Primary key-nya (karena di ERD kamu pakai id_kelas)
    protected $primaryKey = 'id_kelas';

    // Kolom yang boleh diisi
    protected $fillable = ['label_kelas'];
}