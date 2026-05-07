<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPesertaTanding extends Model
{
    protected $table = 'detail_peserta_tanding';
    protected $primaryKey = 'id_detail';
    
    protected $fillable = [
        'id_partai', 'id_peserta', 'sudut', 'no_urut', 'nilai_median', 
        'nilai_deviasi', 'hukuman_ketua', 'total_nilai', 
        'waktu_tampil', 'is_timer_running', 'is_disqualifikasi', 'status_mulai', 'is_pemenang'
    ];

    // Relasi balik ke Partai
    public function partai()
    {
        return $this->belongsTo(Partai::class, 'id_partai', 'id_partai');
    }

    // Relasi ke data diri Peserta
    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'id_peserta', 'id_peserta');
    }
}