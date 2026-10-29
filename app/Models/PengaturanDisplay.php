<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanDisplay extends Model
{
    protected $table = 'pengaturan_display';

    protected $fillable = [
        'nama_kejuaraan',
        'gelanggang',
        'logo_kiri',
        'logo_kanan',
    ];

    protected static $cachedSetting = null;

    /**
     * Mengambil instance setting display tunggal.
     */
    public static function getSetting()
    {
        if (static::$cachedSetting !== null) {
            return static::$cachedSetting;
        }

        return static::$cachedSetting = static::firstOrCreate(
            ['id' => 1],
            [
                'nama_kejuaraan' => 'PERTANDINGAN SMI CUP 2026',
                'gelanggang' => 'GELANGGANG 1',
                'logo_kiri' => 'smi.png',
                'logo_kanan' => null,
            ]
        );
    }
}

