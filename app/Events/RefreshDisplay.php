<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefreshDisplay implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $gelanggang;

    public function __construct($gelanggang)
    {
        // Akan menerima 'tunggal.A', 'ganda.A', atau 'solo.A'
        $this->gelanggang = $gelanggang;
    }

    public function broadcastOn()
    {
        // Hasil akhir misal: gelanggang.global.tunggal.A
        return new Channel('gelanggang.global.' . $this->gelanggang);
    }
}