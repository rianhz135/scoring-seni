<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ToggleKontrolJuriPool implements ShouldBroadcastNow
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
    public $jenis;
    public $noUrut; // <--- 1. TAMBAHKAN PROPERTI INI

    // 2. TAMBAHKAN $noUrut = 0 DI PALING BELAKANG
    public function __construct($partaiId, $noJuri, $isOn, $statusInput, $median = null, $deviasi = null, $totalNilai = null, $sudut = null, $hukuman = 0, $isDisq = 0, $jenis = 'solo', $noUrut = 0)
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
        $this->jenis = $jenis;
        $this->noUrut = $noUrut; // <--- 3. DEFINISIKAN DI SINI
    }

    public function broadcastOn()
    {
        return new Channel('partai.' . $this->jenis . '.' . $this->partaiId);
    }

    public function broadcastAs()
    {
        return 'ToggleKontrolJuriPool';
    }
}