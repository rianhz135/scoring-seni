<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partai extends Model
{
    use HasFactory;

    // 1. Definisikan nama tabel (karena kita pakai nama tunggal 'partai')
    protected $table = 'partai';

    // 2. Tentukan Primary Key (default Laravel adalah 'id', jadi harus kita ubah)
    protected $primaryKey = 'id_partai';

    // 3. Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'id_kategori',
        'id_kelas',
        'no_partai',
        'babak',
        'gelanggang',
        'status_pertandingan',
        'timer_live'
    ];

    // --- RELASI ---
    
    public function detail_peserta()
    {
        // Relasi One-to-Many ke DetailPesertaTanding
        return $this->hasMany(DetailPesertaTanding::class, 'id_partai', 'id_partai');
    }

    public function kelas()
    {
        // Menghubungkan ke tabel ref_kelas
        return $this->belongsTo(RefKelas::class, 'id_kelas', 'id_kelas');
    }

    public function kategori()
    {
        // Menghubungkan ke tabel ref_kategori
        return $this->belongsTo(RefKategori::class, 'id_kategori', 'id_kategori');
    }
}