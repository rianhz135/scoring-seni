<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // WAJIB PAKAI INI AGAR INSTAN
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimerTunggalUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partaiId;
    public $sudut;
    public $waktu;
    public $statusRunning;

    // Data yang dikirim dari Controller ditangkap di sini
    public function __construct($partaiId, $sudut, $waktu, $statusRunning)
    {
        $this->partaiId = $partaiId;
        $this->sudut = $sudut;
        $this->waktu = $waktu;
        $this->statusRunning = $statusRunning;
    }

    // Tentukan "Channel" radio yang akan memancarkan data ini
    public function broadcastOn()
    {
        // Channel bersifat publik (bisa diakses layar display tanpa login)
        return new Channel('partai.tunggal.' . $this->partaiId);
    }

    public function broadcastAs()
    {
        return 'TimerTunggalUpdated';
    }
}