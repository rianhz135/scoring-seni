<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
// Import interface ini
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; 
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// TAMBAHKAN: implements ShouldBroadcastNow
class TimerPoolUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partaiId;
    public $waktu;
    public $statusRunning;
    public $jenis;

    public function __construct($partaiId, $waktu, $statusRunning, $jenis = 'solo')
    {
        $this->partaiId = $partaiId;
        $this->waktu = $waktu;
        $this->statusRunning = $statusRunning;
        $this->jenis = $jenis;
    }

    public function broadcastOn()
    {
        return new Channel('partai.' . $this->jenis . '.' . $this->partaiId);
    }

    /**
     * Nama Event yang akan didengar di JavaScript (Echo)
     * Jika tidak dibuat, Laravel akan menggunakan nama Class: 'TimerPoolUpdated'
     */
    public function broadcastAs()
    {
        return 'TimerPoolUpdated';
    }
}