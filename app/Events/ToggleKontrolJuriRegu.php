<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class ToggleKontrolJuriRegu implements ShouldBroadcastNow
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

    public function __construct($partaiId, $noJuri, $isOn, $statusInput = 'Proses', $median = null, $deviasi = null, $totalNilai = null, $sudut = null, $hukuman = null, $isDisq = 0)
    {
        $this->partaiId = $partaiId;
        $this->noJuri = $noJuri;
        $this->isOn = $isOn;
        $this->statusInput = $statusInput;
        $this->median = $median;
        $this->deviasi = $deviasi;
        $this->totalNilai = $totalNilai;
        $this->sudut = $sudut;
        $this->hukuman = $hukuman;
        $this->isDisq = $isDisq;
    }

    public function broadcastOn()
    {
        return new Channel('partai.regu.' . $this->partaiId);
    }

    public function broadcastAs()
    {
        return 'ToggleKontrolJuriRegu';
    }
}