@extends('layouts.dewan')

@section('content')
    <style>
        body {
            background-color: #f1f5f9;
            overflow: hidden;
            touch-action: pan-y;
            /* Hindari pan-x agar tidak tergeser horizontal */
        }

        /* Menghilangkan scrollbar tapi tetap bisa scroll jika perlu */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    {{-- JIKA TIDAK ADA PARTAI YANG AKTIF --}}
    @if (!$partai)
        <div class="h-[100dvh] flex flex-col items-center justify-center relative overflow-hidden">
            <div
                class="absolute inset-0 opacity-5 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-slate-900 via-transparent to-transparent">
            </div>
            <div
                class="animate-pulse bg-white border border-slate-200 rounded-3xl p-16 text-center shadow-2xl relative z-10">
                <h2 class="text-3xl font-black text-slate-400 mb-2">JURI {{ $no_juri ?? '-' }}</h2>
                <div class="w-24 h-2 bg-slate-200 rounded-full mx-auto mb-6"></div>

                <p class="text-xl text-slate-500 font-medium">
                    Menunggu pertandingan
                    <span class="font-bold text-slate-700 uppercase">TUNGGAL / REGU</span>
                    dimulai...
                </p>
            </div>
        </div>
    @else
        @php
            $sudutDb = ucfirst($sudut);
            // Cek status is_on dari table status_kontrol_juri, default 1 jika tidak ada
            $isOn = isset($kontrol) ? $kontrol->is_on : 1;
        @endphp

        {{-- MODAL STANDBY / BLANK SCREEN --}}
        <div id="modal-standby"
            class="fixed inset-0 z-40 transition-all duration-300 pointer-events-none {{ $isOn == 1 ? 'hidden' : '' }}">
            <div class="absolute inset-0 bg-slate-900/95 backdrop-blur-md pointer-events-auto flex flex-col items-center justify-center text-center"
                style="pointer-events: auto;">
                <div id="bg-modal-juri"
                    class="px-8 py-3 rounded-full border mb-6 shadow-inner animate-pulse {{ strtolower($sudut) == 'biru' ? 'bg-blue-600 border-blue-500' : 'bg-red-600 border-red-500' }}">
                    <h2 class="text-3xl font-black text-white tracking-[0.2em] mb-0">JURI {{ $no_juri }}</h2>
                </div>
                <h1
                    class="text-5xl md:text-7xl font-black text-white tracking-tighter uppercase leading-none mb-4 drop-shadow-lg">
                    PERTANDINGAN<br>BELUM DIMULAI
                </h1>
                <p id="teks-sudut-modal" class="text-slate-400 text-lg md:text-xl font-bold tracking-widest uppercase">
                    SUDUT {{ strtoupper($sudutDb) }}
                </p>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- MODE AKTIF (Pertandingan Berjalan)         --}}
        {{-- ========================================== --}}
        @php
            $sudutDb = ucfirst($sudut);
            $isLocked = isset($kontrol) && strtolower($kontrol->status_input) == 'selesai';
            $kategoriNama = $partai->kategori->nama_kategori ?? 'TUNGGAL / REGU';
        @endphp

        <div id="landscape-wrapper" class="w-full p-3 flex flex-col gap-3 overflow-hidden">

            {{-- BARIS 1: HEADER + TOTAL NILAI --}}
            <div class="flex gap-3 shrink-0">
                {{-- Sisi kiri: Info Juri & Nama Peserta --}}
                <div
                    class="bg-emerald-600 rounded-xl md:rounded-2xl px-3 md:px-6 py-3 md:py-6 shadow-lg flex items-center text-white relative overflow-hidden flex-1">
                    <div class="absolute top-0 right-0 w-40 h-full bg-white/10 skew-x-12 translate-x-10"></div>
                    <div class="relative z-10 flex items-center gap-3 md:gap-6 w-full">

                        <div
                            class="bg-white/20 backdrop-blur-md px-3 md:px-5 py-2 md:py-3 rounded-xl md:rounded-2xl border border-white/30 text-center shadow-inner shrink-0">
                            <p class="text-[10px] md:text-[14px] font-black uppercase tracking-[0.2em] opacity-80 mb-0.5">
                                JURI</p>
                            <h2 class="text-3xl md:text-6xl font-black italic leading-none">{{ $no_juri }}</h2>
                        </div>

                        <div class="flex-1 border-l border-white/20 pl-3 md:pl-6 min-w-0">
                            <p
                                class="text-[8px] md:text-xl font-bold uppercase tracking-[0.1em] text-emerald-100 mb-0.5 md:mb-1 leading-tight">
                                PARTAI #{{ $partai->no_partai }} &bull; {{ $partai->kategori->nama_kategori ?? 'SENI' }}
                                &bull; NO. URUT {{ $aktif->no_urut ?? '-' }}
                            </p>

                            <h1
                                class="text-lg md:text-5xl font-black uppercase italic tracking-tight leading-none truncate">
                                @php
                                    $pesertaTampil = $partai->detail_peserta->where('status_mulai', 1);
                                @endphp
                                @foreach ($pesertaTampil as $det)
                                    {{ $det->peserta->nama_peserta ?? '-' }}@if (!$loop->last)
                                        &
                                    @endif
                                @endforeach
                            </h1>

                            <p class="text-[11px] md:text-lg font-medium text-white/80 mt-0.5 md:mt-1">
                                {{ $aktif->peserta->kontingen ?? '' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Sisi kanan: Nilai Default & Nilai Akhir --}}
                <div
                    class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 px-6 py-5 flex items-center gap-6 shrink-0">
                    <div class="text-center pr-6 border-r border-slate-600">
                        <p class="text-lg font-black text-slate-400 uppercase tracking-widest mb-1">Nilai Default</p>
                        <p class="text-4xl font-black text-white font-mono leading-none">9.90</p>
                    </div>
                    <div class="text-center min-w-[120px]">
                        <p class="text-lg font-black text-slate-400 uppercase tracking-widest mb-1">Nilai Akhir</p>
                        <p id="display-total"
                            class="text-5xl font-black text-emerald-400 font-mono leading-none drop-shadow-md transition-all duration-300">
                            {{ number_format($inputJuri->skor_akhir_juri ?? 9.9, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- BARIS 2: KOLOM PENILAIAN (GRID 2 KOLOM) --}}
            <div class="flex-1 grid grid-cols-2 gap-4 min-h-0 relative">

                {{-- OVERLAY KUNCI DENGAN ID AGAR BISA DIKONTROL JAVASCRIPT --}}
                <div id="overlay-kunci"
                    class="absolute inset-0 z-50 bg-slate-900/50 backdrop-blur-md rounded-2xl flex items-center justify-center transition-all duration-300 {{ $isLocked ? '' : 'hidden' }}">
                    <div
                        class="bg-white px-8 py-4 rounded-full shadow-xl flex items-center gap-3 border-2 border-slate-300">
                        <svg class="w-8 h-8 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="text-2xl font-black text-slate-600 tracking-widest">NILAI TERKUNCI</span>
                    </div>
                </div>

                {{-- KOLOM KIRI (Kemantapan / Nilai B) --}}
                <div class="bg-white rounded-2xl border-2 border-emerald-100 shadow-sm p-5 flex flex-col gap-4">
                    <div class="shrink-0 text-center">
                        <h3 class="text-lg xl:text-2xl font-black text-emerald-500 uppercase tracking-[0.2em]">Kemantapan
                        </h3>
                        <p class="text-slate-400 font-bold mt-1 tracking-widest text-xs">TOTAL PENAMBAHAN</p>
                    </div>

                    <div class="flex-1 flex items-center justify-center min-h-[120px]">
                        <div id="display-nilai-b"
                            class="font-black text-emerald-500 leading-none font-mono transition-all duration-200"
                            style="font-size: clamp(2rem, 10vw, 5rem);">
                            +0.00
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3 shrink-0" style="height: clamp(60px, 10vh, 120px);">
                        <button onclick="updateNilai('B', -0.01)"
                            class="col-span-1 bg-slate-100 hover:bg-slate-200 text-slate-500 font-black rounded-2xl shadow-sm active:scale-95 transition-all flex flex-col items-center justify-center gap-2 text-xs uppercase tracking-widest">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                            Undo
                        </button>
                        <button onclick="updateNilai('B', 0.01)"
                            class="col-span-2 bg-emerald-500 hover:bg-emerald-600 text-white font-black rounded-2xl shadow-md active:scale-95 transition-all flex flex-col items-center justify-center gap-1">
                            <span class="font-mono leading-none" style="font-size: clamp(2rem, 5vw, 2rem);">+0.01</span>
                            <span class="text-xs font-bold opacity-90 uppercase tracking-widest">Mantap</span>
                        </button>
                    </div>
                </div>

                {{-- KOLOM KANAN (Kebenaran Gerak / Nilai A) --}}
                <div class="bg-white rounded-2xl border-2 border-red-100 shadow-sm p-5 flex flex-col gap-4">
                    <div class="shrink-0 text-center">
                        <h3 class="text-lg xl:text-2xl font-black text-red-500 uppercase tracking-[0.2em]">Kebenaran Gerak
                        </h3>
                        <p class="text-slate-400 font-bold mt-1 tracking-widest text-xs">TOTAL PENGURANGAN</p>
                    </div>

                    <div class="flex-1 flex items-center justify-center min-h-[120px]">
                        <div id="display-nilai-a"
                            class="font-black text-red-600 leading-none font-mono transition-all duration-200"
                            style="font-size: clamp(2rem, 10vw, 5rem);">
                            -0.00
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3 shrink-0" style="height: clamp(60px, 10vh, 120px);">
                        <button onclick="updateNilai('A', -0.01)"
                            class="col-span-1 bg-slate-100 hover:bg-slate-200 text-slate-500 font-black rounded-2xl shadow-sm active:scale-95 transition-all flex flex-col items-center justify-center gap-2 text-xs uppercase tracking-widest">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                            Undo
                        </button>
                        <button onclick="updateNilai('A', 0.01)"
                            class="col-span-2 bg-red-500 hover:bg-red-600 text-white font-black rounded-2xl shadow-md active:scale-95 transition-all flex flex-col items-center justify-center gap-1">
                            <span class="font-mono leading-none" style="font-size: clamp(2rem, 5vw, 2rem);">-0.01</span>
                            <span class="text-xs font-bold opacity-90 uppercase tracking-widest">Salah Gerak</span>
                        </button>
                    </div>
                </div>

            </div>

            {{-- BARIS 3: TOMBOL KIRIM / SIMPAN KE DEWAN --}}
            <div class="shrink-0">
                {{-- TOMBOL AKTIF (Disembunyikan jika isLocked = true) --}}
                <button id="btn-kirim-aktif" onclick="simpanKeServer()"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black text-3xl py-5 rounded-2xl shadow-xl active:scale-[0.98] transition-all flex items-center justify-center gap-4 {{ $isLocked ? 'hidden' : '' }}">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    SELESAI DAN KUNCI NILAI
                </button>

                {{-- TOMBOL TERKUNCI (Disembunyikan jika isLocked = false) --}}
                <button id="btn-kirim-terkunci" disabled
                    class="w-full bg-slate-400 text-white font-black text-3xl py-5 rounded-2xl shadow-lg flex items-center justify-center gap-3 cursor-not-allowed {{ $isLocked ? '' : 'hidden' }}">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    NILAI TELAH TERKUNCI
                </button>
            </div>

        </div>
    @endif
    <div class="fixed bottom-4 right-4 flex gap-2 z-50">

        {{-- Zoom In --}}
        <button onclick="zoomIn()"
            class="w-12 h-12 sm:w-12 sm:h-12 md:w-16 md:h-16 lg:w-20 lg:h-20 bg-slate-700 hover:bg-slate-600 active:scale-95 text-white rounded-xl md:rounded-2xl shadow-lg flex items-center justify-center transition-all">
            <svg class="w-5 h-5 md:w-6 md:h-6 lg:w-8 lg:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-4.35-4.35M11 19A8 8 0 1 0 11 3a8 8 0 0 0 0 16z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11h6M11 8v6" />
            </svg>
        </button>

        {{-- Zoom Out --}}
        <button onclick="zoomOut()"
            class="w-12 h-12 sm:w-12 sm:h-12 md:w-16 md:h-16 lg:w-20 lg:h-20 bg-slate-700 hover:bg-slate-600 active:scale-95 text-white rounded-xl md:rounded-2xl lg:rounded-3xl shadow-lg flex items-center justify-center transition-all">
            <svg class="w-5 h-5 md:w-6 md:h-6 lg:w-8 lg:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-4.35-4.35M11 19A8 8 0 1 0 11 3a8 8 0 0 0 0 16z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11h6" />
            </svg>
        </button>

        {{-- Fullscreen / Exit Fullscreen --}}
        <button onclick="toggleFullscreen()" id="btn-fullscreen"
            class="w-12 h-12 sm:w-12 sm:h-12 md:w-16 md:h-16 lg:w-20 lg:h-20 bg-slate-700 hover:bg-slate-600 active:scale-95 text-white rounded-xl md:rounded-2xl lg:rounded-3xl shadow-lg flex items-center justify-center transition-all">
            <svg id="icon-enter-fs" class="w-5 h-5 md:w-6 md:h-6 lg:w-8 lg:h-8" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 8V5a1 1 0 0 1 1-1h3M16 4h3a1 1 0 0 1 1 1v3M20 16v3a1 1 0 0 1-1 1h-3M8 20H5a1 1 0 0 1-1-1v-3" />
            </svg>
            <svg id="icon-exit-fs" class="w-5 h-5 md:w-6 md:h-6 lg:w-8 lg:h-8 hidden" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8 3v3a1 1 0 0 1-1 1H4M21 8h-3a1 1 0 0 1-1-1V4M4 16h3a1 1 0 0 1 1 1v3M16 21v-3a1 1 0 0 1 1-1h3" />
            </svg>
        </button>

        {{-- Refresh --}}
        <button onclick="window.location.reload()"
            class="w-12 h-12 sm:w-12 sm:h-12 md:w-16 md:h-16 lg:w-20 lg:h-20 bg-amber-500 hover:bg-amber-400 active:scale-95 text-white rounded-xl md:rounded-2xl lg:rounded-3xl shadow-lg flex items-center justify-center transition-all">
            <svg class="w-5 h-5 md:w-6 md:h-6 lg:w-8 lg:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h5M20 20v-5h-5" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 9a9 9 0 0 1 15-3.87M20 15a9 9 0 0 1-15 3.87" />
            </svg>
        </button>

    </div>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script>
        let zoomLevel = 1;
        const ZOOM_STEP = 0.1;
        const ZOOM_MIN = 0.6;
        const ZOOM_MAX = 1.4;

        function applyZoom() {
            const wrapper = document.getElementById('landscape-wrapper');
            if (wrapper) {
                // Gunakan properti CSS 'zoom' agar berlaku seperti zoom native browser
                wrapper.style.zoom = zoomLevel;

                // Bersihkan properti transform lama jika sebelumnya tersimpan
                wrapper.style.transform = '';
                wrapper.style.transformOrigin = '';
                wrapper.style.width = '100%';
                wrapper.style.height = '100dvh';
            }
        }

        function zoomIn() {
            if (zoomLevel < ZOOM_MAX) {
                zoomLevel = Math.min(ZOOM_MAX, parseFloat((zoomLevel + ZOOM_STEP).toFixed(1)));
                applyZoom();
            }
        }

        function zoomOut() {
            if (zoomLevel > ZOOM_MIN) {
                zoomLevel = Math.max(ZOOM_MIN, parseFloat((zoomLevel - ZOOM_STEP).toFixed(1)));
                applyZoom();
            }
        }

        // ----------------------------------------------------
        // FULLSCREEN
        // ----------------------------------------------------
        function toggleFullscreen() {
            const iconEnter = document.getElementById('icon-enter-fs');
            const iconExit = document.getElementById('icon-exit-fs');

            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
                if (iconEnter) iconEnter.classList.add('hidden');
                if (iconExit) iconExit.classList.remove('hidden');
            } else {
                document.exitFullscreen().catch(() => {});
                if (iconEnter) iconEnter.classList.remove('hidden');
                if (iconExit) iconExit.classList.add('hidden');
            }
        }

        // 1. Inisialisasi State / Variabel Global
        // Ambil nilai dari database saat halaman dimuat (jika ada), jika tidak mulai dari 0
        let nilaiA = {{ $inputJuri->nilai_a ?? 0 }};
        let nilaiB = {{ $inputJuri->nilai_b ?? 0 }};
        let isLocked = false; // Set true jika form disubmit atau dikunci dewan

        // Pastikan UI menampilkan data awal saat halaman pertama dimuat
        document.addEventListener("DOMContentLoaded", function() {
            renderDisplay();
        });

        // 2. Fungsi Utama
        function updateNilai(jenis, amount) {
            if (isLocked) return; // Cegah input jika sudah dikunci Dewan

            if (jenis === 'A') {
                // Nilai A (Kesalahan) tidak boleh < 0. Batas atas bebas atau disesuaikan (misal 1.00)
                nilaiA = Math.max(0, Math.min(1.00, Math.round((nilaiA + amount) * 100) / 100));
            } else if (jenis === 'B') {
                // Nilai B (Kemantapan) tidak boleh < 0 dan maksimal (misal) 0.10 jika pakai referensi di atas
                nilaiB = Math.max(0, Math.min(0.10, Math.round((nilaiB + amount) * 100) / 100));
            }

            renderDisplay(); // Update UI
            kirimKeServerRealtime(); // Kirim ke server di latar belakang
        }

        function kirimKeServerRealtime() {
            // Ambil variabel dari Blade, pastikan Anda mempassing $partai dan $aktif (peserta yang sedang tampil)
            const partaiId = "{{ isset($partai) ? $partai->id_partai : 0 }}";
            const noJuri = "{{ $no_juri ?? 0 }}";
            const noUrut = "{{ isset($aktif) ? $aktif->no_urut : 0 }}";

            if (partaiId == "0" || noUrut == "0") {
                console.warn("Data partai atau nomor urut kosong, gagal mengirim nilai.");
                return;
            }

            // Tembak ke URL Route yang akan kita buat di bawah
            fetch(`/juri/pool/tunggal-regu/input-nilai/${partaiId}/${noJuri}/${noUrut}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        nilai_a: nilaiA,
                        nilai_b: nilaiB
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        console.error("Error Simpan Realtime:", data.message);
                    }
                })
                .catch(err => console.error("Koneksi bermasalah:", err));
        }
        // 3. Fungsi Update Tampilan Layar
        function renderDisplay() {
            // Update teks di elemen box besar
            document.getElementById('display-nilai-a').innerText = '-' + nilaiA.toFixed(2);
            document.getElementById('display-nilai-b').innerText = '+' + nilaiB.toFixed(2);

            // Kalkulasi Total Nilai (Baseline Tunggal/Regu adalah 9.90)
            let total = 9.90 - nilaiA + nilaiB;

            let elTotal = document.getElementById('display-total');
            if (elTotal) {
                elTotal.innerText = total.toFixed(2);
            }
        }


        // ----------------------------------------------------
        // 2. FUNGSI MASTER: MENGUNCI & MEMBUKA FORM JURI
        // ----------------------------------------------------
        // Fungsi ini dipisah di luar DOMContentLoaded agar bisa diakses oleh simpanKeServer()
        function updateTampilanJuriPool(isOn, statusInput) {
            // A. BUKA/TUTUP MODAL STANDBY (ON/OFF)
            const modalStandby = document.getElementById('modal-standby');
            const wrapperForm = document.getElementById('landscape-wrapper'); // ID wrapper utama form

            if (isOn == 1) {
                if (modalStandby) modalStandby.classList.add('hidden');
                if (wrapperForm) wrapperForm.classList.remove('pointer-events-none');
            } else {
                if (modalStandby) modalStandby.classList.remove('hidden');
                if (wrapperForm) wrapperForm.classList.add('pointer-events-none');
            }

            // B. KUNCI / BUKA FORM SESUAI STATUS (SELESAI/PROSES)
            const statusSaatIni = (statusInput || 'proses').toLowerCase();

            // Tangkap elemen-elemen HTML khusus Tunggal/Regu
            const overlayKunci = document.getElementById('overlay-kunci');
            const btnAktif = document.getElementById('btn-kirim-aktif');
            const btnTerkunci = document.getElementById('btn-kirim-terkunci');

            if (statusSaatIni === 'selesai') {
                isLocked = true; // Kunci variabel global agar fungsi updateNilai() berhenti bekerja

                // Tampilkan Overlay Gelap "NILAI TERKUNCI"
                if (overlayKunci) overlayKunci.classList.remove('hidden');

                // Sembunyikan tombol hijau, tampilkan tombol abu-abu
                if (btnAktif) btnAktif.classList.add('hidden');
                if (btnTerkunci) btnTerkunci.classList.remove('hidden');

            } else {
                isLocked = false; // Buka kuncian variabel global

                // Sembunyikan Overlay Gelap
                if (overlayKunci) overlayKunci.classList.add('hidden');

                // Tampilkan tombol hijau, sembunyikan tombol abu-abu
                if (btnAktif) btnAktif.classList.remove('hidden');
                if (btnTerkunci) btnTerkunci.classList.add('hidden');
            }
        }

        // ----------------------------------------------------
        // 3. INISIALISASI & LISTENER REVERB
        // ----------------------------------------------------
        document.addEventListener('DOMContentLoaded', function() {
            const isOnDb = {{ isset($partai) && isset($kontrol) ? $kontrol->is_on : 0 }};
            const statusDb = "{{ isset($kontrol) ? $kontrol->status_input : 'Proses' }}";
            const partaiId = "{{ $partai->id_partai ?? 0 }}";
            const noJuri = "{{ $no_juri ?? 0 }}";

            @php
                $namaKat = strtolower($partai->kategori->nama_kategori ?? '');
                $jenis = 'solo';
                if (str_contains($namaKat, 'ganda')) {
                    $jenis = 'ganda';
                } elseif (str_contains($namaKat, 'regu')) {
                    $jenis = 'regu';
                } elseif (str_contains($namaKat, 'tunggal')) {
                    $jenis = 'tunggal';
                }
            @endphp

            const jenis = "{{ $jenis }}";
            const namaGelanggang = "{{ $partai->gelanggang ?? 'A' }}";

            // Set tampilan awal saat halaman pertama kali dibuka
            updateTampilanJuriPool(isOnDb, statusDb);

            if (window.Echo) {
                // A. PENDENGAR KONTROL DEWAN (Tanpa Reload!)
                if (partaiId !== "0") {
                    window.Echo.channel(`partai.${jenis}.${partaiId}`)
                        .listen('.ToggleKontrolJuriPool', (e) => {
                            if (e.noJuri == noJuri) {
                                updateTampilanJuriPool(e.isOn, e.statusInput);
                            }
                        })
                        .listen('ToggleKontrolJuriPool', (e) => {
                            if (e.noJuri == noJuri) {
                                updateTampilanJuriPool(e.isOn, e.statusInput);
                            }
                        });
                }

                // =================================================================
                // B. PENDENGAR GLOBAL MULAI (Ini Satu-satunya yang Boleh Me-reload)
                // =================================================================
                const eksekusiRefresh = (e) => {
                    console.log("Sinyal Refresh Diterima! Merefresh tablet juri...");
                    document.body.style.opacity = 0;
                    document.body.style.transition = "opacity 0.5s ease";
                    setTimeout(() => window.location.reload(), 500);
                };

                // Kita cukup mendengarkan channel pool yang spesifik untuk gelanggang tersebut.
                // Jika semua pool menggunakan gelanggang 'A', maka ini akan serentak semua.
                const channelPool = `gelanggang.global.pool.${namaGelanggang}`;

                console.log("JURI MENDENGARKAN REFRESH DI CHANNEL:", channelPool);

                window.Echo.channel(channelPool)
                    .listen('.RefreshDisplay', eksekusiRefresh)
                    .listen('RefreshDisplay', eksekusiRefresh);

            } else {
                console.error("Laravel Echo / Reverb tidak terdeteksi!");
            }
        });

        function simpanKeServer() {
            if (isLocked) return;

            Swal.fire({
                title: 'Selesai Menilai?',
                text: "Nilai ini akan dikunci permanen dan diserahkan ke Dewan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Selesai!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#2563eb' // Warna biru menyesuaikan dengan tombol Anda
            }).then((result) => {
                if (result.isConfirmed) {
                    // Ambil parameter untuk sistem Pool
                    const partaiId = "{{ isset($partai) ? $partai->id_partai : 0 }}";
                    const noJuri = "{{ $no_juri ?? 0 }}";
                    const noUrut = "{{ isset($aktif) ? $aktif->no_urut : 0 }}";

                    if (partaiId == "0" || noUrut == "0") return;

                    fetch(`/juri/pool/tunggal-regu/simpan-nilai/${partaiId}/${noJuri}/${noUrut}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                nilai_a: nilaiA,
                                nilai_b: nilaiB
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // Reload halaman agar mode kunci ($isLocked) aktif dari backend
                                window.location.reload();
                            } else {
                                Swal.fire('Error', data.message || 'Gagal menyimpan nilai akhir', 'error');
                            }
                        })
                        .catch(err => {
                            Swal.fire('Error', 'Terjadi kesalahan koneksi', 'error');
                        });
                }
            });
        }
    </script>
@endsection
