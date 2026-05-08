@extends('layouts.dewan')

@section('content')
    <style>
        body {
            background-color: #0f172a;
            /* Slate 900 */
        }
    </style>

    {{-- CEK APAKAH ADA PARTAI YANG SEDANG BERTANDING --}}
    @if (!$partai)
        {{-- TAMPILAN MODE STANDBY (ISTIRAHAT / BELUM MULAI) --}}
        <div class="h-[95vh] flex flex-col items-center justify-center">
            <div class="animate-pulse bg-slate-800/80 border border-slate-700 rounded-3xl p-12 text-center shadow-2xl">
                <h1 class="text-6xl font-black text-slate-300 tracking-widest mb-4">MODE STANDBY</h1>
                <p class="text-2xl text-slate-500 uppercase tracking-widest">Menunggu Pertandingan Dimulai oleh Operator</p>
            </div>
        </div>
    @else
        {{-- JIKA ADA YANG BERTANDING, JALANKAN LOGIKA INI --}}
        @php
            // Cari sudut yang status_mulai-nya 1 (sedang bertanding).
            $aktif = $partai->detail_peserta->where('status_mulai', 1)->first() ?? $partai->detail_peserta->first();
            // Tentukan tema warna
            $isMerah = $aktif && strtolower($aktif->sudut) == 'merah';
            $bgWarna = $isMerah ? 'bg-red-600' : 'bg-blue-600';
            $teksWarna = $isMerah ? 'text-red-100' : 'text-blue-100';
        @endphp
        <div class="h-[95vh] flex flex-col gap-6">
            {{-- HEADER: INFO PARTAI & TIMER --}}
            <div class="flex justify-between items-stretch gap-6 h-32">
                <div
                    class="bg-slate-800/80 border border-slate-700 rounded-3xl p-6 flex flex-col justify-center flex-1 shadow-lg">
                    <div class="flex items-center gap-3 mb-2">
                        <span
                            class="px-4 py-1.5 bg-primary-600 text-white text-2xl font-black uppercase rounded-lg tracking-[0.2em]">
                            GELANGGANG {{ $partai->gelanggang ?? 'A' }}
                        </span>
                        <span
                            class="px-4 py-1.5 bg-slate-700 text-slate-300 text-2xl font-black uppercase rounded-lg tracking-[0.2em]">
                            PARTAI #{{ $partai->no_partai }}
                        </span>
                        <span
                            class="px-4 py-1.5 bg-slate-700 text-slate-300 text-2xl font-black uppercase rounded-lg tracking-[0.2em] border border-slate-600">
                            {{ $partai->kategori->nama_kategori ?? 'KATEGORI KOSONG' }}
                        </span>
                        <span
                            class="px-4 py-1.5 bg-slate-700 text-slate-300 text-2xl font-black uppercase rounded-lg tracking-[0.2em]">
                            {{ $partai->kelas->nama_kelas ?? ($partai->kelas->label_kelas ?? 'KATEGORI SENI') }}
                        </span>
                    </div>
                </div>
                {{-- Bagian Timer di Display --}}
                <div
                    class="bg-slate-800 border border-slate-700 rounded-3xl p-8 flex flex-col items-center justify-center shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 w-full h-1 bg-primary-500"></div>
                    <div class="text-8xl font-black text-white font-mono tracking-tighter tabular-nums" id="display-timer"
                        data-seconds="{{ $aktif->waktu_tampil ?? 0 }}" data-running="{{ $aktif->is_timer_running ?? 0 }}">
                        00:00
                    </div>
                </div>
            </div>

            {{-- IDENTITAS ATLET (SUDUT BIRU / MERAH) --}}
            <div
                class="{{ $bgWarna }} rounded-3xl p-8 flex justify-between items-center shadow-xl relative overflow-hidden transition-colors duration-500">
                <div class="absolute top-0 right-0 w-64 h-full bg-white opacity-10 skew-x-12 translate-x-10"></div>
                <div class="relative z-10">
                    <div
                        class="inline-block px-4 py-1 bg-black/20 backdrop-blur-sm text-white text-sm font-black uppercase tracking-[0.3em] rounded-lg mb-3">
                        SUDUT {{ strtoupper($aktif->sudut ?? 'BIRU') }}
                    </div>
                    <h2 class="text-4xl font-black text-white uppercase tracking-tight mb-2">
                        {{ $aktif->peserta->kontingen ?? '-' }}
                    </h2>

                    {{-- PERBAIKAN: LOOPING NAMA PESERTA UNTUK GANDA/REGU --}}
                    <p class="text-3xl font-bold {{ $teksWarna }} uppercase tracking-wide">
                        @php
                            // Ambil seluruh peserta di sudut dan partai yang sama
                            $anggotaTim = $partai->detail_peserta->where('sudut', $aktif->sudut);
                        @endphp

                        @foreach ($anggotaTim as $anggota)
                            {{ $anggota->peserta->nama_peserta ?? ($anggota->peserta->nama ?? 'NAMA PESERTA KOSONG') }}
                            @if (!$loop->last)
                                &
                            @endif
                        @endforeach
                    </p>
                </div>
            </div>

            {{-- MENGAMBIL DATA JURI DARI DATABASE (SAMA PERSIS SEPERTI DEWAN) --}}
            @php
                $juriDb = \Illuminate\Support\Facades\DB::table('input_nilai_juri')
                    ->where('id_detail', $aktif->id_detail ?? 0)
                    ->orderBy('no_juri', 'asc')
                    ->get()
                    ->keyBy('no_juri');

                // Deteksi Otomatis untuk Nilai Dasar (Tunggal/Regu = 9.90, Solo/Ganda = 9.10)
                $namaKategori = strtolower($partai->kelas->nama_kelas ?? ($partai->kelas->label_kelas ?? ''));
                if (str_contains($namaKategori, 'tunggal') || str_contains($namaKategori, 'regu')) {
                    $baseScore = 9.9;
                } else {
                    $baseScore = 9.1;
                }
            @endphp

            {{-- NILAI JURI 1 - 6 --}}
            <div class="flex w-full gap-4 transition-all duration-500">
                @php
                    // MENGAMBIL STATUS KONTROL JURI (ON/OFF) LANGSUNG DARI DATABASE
                    $kontrolDb = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                        ->where('id_partai', $partai->id_partai ?? 0)
                        ->get()
                        ->keyBy('no_juri');
                @endphp

                @for ($i = 1; $i <= 6; $i++)
                    @php
                        // Cek apakah kotak juri harus disembunyikan (ON/OFF)
                        // Jika is_on = 0 (OFF), maka tambahkan class 'hidden'
                        $isOn = isset($kontrolDb[$i]) ? $kontrolDb[$i]->is_on : 1;
                        $isHidden = $isOn == 0 ? 'hidden' : '';

                        // RUMUS KALKULASI PERSIS SEPERTI HALAMAN DEWAN
                        $nilaiA = $juriDb[$i]->nilai_a ?? 0;
                        $nilaiB = $juriDb[$i]->nilai_b ?? 0;
                        $nilaiC = $juriDb[$i]->nilai_c ?? 0;

                        $totalNilaiJuri = $baseScore + $nilaiA + $nilaiB + $nilaiC;
                    @endphp

                    <div id="box-juri-{{ $i }}"
                        class="flex-1 bg-slate-800 border border-slate-700 rounded-2xl flex flex-col overflow-hidden transition-all duration-300 {{ $isHidden }}">
                        <div class="bg-slate-700/50 py-3 text-center border-b border-slate-700">
                            <span class="text-2xl font-black text-slate-300 uppercase tracking-widest">JURI
                                {{ $i }}</span>
                        </div>
                        <div class="py-6 flex items-center justify-center">
                            <span id="nilai-juri-{{ $i }}" class="text-5xl font-black text-white">
                                {{ number_format((float) $totalNilaiJuri, 2, '.', '') }}
                            </span>
                        </div>
                    </div>
                @endfor
            </div>

            {{-- AKUMULASI (MEDIAN, HUKUMAN, DEVIASI, TOTAL) --}}
            <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 flex flex-col gap-4">
                    <div class="flex-1 grid grid-cols-2 gap-4">
                        <div class="bg-slate-800 border border-slate-700 rounded-3xl flex flex-col overflow-hidden">
                            <div class="bg-slate-700/50 py-3 text-center border-b border-slate-700">
                                <span class="text-2xl font-black text-slate-300 uppercase tracking-widest">Median</span>
                            </div>
                            <div class="flex-1 flex items-center justify-center">
                                {{-- Mengambil nilai awal dari database --}}
                                <span id="nilai-median" class="text-8xl font-black text-white">
                                    {{ number_format($aktif->nilai_median ?? 0, 3, '.', '') }}
                                </span>
                            </div>
                        </div>
                        <div
                            class="bg-slate-800 border border-slate-700 rounded-3xl flex flex-col overflow-hidden relative">
                            <div class="absolute top-0 w-full h-1 bg-red-500"></div>
                            <div class="bg-slate-700/50 py-3 text-center border-b border-slate-700">
                                <span class="text-2xl font-black text-slate-300 uppercase tracking-widest">Hukuman</span>
                            </div>
                            <div class="flex-1 flex items-center justify-center">
                                @php
                                    // TANGKAP SEMUA KEMUNGKINAN NAMA KOLOM HUKUMAN
                                    $nilaiHukumanDb =
                                        $aktif->hukuman_ketua ?? ($aktif->hukuman ?? ($aktif->nilai_hukuman ?? 0));
                                @endphp
                                <span id="nilai-hukuman" class="text-8xl font-black text-red-500">
                                    {{-- Menggunakan abs() agar jika di database sudah minus, tidak jadi dobel minus (--0.50) --}}
                                    -{{ number_format(abs((float) $nilaiHukumanDb), 2, '.', '') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 bg-slate-800 border border-slate-700 rounded-3xl flex flex-col overflow-hidden">
                        <div class="bg-slate-700/50 py-3 text-center border-b border-slate-700">
                            <span class="text-2xl font-black text-slate-300 uppercase tracking-widest">Standar
                                Deviasi</span>
                        </div>
                        <div class="flex-1 flex items-center justify-center">
                            {{-- TANGKAP KEMUNGKINAN NAMA KOLOM DEVIASI --}}
                            <span id="nilai-deviasi" class="text-7xl font-black text-white font-mono tracking-widest">
                                {{ number_format((float) ($aktif->deviasi ?? ($aktif->standar_deviasi ?? ($aktif->nilai_deviasi ?? 0))), 15, '.', '') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-slate-800 border border-slate-700 rounded-3xl flex flex-col overflow-hidden shadow-2xl relative">
                    <div class="absolute top-0 w-full h-2 bg-green-400"></div>
                    <div class="bg-slate-700/50 py-5 text-center border-b border-slate-700">
                        <span class="text-2xl font-black text-slate-300 uppercase tracking-[0.3em]">Total Nilai Akhir</span>
                    </div>
                    <div
                        class="flex-1 flex items-center justify-center bg-slate-900/50 min-h-[250px] w-full overflow-hidden">
                        @php
                            // Cara paling aman membaca kolom DB di Laravel (bahkan jika ada typo nama kolom)
                            $statusDisq = $aktif->is_disqualifikasi ?? ($aktif->is_disqulifikasi ?? 0);
                            $sedangDisq = $statusDisq == 1;
                        @endphp

                        <span id="nilai-total"
                            class="block w-full px-2 text-center font-black tracking-tighter leading-none drop-shadow-2xl transition-all duration-300 {{ $sedangDisq ? 'text-4xl sm:text-5xl md:text-6xl lg:text-[5rem] text-red-500' : 'text-[10rem] text-green-400 shadow-green-400/20' }}">
                            {{ $sedangDisq ? 'DISKUALIFIKASI' : number_format($aktif->total_nilai ?? 0, 3, '.', '') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <script>
            let localSeconds = parseInt(document.getElementById('display-timer')?.getAttribute('data-seconds')) || 0;
            let isTimerRunning = (document.getElementById('display-timer')?.getAttribute('data-running') == '1');
            let displayInterval;

            function formatTime(totalSeconds) {
                const mins = Math.floor(totalSeconds / 60);
                const secs = totalSeconds % 60;
                return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }

            function updateDisplayTimer() {
                const timerEl = document.getElementById('display-timer');
                if (timerEl) timerEl.innerText = formatTime(localSeconds);
            }

            if (isTimerRunning) {
                displayInterval = setInterval(() => {
                    localSeconds++;
                    updateDisplayTimer();
                }, 1000);
            } else {
                updateDisplayTimer();
            }

            document.addEventListener('DOMContentLoaded', function() {
                if (window.Echo) {
                    // 1. GUNAKAN PHP UNTUK MENDETEKSI JENIS KATEGORI (SOLO/GANDA/REGU)
                    @php
                        $namaKategori = strtolower($partai->kategori->nama_kategori ?? '');
                        $jenisChannel = 'solo'; // Default
                        if (str_contains($namaKategori, 'ganda')) {
                            $jenisChannel = 'ganda';
                        } elseif (str_contains($namaKategori, 'regu')) {
                            $jenisChannel = 'regu';
                        }
                    @endphp

                    const partaiId = "{{ $partai->id_partai ?? 0 }}";
                    const channelName = `partai.{{ $jenisChannel }}.${partaiId}`;
                    const channel = window.Echo.channel(channelName);

                    console.log("Display terhubung ke Channel:", channelName);

                    // ==========================================
                    // FUNGSI HANDLER TIMER
                    // ==========================================
                    function prosesSinyalTimer(e) {
                        localSeconds = parseInt(e.waktu);
                        updateDisplayTimer();

                        if (e.statusRunning == 1 && !isTimerRunning) {
                            isTimerRunning = true;
                            if (typeof displayInterval !== 'undefined') clearInterval(displayInterval);
                            displayInterval = setInterval(() => {
                                localSeconds++;
                                updateDisplayTimer();
                            }, 1000);
                        } else if (e.statusRunning == 0 && isTimerRunning) {
                            isTimerRunning = false;
                            if (typeof displayInterval !== 'undefined') clearInterval(displayInterval);
                        }
                    }

                    channel.listen('TimerSoloUpdated', prosesSinyalTimer)
                        .listen('TimerGandaUpdated', prosesSinyalTimer)
                        .listen('TimerReguUpdated', prosesSinyalTimer);


                    // ==========================================
                    // FUNGSI KONTROL JURI (ON/OFF, HUKUMAN, TOTAL)
                    // ==========================================
                    function prosesKontrolJuri(e) {
                        console.log("Sinyal Toggle/Hukuman Masuk:", e);

                        // 1. Logika ON/OFF Kotak Juri
                        let noJ = e.noJuri || e.no_juri;
                        if (noJ && noJ != 0) {
                            const boxJuri = document.getElementById(`box-juri-${noJ}`);
                            if (boxJuri) {
                                if (e.isOn == 1) boxJuri.classList.remove('hidden');
                                else boxJuri.classList.add('hidden');
                            }
                        }

                        // 2. Tangkap variabel dengan fallback
                        let nMedian = e.median ?? e.nilai_median;
                        let nDeviasi = e.deviasi ?? e.standar_deviasi;
                        let nTotal = e.totalNilai ?? e.total_nilai;
                        let nHukuman = e.hukuman ?? e.hukuman_ketua;
                        let isDisq = e.isDisq;

                        // 3. Eksekusi Perubahan HTML secara Instan
                        if (nMedian !== undefined && nMedian !== null) {
                            const elMed = document.getElementById('nilai-median');
                            if (elMed) elMed.innerText = parseFloat(nMedian).toFixed(3);
                        }

                        if (nDeviasi !== undefined && nDeviasi !== null) {
                            const elDev = document.getElementById('nilai-deviasi');
                            if (elDev) elDev.innerText = parseFloat(nDeviasi).toFixed(15);
                        }

                        const elTot = document.getElementById('nilai-total');
                        if (elTot) {
                            if (isDisq == 1) {
                                elTot.innerText = "DISKUALIFIKASI";
                                elTot.classList.remove('text-[10rem]', 'text-green-400', 'shadow-green-400/20');
                                elTot.classList.add('text-4xl', 'sm:text-5xl', 'md:text-6xl', 'lg:text-[5rem]',
                                    'text-red-500');
                            } else {
                                elTot.classList.remove('text-4xl', 'sm:text-5xl', 'md:text-6xl', 'lg:text-[5rem]',
                                    'text-red-500');
                                elTot.classList.add('text-[10rem]', 'text-green-400', 'shadow-green-400/20');
                                if (nTotal !== undefined && nTotal !== null) {
                                    elTot.innerText = parseFloat(nTotal).toFixed(3);
                                }
                            }
                        }

                        if (nHukuman !== undefined && nHukuman !== null) {
                            const elHukuman = document.getElementById('nilai-hukuman');
                            if (elHukuman) elHukuman.innerText = "-" + parseFloat(nHukuman).toFixed(2);
                        }
                    }

                    // Daftarkan semua kemungkinan nama Event kontrol juri
                    channel.listen('ToggleKontrolJuri', prosesKontrolJuri)
                        .listen('ToggleKontrolJuriGanda', prosesKontrolJuri);


                    // ==========================================
                    // FUNGSI PENDENGAR NILAI INDIVIDU DARI JURI
                    // ==========================================
                    channel.listen('UpdateNilaiJuri', (e) => {
                        const elNilaiJuri = document.getElementById(`nilai-juri-${e.noJuri}`);
                        if (elNilaiJuri) {
                            elNilaiJuri.innerText = parseFloat(e.skorAkhir).toFixed(2);
                            elNilaiJuri.classList.add('text-green-400');
                            setTimeout(() => {
                                elNilaiJuri.classList.remove('text-green-400');
                            }, 500);
                        }
                    });
                    // Jika ada event juri ganda:
                    channel.listen('UpdateNilaiJuriGanda', (e) => {
                        const elNilaiJuri = document.getElementById(`nilai-juri-${e.noJuri}`);
                        if (elNilaiJuri) {
                            elNilaiJuri.innerText = parseFloat(e.skorAkhir).toFixed(2);
                            elNilaiJuri.classList.add('text-green-400');
                            setTimeout(() => {
                                elNilaiJuri.classList.remove('text-green-400');
                            }, 500);
                        }
                    });

                    // Tambahkan fungsi untuk menangkap event UpdateHukumanGanda
                    channel.listen('UpdateHukumanGanda', (e) => {
                        console.log("Hukuman Masuk:", e);

                        // Update Hukuman
                        const elHukuman = document.getElementById('nilai-hukuman');
                        if (elHukuman) elHukuman.innerText = "-" + parseFloat(e.hukuman).toFixed(2);

                        // Update Total Nilai
                        const elTot = document.getElementById('nilai-total');
                        if (elTot) elTot.innerText = parseFloat(e.totalNilai).toFixed(3);
                    });

                }
            });
        </script>
    @endif
    {{-- ======================================================== --}}
    {{-- PENDENGAR GLOBAL UNTUK REFRESH LAYAR (JALAN TERUS)       --}}
    {{-- ======================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.Echo) {
                // Kita mendengarkan instruksi global dari gelanggang A 
                // (Jika dinamis, Anda bisa ganti 'A' dengan variabel gelanggang)
                const globalChannel = window.Echo.channel('gelanggang.global.A');

                globalChannel.listen('RefreshDisplay', (e) => {
                    console.log("Sinyal Pergantian Partai Diterima! Merefresh layar...");

                    // Efek visual sedikit sebelum refresh agar lebih halus
                    document.body.style.opacity = 0;
                    document.body.style.transition = "opacity 0.5s ease";

                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                });
            }
        });
    </script>
    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            window.Echo.channel('display-pengumuman-solo')
                .listen('TampilkanPemenangSolo', (e) => {
                    // JIKA PERINTAHNYA TUTUP
                    if (e.action === 'close') {
                        Swal.close();
                        return;
                    }

                    // JIKA PERINTAHNYA TAMPILKAN
                    if (e.winner && e.loser) {
                        const isBiruWin = e.winner.sudut.toUpperCase() === 'BIRU';

                        Swal.fire({
                            title: 'KEPUTUSAN PEMENANG',
                            html: `
    <div style="display: flex; flex-direction: column; gap: 30px; margin-top: 20px; align-items: center; text-align: left;">
        
        <div style="width: 100%; max-width: 800px; border: 8px solid #fbbf24; border-radius: 35px; padding: 30px; background: #fffbeb; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
            <div style="background: #fbbf24; color: #000; padding: 8px 25px; border-radius: 50px; display: inline-block; font-weight: 900; font-size: 1rem; margin-bottom: 20px; letter-spacing: 2px;">PEMENANG</div>
            
            <div style="color: ${isBiruWin ? '#2563eb' : '#dc2626'}; font-weight: 900; font-size: 1.8rem; letter-spacing: 1px;">SUDUT ${e.winner.sudut}</div>
            <div style="font-size: 4rem; font-weight: 900; line-height: 1; margin: 15px 0; color: #1e293b; text-transform: uppercase;">${e.winner.nama}</div>
            <div style="font-size: 1.8rem; color: #64748b; font-weight: bold; margin-bottom: 25px;">${e.winner.kontingen}</div>
            
            <div style="background: #fff; border-radius: 20px; padding: 25px; border: 2px solid #fde68a; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span style="font-size: 1.2rem; font-weight: bold; color: #94a3b8; display: block; margin-bottom: 5px;">TOTAL NILAI</span>
                    <span style="font-size: 5rem; font-weight: 900; color: #10b981; line-height: 1;">${e.winner.total}</span>
                </div>
                <div style="text-align: right; border-left: 2px solid #f3f4f6; padding-left: 30px;">
                    <div style="font-size: 1.2rem; font-weight: bold; color: #475569; margin-bottom: 5px;">MEDIAN: ${e.winner.median}</div>
                    <div style="font-size: 1.2rem; font-weight: bold; color: #475569; margin-bottom: 5px;">DEV: ${e.winner.deviasi}</div>
                    <div style="font-size: 1.2rem; font-weight: bold; color: #ef4444; margin-bottom: 5px;">HUK: ${e.winner.hukuman}</div>
                    <div style="font-size: 1.2rem; font-weight: bold; color: #2563eb;">WAKTU: ${e.winner.waktu}</div>
                </div>
            </div>
        </div>

        <div style="width: 100%; max-width: 650px; border: 2px solid #e2e8f0; border-radius: 25px; padding: 20px; background: #f8fafc; opacity: 0.9;">
            <div style="background: #e2e8f0; color: #64748b; padding: 5px 15px; border-radius: 50px; display: inline-block; font-weight: 900; font-size: 0.7rem; margin-bottom: 10px;">PESERTA LAIN</div>
            
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="color: ${!isBiruWin ? '#2563eb' : '#dc2626'}; font-weight: 900; font-size: 1rem;">SUDUT ${e.loser.sudut}</div>
                    <div style="font-size: 1.8rem; font-weight: 900; color: #475569; text-transform: uppercase; margin: 5px 0;">${e.loser.nama}</div>
                    <div style="font-size: 1rem; color: #94a3b8; font-weight: bold; margin-bottom: 10px;">${e.loser.kontingen}</div>
                    
                    <div style="display: flex; gap: 15px; font-size: 0.85rem; font-weight: bold; color: #64748b; background: #fff; padding: 8px 15px; border-radius: 10px; border: 1px solid #e2e8f0;">
                        <span>MED: ${e.loser.median}</span>
                        <span>DEV: ${e.loser.deviasi}</span>
                        <span style="color: #ef4444;">HUK: ${e.loser.hukuman}</span>
                        <span style="color: #2563eb;">WAKTU: ${e.loser.waktu}</span>
                    </div>
                </div>
                <div style="text-align: right; padding-left: 20px;">
                    <span style="font-size: 0.8rem; font-weight: bold; color: #cbd5e1; display: block;">TOTAL</span>
                    <span style="font-size: 2rem; font-weight: 600; color: #94a3b8; line-height: 1;">${e.loser.total}</span>
                </div>
            </div>
            
            ${e.loser.is_disq ? '<div style="margin-top: 15px; background: #fee2e2; color: #ef4444; text-align: center; font-weight: 900; border-radius: 12px; padding: 8px; font-size: 1rem; border: 1px solid #fecaca;">TERDISKUALIFIKASI</div>' : ''}
        </div>
    </div>
    `,
                            width: '95%',
                            padding: '1em',
                            backdrop: `rgba(15, 23, 42, 0.95)`,
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });
                    }
                });
        });
    </script>
@endsection
