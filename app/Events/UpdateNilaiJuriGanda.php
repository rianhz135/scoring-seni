<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class UpdateNilaiJuriGanda implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partaiId;
    public $noJuri;
    public $nilaiA, $nilaiB, $nilaiC, $skorAkhir;
    public $median, $deviasi, $totalNilai;

    public function __construct($partaiId, $noJuri, $nilaiA, $nilaiB, $nilaiC, $skorAkhir, $median, $deviasi, $totalNilai)
    {
        $this->partaiId = $partaiId;
        $this->noJuri = $noJuri;
        $this->nilaiA = $nilaiA;
        $this->nilaiB = $nilaiB;
        $this->nilaiC = $nilaiC;
        $this->skorAkhir = $skorAkhir;
        $this->median = $median;
        $this->deviasi = $deviasi;
        $this->totalNilai = $totalNilai;
    }

    public function broadcastOn()
    {
        // KUNCI UTAMA: Ubah channel menjadi ganda
        return new Channel('partai.ganda.' . $this->partaiId);
    }
}