<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateHukumanPool implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partaiId;
    public $idDetail;
    public $hukuman;
    public $totalNilai;
    public $jenis; // solo, ganda, regu, tunggal

    public function __construct($partaiId, $idDetail, $hukuman, $totalNilai, $jenis = 'solo')
    {
        $this->partaiId = $partaiId;
        $this->idDetail = $idDetail;
        $this->hukuman = $hukuman;
        $this->totalNilai = $totalNilai;
        $this->jenis = $jenis;
    }

    public function broadcastOn()
    {
        return new Channel('partai.' . $this->jenis . '.' . $this->partaiId);
    }

    public function broadcastAs()
    {
        return 'UpdateHukumanPool';
    }
}