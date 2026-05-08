<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class UpdateHukumanGanda implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partaiId;
    public $sudut;
    public $hukuman;
    public $totalNilai;

    public function __construct($partaiId, $sudut, $hukuman, $totalNilai)
    {
        $this->partaiId = $partaiId;
        $this->sudut = $sudut;
        $this->hukuman = $hukuman;
        $this->totalNilai = $totalNilai;
    }

    public function broadcastOn()
    {
        // Pastikan melempar ke channel ganda!
        return new Channel('partai.ganda.' . $this->partaiId);
    }
}