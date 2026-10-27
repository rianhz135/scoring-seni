<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartaiTanding extends Model
{
    // Beri tahu Laravel nama tabel aslinya
    protected $table = 'partai_tanding';

    // Izinkan semua kolom untuk diisi
    protected $guarded = ['id'];

    public function pesertaBiru()
    {
        return $this->belongsTo(PesertaTanding::class, 'id_biru', 'id');
    }

    public function pesertaMerah()
    {
        return $this->belongsTo(PesertaTanding::class, 'id_merah', 'id');
    }

    public function pemenang()
    {
        return $this->belongsTo(PesertaTanding::class, 'id_pemenang', 'id');
    }

    public function statusJuri()
    {
        return $this->hasMany(StatusKontrolJuriTanding::class, 'id_partai', 'id');
    }

    public function poinSah()
    {
        return $this->hasMany(PoinSahTanding::class, 'id_partai', 'id');
    }

    public function logNilaiJuri()
    {
        return $this->hasMany(LogNilaiJuriTanding::class, 'id_partai', 'id');
    }

    public function pelanggaran()
    {
        return $this->hasMany(PelanggaranTanding::class, 'id_partai', 'id');
    }

    public function jatuhan()
    {
        return $this->hasMany(JatuhanTanding::class, 'id_partai', 'id');
    }

    public function verifikasi()
    {
        return $this->hasMany(VerifikasiDewan::class, 'id_partai', 'id');
    }
}