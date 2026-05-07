<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class RefreshDisplay implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $gelanggang;

    public function __construct($gelanggang = 'A')
    {
        $this->gelanggang = $gelanggang;
    }

    public function broadcastOn()
    {
        // Channel global untuk seluruh gelanggang (Bisa disesuaikan jika ada Gelanggang B, C, dst)
        return new Channel('gelanggang.global.' . $this->gelanggang);
    }
}