<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VerifikasiTandingUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partaiId;
    public $action; // 'start', 'vote', 'close'
    public $verifikasiId;
    public $keterangan;
    public $babak;
    public $status;
    public $noJuri;
    public $pilihan;
    public $jawaban; // [ 1 => 'Biru', 2 => null, 3 => 'Merah' ]
    public $hasil; // 'Biru', 'Merah', 'Tidak Valid', or null
    public $keputusan;
    public $pesertaBiru;
    public $pesertaMerah;

    public function __construct($partaiId, $action, array $payload = [])
    {
        $this->partaiId = $partaiId;
        $this->action = $action;
        $this->verifikasiId = $payload['verifikasiId'] ?? null;
        $this->keterangan = $payload['keterangan'] ?? 'Verifikasi Juri (VAR)';
        $this->babak = $payload['babak'] ?? 1;
        $this->status = $payload['status'] ?? 'Aktif';
        $this->noJuri = $payload['noJuri'] ?? null;
        $this->pilihan = $payload['pilihan'] ?? null;
        $this->jawaban = $payload['jawaban'] ?? [];
        $this->hasil = $payload['hasil'] ?? null;
        $this->keputusan = $payload['keputusan'] ?? null;
        $this->pesertaBiru = $payload['pesertaBiru'] ?? null;
        $this->pesertaMerah = $payload['pesertaMerah'] ?? null;
    }

    public function broadcastOn()
    {
        return new Channel('partai.tanding.' . $this->partaiId);
    }
}

