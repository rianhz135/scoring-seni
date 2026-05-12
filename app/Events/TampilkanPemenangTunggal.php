<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// WAJIB tambahkan 'implements ShouldBroadcastNow' agar langsung dikirim via WebSockets/Reverb
class TampilkanPemenangTunggal implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id_partai;
    public $winner;
    public $loser;
    public $action;

    public function __construct($id_partai, $winner, $loser, $action = 'show')
    {
        $this->id_partai = $id_partai;
        $this->winner = $winner;
        $this->loser = $loser;
        $this->action = $action; // Default-nya menampilkan
    }

    public function broadcastOn()
    {
        // Ubah menjadi channel global statis
        return new Channel('display-pengumuman-tunggal');
    }

    public function broadcastWith()
    {
        return [
            'action' => $this->action,
            'winner' => $this->winner,
            'loser' => $this->loser,
            'id_partai' => $this->id_partai,
        ];
    }
}