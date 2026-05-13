<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class UpdateDisqPool implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partaiId;
    public $idDetail;
    public $isDisq;
    public $totalNilai;
    public $jenis; // Tambahkan properti ini

    public function __construct($partaiId, $idDetail, $isDisq, $totalNilai, $jenis = 'solo')
    {
        $this->partaiId = $partaiId;
        $this->idDetail = $idDetail;
        $this->isDisq = $isDisq;
        $this->totalNilai = $totalNilai;
        $this->jenis = $jenis; // misal: 'solo', 'ganda', 'regu'
    }

    public function broadcastOn()
    {
        return new Channel('partai.' . $this->jenis . '.' . $this->partaiId);
    }
}