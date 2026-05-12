<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class UpdateHukumanRegu implements ShouldBroadcastNow
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
        return new Channel('partai.regu.' . $this->partaiId);
    }

    // TAMBAHKAN INI: Agar di JS cukup panggil 'UpdateHukumanRegu'
    public function broadcastAs()
    {
        return 'UpdateHukumanRegu';
    }
}