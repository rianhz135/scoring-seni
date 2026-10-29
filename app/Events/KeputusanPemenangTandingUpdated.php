<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KeputusanPemenangTandingUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partaiId;
    public $action; // 'show', 'close'
    public $pemenang; // 'Merah', 'Biru', 'Babak Tambahan', 'Undian', or null
    public $alasan;
    public $kriteriaTahap;
    public $pesertaPemenang;
    public $namaMerah;
    public $namaBiru;
    public $kontingenMerah;
    public $kontingenBiru;
    public $totalMerah;
    public $totalBiru;
    public $hukumanMerah;
    public $hukumanBiru;
    public $jatuhanMerah;
    public $jatuhanBiru;
    public $tendanganMerah;
    public $tendanganBiru;
    public $pukulanMerah;
    public $pukulanBiru;
    public $statusPemenang;

    public function __construct($partaiId, $action, array $payload = [])
    {
        $this->partaiId = $partaiId;
        $this->action = $action;
        $this->pemenang = $payload['pemenang'] ?? null;
        $this->alasan = $payload['alasan'] ?? '';
        $this->kriteriaTahap = $payload['kriteriaTahap'] ?? 1;
        $this->pesertaPemenang = $payload['pesertaPemenang'] ?? null;
        $this->namaMerah = $payload['namaMerah'] ?? 'SUDUT MERAH';
        $this->namaBiru = $payload['namaBiru'] ?? 'SUDUT BIRU';
        $this->kontingenMerah = $payload['kontingenMerah'] ?? '-';
        $this->kontingenBiru = $payload['kontingenBiru'] ?? '-';
        $this->totalMerah = $payload['totalMerah'] ?? 0;
        $this->totalBiru = $payload['totalBiru'] ?? 0;
        $this->hukumanMerah = $payload['hukumanMerah'] ?? 0;
        $this->hukumanBiru = $payload['hukumanBiru'] ?? 0;
        $this->jatuhanMerah = $payload['jatuhanMerah'] ?? 0;
        $this->jatuhanBiru = $payload['jatuhanBiru'] ?? 0;
        $this->tendanganMerah = $payload['tendanganMerah'] ?? 0;
        $this->tendanganBiru = $payload['tendanganBiru'] ?? 0;
        $this->pukulanMerah = $payload['pukulanMerah'] ?? 0;
        $this->pukulanBiru = $payload['pukulanBiru'] ?? 0;
        $this->statusPemenang = $payload['statusPemenang'] ?? ($action === 'show' ? 1 : 0);
    }

    public function broadcastOn()
    {
        return new Channel('partai.tanding.' . $this->partaiId);
    }
}

