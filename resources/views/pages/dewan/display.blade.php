@extends('layouts.dewan')

@section('content')
    <style>
        body {
            background-color: #0c1938;
            /* Slate 900 */
        }

        @keyframes juriBlinkBiru {
            0% {
                background-color: #2563eb;
                border-color: #93c5fd;
                color: #ffffff;
                box-shadow: 0 0 25px #3b82f6, 0 0 35px #60a5fa;
                transform: scale(1.08);
            }
            60% {
                background-color: #2563eb;
                border-color: #93c5fd;
                color: #ffffff;
                box-shadow: 0 0 20px #3b82f6;
                transform: scale(1.05);
            }
            100% {
                background-color: rgba(30, 41, 59, 0.8);
                border-color: rgba(51, 65, 85, 0.8);
                color: #94a3b8;
                box-shadow: none;
                transform: scale(1);
            }
        }

        @keyframes juriBlinkMerah {
            0% {
                background-color: #dc2626;
                border-color: #fca5a5;
                color: #ffffff;
                box-shadow: 0 0 25px #ef4444, 0 0 35px #f87171;
                transform: scale(1.08);
            }
            60% {
                background-color: #dc2626;
                border-color: #fca5a5;
                color: #ffffff;
                box-shadow: 0 0 20px #ef4444;
                transform: scale(1.05);
            }
            100% {
                background-color: rgba(30, 41, 59, 0.8);
                border-color: rgba(51, 65, 85, 0.8);
                color: #94a3b8;
                box-shadow: none;
                transform: scale(1);
            }
        }

        @keyframes juriBlinkGold {
            0% {
                background-color: #eab308;
                border-color: #fef08a;
                color: #0f172a;
                box-shadow: 0 0 30px #facc15, 0 0 45px #eab308;
                transform: scale(1.12);
            }
            70% {
                background-color: #eab308;
                border-color: #fef08a;
                color: #0f172a;
                box-shadow: 0 0 25px #facc15;
                transform: scale(1.08);
            }
            100% {
                background-color: rgba(30, 41, 59, 0.8);
                border-color: rgba(51, 65, 85, 0.8);
                color: #94a3b8;
                box-shadow: none;
                transform: scale(1);
            }
        }

        .lamp-blink-biru {
            animation: juriBlinkBiru 0.8s ease-out 1 forwards !important;
        }

        .lamp-blink-merah {
            animation: juriBlinkMerah 0.8s ease-out 1 forwards !important;
        }

        .lamp-blink-gold {
            animation: juriBlinkGold 1.0s ease-out 1 forwards !important;
        }

        /* Skor Utama Display Penonton */
        .score-display-number {
            font-size: clamp(9rem, min(34vh, 24vw), 24rem);
            line-height: 0.85;
            font-weight: 900;
            letter-spacing: -0.04em;
        }

        .score-seni-total {
            font-size: clamp(6.5rem, min(22vh, 10.5vw), 11.5rem);
            line-height: 0.9;
        }
    </style>

    @if (($mode ?? '') === 'tanding' && isset($partaiTanding) && $partaiTanding)
        @php
            $sb = $scoresTanding['biru'] ?? [
                'total' => 0, 'poin_masuk' => 0, 'jatuhan_count' => 0, 'poin_jatuhan' => 0,
                'poin_minus' => 0, 'binaan_count_babak' => 0, 'teguran_list' => [],
                'peringatan_list' => [], 'is_disq' => false,
                'has_binaan_1' => false, 'has_binaan_2' => false,
                'has_teguran_1' => false, 'has_teguran_2' => false,
                'has_peringatan_1' => false, 'has_peringatan_2' => false,
            ];
            $sm = $scoresTanding['merah'] ?? [
                'total' => 0, 'poin_masuk' => 0, 'jatuhan_count' => 0, 'poin_jatuhan' => 0,
                'poin_minus' => 0, 'binaan_count_babak' => 0, 'teguran_list' => [],
                'peringatan_list' => [], 'is_disq' => false,
                'has_binaan_1' => false, 'has_binaan_2' => false,
                'has_teguran_1' => false, 'has_teguran_2' => false,
                'has_peringatan_1' => false, 'has_peringatan_2' => false,
            ];
            $babakAktif = $partaiTanding->babak_aktif ?: 1;
            $waktuTimer = (int) ($partaiTanding->waktu_timer ?? 0);
            $statusTimer = $partaiTanding->status_timer ?? 'stop';
        @endphp

        <div class="h-[96vh] flex flex-col justify-between p-3.5 gap-3 text-white select-none">
            {{-- 0. TOP EVENT BAR (GRID 1:3:1 -> LOGO 1, INFORMASI PERTANDINGAN, LOGO 2) --}}
            <div class="bg-slate-800/90 border border-slate-700 rounded-3xl px-6 py-2.5 grid grid-cols-5 items-center shadow-xl backdrop-blur-md shrink-0 gap-4">
                {{-- Grid 1: Logo Kiri (Logo 1) - Col 1 (Rasio 1) --}}
                <div class="col-span-1 flex items-center justify-start">
                    @if (file_exists(public_path('smi.png')))
                        <img src="{{ asset('smi.png') }}" alt="Logo 1" class="h-12 md:h-14 xl:h-16 max-w-full object-contain drop-shadow">
                    @elseif (file_exists(public_path('logo1.png')))
                        <img src="{{ asset('logo1.png') }}" alt="Logo 1" class="h-12 md:h-14 xl:h-16 max-w-full object-contain drop-shadow">
                    @else
                        {{-- Fallback SVG Logo 1 (Perisai Beladiri) --}}
                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-slate-700/80 border border-slate-600 flex items-center justify-center text-indigo-300 shadow">
                            <svg class="w-7 h-7 md:w-8 md:h-8 text-indigo-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3zm0 2.18l6 2.25v4.66c0 4.1-2.67 7.94-6 9.01-3.33-1.07-6-4.91-6-9.01V6.43l6-2.25zM12 7a3 3 0 100 6 3 3 0 000-6z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Grid 2: Informasi Pertandingan - Col 3 (Rasio 3) --}}
                <div class="col-span-3 flex flex-col items-center justify-center text-center">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl xl:text-5xl font-black uppercase tracking-[0.15em] sm:tracking-[0.2em] md:tracking-[0.25em] text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-yellow-200 to-amber-400 drop-shadow-[0_4px_15px_rgba(245,158,11,0.4)] leading-tight whitespace-nowrap">
                        PERTANDINGAN SMI CUP 2026
                    </h1>
                </div>

                {{-- Grid 3: Logo Kanan (Logo 2) - Col 1 (Rasio 1) --}}
                <div class="col-span-1 flex items-center justify-end">
                    @if (file_exists(public_path('logo2.png')))
                        <img src="{{ asset('logo2.png') }}" alt="Logo 2" class="h-12 md:h-14 xl:h-16 max-w-full object-contain drop-shadow">
                    @else
                        {{-- Fallback SVG Logo 2 (Piala Juara) --}}
                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-slate-700/80 border border-slate-600 flex items-center justify-center text-amber-300 shadow">
                            <svg class="w-7 h-7 md:w-8 md:h-8 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94.63 1.5 1.98 2.63 3.61 2.96V19H7v2h10v-2h-4v-3.1c1.63-.33 2.98-1.46 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 1. HEADER INFO PERTANDINGAN & TIMER UTAMA (GRID TERPISAH RASIO 3:1) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch shrink-0">
                {{-- Kiri: Info Gelanggang, Kelas, dan Status Babak (9 Cols = 3 Rasio) --}}
                <div class="lg:col-span-9 bg-slate-800/90 border border-slate-700 rounded-3xl p-4 px-6 flex items-center justify-between shadow-2xl backdrop-blur-md">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="px-4 py-2 bg-primary-600 text-white text-xl font-black uppercase rounded-xl tracking-wider shadow">
                            GELANGGANG {{ $partaiTanding->gelanggang ?? 'A' }}
                        </span>
                        <span class="px-4 py-2 bg-slate-700 text-slate-100 text-xl font-black uppercase rounded-xl tracking-wider border border-slate-600">
                            PARTAI #{{ $partaiTanding->no_partai }}
                        </span>
                        <span class="px-4 py-2 bg-slate-700 text-slate-100 text-xl font-black uppercase rounded-xl tracking-wider border border-slate-600">
                            TANDING - {{ $partaiTanding->kelas ?? 'DEWASA' }}
                        </span>
                        <span class="px-3.5 py-2 bg-slate-700 text-slate-200 text-lg font-extrabold uppercase rounded-xl border border-slate-600">
                            {{ $partaiTanding->fase_babak ?? 'Penyisihan' }}
                        </span>
                    </div>

                    {{-- Babak Badge --}}
                    <div id="tanding-babak-badge" class="px-6 py-2 rounded-2xl bg-slate-700/80 border-2 border-amber-400/80 text-amber-300 text-2xl font-black uppercase tracking-[0.25em] shadow-lg shrink-0">
                        BABAK {{ $babakAktif }}
                    </div>
                </div>

                {{-- Kanan: Timer Digital (3 Cols = 1 Rasio -> Total 9:3 = 3:1) --}}
                <div class="lg:col-span-3 bg-slate-800/90 border border-slate-700 rounded-3xl p-3 px-6 flex items-center justify-center shadow-2xl backdrop-blur-md">
                    <div class="flex items-center justify-center gap-3.5 w-full h-full">
                        <span id="tanding-timer-dot" class="w-4 h-4 rounded-full {{ $statusTimer === 'running' ? 'bg-emerald-400 shadow-[0_0_12px_#34d399]' : ($statusTimer === 'pause' ? 'bg-amber-400' : 'bg-red-500') }}"></span>
                        <span id="display-timer-tanding"
                              class="text-6xl xl:text-7xl 2xl:text-8xl font-black text-white font-mono tracking-tight tabular-nums leading-none"
                              data-seconds="{{ $waktuTimer }}"
                              data-status="{{ $statusTimer }}">
                            {{ sprintf('%02d:%02d', floor($waktuTimer / 60), $waktuTimer % 60) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- 2. ARENA SKOR UTAMA (SPLIT 2 SUDUT: BIRU & MERAH) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 flex-1 min-h-0">
                
                {{-- ==================== SUDUT BIRU ==================== --}}
                <div style="background-color: #2563eb !important;"
                    class="flex flex-col justify-between rounded-3xl p-5 shadow-2xl ring-4 ring-white/10 border border-white/10 relative overflow-hidden transition-all duration-300">
                    {{-- Dekorasi Background Putih Transparan seperti Seni --}}
                    <div class="absolute top-0 right-0 w-64 h-full bg-white/10 skew-x-12 translate-x-16 pointer-events-none"></div>

                    {{-- Header Atlet Biru --}}
                    <div class="relative z-10 flex justify-between items-start pb-3 border-b border-white/20">
                        <div class="flex-1 pr-3">
                            <span class="inline-block px-3.5 py-1 bg-slate-900/60 backdrop-blur-md text-white text-xs font-black uppercase tracking-[0.25em] rounded-lg shadow mb-1.5 border border-white/20">
                                SUDUT BIRU
                            </span>
                            <h2 class="text-3xl xl:text-4xl font-black text-white uppercase tracking-tight truncate drop-shadow-md">
                                {{ $partaiTanding->pesertaBiru->nama ?? 'SUDUT BIRU' }}
                            </h2>
                            <p class="text-lg font-bold text-blue-100 uppercase tracking-wide truncate drop-shadow-sm">
                                {{ $partaiTanding->pesertaBiru->kontingen ?? '-' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] font-black uppercase tracking-widest text-blue-100/80 block mb-0.5">Jatuhan</span>
                            <div class="px-3.5 py-1.5 rounded-xl bg-black/20 border border-white/20 text-white text-base font-black shadow-inner">
                                <span id="count-biru-jatuhan">{{ $sb['jatuhan_count'] }}</span>x (+<span id="poin-biru-jatuhan">{{ $sb['poin_jatuhan'] }}</span>)
                            </div>
                        </div>
                    </div>

                    {{-- SKOR UTAMA BIRU (TANPA TEXT "TOTAL SKOR" & TANPA EFEK GLOW) --}}
                    <div class="relative z-10 my-auto py-2 text-center flex items-center justify-center flex-1 min-h-0">
                        <div id="disq-biru-badge" class="{{ $sb['is_disq'] ? '' : 'hidden' }} absolute inset-0 flex items-center justify-center bg-red-950/95 border-4 border-red-500 rounded-2xl z-20 backdrop-blur-sm animate-pulse">
                            <span class="text-4xl xl:text-5xl font-black text-red-100 uppercase tracking-[0.2em] drop-shadow-lg">
                                TERDISKUALIFIKASI
                            </span>
                        </div>
                        <div class="flex items-center justify-center w-full">
                            <span id="score-biru-display" class="score-display-number font-black font-mono tracking-tight text-white leading-none drop-shadow-md whitespace-nowrap">
                                {{ $sb['is_disq'] ? 'DISQ' : $sb['total'] }}
                            </span>
                        </div>
                    </div>

                    {{-- STATUS HUKUMAN & SENSOR JURI BIRU --}}
                    <div class="relative z-10 space-y-3 pt-3 border-t border-white/20">
                        {{-- STATUS PELANGGARAN: BINAAN, TEGURAN, PERINGATAN --}}
                        <div class="grid grid-cols-3 gap-2">
                            {{-- Binaan --}}
                            <div class="bg-slate-900/65 rounded-2xl p-2.5 border border-white/20 text-center shadow-md backdrop-blur-xs">
                                <span class="text-[10px] font-black uppercase tracking-wider text-white block mb-1.5">Binaan (Babak)</span>
                                <div class="flex justify-center gap-1.5">
                                    <span id="badge-biru-binaan-1" class="w-7 h-7 rounded-lg text-xs font-black flex items-center justify-center transition-all {{ $sb['has_binaan_1'] ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/40 ring-2 ring-amber-300' : 'bg-slate-950/60 text-white/50 border border-white/10' }}">1</span>
                                    <span id="badge-biru-binaan-2" class="w-7 h-7 rounded-lg text-xs font-black flex items-center justify-center transition-all {{ $sb['has_binaan_2'] ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/40 ring-2 ring-amber-300' : 'bg-slate-950/60 text-white/50 border border-white/10' }}">2</span>
                                </div>
                            </div>
                            {{-- Teguran --}}
                            <div class="bg-slate-900/65 rounded-2xl p-2.5 border border-white/20 text-center shadow-md backdrop-blur-xs">
                                <span class="text-[10px] font-black uppercase tracking-wider text-white block mb-1.5">Teguran (Babak)</span>
                                <div class="flex justify-center gap-1.5">
                                    <span id="badge-biru-teguran-1" class="px-2 h-7 rounded-lg text-[11px] font-black flex items-center justify-center transition-all {{ $sb['has_teguran_1'] ? 'bg-orange-500 text-white shadow-md shadow-orange-500/40 ring-2 ring-orange-400' : 'bg-slate-950/60 text-white/50 border border-white/10' }}">T1 (-1)</span>
                                    <span id="badge-biru-teguran-2" class="px-2 h-7 rounded-lg text-[11px] font-black flex items-center justify-center transition-all {{ $sb['has_teguran_2'] ? 'bg-orange-600 text-white shadow-md shadow-orange-600/40 ring-2 ring-orange-500' : 'bg-slate-950/60 text-white/50 border border-white/10' }}">T2 (-2)</span>
                                </div>
                            </div>
                            {{-- Peringatan --}}
                            <div class="bg-slate-900/65 rounded-2xl p-2.5 border border-white/20 text-center shadow-md backdrop-blur-xs">
                                <span class="text-[10px] font-black uppercase tracking-wider text-white block mb-1.5">Peringatan (Match)</span>
                                <div class="flex justify-center gap-1.5">
                                    <span id="badge-biru-peringatan-1" class="px-2 h-7 rounded-lg text-[11px] font-black flex items-center justify-center transition-all {{ $sb['has_peringatan_1'] ? 'bg-red-600 text-white shadow-md shadow-red-600/40 ring-2 ring-red-400' : 'bg-slate-950/60 text-white/50 border border-white/10' }}">P1 (-5)</span>
                                    <span id="badge-biru-peringatan-2" class="px-2 h-7 rounded-lg text-[11px] font-black flex items-center justify-center transition-all {{ $sb['has_peringatan_2'] ? 'bg-red-700 text-white shadow-md shadow-red-700/50 ring-2 ring-red-500' : 'bg-slate-950/60 text-white/50 border border-white/10' }}">P2 (-10)</span>
                                </div>
                            </div>
                        </div>

                        {{-- STATUS INPUT JURI 1, 2, 3 BIRU (PUKULAN & TENDANGAN) --}}
                        <div class="bg-slate-900/65 border border-white/20 rounded-2xl p-3 shadow-inner backdrop-blur-xs">
                            <div class="flex items-center justify-between text-[11px] font-black uppercase tracking-wider text-white mb-2">
                                <span>Status Input Juri Sudut Biru</span>
                                <span class="text-[9px] text-blue-100/70 font-mono tracking-normal">Konsensus IPSI 2/3</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                {{-- Pukulan (+1) --}}
                                <div class="bg-slate-950/60 rounded-xl p-2 border border-white/10">
                                    <div class="flex justify-between items-center mb-1.5 px-1">
                                        <span class="text-xs font-bold text-white">Pukulan</span>
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-white/20 text-white">+1</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-1 text-center">
                                        <div id="lamp-biru-pukulan-1" class="lamp-box py-1.5 px-1 rounded-lg bg-slate-800/90 border border-white/15 text-white/70 font-bold transition-all duration-150">
                                            <span class="lamp-dot block w-2 h-2 rounded-full bg-white/40 mx-auto mb-1"></span>
                                            <span class="text-[11px]">J1</span>
                                        </div>
                                        <div id="lamp-biru-pukulan-2" class="lamp-box py-1.5 px-1 rounded-lg bg-slate-800/90 border border-white/15 text-white/70 font-bold transition-all duration-150">
                                            <span class="lamp-dot block w-2 h-2 rounded-full bg-white/40 mx-auto mb-1"></span>
                                            <span class="text-[11px]">J2</span>
                                        </div>
                                        <div id="lamp-biru-pukulan-3" class="lamp-box py-1.5 px-1 rounded-lg bg-slate-800/90 border border-white/15 text-white/70 font-bold transition-all duration-150">
                                            <span class="lamp-dot block w-2 h-2 rounded-full bg-white/40 mx-auto mb-1"></span>
                                            <span class="text-[11px]">J3</span>
                                        </div>
                                    </div>
                                </div>
                                {{-- Tendangan (+2) --}}
                                <div class="bg-slate-950/60 rounded-xl p-2 border border-white/10">
                                    <div class="flex justify-between items-center mb-1.5 px-1">
                                        <span class="text-xs font-bold text-white">Tendangan</span>
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-white/20 text-white">+2</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-1 text-center">
                                        <div id="lamp-biru-tendangan-1" class="lamp-box py-1.5 px-1 rounded-lg bg-slate-800/90 border border-white/15 text-white/70 font-bold transition-all duration-150">
                                            <span class="lamp-dot block w-2 h-2 rounded-full bg-white/40 mx-auto mb-1"></span>
                                            <span class="text-[11px]">J1</span>
                                        </div>
                                        <div id="lamp-biru-tendangan-2" class="lamp-box py-1.5 px-1 rounded-lg bg-slate-800/90 border border-white/15 text-white/70 font-bold transition-all duration-150">
                                            <span class="lamp-dot block w-2 h-2 rounded-full bg-white/40 mx-auto mb-1"></span>
                                            <span class="text-[11px]">J2</span>
                                        </div>
                                        <div id="lamp-biru-tendangan-3" class="lamp-box py-1.5 px-1 rounded-lg bg-slate-800/90 border border-white/15 text-white/70 font-bold transition-all duration-150">
                                            <span class="lamp-dot block w-2 h-2 rounded-full bg-white/40 mx-auto mb-1"></span>
                                            <span class="text-[11px]">J3</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== SUDUT MERAH ==================== --}}
                <div style="background-color: #dc2626 !important;"
                    class="flex flex-col justify-between rounded-3xl p-5 shadow-2xl ring-4 ring-white/10 border border-white/10 relative overflow-hidden transition-all duration-300">
                    {{-- Dekorasi Background Putih Transparan seperti Seni --}}
                    <div class="absolute top-0 right-0 w-64 h-full bg-white/10 skew-x-12 translate-x-16 pointer-events-none"></div>

                    {{-- Header Atlet Merah --}}
                    <div class="relative z-10 flex justify-between items-start pb-3 border-b border-white/20">
                        <div class="flex-1 pr-3">
                            <span class="inline-block px-3.5 py-1 bg-slate-900/60 backdrop-blur-md text-white text-xs font-black uppercase tracking-[0.25em] rounded-lg shadow mb-1.5 border border-white/20">
                                SUDUT MERAH
                            </span>
                            <h2 class="text-3xl xl:text-4xl font-black text-white uppercase tracking-tight truncate drop-shadow-md">
                                {{ $partaiTanding->pesertaMerah->nama ?? 'SUDUT MERAH' }}
                            </h2>
                            <p class="text-lg font-bold text-red-100 uppercase tracking-wide truncate drop-shadow-sm">
                                {{ $partaiTanding->pesertaMerah->kontingen ?? '-' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] font-black uppercase tracking-widest text-red-100/80 block mb-0.5">Jatuhan</span>
                            <div class="px-3.5 py-1.5 rounded-xl bg-black/20 border border-white/20 text-white text-base font-black shadow-inner">
                                <span id="count-merah-jatuhan">{{ $sm['jatuhan_count'] }}</span>x (+<span id="poin-merah-jatuhan">{{ $sm['poin_jatuhan'] }}</span>)
                            </div>
                        </div>
                    </div>

                    {{-- SKOR UTAMA MERAH (TANPA TEXT "TOTAL SKOR" & TANPA EFEK GLOW) --}}
                    <div class="relative z-10 my-auto py-2 text-center flex items-center justify-center flex-1 min-h-0">
                        <div id="disq-merah-badge" class="{{ $sm['is_disq'] ? '' : 'hidden' }} absolute inset-0 flex items-center justify-center bg-red-950/95 border-4 border-red-500 rounded-2xl z-20 backdrop-blur-sm animate-pulse">
                            <span class="text-4xl xl:text-5xl font-black text-red-100 uppercase tracking-[0.2em] drop-shadow-lg">
                                TERDISKUALIFIKASI
                            </span>
                        </div>
                        <div class="flex items-center justify-center w-full">
                            <span id="score-merah-display" class="score-display-number font-black font-mono tracking-tight text-white leading-none drop-shadow-md whitespace-nowrap">
                                {{ $sm['is_disq'] ? 'DISQ' : $sm['total'] }}
                            </span>
                        </div>
                    </div>

                    {{-- STATUS HUKUMAN & SENSOR JURI MERAH --}}
                    <div class="relative z-10 space-y-3 pt-3 border-t border-white/20">
                        {{-- STATUS PELANGGARAN: BINAAN, TEGURAN, PERINGATAN --}}
                        <div class="grid grid-cols-3 gap-2">
                            {{-- Binaan --}}
                            <div class="bg-slate-900/65 rounded-2xl p-2.5 border border-white/20 text-center shadow-md backdrop-blur-xs">
                                <span class="text-[10px] font-black uppercase tracking-wider text-white block mb-1.5">Binaan (Babak)</span>
                                <div class="flex justify-center gap-1.5">
                                    <span id="badge-merah-binaan-1" class="w-7 h-7 rounded-lg text-xs font-black flex items-center justify-center transition-all {{ $sm['has_binaan_1'] ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/40 ring-2 ring-amber-300' : 'bg-slate-950/60 text-white/50 border border-white/10' }}">1</span>
                                    <span id="badge-merah-binaan-2" class="w-7 h-7 rounded-lg text-xs font-black flex items-center justify-center transition-all {{ $sm['has_binaan_2'] ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/40 ring-2 ring-amber-300' : 'bg-slate-950/60 text-white/50 border border-white/10' }}">2</span>
                                </div>
                            </div>
                            {{-- Teguran --}}
                            <div class="bg-slate-900/65 rounded-2xl p-2.5 border border-white/20 text-center shadow-md backdrop-blur-xs">
                                <span class="text-[10px] font-black uppercase tracking-wider text-white block mb-1.5">Teguran (Babak)</span>
                                <div class="flex justify-center gap-1.5">
                                    <span id="badge-merah-teguran-1" class="px-2 h-7 rounded-lg text-[11px] font-black flex items-center justify-center transition-all {{ $sm['has_teguran_1'] ? 'bg-orange-500 text-white shadow-md shadow-orange-500/40 ring-2 ring-orange-400' : 'bg-slate-950/60 text-white/50 border border-white/10' }}">T1 (-1)</span>
                                    <span id="badge-merah-teguran-2" class="px-2 h-7 rounded-lg text-[11px] font-black flex items-center justify-center transition-all {{ $sm['has_teguran_2'] ? 'bg-orange-600 text-white shadow-md shadow-orange-600/40 ring-2 ring-orange-500' : 'bg-slate-950/60 text-white/50 border border-white/10' }}">T2 (-2)</span>
                                </div>
                            </div>
                            {{-- Peringatan --}}
                            <div class="bg-slate-900/65 rounded-2xl p-2.5 border border-white/20 text-center shadow-md backdrop-blur-xs">
                                <span class="text-[10px] font-black uppercase tracking-wider text-white block mb-1.5">Peringatan (Match)</span>
                                <div class="flex justify-center gap-1.5">
                                    <span id="badge-merah-peringatan-1" class="px-2 h-7 rounded-lg text-[11px] font-black flex items-center justify-center transition-all {{ $sm['has_peringatan_1'] ? 'bg-red-600 text-white shadow-md shadow-red-600/40 ring-2 ring-red-400' : 'bg-slate-950/60 text-white/50 border border-white/10' }}">P1 (-5)</span>
                                    <span id="badge-merah-peringatan-2" class="px-2 h-7 rounded-lg text-[11px] font-black flex items-center justify-center transition-all {{ $sm['has_peringatan_2'] ? 'bg-red-700 text-white shadow-md shadow-red-700/50 ring-2 ring-red-500' : 'bg-slate-950/60 text-white/50 border border-white/10' }}">P2 (-10)</span>
                                </div>
                            </div>
                        </div>

                        {{-- STATUS INPUT JURI 1, 2, 3 MERAH (PUKULAN & TENDANGAN) --}}
                        <div class="bg-slate-900/65 border border-white/20 rounded-2xl p-3 shadow-inner backdrop-blur-xs">
                            <div class="flex items-center justify-between text-[11px] font-black uppercase tracking-wider text-white mb-2">
                                <span>Status Input Juri Sudut Merah</span>
                                <span class="text-[9px] text-red-100/70 font-mono tracking-normal">Konsensus IPSI 2/3</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                {{-- Pukulan (+1) --}}
                                <div class="bg-slate-950/60 rounded-xl p-2 border border-white/10">
                                    <div class="flex justify-between items-center mb-1.5 px-1">
                                        <span class="text-xs font-bold text-white">Pukulan</span>
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-white/20 text-white">+1</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-1 text-center">
                                        <div id="lamp-merah-pukulan-1" class="lamp-box py-1.5 px-1 rounded-lg bg-slate-800/90 border border-white/15 text-white/70 font-bold transition-all duration-150">
                                            <span class="lamp-dot block w-2 h-2 rounded-full bg-white/40 mx-auto mb-1"></span>
                                            <span class="text-[11px]">J1</span>
                                        </div>
                                        <div id="lamp-merah-pukulan-2" class="lamp-box py-1.5 px-1 rounded-lg bg-slate-800/90 border border-white/15 text-white/70 font-bold transition-all duration-150">
                                            <span class="lamp-dot block w-2 h-2 rounded-full bg-white/40 mx-auto mb-1"></span>
                                            <span class="text-[11px]">J2</span>
                                        </div>
                                        <div id="lamp-merah-pukulan-3" class="lamp-box py-1.5 px-1 rounded-lg bg-slate-800/90 border border-white/15 text-white/70 font-bold transition-all duration-150">
                                            <span class="lamp-dot block w-2 h-2 rounded-full bg-white/40 mx-auto mb-1"></span>
                                            <span class="text-[11px]">J3</span>
                                        </div>
                                    </div>
                                </div>
                                {{-- Tendangan (+2) --}}
                                <div class="bg-slate-950/60 rounded-xl p-2 border border-white/10">
                                    <div class="flex justify-between items-center mb-1.5 px-1">
                                        <span class="text-xs font-bold text-white">Tendangan</span>
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-white/20 text-white">+2</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-1 text-center">
                                        <div id="lamp-merah-tendangan-1" class="lamp-box py-1.5 px-1 rounded-lg bg-slate-800/90 border border-white/15 text-white/70 font-bold transition-all duration-150">
                                            <span class="lamp-dot block w-2 h-2 rounded-full bg-white/40 mx-auto mb-1"></span>
                                            <span class="text-[11px]">J1</span>
                                        </div>
                                        <div id="lamp-merah-tendangan-2" class="lamp-box py-1.5 px-1 rounded-lg bg-slate-800/90 border border-white/15 text-white/70 font-bold transition-all duration-150">
                                            <span class="lamp-dot block w-2 h-2 rounded-full bg-white/40 mx-auto mb-1"></span>
                                            <span class="text-[11px]">J2</span>
                                        </div>
                                        <div id="lamp-merah-tendangan-3" class="lamp-box py-1.5 px-1 rounded-lg bg-slate-800/90 border border-white/15 text-white/70 font-bold transition-all duration-150">
                                            <span class="lamp-dot block w-2 h-2 rounded-full bg-white/40 mx-auto mb-1"></span>
                                            <span class="text-[11px]">J3</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- JAVASCRIPT LOGIC REAL-TIME REVERB KHUSUS TANDING --}}
        <script>
            (function() {
                const partaiTandingId = "{{ $partaiTanding->id }}";
                let localTimerTanding = parseInt("{{ $waktuTimer }}") || 0;
                let isTimerRunning = ("{{ $statusTimer }}" === 'running');
                let timerTandingInterval = null;

                function formatTandingTime(totalSeconds) {
                    const mins = Math.floor(totalSeconds / 60);
                    const secs = totalSeconds % 60;
                    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                }

                function renderTandingTimer() {
                    const el = document.getElementById('display-timer-tanding');
                    if (el) el.innerText = formatTandingTime(localTimerTanding);
                }

                function setTimerState(running) {
                    const dot = document.getElementById('tanding-timer-dot');
                    if (running) {
                        isTimerRunning = true;
                        if (dot) dot.className = "w-4 h-4 rounded-full bg-emerald-400 shadow-[0_0_12px_#34d399]";
                        if (timerTandingInterval) clearInterval(timerTandingInterval);
                        timerTandingInterval = setInterval(() => {
                            if (localTimerTanding > 0) {
                                localTimerTanding--;
                                renderTandingTimer();
                            } else {
                                clearInterval(timerTandingInterval);
                                isTimerRunning = false;
                                if (dot) dot.className = "w-4 h-4 rounded-full bg-red-500";
                            }
                        }, 1000);
                    } else {
                        isTimerRunning = false;
                        if (dot) dot.className = "w-4 h-4 rounded-full bg-amber-400";
                        if (timerTandingInterval) clearInterval(timerTandingInterval);
                    }
                }

                if (isTimerRunning) {
                    setTimerState(true);
                } else {
                    renderTandingTimer();
                }

                // Render Score & Pelanggaran Helper
                window.renderTandingScores = function(scores, babak) {
                    if (!scores) return;

                    // Update Babak Badge if provided
                    if (babak) {
                        const babakBadge = document.getElementById('tanding-babak-badge');
                        if (babakBadge) babakBadge.innerText = `BABAK ${babak}`;
                    }

                    // 1. Sudut Biru
                    if (scores.biru) {
                        const sb = scores.biru;
                        const scoreEl = document.getElementById('score-biru-display');
                        const disqBadge = document.getElementById('disq-biru-badge');
                        if (scoreEl) scoreEl.innerText = sb.is_disq ? 'DISQ' : sb.total;
                        if (disqBadge) {
                            if (sb.is_disq) disqBadge.classList.remove('hidden');
                            else disqBadge.classList.add('hidden');
                        }

                        const jCount = document.getElementById('count-biru-jatuhan');
                        const jPoin = document.getElementById('poin-biru-jatuhan');
                        if (jCount) jCount.innerText = sb.jatuhan_count;
                        if (jPoin) jPoin.innerText = sb.poin_jatuhan;

                        // Binaan
                        updatePill('badge-biru-binaan-1', sb.has_binaan_1, 'amber');
                        updatePill('badge-biru-binaan-2', sb.has_binaan_2, 'amber');

                        // Teguran
                        updatePill('badge-biru-teguran-1', sb.has_teguran_1, 'orange');
                        updatePill('badge-biru-teguran-2', sb.has_teguran_2, 'orange');

                        // Peringatan
                        updatePill('badge-biru-peringatan-1', sb.has_peringatan_1, 'red');
                        updatePill('badge-biru-peringatan-2', sb.has_peringatan_2, 'red');
                    }

                    // 2. Sudut Merah
                    if (scores.merah) {
                        const sm = scores.merah;
                        const scoreEl = document.getElementById('score-merah-display');
                        const disqBadge = document.getElementById('disq-merah-badge');
                        if (scoreEl) scoreEl.innerText = sm.is_disq ? 'DISQ' : sm.total;
                        if (disqBadge) {
                            if (sm.is_disq) disqBadge.classList.remove('hidden');
                            else disqBadge.classList.add('hidden');
                        }

                        const jCount = document.getElementById('count-merah-jatuhan');
                        const jPoin = document.getElementById('poin-merah-jatuhan');
                        if (jCount) jCount.innerText = sm.jatuhan_count;
                        if (jPoin) jPoin.innerText = sm.poin_jatuhan;

                        // Binaan
                        updatePill('badge-merah-binaan-1', sm.has_binaan_1, 'amber');
                        updatePill('badge-merah-binaan-2', sm.has_binaan_2, 'amber');

                        // Teguran
                        updatePill('badge-merah-teguran-1', sm.has_teguran_1, 'orange');
                        updatePill('badge-merah-teguran-2', sm.has_teguran_2, 'orange');

                        // Peringatan
                        updatePill('badge-merah-peringatan-1', sm.has_peringatan_1, 'red');
                        updatePill('badge-merah-peringatan-2', sm.has_peringatan_2, 'red');
                    }
                };

                function updatePill(id, isActive, color) {
                    const el = document.getElementById(id);
                    if (!el) return;
                    if (isActive) {
                        if (color === 'amber') {
                            el.className = "w-7 h-7 rounded-lg text-xs font-black flex items-center justify-center transition-all bg-amber-400 text-slate-950 shadow-md shadow-amber-400/40 ring-2 ring-amber-300";
                        } else if (color === 'orange') {
                            el.className = "px-2 h-7 rounded-lg text-[11px] font-black flex items-center justify-center transition-all bg-orange-500 text-white shadow-md shadow-orange-500/40 ring-2 ring-orange-400";
                        } else if (color === 'red') {
                            el.className = "px-2 h-7 rounded-lg text-[11px] font-black flex items-center justify-center transition-all bg-red-600 text-white shadow-md shadow-red-600/40 ring-2 ring-red-400";
                        }
                    } else {
                        if (id.includes('binaan')) {
                            el.className = "w-7 h-7 rounded-lg text-xs font-black flex items-center justify-center transition-all bg-slate-950/60 text-white/50 border border-white/10";
                        } else {
                            el.className = "px-2 h-7 rounded-lg text-[11px] font-black flex items-center justify-center transition-all bg-slate-950/60 text-white/50 border border-white/10";
                        }
                    }
                }

                // Handler Input Sensor Juri Real-time
                function triggerJuriLamp(sudut, jenis, noJuri) {
                    const lampId = `lamp-${sudut.toLowerCase()}-${jenis.toLowerCase()}-${noJuri}`;
                    const lamp = document.getElementById(lampId);
                    if (!lamp) return;

                    const dot = lamp.querySelector('.lamp-dot');
                    const isBiru = (sudut.toLowerCase() === 'biru');

                    // Bersihkan timer reset sebelumnya jika juri klik berkali-kali
                    if (lamp._resetTimer) clearTimeout(lamp._resetTimer);

                    // Hapus class blink sebelumnya & trigger reflow agar animasi 1x kedip dapat dipicu ulang
                    lamp.classList.remove('lamp-blink-biru', 'lamp-blink-merah', 'lamp-blink-gold');
                    void lamp.offsetWidth;

                    // Aktifkan efek 1 kali kedip (flash) hanya pada lampu juri yang klik
                    if (isBiru) {
                        lamp.className = "lamp-box py-1.5 px-1 rounded-lg text-white font-black border-2 border-blue-400 lamp-blink-biru";
                        if (dot) dot.className = "lamp-dot block w-2 h-2 rounded-full bg-white mx-auto mb-1";
                    } else {
                        lamp.className = "lamp-box py-1.5 px-1 rounded-lg text-white font-black border-2 border-red-400 lamp-blink-merah";
                        if (dot) dot.className = "lamp-dot block w-2 h-2 rounded-full bg-white mx-auto mb-1";
                    }

                    // Reset kembali ke keadaan idle setelah 1x kedip (800ms)
                    lamp._resetTimer = setTimeout(() => {
                        lamp.classList.remove('lamp-blink-biru', 'lamp-blink-merah', 'lamp-blink-gold');
                        lamp.className = "lamp-box py-1.5 px-1 rounded-lg bg-slate-800/90 border border-white/15 text-white/70 font-bold transition-all duration-150";
                        if (dot) dot.className = "lamp-dot block w-2 h-2 rounded-full bg-white/40 mx-auto mb-1";
                    }, 800);
                }

                // Connect Reverb Channel Khusus Tanding
                document.addEventListener('DOMContentLoaded', function() {
                    if (window.Echo) {
                        const channelName = `partai.tanding.${partaiTandingId}`;
                        const tandingChannel = window.Echo.channel(channelName);
                        console.log("Display Tanding terhubung ke Channel Reverb:", channelName);

                        // 1. Timer Update
                        tandingChannel.listen('TimerTandingUpdated', function(e) {
                            console.log("Reverb Timer Tanding:", e);
                            localTimerTanding = parseInt(e.waktu) || 0;
                            renderTandingTimer();
                            setTimerState(e.statusTimer === 'running');
                            if (e.babakAktif) {
                                const babakBadge = document.getElementById('tanding-babak-badge');
                                if (babakBadge) babakBadge.innerText = `BABAK ${e.babakAktif}`;
                            }
                        });

                        // 2. Score & Pelanggaran Update
                        tandingChannel.listen('TandingScoreUpdated', function(e) {
                            console.log("Reverb Score Tanding:", e);
                            renderTandingScores(e.scores, e.babakAktif);
                        });

                        // 3. Juri Input Real-Time & Sensor Lamp
                        tandingChannel.listen('JuriTandingInputReceived', function(e) {
                            console.log("Reverb Input Juri Tanding:", e);
                            triggerJuriLamp(e.sudut, e.jenisSerangan, e.noJuri, e.poinSah);
                            if (e.scores) {
                                renderTandingScores(e.scores);
                            }
                        });
                    }
                });
            })();
        </script>
    @elseif (!$partai)
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
        <div class="h-[96vh] flex flex-col gap-3 p-3.5 text-white select-none">
            {{-- 0. TOP EVENT BAR (GRID 1:3:1 -> LOGO 1, INFORMASI PERTANDINGAN, LOGO 2) --}}
            <div class="bg-slate-800/90 border border-slate-700 rounded-3xl px-6 py-2.5 grid grid-cols-5 items-center shadow-xl backdrop-blur-md shrink-0 gap-4">
                {{-- Grid 1: Logo Kiri (Logo 1) - Col 1 (Rasio 1) --}}
                <div class="col-span-1 flex items-center justify-start">
                    @if (file_exists(public_path('smi.png')))
                        <img src="{{ asset('smi.png') }}" alt="Logo 1" class="h-12 md:h-14 xl:h-16 max-w-full object-contain drop-shadow">
                    @elseif (file_exists(public_path('logo1.png')))
                        <img src="{{ asset('logo1.png') }}" alt="Logo 1" class="h-12 md:h-14 xl:h-16 max-w-full object-contain drop-shadow">
                    @else
                        {{-- Fallback SVG Logo 1 (Perisai Beladiri) --}}
                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-slate-700/80 border border-slate-600 flex items-center justify-center text-indigo-300 shadow">
                            <svg class="w-7 h-7 md:w-8 md:h-8 text-indigo-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3zm0 2.18l6 2.25v4.66c0 4.1-2.67 7.94-6 9.01-3.33-1.07-6-4.91-6-9.01V6.43l6-2.25zM12 7a3 3 0 100 6 3 3 0 000-6z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Grid 2: Informasi Pertandingan - Col 3 (Rasio 3) --}}
                <div class="col-span-3 flex flex-col items-center justify-center text-center">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl xl:text-5xl font-black uppercase tracking-[0.15em] sm:tracking-[0.2em] md:tracking-[0.25em] text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-yellow-200 to-amber-400 drop-shadow-[0_4px_15px_rgba(245,158,11,0.4)] leading-tight whitespace-nowrap">
                        PERTANDINGAN SMI CUP 2026
                    </h1>
                </div>

                {{-- Grid 3: Logo Kanan (Logo 2) - Col 1 (Rasio 1) --}}
                <div class="col-span-1 flex items-center justify-end">
                    @if (file_exists(public_path('logo2.png')))
                        <img src="{{ asset('logo2.png') }}" alt="Logo 2" class="h-12 md:h-14 xl:h-16 max-w-full object-contain drop-shadow">
                    @else
                        {{-- Fallback SVG Logo 2 (Piala Juara) --}}
                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-slate-700/80 border border-slate-600 flex items-center justify-center text-amber-300 shadow">
                            <svg class="w-7 h-7 md:w-8 md:h-8 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94.63 1.5 1.98 2.63 3.61 2.96V19H7v2h10v-2h-4v-3.1c1.63-.33 2.98-1.46 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

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
                            class="block w-full px-2 text-center font-black tracking-tighter leading-none drop-shadow-2xl transition-all duration-300 {{ $sedangDisq ? 'text-4xl sm:text-5xl md:text-6xl lg:text-[5rem] text-red-500' : 'score-seni-total text-green-400 drop-shadow-[0_10px_35px_rgba(74,222,128,0.35)]' }}">
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
                                elTot.classList.remove('text-[10rem]', 'score-seni-total', 'text-green-400', 'shadow-green-400/20', 'drop-shadow-[0_10px_35px_rgba(74,222,128,0.35)]');
                                elTot.classList.add('text-4xl', 'sm:text-5xl', 'md:text-6xl', 'lg:text-[5rem]',
                                    'text-red-500');
                            } else {
                                elTot.classList.remove('text-4xl', 'sm:text-5xl', 'md:text-6xl', 'lg:text-[5rem]',
                                    'text-red-500');
                                elTot.classList.add('score-seni-total', 'text-green-400', 'drop-shadow-[0_10px_35px_rgba(74,222,128,0.35)]');
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
                const daftarKategori = ['tunggal', 'ganda', 'solo', 'regu', 'pool', 'tanding'];
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
