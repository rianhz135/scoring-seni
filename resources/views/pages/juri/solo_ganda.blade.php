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
                <h2 class="text-3xl font-black text-slate-400 mb-2">JURI {{ $no_juri }}</h2>
                <h1 class="text-7xl font-black text-slate-800 tracking-tighter mb-4">STANDBY</h1>
                <p class="text-xl font-bold text-slate-400 uppercase tracking-widest">Menunggu Giliran Peserta</p>
            </div>
        </div>
    @else
        @php
            // Status Kontrol Juri (ON/OFF)
            $isOn = isset($kontrol) ? $kontrol->is_on : 1;
            $isLocked = isset($kontrol) && strtolower($kontrol->status_input) == 'selesai';
        @endphp

        {{-- MODAL STANDBY (Digerakkan Reverb) --}}
        <div id="modal-standby"
            class="fixed inset-0 z-50 bg-slate-900/95 backdrop-blur-md flex flex-col items-center justify-center transition-all duration-300 {{ $isOn == 1 ? 'hidden' : '' }}">
            <div class="flex flex-col items-center text-center p-6">
                <div class="px-10 py-4 rounded-full bg-indigo-600 border border-indigo-500 mb-8 shadow-xl animate-bounce">
                    <h2 class="text-4xl font-black text-white tracking-[0.3em]">JURI {{ $no_juri }}</h2>
                </div>
                <h1 class="text-5xl md:text-7xl font-black text-white tracking-tighter uppercase leading-tight mb-4">
                    SILAKAN FOKUS<br>KE GELANGGANG
                </h1>
                <p class="text-indigo-300 text-xl md:text-2xl font-bold tracking-widest uppercase">
                    MENUNGGU INSTRUKSI DEWAN HAKIM
                </p>
            </div>
        </div>

        <div id="landscape-wrapper" class="w-full h-[100dvh] p-2 md:p-3 flex flex-col gap-2 md:gap-3 overflow-hidden">

            {{-- BARIS 1: HEADER & NILAI --}}
            <div class="flex gap-2 md:gap-3 shrink-0 mt-2 md:mt-6">
                {{-- Info Peserta --}}
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

                {{-- Display Skor --}}
                <div
                    class="bg-slate-800 rounded-xl md:rounded-2xl shadow-lg border border-slate-700 px-4 md:px-8 py-3 md:py-6 flex items-center gap-4 md:gap-8 shrink-0">
                    <div class="text-center pr-4 md:pr-8 border-r border-slate-600">
                        <p
                            class="text-[10px] md:text-sm font-black text-slate-400 uppercase tracking-widest mb-0.5 md:mb-1">
                            Standar</p>
                        <p class="text-2xl md:text-5xl font-black text-white font-mono">9.10</p>
                    </div>
                    <div class="text-center">
                        <p
                            class="text-[10px] md:text-sm font-black text-slate-400 uppercase tracking-widest mb-0.5 md:mb-1">
                            Total Nilai</p>
                        <p id="display-total" class="text-3xl md:text-6xl font-black text-emerald-400 font-mono italic">
                            {{ number_format($nilai->skor_akhir_juri ?? 9.1, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- BARIS 2: INPUT DROPDOWN --}}
            <div class="grid grid-cols-3 gap-2 md:gap-4 shrink-0">
                {{-- KEBENARAN TEKNIK (A) --}}
                <div
                    class="bg-white rounded-2xl md:rounded-3xl border-2 md:border-4 border-indigo-100 shadow-xl p-3 md:p-6">
                    <h3
                        class="text-center text-[11px] md:text-xl font-black text-slate-500 uppercase tracking-[0.1em] md:tracking-[0.2em] mb-2 md:mb-4">
                        Teknik Serangan</h3>
                    <select id="nilai_a" onchange="hitungTotalLokal()" {{ $isLocked ? 'disabled' : '' }}
                        class="w-full text-3xl md:text-6xl font-black text-indigo-700 bg-indigo-50 border-2 md:border-4 border-indigo-200 rounded-xl md:rounded-2xl py-5 md:py-10 appearance-none shadow-inner {{ $isLocked ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:bg-indigo-100' }}">
                        @for ($i = 0; $i <= 30; $i++)
                            @php $val = number_format($i / 100, 2); @endphp
                            <option value="{{ $val }}"
                                {{ number_format($nilai->nilai_a ?? 0, 2) == $val ? 'selected' : '' }}>
                                +{{ $val }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- KEMANTAPAN (B) --}}
                <div
                    class="bg-white rounded-2xl md:rounded-3xl border-2 md:border-4 border-emerald-100 shadow-xl p-3 md:p-6">
                    <h3
                        class="text-center text-[11px] md:text-xl font-black text-slate-500 uppercase tracking-[0.1em] md:tracking-[0.2em] mb-2 md:mb-4">
                        Ketegasan / Mantap</h3>
                    <select id="nilai_b" onchange="hitungTotalLokal()" {{ $isLocked ? 'disabled' : '' }}
                        class="w-full text-3xl md:text-6xl font-black text-emerald-700 bg-emerald-50 border-2 md:border-4 border-emerald-200 rounded-xl md:rounded-2xl py-5 md:py-10 appearance-none shadow-inner {{ $isLocked ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:bg-emerald-100' }}">
                        @for ($i = 0; $i <= 30; $i++)
                            @php $val = number_format($i / 100, 2); @endphp
                            <option value="{{ $val }}"
                                {{ number_format($nilai->nilai_b ?? 0, 2) == $val ? 'selected' : '' }}>
                                +{{ $val }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- PENJIWAAN (C) --}}
                <div class="bg-white rounded-2xl md:rounded-3xl border-2 md:border-4 border-amber-100 shadow-xl p-3 md:p-6">
                    <h3
                        class="text-center text-[11px] md:text-xl font-black text-slate-500 uppercase tracking-[0.1em] md:tracking-[0.2em] mb-2 md:mb-4">
                        Penjiwaan / Irama</h3>
                    <select id="nilai_c" onchange="hitungTotalLokal()" {{ $isLocked ? 'disabled' : '' }}
                        class="w-full text-3xl md:text-6xl font-black text-amber-700 bg-amber-50 border-2 md:border-4 border-amber-200 rounded-xl md:rounded-2xl py-5 md:py-10 appearance-none shadow-inner {{ $isLocked ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:bg-amber-100' }}">
                        @for ($i = 0; $i <= 30; $i++)
                            @php $val = number_format($i / 100, 2); @endphp
                            <option value="{{ $val }}"
                                {{ number_format($nilai->nilai_c ?? 0, 2) == $val ? 'selected' : '' }}>
                                +{{ $val }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- BARIS 3: TOMBOL SUBMIT --}}
            <div class="flex-1 flex flex-col pb-1 md:pb-2">
                @if ($isLocked)
                    <div
                        class="w-full bg-slate-400 text-white font-black text-lg md:text-3xl py-5 md:py-8 rounded-2xl md:rounded-3xl shadow-lg flex items-center justify-center gap-2 md:gap-4">
                        <svg class="w-6 h-6 md:w-10 md:h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        NILAI TELAH TERKIRIM & TERKUNCI
                    </div>
                @else
                    <button id="btn-submit" onclick="simpanKeServer()"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-black text-2xl md:text-4xl py-6 md:py-10 rounded-2xl md:rounded-3xl shadow-2xl active:scale-[0.97] transition-all flex items-center justify-center gap-3 md:gap-6">
                        <svg class="w-8 h-8 md:w-12 md:h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        KUNCI & KIRIM NILAI SEKARANG
                    </button>
                @endif
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

        function hitungTotalLokal() {
            const valA = parseFloat(document.getElementById('nilai_a').value) || 0;
            const valB = parseFloat(document.getElementById('nilai_b').value) || 0;
            const valC = parseFloat(document.getElementById('nilai_c').value) || 0;
            const total = 9.10 + valA + valB + valC;
            document.getElementById('display-total').innerText = total.toFixed(2);
        }

        function simpanKeServer() {
            const valA = document.getElementById('nilai_a').value;
            const valB = document.getElementById('nilai_b').value;
            const valC = document.getElementById('nilai_c').value;
            const totalAkhir = (9.10 + parseFloat(valA) + parseFloat(valB) + parseFloat(valC)).toFixed(2);

            Swal.fire({
                title: 'Konfirmasi Nilai',
                html: `<div class="text-6xl font-black text-indigo-600 my-4">${totalAkhir}</div><p>Kirim nilai sekarang? <br><b>Data tidak dapat diubah setelah dikirim!</b></p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'YA, KIRIM!',
                cancelButtonText: 'BATAL',
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#dc2626',
            }).then((result) => {
                if (result.isConfirmed) {
                    const btn = document.getElementById('btn-submit');
                    btn.disabled = true;
                    btn.innerText = 'MEMPROSES...';

                    fetch("{{ route('juri.solo-ganda.simpan', ['id_detail' => $aktif->id_detail ?? 0, 'no_juri' => $no_juri]) }}", {
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
                                Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: 'Nilai terkirim!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                    .then(() => window.location.reload());
                            } else {
                                btn.disabled = false;
                                btn.innerText = 'KUNCI & KIRIM NILAI SEKARANG';
                                Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                            }
                        })
                        .catch(err => {
                            btn.disabled = false;
                            Swal.fire('Error', 'Gagal terhubung ke server', 'error');
                        });
                }
            });
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) document.documentElement.requestFullscreen();
            else document.exitFullscreen();
        }

        // --- REVERB LISTENER ---
        document.addEventListener('DOMContentLoaded', function() {
            const partaiId = "{{ $partai->id_partai ?? 0 }}";
            const noJuri = "{{ $no_juri }}";

            if (window.Echo && partaiId !== "0") {
                // Listen untuk ON/OFF dari Dewan
                window.Echo.channel(`kontrol.juri.${partaiId}`)
                    .listen('ToggleKontrolJuri', (e) => {
                        if (e.noJuri == noJuri) {
                            const modal = document.getElementById('modal-standby');
                            if (e.isOn == 1) modal.classList.add('hidden');
                            else modal.classList.remove('hidden');

                            if (e.statusInput === 'Selesai') window.location.reload();
                        }
                    });

                // Listen jika ada refresh global
                window.Echo.channel('gelanggang.global')
                    .listen('RefreshDisplay', () => window.location.reload());
            }
        });
    </script>
@endsection
