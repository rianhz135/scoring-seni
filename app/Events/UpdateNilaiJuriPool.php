<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateNilaiJuriPool implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partaiId;
    public $noJuri;
    public $nilaiA, $nilaiB, $nilaiC, $skorAkhir;
    public $median, $deviasi, $totalNilai;
    public $jenis;
    public $noUrut; // Tambahkan variabel di sini

    // PERHATIKAN: $noUrut diletakkan PALING BELAKANG dengan nilai default = 0
    public function __construct($partaiId, $noJuri, $nilaiA, $nilaiB, $nilaiC, $skorAkhir, $median, $deviasi, $totalNilai, $jenis = 'solo', $noUrut = 0)
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
        $this->jenis = $jenis;
        $this->noUrut = $noUrut; // Definisikan di sini
    }

    public function broadcastOn()
    {
        return new Channel('partai.' . $this->jenis . '.' . $this->partaiId);
    }

    public function broadcastAs()
    {
        return 'UpdateNilaiJuriPool';
    }
}