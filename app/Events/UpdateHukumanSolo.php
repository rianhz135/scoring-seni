<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class UpdateHukumanSolo implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partaiId;
    public $hukuman;
    public $totalNilai;

    public function __construct($partaiId, $hukuman, $totalNilai)
    {
        $this->partaiId = $partaiId;
        $this->hukuman = $hukuman;
        $this->totalNilai = $totalNilai;
    }

    public function broadcastOn()
    {
        // Channel ini yang ditangkap oleh window.Echo di Layar Display
        return new Channel('partai.solo.' . $this->partaiId);
    }
}