<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimerTandingUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partaiId;
    public $waktu;
    public $statusTimer;
    public $babakAktif;

    public function __construct($partaiId, $waktu, $statusTimer, $babakAktif = 1)
    {
        $this->partaiId = $partaiId;
        $this->waktu = $waktu;
        $this->statusTimer = $statusTimer;
        $this->babakAktif = $babakAktif;
    }

    public function broadcastOn()
    {
        return new Channel('partai.tanding.' . $this->partaiId);
    }
}

