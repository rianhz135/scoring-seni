<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class ToggleKontrolJuri implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partaiId;
    public $noJuri;
    public $isOn;
    public $statusInput;
    public $median;
    public $deviasi;
    public $totalNilai;
    public $sudut;
    public $hukuman;
    public $isDisq;

    // Tambahkan 3 parameter null di akhir
    public function __construct($partaiId, $noJuri, $isOn, $statusInput, $median = null, $deviasi = null, $totalNilai = null, $sudut = null, $hukuman = 0, $isDisq = 0)
    {
        $this->partaiId = $partaiId;
        $this->noJuri = $noJuri;
        $this->isOn = $isOn;
        $this->statusInput = $statusInput;
        $this->hukuman = $hukuman;
        $this->isDisq = $isDisq;
        $this->median = $median;
        $this->deviasi = $deviasi;
        $this->totalNilai = $totalNilai;
        $this->sudut = $sudut;
    }

    public function broadcastOn()
    {
        return new Channel('partai.solo.' . $this->partaiId);
    }
}