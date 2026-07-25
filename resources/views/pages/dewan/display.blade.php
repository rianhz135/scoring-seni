@extends('layouts.dewan')

@section('content')
    <style>
        body {
            background-color: #0c1938;
            /* Slate 900 */
        }
    </style>

    @if (!$partai)
        {{-- TAMPILAN MODE STANDBY (ISTIRAHAT / BELUM MULAI) --}}
        <div class="h-[100dvh] flex flex-col items-center justify-center px-6 overflow-hidden">

            {{-- Logo / Gambar --}}
            <div class="mb-4 flex justify-center">
                <img src="{{ asset('smi.png') }}" alt="SMI Logo"
                    class="h-[220px] md:h-[300px] w-auto object-contain opacity-90">
            </div>

            {{-- Box Standby --}}
            <div
                class="animate-pulse bg-slate-800/80 border border-slate-700 rounded-3xl px-8 py-6 text-center shadow-2xl backdrop-blur-sm max-w-4xl w-full">
                <h1 class="text-4xl md:text-5xl font-black text-slate-300 tracking-widest mb-2">
                    STANDBY GELANGANG 1
                </h1>
                <p class="text-base md:text-xl text-slate-500 uppercase tracking-widest">
                    Menunggu Pertandingan Dimulai oleh Operator
                </p>
            </div>

        </div>
    @else
        @php
            // 1. Ambil peserta aktif
            $aktif = $partai->detail_peserta->where('status_mulai', 1)->first() ?? $partai->detail_peserta->first();
            $sudutCek = $aktif->sudut ?? '';

            // 2. Logika Penentuan Warna & Filter Anggota (Battle vs Pool)
            if (!empty($sudutCek)) {
                // --- SISTEM BATTLE (MERAH / BIRU) ---
                // Filter anggota tim berdasarkan SUDUT
                $anggotaTim = $partai->detail_peserta->where('sudut', $aktif->sudut);

                if (strtolower($sudutCek) == 'merah') {
                    $hexWarna = '#dc2626'; // Merah (Red-600)
                    $labelIdentitas = 'SUDUT MERAH';
                    $teksWarna = 'text-red-100';
                } else {
                    $hexWarna = '#2563eb'; // Biru (Blue-600)
                    $labelIdentitas = 'SUDUT BIRU';
                    $teksWarna = 'text-blue-100';
                }
            } else {
                // --- SISTEM POOL (HIJAU / NO URUT) ---
                // Filter anggota tim berdasarkan NO URUT
                $anggotaTim = $partai->detail_peserta->where('no_urut', $aktif->no_urut);
                $hexWarna = '#059669'; // Hijau (Emerald-600)
                $labelIdentitas = 'NO TAMPIL ' . ($aktif->no_urut ?? '-');
                $teksWarna = 'text-emerald-50';
            }
        @endphp
        <div class="h-[95vh] flex flex-col gap-6">
            {{-- HEADER: INFO PARTAI & TIMER --}}
            <div class="flex justify-between items-stretch gap-6 h-32">
                <div
                    class="bg-slate-800/80 border border-slate-700 rounded-3xl p-6 flex flex-col justify-center flex-1 shadow-lg">
                    <div class="flex items-center gap-3 mb-2">
                        <span
                            class="px-4 py-1.5 bg-primary-600 text-white text-2xl font-black uppercase rounded-lg tracking-[0.2em]">
                            GELANGGANG {{ $partai->gelanggang ?? 'A' }} 1
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

            {{-- IDENTITAS ATLET --}}
            <div style="background-color: {{ $hexWarna }} !important;"
                class="rounded-3xl p-8 flex justify-between items-center shadow-2xl ring-4 ring-white/10 border border-white/10 relative overflow-hidden transition-all duration-500 min-h-[160px]">

                {{-- Dekorasi Background (Garis Putih Transparan) --}}
                <div class="absolute top-0 right-0 w-64 h-full bg-white/10 skew-x-12 translate-x-16"></div>

                <div class="relative z-20 w-full">
                    {{-- Badge Label (Sudut / No Urut) --}}
                    <div
                        class="inline-block px-5 py-1.5 bg-slate-700 backdrop-blur-md text-white text-lg font-black uppercase tracking-[0.3em] rounded-xl mb-4 border border-white/20">
                        {{ $labelIdentitas }}
                    </div>

                    <div class="flex justify-between items-end w-full">
                        <div>
                            <h2 class="text-5xl font-black text-white uppercase tracking-tight mb-2 drop-shadow-md">
                                {{ $aktif->peserta->kontingen ?? '-' }}
                            </h2>

                            <p class="text-4xl font-bold {{ $teksWarna }} uppercase tracking-wide drop-shadow-sm">
                                @foreach ($anggotaTim as $anggota)
                                    {{ $anggota->peserta->nama_peserta ?? ($anggota->peserta->nama ?? 'NAMA KOSONG') }}
                                    @if (!$loop->last)
                                        &
                                    @endif
                                @endforeach
                            </p>
                        </div>

                        <div class="opacity-20">
                            <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MENGAMBIL DATA JURI DARI DATABASE --}}
            @php
                $juriDb = \Illuminate\Support\Facades\DB::table('input_nilai_juri')
                    ->where('id_detail', $aktif->id_detail ?? 0)
                    ->orderBy('no_juri', 'asc')
                    ->get()
                    ->keyBy('no_juri');

                // PERBAIKAN 1: Cek nama kategori dari relasi 'kategori', BUKAN 'kelas'
                $namaKategori = strtolower($partai->kategori->nama_kategori ?? '');

                // Deteksi Otomatis untuk Nilai Dasar dan Rumus
                $isTunggalRegu = str_contains($namaKategori, 'tunggal') || str_contains($namaKategori, 'regu');
                $baseScore = $isTunggalRegu ? 9.9 : 9.1;
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
                        $isOn = isset($kontrolDb[$i]) ? $kontrolDb[$i]->is_on : 1;
                        $isHidden = $isOn == 0 ? 'hidden' : '';

                        // Ambil nilai masing-masing kolom
                        $nilaiA = $juriDb[$i]->nilai_a ?? 0;
                        $nilaiB = $juriDb[$i]->nilai_b ?? 0;
                        $nilaiC = $juriDb[$i]->nilai_c ?? 0;

                        // PERBAIKAN 2: Pisahkan Rumus Tunggal/Regu dengan Solo/Ganda
                        if ($isTunggalRegu) {
                            // Rumus Tunggal & Regu: 9.90 - Kesalahan (A) + Kemantapan (B)
                            $totalNilaiJuri = $baseScore - $nilaiA + $nilaiB;
                        } else {
                            // Rumus Solo & Ganda: 9.10 + A + B + C
                            $totalNilaiJuri = $baseScore + $nilaiA + $nilaiB + $nilaiC;
                        }
                    @endphp

                    <div id="box-juri-{{ $i }}"
                        class="flex-1 bg-slate-800 border border-slate-700 rounded-2xl flex flex-col overflow-hidden relative transition-all duration-300 {{ $isHidden }}">
                        <div class="absolute top-0 w-full h-1 bg-yellow-500"></div>
                        <div class="bg-slate-700/50 py-3 text-center border-b border-slate-700">
                            <span class="text-2xl font-black text-slate-300 uppercase tracking-widest">JURI
                                {{ $i }}</span>
                        </div>
                        <div class="py-6 flex items-center justify-center">
                            <span id="nilai-juri-{{ $i }}"
                                class="text-5xl font-black text-white font-mono transition-all duration-300 ease-out inline-block transform">
                                {{-- Ubah angka 3 menjadi 2 di sini --}}
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
                        <div
                            class="bg-slate-800 border border-slate-700 rounded-3xl flex flex-col overflow-hidden relative">
                            <div class="absolute top-0 w-full h-1 bg-blue-500"></div>
                            <div class="bg-slate-700/50 py-3 text-center border-b border-slate-700">
                                <span class="text-2xl font-black text-slate-300 uppercase tracking-widest">Median</span>
                            </div>
                            <div class="flex-1 flex items-center justify-center">
                                {{-- Mengambil nilai awal dari database --}}
                                <span id="nilai-median" class="text-7xl font-black text-white">
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
                                <span id="nilai-hukuman" class="text-7xl font-black text-red-500">
                                    {{-- Menggunakan abs() agar jika di database sudah minus, tidak jadi dobel minus (--0.50) --}}
                                    -{{ number_format(abs((float) $nilaiHukumanDb), 2, '.', '') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="flex-1 bg-slate-800 border border-slate-700 rounded-3xl flex flex-col overflow-hidden relative">
                        <div class="absolute top-0 w-full h-1 bg-yellow-500"></div>
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
                    @php
                        $namaKategoriDb = strtolower($partai->kelas->nama_kelas ?? ($partai->kategori->nama_kategori ?? ''));
                        $jenisChannel = 'solo'; // Default

                        if (str_contains($namaKategoriDb, 'ganda')) {
                            $jenisChannel = 'ganda';
                        } elseif (str_contains($namaKategoriDb, 'regu')) {
                            $jenisChannel = 'regu';
                        } elseif (str_contains($namaKategoriDb, 'tunggal')) {
                            $jenisChannel = 'tunggal';
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
                        .listen('.TimerReguUpdated', prosesSinyalTimer)
                        .listen('.TimerTunggalUpdated', prosesSinyalTimer)
                        .listen('.TimerPoolUpdated', prosesSinyalTimer);


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

                    channel.listen('ToggleKontrolJuri', prosesKontrolJuri)
                        .listen('ToggleKontrolJuriGanda', prosesKontrolJuri)
                        .listen('.ToggleKontrolJuriTunggal', prosesKontrolJuri)
                        .listen('.ToggleKontrolJuriRegu', prosesKontrolJuri)
                        .listen('.ToggleKontrolJuriPool', prosesKontrolJuri)
                        .listen('UpdateDisqPool', prosesKontrolJuri);


                    // ==========================================
                    // FUNGSI PENDENGAR NILAI INDIVIDU DARI JURI
                    // ==========================================
                    function animateBoxJuri(e) {
                        // 1. Update Skor Juri individu
                        const elNilaiJuri = document.getElementById(`nilai-juri-${e.noJuri}`);
                        if (elNilaiJuri) {
                            elNilaiJuri.innerText = parseFloat(e.skorAkhir).toFixed(2);
                        }

                        // 2. Update Statistik (Hanya jika data statistik dikirim/milik Tunggal)
                        if (e.median !== undefined && e.median !== null) {
                            const elMed = document.getElementById('nilai-median');
                            const elDev = document.getElementById('nilai-deviasi');
                            const elTot = document.getElementById('nilai-total');

                            if (elMed) elMed.innerText = parseFloat(e.median).toFixed(3);
                            if (elDev) elDev.innerText = parseFloat(e.deviasi).toFixed(15);

                            if (elTot && e.isDisq != 1) {
                                // PAKSA PARSE FLOAT untuk menghilangkan angka juri yang nyelip di depan
                                let scoreFinal = parseFloat(e.totalNilai);
                                elTot.textContent = scoreFinal.toFixed(3);
                            }
                        }
                    }

                    // Telinga pendengar tetap seperti ini (Sudah benar)
                    channel.listen('UpdateNilaiJuri', animateBoxJuri)
                        .listen('UpdateNilaiJuriGanda', animateBoxJuri)
                        .listen('.UpdateNilaiJuriTunggal', animateBoxJuri)
                        .listen('.UpdateNilaiJuriRegu', animateBoxJuri)
                        .listen('.UpdateNilaiJuriPool', animateBoxJuri);


                    // ==========================================
                    // FUNGSI PENDENGAR HUKUMAN DEWAN
                    // ==========================================
                    function prosesUpdateHukuman(e) {
                        console.log("Hukuman Masuk:", e);
                        const elHukuman = document.getElementById('nilai-hukuman');
                        if (elHukuman) elHukuman.innerText = "-" + parseFloat(e.hukuman).toFixed(2);

                        const elTot = document.getElementById('nilai-total');
                        if (elTot) elTot.innerText = parseFloat(e.totalNilai).toFixed(3);
                    }

                    channel.listen('UpdateHukuman', prosesUpdateHukuman)
                        .listen('UpdateHukumanGanda', prosesUpdateHukuman)
                        .listen('.UpdateHukumanTunggal', prosesUpdateHukuman)
                        .listen('.UpdateHukumanRegu', prosesUpdateHukuman)
                        .listen('.UpdateHukumanPool', prosesUpdateHukuman);
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
                console.log("Layar Display Siap! Mendengarkan semua kategori di Gelanggang A...");

                // Fungsi reusable agar tidak ngetik ulang
                const eksekusiRefresh = (e) => {
                    console.log("Sinyal Pergantian Partai Diterima! Merefresh layar...");
                    document.body.style.opacity = 0;
                    document.body.style.transition = "opacity 0.5s ease";
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                };

                // Pasang 4 antena sekaligus untuk memantau Tunggal, Ganda, regu, dan Solo
                const daftarKategori = ['tunggal', 'ganda', 'solo', 'regu', 'pool'];
                daftarKategori.forEach(kategori => {
                    window.Echo.channel(`gelanggang.global.${kategori}.A`)
                        .listen('RefreshDisplay', eksekusiRefresh);
                });

            } else {
                console.error("Echo belum siap di Display!");
            }
        });
    </script>

    {{-- ======================================================= --}}
    {{-- MODAL PEMENANG POOL (KHUSUS UNTUK LAYAR DISPLAY) --}}
    {{-- ======================================================= --}}
    <div id="modal-pemenang-pool"
        class="hidden fixed inset-0 z-[999] bg-slate-900/90 backdrop-blur-md items-center justify-center p-8">
        <div
            class="bg-white rounded-3xl w-full max-w-7xl overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)] flex flex-col transform transition-all">

            {{-- Header Modal Display (Warna Gradasi Fix) --}}
            <div class="p-8 text-center relative" style="background: linear-gradient(to right, #3730a3, #1d4ed8);">
                <h2 class="text-5xl font-black text-white uppercase tracking-widest drop-shadow-lg">HASIL KEPUTUSAN PEMENANG
                </h2>
            </div>

            {{-- Body Tabel Display --}}
            <div class="p-0 overflow-x-auto bg-gray-50">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-200 border-b-2 border-gray-300">
                        <tr class="text-lg font-black text-gray-600 uppercase tracking-widest">
                            <th class="px-6 py-6 text-center w-32">Rank</th>
                            <th class="px-6 py-6">Peserta / Kontingen</th>
                            <th class="px-6 py-6 text-center">Waktu</th>
                            <th class="px-6 py-6 text-center">Median</th>
                            <th class="px-6 py-6 text-center">Deviasi</th>
                            <th class="px-6 py-6 text-center text-red-500">Hukuman</th>
                            <th class="px-6 py-6 text-right pr-10">Total Skor</th>
                        </tr>
                    </thead>
                    {{-- ID ini yang dicari oleh JavaScript Anda --}}
                    <tbody id="pool-winner-tbody" class="divide-y-2 divide-gray-100 bg-white">
                        {{-- OTOMATIS DIISI OLEH JAVASCRIPT REVERB SAAT TOMBOL DIKLIK --}}
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            // --- FUNGSI REUSABLE UNTUK MENAMPILKAN MODAL PEMENANG ---
            const renderPemenangModal = (e) => {
                if (e.action === 'close') {
                    Swal.close();
                    return;
                }

                if (e.winner && e.loser) {
                    const isBiruWin = e.winner.sudut.toUpperCase() === 'BIRU';

                    // Warna background full layar (Biru gelap atau Merah gelap)
                    const bgLayar = isBiruWin ? '#1d4ed8' : '#b91c1c';
                    // Warna teks untuk Sudut Pemenang di dalam kotak putih
                    const textColor = isBiruWin ? '#2563eb' : '#dc2626';

                    Swal.fire({
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        background: bgLayar,
                        backdrop: bgLayar,
                        customClass: {
                            popup: 'swal-fullscreen'
                        },
                        html: `
                    <style>
                        .swal-fullscreen {
                            width: 100vw !important;
                            height: 100vh !important;
                            max-width: 100% !important;
                            margin: 0 !important;
                            border-radius: 0 !important;
                            padding: 0 !important;
                            display: flex !important;
                            flex-direction: column !important;
                            overflow: hidden !important;
                        }
                        .swal2-container {
                            padding: 0 !important;
                        }
                        .swal2-html-container {
                            margin: 0 !important;
                            padding: 0 !important;
                            height: 100%;
                            overflow: hidden !important; 
                        }
                    </style>
                    
                    <div style="height: 100vh; width: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 15px; box-sizing: border-box; background-color: ${bgLayar}; overflow: hidden;">
                        
                        <h1 style="color: #fde047; font-size: 2.2rem; font-weight: 900; margin-bottom: 25px; margin-top: 0; letter-spacing: 3px; text-shadow: 0 4px 10px rgba(0,0,0,0.3);">KEPUTUSAN PEMENANG</h1>
                        
                        <div style="width: 100%; max-width: 800px; background: #ffffff; border-radius: 30px; padding: 30px 40px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); position: relative; border: 6px solid #fcd34d;">
                            
                            <div style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); background: #fcd34d; color: #92400e; padding: 8px 40px; border-radius: 50px; font-weight: 900; font-size: 1.2rem; letter-spacing: 2px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">PEMENANG</div>
                            
                            <div style="color: ${textColor}; font-weight: 900; font-size: 1.6rem; letter-spacing: 2px; margin-top: 10px; text-align: center;">SUDUT ${e.winner.sudut}</div>
                            <div style="font-size: 4rem; font-weight: 900; line-height: 1; margin: 10px 0; color: #0f172a; text-transform: uppercase; text-align: center;">${e.winner.nama}</div>
                            <div style="font-size: 1.5rem; color: #64748b; font-weight: bold; margin-bottom: 25px; text-align: center;">${e.winner.kontingen}</div>
                            
                            <div style="background: #f8fafc; border-radius: 20px; padding: 20px 30px; border: 2px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                                <div style="text-align: left;">
                                    <span style="font-size: 1.2rem; font-weight: bold; color: #94a3b8; display: block; margin-bottom: 2px;">TOTAL NILAI</span>
                                    <span style="font-size: 5rem; font-weight: 900; color: #10b981; line-height: 1;">${e.winner.total}</span>
                                </div>
                                <div style="text-align: right; border-left: 3px solid #e2e8f0; padding-left: 30px;">
                                    <div style="font-size: 1.2rem; font-weight: bold; color: #475569; margin-bottom: 4px;">MEDIAN: ${e.winner.median}</div>
                                    <div style="font-size: 1.2rem; font-weight: bold; color: #475569; margin-bottom: 4px;">DEV: ${e.winner.deviasi}</div>
                                    <div style="font-size: 1.2rem; font-weight: bold; color: #ef4444; margin-bottom: 4px;">HUK: ${e.winner.hukuman}</div>
                                    <div style="font-size: 1.2rem; font-weight: bold; color: #2563eb;">WAKTU: ${e.winner.waktu}</div>
                                </div>
                            </div>
                        </div>

                        <div style="width: 100%; max-width: 700px; margin-top: 25px; background: rgba(0, 0, 0, 0.2); backdrop-filter: blur(10px); border-radius: 20px; padding: 20px 30px; border: 1px solid rgba(255, 255, 255, 0.1); color: white;">
                            <div style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.9); padding: 6px 20px; border-radius: 50px; display: inline-block; font-weight: 900; font-size: 0.8rem; margin-bottom: 15px; letter-spacing: 2px;">PESERTA LAIN</div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; text-align: left;">
                                <div>
                                    <div style="font-weight: 900; font-size: 1.2rem; opacity: 0.9; color: #fde047;">SUDUT ${e.loser.sudut}</div>
                                    <div style="font-size: 2rem; font-weight: 900; text-transform: uppercase; margin: 2px 0;">${e.loser.nama}</div>
                                    <div style="font-size: 1rem; font-weight: bold; opacity: 0.8; margin-bottom: 12px;">${e.loser.kontingen}</div>
                                    
                                    <div style="display: flex; gap: 15px; font-size: 0.9rem; font-weight: bold; background: rgba(0,0,0,0.3); padding: 8px 20px; border-radius: 12px;">
                                        <span>MED: ${e.loser.median}</span>
                                        <span>DEV: ${e.loser.deviasi}</span>
                                        <span style="color: #fca5a5;">HUK: ${e.loser.hukuman}</span>
                                        <span style="color: #93c5fd;">WAKTU: ${e.loser.waktu}</span>
                                    </div>
                                </div>
                                <div style="text-align: right; padding-left: 20px;">
                                    <span style="font-size: 1rem; font-weight: bold; opacity: 0.8; display: block; margin-bottom: 2px;">TOTAL</span>
                                    <span style="font-size: 3.5rem; font-weight: 900; line-height: 1;">${e.loser.total}</span>
                                </div>
                            </div>
                            ${e.loser.is_disq ? '<div style="margin-top: 15px; background: rgba(220, 38, 38, 0.9); color: white; text-align: center; font-weight: 900; border-radius: 12px; padding: 10px; font-size: 1.2rem; letter-spacing: 2px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">TERDISKUALIFIKASI</div>' : ''}
                        </div>

                    </div>
                    `
                    });
                }
            };

            // 1. LISTEN UNTUK SOLO
            window.Echo.channel('display-pengumuman-solo')
                .listen('TampilkanPemenangSolo', renderPemenangModal);

            // 2. LISTEN UNTUK GANDA
            window.Echo.channel('display-pengumuman-ganda')
                .listen('TampilkanPemenangGanda', renderPemenangModal);

            // 3. LISTEN UNTUK TUNGGAL
            window.Echo.channel('display-pengumuman-tunggal')
                .listen('TampilkanPemenangTunggal', renderPemenangModal);

            // 4. LISTEN UNTUK REGU
            window.Echo.channel('display-pengumuman-regu')
                .listen('TampilkanPemenangRegu', renderPemenangModal);


        });
        window.Echo.channel('display-pengumuman-pool')
            .listen('.TampilkanPemenangPool', (e) => {
                const modalDisplay = document.getElementById(
                    'modal-pemenang-pool'); // Pastikan ID ini ada di HTML Display Anda

                if (e.action === 'close') {
                    if (modalDisplay) {
                        modalDisplay.classList.add('hidden');
                        modalDisplay.classList.remove('flex');
                    }
                    return;
                }

                if (e.action === 'show') {
                    const tbody = document.getElementById(
                        'pool-winner-tbody'); // Pastikan ID ini ada di HTML Display Anda
                    if (!tbody) return;

                    tbody.innerHTML = '';

                    // Render ulang data yang dikirim dari controller admin
                    e.peserta_ranked.forEach(p => {
                        let rankBadge = `<span class="text-xl font-bold text-gray-400">#${p.rank}</span>`;
                        let rowClass = "bg-white";

                        if (p.rank === 1) {
                            rankBadge =
                                `<span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-100 text-yellow-700 font-black text-2xl border-4 border-yellow-300 shadow-xl animate-bounce">1</span>`;
                            rowClass = "bg-yellow-50";
                        } else if (p.rank === 2) {
                            rankBadge =
                                `<span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-700 font-black text-2xl border-4 border-slate-300 shadow-md">2</span>`;
                        } else if (p.rank === 3) {
                            rankBadge =
                                `<span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-orange-100 text-orange-800 font-black text-2xl border-4 border-orange-300 shadow-md">3</span>`;
                        }

                        const tr = `
                    <tr class="${rowClass} border-b border-gray-200">
                        <td class="px-6 py-6 text-center">${rankBadge}</td>
                        <td class="px-6 py-6">
                            <div class="font-black text-gray-900 uppercase text-2xl tracking-wide">${p.nama}</div>
                            <div class="text-sm text-gray-500 uppercase mt-1 font-bold tracking-wider">${p.kontingen}</div>
                        </td>
                        <td class="px-6 py-6 text-center"><span class="bg-gray-100 px-4 py-2 rounded text-lg font-mono font-bold">${p.waktu}</span></td>
                        <td class="px-6 py-6 text-center font-bold text-gray-700 text-xl">${p.median}</td>
                        <td class="px-6 py-6 text-center text-xl text-gray-700 font-bold">${p.deviasi}</td>
                        <td class="px-6 py-6 text-center text-red-600 font-bold text-xl">-${p.hukuman}</td>
                        <td class="px-6 py-6 text-right pr-8"><span class="text-4xl font-black text-blue-600">${p.total}</span></td>
                    </tr>
                `;
                        tbody.innerHTML += tr;
                    });

                    // Munculkan Modal di Display
                    modalDisplay.classList.remove('hidden');
                    modalDisplay.classList.add('flex');
                }
            });
    </script>
@endsection
