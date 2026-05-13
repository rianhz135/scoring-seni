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

    // 10 Variabel standar agar cocok dengan fungsi prosesKontrolJuri di JS Display
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
    
    // Variabel tambahan untuk menentukan tujuan channel (solo/ganda/regu)
    public $jenis;

    /**
     * Create a new event instance.
     */
    public function __construct(
        $partaiId, 
        $noJuri, 
        $isOn, 
        $statusInput = null, 
        $median = null, 
        $deviasi = null, 
        $totalNilai = null, 
        $sudut = null, 
        $hukuman = null, 
        $isDisq = null, 
        $jenis = 'solo'
    ) {
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
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        // Mengirim secara dinamis ke partai.solo.ID atau partai.ganda.ID, dst.
        return new Channel('partai.' . $this->jenis . '.' . $this->partaiId);
    }

    /**
     * Nama Event yang akan didengar oleh Laravel Echo dengan awalan titik (.)
     */
    public function broadcastAs()
    {
        return 'ToggleKontrolJuriPool';
    }
}