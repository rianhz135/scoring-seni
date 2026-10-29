<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ToggleKontrolJuriTanding implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partaiId;
    public $noJuri;
    public $isActive;
    public $status;

    public function __construct($partaiId, $noJuri, $isActive, $status = 'Active')
    {
        $this->partaiId = $partaiId;
        $this->noJuri = $noJuri;
        $this->isActive = $isActive;
        $this->status = $status;
    }

    public function broadcastOn()
    {
        return new Channel('partai.tanding.' . $this->partaiId);
    }
}

