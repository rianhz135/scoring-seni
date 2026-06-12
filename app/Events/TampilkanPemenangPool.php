<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TampilkanPemenangPool implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id_partai;
    public $peserta_ranked; // Array klasemen peserta
    public $action;

    public function __construct($id_partai, $peserta_ranked, $action = 'show')
    {
        $this->id_partai = $id_partai;
        $this->peserta_ranked = $peserta_ranked;
        $this->action = $action;
    }

    public function broadcastOn()
    {
        // Saluran khusus untuk display pool
        return new Channel('display-pengumuman-pool');
    }

    public function broadcastAs()
    {
        return 'TampilkanPemenangPool';
    }
}