<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JuriTandingInputReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partaiId;
    public $noJuri;
    public $sudut;
    public $jenisSerangan;
    public $poinSah;
    public $scores;
    public $keterangan;
    public $babak;
    public $time;
    public $partnerNoJuri;
    public $juristDetails;

    public function __construct(
        $partaiId,
        $noJuri,
        $sudut,
        $jenisSerangan,
        $poinSah = false,
        $scores = null,
        $keterangan = null,
        $babak = 1,
        $time = null,
        $partnerNoJuri = null,
        $juristDetails = []
    ) {
        $this->partaiId = $partaiId;
        $this->noJuri = $noJuri;
        $this->sudut = $sudut;
        $this->jenisSerangan = $jenisSerangan;
        $this->poinSah = $poinSah;
        $this->scores = $scores;
        $this->keterangan = $keterangan;
        $this->babak = $babak;
        $this->time = $time ?: now()->format('H:i:s');
        $this->partnerNoJuri = $partnerNoJuri;
        $this->juristDetails = $juristDetails;
    }

    public function broadcastOn()
    {
        return new Channel('partai.tanding.' . $this->partaiId);
    }
}

