<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class UpdateNilaiJuriTunggal implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partaiId, $noJuri, $nilaiA, $nilaiB, $skorAkhir, $median, $deviasi, $totalNilai;

    public function __construct($partaiId, $noJuri, $nilaiA, $nilaiB, $skorAkhir, $median = null, $deviasi = null, $totalNilai = null)
    {
        $this->partaiId = $partaiId;
        $this->noJuri = $noJuri;
        $this->nilaiA = $nilaiA;
        $this->nilaiB = $nilaiB;
        $this->skorAkhir = $skorAkhir;
        $this->median = $median;
        $this->deviasi = $deviasi;
        $this->totalNilai = $totalNilai;
    }

    // INI KUNCINYA: Memaksa nama variabel agar tidak tertukar di JavaScript
    // Tambahkan ini di dalam class UpdateNilaiJuriTunggal
    public function broadcastWith()
    {
        return [
            'partaiId' => $this->partaiId,
            'noJuri' => (int) $this->noJuri,
            'nilaiA' => (float) $this->nilaiA,
            'nilaiB' => (float) $this->nilaiB,
            'skorAkhir' => (float) $this->skorAkhir,
            'median' => (float) $this->median,
            'deviasi' => (float) $this->deviasi,
            'totalNilai' => (float) $this->totalNilai,
        ];
    }

    public function broadcastOn()
    {
        return new Channel('partai.tunggal.' . $this->partaiId);
    }

    public function broadcastAs()
    {
        return 'UpdateNilaiJuriTunggal';
    }
}