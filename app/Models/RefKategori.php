<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefKategori extends Model
{
    protected $table = 'ref_kategori'; // Pastikan nama tabelnya benar
    protected $primaryKey = 'id_kategori';
    protected $fillable = ['nama_kategori'];
}