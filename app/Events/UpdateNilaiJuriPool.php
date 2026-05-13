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
    public $skorAkhir;
    public $median;
    public $deviasi;
    public $totalNilai;
    public $jenis;

    public function __construct($partaiId, $noJuri, $skorAkhir, $median, $deviasi, $totalNilai, $jenis = 'solo')
    {
        $this->partaiId = $partaiId;
        $this->noJuri = $noJuri;
        $this->skorAkhir = $skorAkhir;
        $this->median = $median;
        $this->deviasi = $deviasi;
        $this->totalNilai = $totalNilai;
        $this->jenis = $jenis;
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