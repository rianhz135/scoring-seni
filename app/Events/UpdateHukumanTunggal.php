<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class UpdateHukumanTunggal implements ShouldBroadcastNow
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
        return new Channel('partai.tunggal.' . $this->partaiId);
    }

    // TAMBAHKAN INI: Agar di JS cukup panggil 'UpdateHukumanTunggal'
    public function broadcastAs()
    {
        return 'UpdateHukumanTunggal';
    }
}