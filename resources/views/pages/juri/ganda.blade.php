@extends('layouts.dewan')

@section('content')
    <style>
        body {
            background-color: #f1f5f9;
            overflow: hidden;
            touch-action: pan-x;
        }

        select {
            background-position: right 1rem center !important;
            background-size: 2em 2em !important;
            text-align-last: center !important;
            text-align: center !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        select option {
            text-align: center;
        }
    </style>

    {{-- JIKA TIDAK ADA PARTAI YANG AKTIF (STANDBY MODE) --}}
    @if (!$partai)
        <div class="h-[100dvh] flex flex-col items-center justify-center relative overflow-hidden">
            <div
                class="absolute inset-0 opacity-5 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-slate-900 via-transparent to-transparent">
            </div>
            <div
                class="animate-pulse bg-white border border-slate-200 rounded-3xl p-16 text-center shadow-2xl relative z-10">
                <h2 class="text-3xl font-black text-slate-400 mb-2">JURI {{ $no_juri }}</h2>
                <h1 class="text-7xl font-black text-slate-800 tracking-tighter mb-4">STANDBY</h1>
                <p class="text-xl font-bold text-slate-400 uppercase tracking-widest">Menunggu Pertandingan Dimulai</p>
            </div>
        </div>
    @else
        {{-- JIKA ADA PARTAI YANG AKTIF, TAMPILKAN FORM PENILAIAN --}}
        @php
            $sudutDb = ucfirst($sudut);
            // Cek status is_on dari table status_kontrol_juri, default 1 jika tidak ada
            $isOn = isset($kontrol) ? $kontrol->is_on : 1;
        @endphp

        {{-- ---------------------------------------------------- --}}
        {{-- MODAL FULLSCREEN: MUNCUL JIKA IS_ON == 0 --}}
        {{-- ---------------------------------------------------- --}}
        {{-- ---------------------------------------------------- --}}
        {{-- MODAL FULLSCREEN: DIKONTROL OLEH JS & REVERB --}}
        {{-- ---------------------------------------------------- --}}
        <div id="modal-standby"
            class="fixed inset-0 z-40 bg-slate-900/95 backdrop-blur-md flex flex-col items-center justify-center transition-all duration-300 {{ $isOn == 1 ? 'hidden' : '' }}">
            <div class="flex flex-col items-center text-center">
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
        {{-- ---------------------------------------------------- --}}
        {{-- ---------------------------------------------------- --}}

        <div id="landscape-wrapper" class="w-full h-[100dvh] p-3 flex flex-col gap-3 overflow-hidden">

            {{-- BARIS 1: HEADER + TOTAL NILAI (digabung sebaris) --}}
            <div class="flex gap-3 shrink-0">

                {{-- Sisi kiri: Info Juri & Nama Peserta --}}
                <div
                    class="{{ $sudut == 'biru' ? 'bg-blue-600' : 'bg-red-600' }} rounded-2xl px-6 py-6 shadow-lg flex items-center text-white relative overflow-hidden flex-1">
                    <div class="absolute top-0 right-0 w-40 h-full bg-white/10 skew-x-12 translate-x-10"></div>
                    <div class="relative z-10 flex items-center gap-4 w-full">

                        <div
                            class="bg-white/20 backdrop-blur-md px-4 py-2 rounded-xl border border-white/30 text-center shadow-inner shrink-0">
                            <p class="text-[14px] font-black uppercase tracking-[0.2em] opacity-80 mb-0.5">JURI</p>
                            <h2 class="text-6xl font-black uppercase italic leading-none">{{ $no_juri }}</h2>
                        </div>

                        <div class="flex-1 border-l border-white/20 pl-4 min-w-0">
                            {{-- TAMBAHAN KATEGORI GANDA (STATIS) --}}
                            <p class="text-2xl font-black uppercase tracking-[0.2em] opacity-80 mb-1">
                                SUDUT {{ $sudutDb }} &bull; PARTAI #{{ $partai->no_partai }} &bull; KATEGORI GANDA
                            </p>

                            {{-- PERBAIKAN: MENAMPILKAN 2 NAMA PESERTA GANDA --}}
                            <h1
                                class="text-4xl md:text-5xl font-black uppercase italic tracking-tight leading-none truncate">
                                @php
                                    $pesertaGanda = $partai->detail_peserta->where('sudut', ucfirst($sudut));
                                @endphp
                                @foreach ($pesertaGanda as $det)
                                    {{ $det->peserta->nama_peserta ?? '-' }}@if (!$loop->last)
                                        &
                                    @endif
                                @endforeach
                            </h1>
                        </div>

                    </div>
                </div>

                {{-- Sisi kanan: Nilai Default & Nilai Akhir --}}
                <div
                    class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700 px-6 py-6 flex items-center gap-4 shrink-0">
                    <div class="text-center pr-4 border-r border-slate-600">
                        <p class="text-xl font-black text-slate-400 uppercase tracking-widest mb-0.5">Nilai Default</p>
                        <p class="text-4xl font-black text-white font-mono leading-none">9.10</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xl font-black text-slate-400 uppercase tracking-widest mb-0.5">Nilai Akhir</p>
                        <p id="display-total" class="text-4xl font-black text-white font-mono leading-none">
                            {{ number_format($nilai->skor_akhir_juri ?? 9.1, 2) }}
                        </p>
                    </div>
                </div>

            </div>

            {{-- INPUT DROPDOWN --}}
            @php
                $isLocked = isset($kontrol) && strtolower($kontrol->status_input) == 'selesai';
            @endphp
            <div class="grid grid-cols-3 gap-3 shrink-0">
                <div class="bg-white rounded-2xl border-4 border-indigo-100 shadow-sm p-6 flex flex-col justify-center">
                    <h3 class="text-center text-2xl font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Teknik
                        Serangan</h3>
                    <select id="nilai_a" onchange="hitungTotalLokal()" {{ $isLocked ? 'disabled' : '' }}
                        class="w-full text-4xl font-black text-center text-indigo-700 bg-indigo-50 border-4 border-indigo-200 rounded-xl py-8 appearance-none {{ $isLocked ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                        @for ($i = 0; $i <= 30; $i++)
                            @php $val = number_format($i / 100, 2); @endphp
                            <option value="{{ $val }}"
                                {{ number_format($nilai->nilai_a ?? 0, 2) == $val ? 'selected' : '' }}>+
                                {{ $val }}</option>
                        @endfor
                    </select>
                </div>
                <div class="bg-white rounded-2xl border-4 border-emerald-100 shadow-sm p-6 flex flex-col justify-center">
                    <h3 class="text-center text-2xl font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Ketegasan
                    </h3>
                    <select id="nilai_b" onchange="hitungTotalLokal()" {{ $isLocked ? 'disabled' : '' }}
                        class="w-full text-4xl font-black text-center text-emerald-700 bg-emerald-50 border-4 border-emerald-200 rounded-xl py-8 appearance-none shadow-inner {{ $isLocked ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                        @for ($i = 0; $i <= 30; $i++)
                            @php $val = number_format($i / 100, 2); @endphp
                            <option value="{{ $val }}"
                                {{ number_format($nilai->nilai_b ?? 0, 2) == $val ? 'selected' : '' }}>+
                                {{ $val }}</option>
                        @endfor
                    </select>
                </div>
                <div class="bg-white rounded-2xl border-4 border-amber-100 shadow-sm p-6 flex flex-col justify-center">
                    <h3 class="text-center text-2xl font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Penjiwaan
                    </h3>
                    <select id="nilai_c" onchange="hitungTotalLokal()" {{ $isLocked ? 'disabled' : '' }}
                        class="w-full text-4xl font-black text-center text-amber-700 bg-amber-50 border-4 border-amber-200 rounded-xl py-8 appearance-none shadow-inner {{ $isLocked ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                        @for ($i = 0; $i <= 30; $i++)
                            @php $val = number_format($i / 100, 2); @endphp
                            <option value="{{ $val }}"
                                {{ number_format($nilai->nilai_c ?? 0, 2) == $val ? 'selected' : '' }}>+
                                {{ $val }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="submit-wrapper flex-1 flex flex-col">
                @if ($isLocked)
                    <button id="btn-submit" disabled
                        class="locked-btn w-full bg-slate-400 text-white font-black text-2xl py-4 rounded-2xl shadow-lg flex items-center justify-center gap-3 cursor-not-allowed">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        NILAI TELAH TERKUNCI
                    </button>
                @else
                    <button id="btn-submit" onclick="simpanKeServer()"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-black text-4xl py-8 rounded-3xl shadow-xl active:scale-[0.98] transition-all flex items-center justify-center gap-4">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        KIRIM NILAI KE DEWAN
                    </button>
                @endif
            </div>

        </div>
    @endif

    {{-- FLOATING CONTROL BUTTONS (kanan bawah) --}}
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
        {{-- Home --}}
        <a href="{{ url('/juri/dashboard') }}"
            class="w-12 h-12 sm:w-12 sm:h-12 md:w-16 md:h-16 lg:w-20 lg:h-20 bg-indigo-600 hover:bg-indigo-500 active:scale-95 text-white rounded-xl md:rounded-2xl lg:rounded-3xl shadow-lg flex items-center justify-center transition-all">
            <svg class="w-5 h-5 md:w-6 md:h-6 lg:w-8 lg:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m-7 0h10a2 2 0 002-2v-6" />
            </svg>
        </a>
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
        // ----------------------------------------------------
        // ZOOM
        // ----------------------------------------------------
        let zoomLevel = 1;
        const ZOOM_STEP = 0.1;
        const ZOOM_MIN = 0.6;
        const ZOOM_MAX = 1.4;

        function applyZoom() {
            const wrapper = document.getElementById('landscape-wrapper');
            if (wrapper) {
                wrapper.style.transform = `scale(${zoomLevel})`;
                wrapper.style.transformOrigin = 'top left';
                wrapper.style.width = `${100 / zoomLevel}%`;
                wrapper.style.height = `${100 / zoomLevel}dvh`;
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
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                document.exitFullscreen().catch(() => {});
            }
        }

        document.addEventListener('fullscreenchange', () => {
            const isFs = !!document.fullscreenElement;
            const iconEnter = document.getElementById('icon-enter-fs');
            const iconExit = document.getElementById('icon-exit-fs');
            if (iconEnter) iconEnter.classList.toggle('hidden', isFs);
            if (iconExit) iconExit.classList.toggle('hidden', !isFs);
        });

        // ----------------------------------------------------
        // SCRIPT KALKULASI & SIMPAN MANUAL
        // ----------------------------------------------------
        @if ($partai)
            function hitungTotalLokal() {
                const valA = parseFloat(document.getElementById('nilai_a').value) || 0;
                const valB = parseFloat(document.getElementById('nilai_b').value) || 0;
                const valC = parseFloat(document.getElementById('nilai_c').value) || 0;

                const total = 9.10 + valA + valB + valC;
                const displayTotal = document.getElementById('display-total');
                if (displayTotal) displayTotal.innerText = total.toFixed(2);
            }

            function simpanKeServer() {
                const valA = parseFloat(document.getElementById('nilai_a').value) || 0;
                const valB = parseFloat(document.getElementById('nilai_b').value) || 0;
                const valC = parseFloat(document.getElementById('nilai_c').value) || 0;
                const totalAkhir = (9.10 + valA + valB + valC).toFixed(2);

                Swal.fire({
                    title: 'Kunci & Kirim Nilai?',
                    html: `Anda akan mengirim Total Nilai Akhir: <br><span class="text-4xl font-black text-green-600 font-mono mt-2 inline-block">${totalAkhir}</span><br><br><span class="text-sm text-red-500">Nilai yang sudah dikirim tidak dapat diubah kembali!</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#22c55e',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Ya, Kirim Sekarang!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const btn = document.getElementById('btn-submit');
                        const originalText = btn.innerHTML;
                        btn.innerHTML = 'MENGIRIM...';
                        btn.disabled = true;

                        const url =
                            "{{ route('juri.ganda.simpan', ['id_detail' => $aktif->id_detail ?? 0, 'no_juri' => $no_juri ?? 0]) }}";

                        fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    nilai_a: valA,
                                    nilai_b: valB,
                                    nilai_c: valC
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    updateTampilanJuri(1, 'Selesai', null); // Otomatis kunci UI lokal
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Nilai telah dikirim ke Layar Dewan.',
                                        timer: 2000,
                                        showConfirmButton: false,
                                        backdrop: `rgba(0,0,0,0.8)`
                                    });
                                } else {
                                    btn.innerHTML = originalText;
                                    btn.disabled = false;
                                    Swal.fire('Gagal', 'Terjadi kesalahan di server', 'error');
                                }
                            })
                            .catch(err => {
                                btn.innerHTML = originalText;
                                btn.disabled = false;
                                Swal.fire('Koneksi Terputus', 'Gagal mengirim nilai. Periksa WiFi tablet.',
                                    'error');
                            });
                    }
                });
            }
        @endif

        // ----------------------------------------------------
        // FUNGSI MASTER: UPDATE UI JURI (MODAL & FORM)
        // ----------------------------------------------------
        function updateTampilanJuri(isOn, statusInput, sudutText) {
            // 1. UPDATE SUDUT (WARNA & TEKS)
            if (sudutText) {
                const sudutTeksLower = sudutText.toLowerCase();
                const labelSudut = document.getElementById('teks-sudut-modal');
                if (labelSudut) labelSudut.innerText = `SUDUT ${sudutText.toUpperCase()}`;

                const bgModal = document.getElementById('bg-modal-juri');
                if (bgModal) {
                    if (sudutTeksLower === 'biru') {
                        bgModal.classList.remove('bg-red-600', 'border-red-500');
                        bgModal.classList.add('bg-blue-600', 'border-blue-500');
                    } else if (sudutTeksLower === 'merah') {
                        bgModal.classList.remove('bg-blue-600', 'border-blue-500');
                        bgModal.classList.add('bg-red-600', 'border-red-500');
                    }
                }
            }

            // 2. BUKA/TUTUP MODAL STANDBY (ON/OFF)
            const modalStandby = document.getElementById('modal-standby');
            const wrapperForm = document.getElementById('landscape-wrapper');

            // Jika nilainya 1 atau true, berarti Juri ON (Modal Hilang, Form Bisa Diklik)
            if (isOn == 1) {
                if (modalStandby) modalStandby.classList.add('hidden');
                if (wrapperForm) wrapperForm.classList.remove('pointer-events-none');
            } else {
                // Jika OFF, Modal Muncul, Form Terkunci
                if (modalStandby) modalStandby.classList.remove('hidden');
                if (wrapperForm) wrapperForm.classList.add('pointer-events-none');
            }

            // 3. KUNCI / BUKA FORM (SELESAI/PROSES)
            const btnSubmit = document.getElementById('btn-submit');
            // Gunakan fallback 'proses' jika statusInput kosong untuk mencegah error toLowerCase()
            const statusSaatIni = (statusInput || 'proses').toLowerCase();

            if (statusSaatIni === 'selesai') {
                ['nilai_a', 'nilai_b', 'nilai_c'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.disabled = true;
                        el.classList.add('cursor-not-allowed', 'bg-gray-200');
                    }
                });
                if (btnSubmit) {
                    btnSubmit.outerHTML = `
                        <div id="btn-submit" class="w-full bg-slate-400 text-white font-black text-2xl py-6 rounded-2xl shadow-lg flex items-center justify-center gap-3 cursor-not-allowed">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            NILAI TELAH TERKUNCI
                        </div>`;
                }
            } else {
                ['nilai_a', 'nilai_b', 'nilai_c'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.disabled = false;
                        el.classList.remove('cursor-not-allowed', 'bg-gray-200');
                    }
                });
                if (btnSubmit) {
                    btnSubmit.outerHTML = `
                        <button id="btn-submit" onclick="simpanKeServer()" class="w-full bg-green-500 hover:bg-green-600 active:bg-green-700 text-white font-black text-2xl py-6 rounded-2xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-3">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            KUNCI & KIRIM NILAI
                        </button>`;
                }
            }
        }

        // ----------------------------------------------------
        // KETIKA HALAMAN PERTAMA KALI DIMUAT
        // ----------------------------------------------------
        document.addEventListener('DOMContentLoaded', function() {
            // PERBAIKAN: Jika $partai kosong, paksa isOnDb jadi 0 agar Modal muncul!
            const isOnDb = {{ isset($partai) && isset($kontrol) ? $kontrol->is_on : 0 }};
            const statusDb = "{{ isset($kontrol) ? $kontrol->status_input : 'Proses' }}";
            const sudutDb = "{{ $sudut ?? '' }}";
            const partaiId = "{{ $partai->id_partai ?? 0 }}";

            // Set tampilan awal sesuai dengan database!
            updateTampilanJuri(isOnDb, statusDb, sudutDb);

            // Mulai mendengarkan Reverb
            if (window.Echo) {

                // 1. PENDENGAR SPESIFIK PARTAI (Hanya aktif jika ada ID Partai)
                if (partaiId != "0") {
                    window.Echo.channel(`partai.ganda.${partaiId}`)
                        .listen('ToggleKontrolJuriGanda', (e) => {
                            // Pastikan event ini memang untuk nomor Juri tablet ini
                            if (e.noJuri == {{ $no_juri ?? 0 }}) {

                                // Panggil Fungsi Master dengan data dari Reverb
                                updateTampilanJuri(e.isOn, e.statusInput, e.sudut);

                                // OPSI REDIRECT: Aktifkan jika Juri harus pindah halaman otomatis
                                if (e.sudut) {
                                    const sudutTeksLower = e.sudut.toLowerCase();
                                    const currentUrlSudut = "{{ strtolower($sudut ?? '') }}";
                                    if (e.isOn == 1 && currentUrlSudut !== sudutTeksLower) {
                                        window.location.href = `/juri/ganda/${partaiId}/${sudutTeksLower}`;
                                    }
                                }
                            }
                        });
                }

                // 2. PENDENGAR GLOBAL (Agar tablet Juri ikut ter-refresh saat Dewan klik "Mulai")
                window.Echo.channel('gelanggang.global.ganda.A')
                    .listen('RefreshDisplay', (e) => {
                        console.log("Sinyal Pertandingan Baru! Reloading...");
                        document.body.style.opacity = 0;
                        document.body.style.transition = "opacity 0.5s ease";
                        setTimeout(() => window.location.reload(), 500);
                    });

            } else {
                console.error("Laravel Echo / Reverb tidak terdeteksi!");
            }
        });
    </script>
@endsection
