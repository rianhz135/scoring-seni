@extends('layouts.dewan')

@section('content')
    <style>
        html, body {
            min-height: 100%;
            min-height: 100dvh;
            margin: 0;
            padding: 0;
            background-color: #0b1329 !important;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Tampilan fleksibel agar layar HP & tablet bisa discroll wajar dan tombol tidak terpotong */
        body > div.min-h-screen {
            min-height: 100dvh !important;
            padding: 0.375rem !important;
            box-sizing: border-box !important;
            display: flex !important;
            flex-direction: column !important;
            overflow-x: hidden !important;
            overflow-y: visible !important;
        }

        @media (min-width: 640px) {
            body > div.min-h-screen {
                padding: 0.5rem !important;
            }
        }

        @media (min-width: 1024px) {
            body > div.min-h-screen {
                padding: 0.75rem !important;
            }
        }

        .strike-btn {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
            transition: all 0.12s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            -webkit-user-select: none;
            position: relative;
            overflow: hidden;
        }

        .strike-btn:active {
            transform: scale(0.95);
            filter: brightness(1.28);
        }

        @keyframes goldGlow {
            0%, 100% { box-shadow: 0 0 15px rgba(234, 179, 8, 0.4); }
            50% { box-shadow: 0 0 35px rgba(234, 179, 8, 0.9); }
        }

        .consensus-flash {
            animation: goldGlow 0.8s ease-in-out infinite;
        }
    </style>

    {{-- 1. KONDISI STANDBY: JIKA BELUM ADA PARTAI TANDING YANG BERLANGSUNG --}}
    @if (!$partai || $partai->status_pertandingan !== 'Berlangsung')
        <div class="h-full min-h-[100dvh] flex flex-col items-center justify-center p-4 sm:p-6 text-center text-white relative">
            <div class="bg-slate-800/90 border border-slate-700 rounded-3xl p-6 sm:p-10 md:p-14 max-w-xl w-full shadow-2xl backdrop-blur-md">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-3xl sm:text-4xl mx-auto mb-4 sm:mb-6">
                    🥊
                </div>
                <div class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 rounded-full bg-slate-700/80 text-slate-300 text-[10px] sm:text-xs font-black uppercase tracking-widest mb-2 sm:mb-3 border border-slate-600">
                    <span>PANEL PENILAIAN JURI {{ $no_juri }} &bull; TANDING</span>
                    <span class="text-amber-400">&bull; {{ $pengaturanDisplay->gelanggang ?? 'GELANGGANG 1' }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl md:text-5xl font-black text-white tracking-tight uppercase mb-2 sm:mb-3">
                    STANDBY
                </h1>
                <p class="text-xs sm:text-base text-slate-400 font-medium mb-6 sm:mb-8">
                    Menunggu operator atau dewan memulai partai tanding di <span class="text-amber-400 font-bold uppercase">{{ $pengaturanDisplay->gelanggang ?? 'Gelanggang' }}</span>...
                </p>
                <div class="flex items-center justify-center gap-3">
                    <a href="{{ route('juri.dashboard') }}"
                        class="px-5 sm:px-6 py-2.5 sm:py-3 bg-slate-700 hover:bg-slate-600 text-white rounded-xl font-bold text-xs sm:text-sm transition-all shadow-md">
                        &larr; Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.Echo) {
                    window.Echo.channel('gelanggang.global.tanding.A')
                        .listen('RefreshDisplay', function() {
                            window.location.reload();
                        });
                }
            });
        </script>
    @else
        @php
            $isActive = $kontrol ? $kontrol->is_active : true;
            $babakAktif = $partai->babak_aktif ?: 1;
        @endphp

        {{-- 2. MODAL OVERLAY: JIKA JURI INI DI-PAUSE / DIJEDA OLEH DEWAN --}}
        <div id="modal-paused-juri"
            class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-md flex flex-col items-center justify-center p-4 sm:p-6 transition-all duration-300 {{ $isActive ? 'hidden' : '' }}">
            <div class="bg-slate-900 border-2 border-amber-500/50 rounded-2xl sm:rounded-3xl p-6 sm:p-8 max-w-md w-full text-center shadow-2xl">
                <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center text-2xl sm:text-3xl mx-auto mb-3 sm:mb-4 animate-pulse">
                    ⏸️
                </div>
                <span class="px-3 py-1 bg-amber-500/20 text-amber-300 text-[10px] sm:text-xs font-black uppercase tracking-widest rounded-full border border-amber-500/30">
                    JURI {{ $no_juri }} DIJEDA
                </span>
                <h2 class="text-xl sm:text-2xl font-black text-white uppercase tracking-tight mt-2.5 sm:mt-3 mb-1.5 sm:mb-2">
                    JURI SEDANG DIJEDA
                </h2>
                <p class="text-slate-400 text-xs sm:text-sm font-medium">
                    Dewan pertandingan sedang menjeda kontrol penilaian Anda. Tombol penilaian akan otomatis aktif kembali saat dewan melanjutkan.
                </p>
            </div>
        </div>

        @php
            $hasVerifAktif = isset($verifikasiAktif) && $verifikasiAktif;
            $sudahVote = $hasVerifAktif ? $verifikasiAktif->jawabanJuri->where('no_juri', $no_juri)->isNotEmpty() : false;
            $showVerifModalInit = ($hasVerifAktif && !$sudahVote);
            $initialVerifId = $hasVerifAktif ? $verifikasiAktif->id : null;
        @endphp

        {{-- MODAL VERIFIKASI JURI --}}
        <div id="modal-verifikasi-juri" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md {{ $showVerifModalInit ? '' : 'hidden' }} transition-all duration-300">
            <div class="bg-gradient-to-b from-slate-900 to-slate-950 rounded-3xl shadow-2xl border-2 border-purple-500/50 w-full max-w-lg p-5 sm:p-6 text-white text-center transform transition-all animate-in fade-in zoom-in-95">
                
                <div class="w-14 h-14 rounded-2xl bg-purple-600/30 border border-purple-400/40 text-purple-300 flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                    📹
                </div>

                <span class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 text-[10px] sm:text-xs font-black uppercase tracking-widest border border-purple-500/30">
                    PERMINTAAN VERIFIKASI DEWAN
                </span>

                <h2 class="text-xl sm:text-2xl font-black uppercase tracking-tight mt-2.5 mb-1 text-white">
                    PILIH KEPUTUSAN VERIFIKASI
                </h2>
                <p class="text-slate-400 text-xs sm:text-sm font-medium mb-5">
                    Juri {{ $no_juri }}, tentukan keputusan Anda untuk diverifikasi di dewan dan display pertandingan:
                </p>

                {{-- 3 Tombol Opsi: Sudut Biru, Sudut Merah, Tidak Valid --}}
                <div class="flex flex-col gap-3">
                    {{-- 1. Sudut Biru --}}
                    <button type="button" onclick="submitVerifikasiJuri('Biru')"
                        class="w-full py-4 px-4 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-500 hover:to-blue-700 active:scale-98 text-white font-black text-base sm:text-lg uppercase tracking-wider flex items-center justify-between border-2 border-blue-400 shadow-[0_8px_20px_rgba(37,99,235,0.4)] transition-all cursor-pointer">
                        <span class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-lg">🔵</span>
                            <span>SUDUT BIRU</span>
                        </span>
                        <span class="text-xs font-black px-2 py-0.5 rounded-full bg-white/20 border border-white/30 truncate max-w-[150px]">
                            {{ $partai->pesertaBiru->nama ?? 'BIRU' }}
                        </span>
                    </button>

                    {{-- 2. Sudut Merah --}}
                    <button type="button" onclick="submitVerifikasiJuri('Merah')"
                        class="w-full py-4 px-4 rounded-2xl bg-gradient-to-r from-red-600 to-red-800 hover:from-red-500 hover:to-red-700 active:scale-98 text-white font-black text-base sm:text-lg uppercase tracking-wider flex items-center justify-between border-2 border-red-400 shadow-[0_8px_20px_rgba(220,38,38,0.4)] transition-all cursor-pointer">
                        <span class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-lg">🔴</span>
                            <span>SUDUT MERAH</span>
                        </span>
                        <span class="text-xs font-black px-2 py-0.5 rounded-full bg-white/20 border border-white/30 truncate max-w-[150px]">
                            {{ $partai->pesertaMerah->nama ?? 'MERAH' }}
                        </span>
                    </button>

                    {{-- 3. Tidak Valid --}}
                    <button type="button" onclick="submitVerifikasiJuri('Tidak Valid')"
                        class="w-full py-3.5 px-4 rounded-2xl bg-slate-800 hover:bg-slate-700 active:scale-98 text-slate-300 hover:text-white font-black text-sm sm:text-base uppercase tracking-wider flex items-center justify-center gap-2 border border-slate-700 shadow-md transition-all cursor-pointer">
                        <span class="text-base">⚪</span>
                        <span>TIDAK VALID / BATAL</span>
                    </button>
                </div>

            </div>
        </div>

        {{-- 3. CONTAINER UTAMA JURI TANDING (RESPONSIF TABLET & HP, BISA DISCROLL) --}}
        <div class="w-full flex-1 flex flex-col gap-2.5 sm:gap-3 text-white pb-6">

            {{-- HEADER: INFO IDENTITAS JURI & PARTAI (RESPONSIF HP, TABLET, DESKTOP) --}}
            <div class="bg-slate-900/90 border border-slate-700/80 rounded-xl sm:rounded-2xl p-2 sm:p-2.5 md:p-3 shadow-xl backdrop-blur-md shrink-0 flex flex-wrap sm:flex-nowrap items-center justify-between gap-2">
                {{-- Kiri: Info Juri & Match --}}
                <div class="flex items-center gap-1.5 sm:gap-2.5">
                    <div class="px-2.5 sm:px-3.5 py-1 sm:py-1.5 bg-indigo-600 text-white rounded-lg sm:rounded-xl text-center shadow-md shrink-0">
                        <span class="text-[8px] sm:text-[9px] font-black uppercase tracking-widest opacity-80 block leading-none">MEJA</span>
                        <span class="text-sm sm:text-base md:text-xl font-black leading-tight">JURI {{ $no_juri }}</span>
                    </div>
                    <div class="flex items-center gap-1 sm:gap-1.5 flex-wrap">
                        <span class="text-[10px] sm:text-xs font-black px-1.5 sm:px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">
                            PARTAI #{{ $partai->no_partai }}
                        </span>
                        <span class="text-[10px] sm:text-xs font-black px-1.5 sm:px-2 py-0.5 rounded bg-slate-800 text-emerald-400 border border-slate-700">
                            {{ $partai->kelas ?? 'TANDING' }}
                        </span>
                        <span class="text-[10px] sm:text-xs font-black px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30 uppercase">
                            {{ $pengaturanDisplay->gelanggang ?? 'GELANGGANG 1' }}
                        </span>
                        <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase hidden sm:inline">
                            &bull; {{ $partai->fase_babak ?? 'Penyisihan' }}
                        </span>
                    </div>
                </div>

                {{-- Tengah: Babak --}}
                <div class="flex items-center gap-1.5 sm:gap-2.5 order-3 sm:order-2 w-full sm:w-auto justify-center sm:justify-start">
                    <div id="juri-babak-badge"
                        class="px-3 sm:px-5 py-1 sm:py-1.5 rounded-lg sm:rounded-xl bg-amber-500/20 border border-amber-400/50 text-amber-300 text-xs sm:text-sm md:text-base font-black uppercase tracking-wider sm:tracking-widest shrink-0 shadow-sm">
                        BABAK {{ $babakAktif }}
                    </div>
                </div>

                {{-- Kanan: Status Aktif & Navigasi --}}
                <div class="flex items-center gap-1.5 sm:gap-2 order-2 sm:order-3 ml-auto sm:ml-0">
                    <span id="badge-status-juri"
                        class="px-2 sm:px-3 py-0.5 sm:py-1 rounded-lg text-[10px] sm:text-xs font-black uppercase tracking-wider {{ $isActive ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}">
                        {{ $isActive ? 'AKTIF' : 'PAUSED' }}
                    </span>
                    <a href="{{ route('juri.dashboard') }}"
                        class="px-2.5 py-1 sm:p-2 rounded-lg sm:rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition-all text-[11px] sm:text-xs font-bold flex items-center gap-1"
                        title="Dashboard">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="hidden sm:inline">Dashboard</span>
                    </a>
                </div>
            </div>

            {{-- 4. AREA TOMBOL PENILAIAN (SPLIT 2 SUDUT: BIRU & MERAH) --}}
            {{-- Otomatis 2 Kolom saat Landscape (HP & Tablet) dan Tablet/Desktop (md:grid-cols-2), serta bisa discroll lancar saat HP Portrait --}}
            <div class="grid grid-cols-1 md:grid-cols-2 landscape:grid-cols-2 gap-3 sm:gap-4 flex-1">

                {{-- ==================== SUDUT BIRU ==================== --}}
                <div class="bg-gradient-to-b from-blue-950/80 to-slate-900 border-2 border-blue-600/70 rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-5 flex flex-col justify-between shadow-xl relative gap-3">
                    {{-- Header Sudut Biru --}}
                    <div class="flex items-center justify-between pb-1.5 sm:pb-2 border-b border-blue-500/30 shrink-0">
                        <div class="min-w-0 pr-2">
                            <span class="inline-block px-2 py-0.5 bg-blue-600 text-white text-[9px] sm:text-[10px] font-black uppercase tracking-widest rounded shadow">
                                SUDUT BIRU
                            </span>
                            <h2 class="text-sm sm:text-lg md:text-xl lg:text-2xl font-black text-white uppercase truncate leading-tight">
                                {{ $partai->pesertaBiru->nama ?? 'SUDUT BIRU' }}
                            </h2>
                            <p class="text-[10px] sm:text-xs font-bold text-blue-300 uppercase truncate">
                                {{ $partai->pesertaBiru->kontingen ?? '-' }}
                            </p>
                        </div>
                        <div class="w-7 h-7 sm:w-9 sm:h-9 md:w-10 md:h-10 rounded-lg sm:rounded-xl bg-blue-600/30 text-blue-300 flex items-center justify-center font-black text-sm sm:text-lg border border-blue-500/40 shrink-0">
                            B
                        </div>
                    </div>

                    {{-- TOMBOL PENILAIAN BIRU (RESPONSIF TINGGI & FLEKSIBEL SESUAI SCREEN) --}}
                    <div class="grid grid-cols-2 gap-2.5 sm:gap-3.5 my-1 sm:my-2">
                        {{-- 1. PUKULAN BIRU (+1) --}}
                        <button type="button"
                            onclick="kirimNilaiTanding('Biru', 'Pukulan', 1, this)"
                            class="strike-btn w-full min-h-[130px] sm:min-h-[150px] md:min-h-[170px] lg:min-h-[200px] py-4 sm:py-5 px-2 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900 hover:from-blue-500 hover:to-blue-800 active:from-blue-400 text-white border-2 sm:border-3 border-blue-400/90 shadow-[0_10px_25px_rgba(37,99,235,0.45)] flex flex-col items-center justify-center cursor-pointer group relative overflow-hidden">
                            
                            {{-- Frosted Icon Capsule --}}
                            <div class="w-12 h-12 sm:w-14 sm:h-14 md:w-18 md:h-18 lg:w-22 lg:h-22 rounded-xl sm:rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center shadow-inner mb-1.5 sm:mb-2 group-active:scale-110 transition-transform shrink-0">
                                <span class="text-2xl sm:text-3xl md:text-5xl lg:text-6xl drop-shadow select-none">👊</span>
                            </div>

                            {{-- Label Serangan --}}
                            <span class="text-base sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl font-black uppercase tracking-wider drop-shadow-md leading-tight text-center">
                                PUKULAN
                            </span>

                            {{-- Poin Badge --}}
                            <span class="mt-1 sm:mt-1.5 px-2.5 sm:px-4 py-0.5 sm:py-1 rounded-full bg-white/25 text-white text-[10px] sm:text-xs md:text-sm lg:text-base font-black tracking-widest border border-white/40 shadow-sm shrink-0">
                                +1 POIN
                            </span>
                        </button>

                        {{-- 2. TENDANGAN BIRU (+2) --}}
                        <button type="button"
                            onclick="kirimNilaiTanding('Biru', 'Tendangan', 2, this)"
                            class="strike-btn w-full min-h-[130px] sm:min-h-[150px] md:min-h-[170px] lg:min-h-[200px] py-4 sm:py-5 px-2 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-blue-950 hover:from-indigo-500 hover:to-blue-900 active:from-indigo-400 text-white border-2 sm:border-3 border-indigo-400/90 shadow-[0_10px_25px_rgba(99,102,241,0.45)] flex flex-col items-center justify-center cursor-pointer group relative overflow-hidden">
                            
                            {{-- Frosted Icon Capsule --}}
                            <div class="w-12 h-12 sm:w-14 sm:h-14 md:w-18 md:h-18 lg:w-22 lg:h-22 rounded-xl sm:rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center shadow-inner mb-1.5 sm:mb-2 group-active:scale-110 transition-transform shrink-0">
                                <span class="text-2xl sm:text-3xl md:text-5xl lg:text-6xl drop-shadow select-none">🦶</span>
                            </div>

                            {{-- Label Serangan --}}
                            <span class="text-base sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl font-black uppercase tracking-wider drop-shadow-md leading-tight text-center">
                                TENDANGAN
                            </span>

                            {{-- Poin Badge --}}
                            <span class="mt-1 sm:mt-1.5 px-2.5 sm:px-4 py-0.5 sm:py-1 rounded-full bg-white/25 text-white text-[10px] sm:text-xs md:text-sm lg:text-base font-black tracking-widest border border-white/40 shadow-sm shrink-0">
                                +2 POIN
                            </span>
                        </button>
                    </div>

                    {{-- Feedback Bar Biru --}}
                    <div id="feedback-biru" class="shrink-0 text-center py-1 sm:py-1.5 px-2.5 sm:px-3 rounded-lg sm:rounded-xl bg-blue-950/70 border border-blue-500/40 text-[11px] sm:text-xs font-bold text-blue-100 truncate shadow-inner">
                        Siap menerima input nilai Sudut Biru
                    </div>
                </div>

                {{-- ==================== SUDUT MERAH ==================== --}}
                <div class="bg-gradient-to-b from-red-950/80 to-slate-900 border-2 border-red-600/70 rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-5 flex flex-col justify-between shadow-xl relative gap-3">
                    {{-- Header Sudut Merah --}}
                    <div class="flex items-center justify-between pb-1.5 sm:pb-2 border-b border-red-500/30 shrink-0">
                        <div class="min-w-0 pr-2">
                            <span class="inline-block px-2 py-0.5 bg-red-600 text-white text-[9px] sm:text-[10px] font-black uppercase tracking-widest rounded shadow">
                                SUDUT MERAH
                            </span>
                            <h2 class="text-sm sm:text-lg md:text-xl lg:text-2xl font-black text-white uppercase truncate leading-tight">
                                {{ $partai->pesertaMerah->nama ?? 'SUDUT MERAH' }}
                            </h2>
                            <p class="text-[10px] sm:text-xs font-bold text-red-300 uppercase truncate">
                                {{ $partai->pesertaMerah->kontingen ?? '-' }}
                            </p>
                        </div>
                        <div class="w-7 h-7 sm:w-9 sm:h-9 md:w-10 md:h-10 rounded-lg sm:rounded-xl bg-red-600/30 text-red-300 flex items-center justify-center font-black text-sm sm:text-lg border border-red-500/40 shrink-0">
                            M
                        </div>
                    </div>

                    {{-- TOMBOL PENILAIAN MERAH (RESPONSIF TINGGI & FLEKSIBEL SESUAI SCREEN) --}}
                    <div class="grid grid-cols-2 gap-2.5 sm:gap-3.5 my-1 sm:my-2">
                        {{-- 1. PUKULAN MERAH (+1) --}}
                        <button type="button"
                            onclick="kirimNilaiTanding('Merah', 'Pukulan', 1, this)"
                            class="strike-btn w-full min-h-[130px] sm:min-h-[150px] md:min-h-[170px] lg:min-h-[200px] py-4 sm:py-5 px-2 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-red-600 via-red-700 to-red-900 hover:from-red-500 hover:to-red-800 active:from-red-400 text-white border-2 sm:border-3 border-red-400/90 shadow-[0_10px_25px_rgba(220,38,38,0.45)] flex flex-col items-center justify-center cursor-pointer group relative overflow-hidden">
                            
                            {{-- Frosted Icon Capsule --}}
                            <div class="w-12 h-12 sm:w-14 sm:h-14 md:w-18 md:h-18 lg:w-22 lg:h-22 rounded-xl sm:rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center shadow-inner mb-1.5 sm:mb-2 group-active:scale-110 transition-transform shrink-0">
                                <span class="text-2xl sm:text-3xl md:text-5xl lg:text-6xl drop-shadow select-none">👊</span>
                            </div>

                            {{-- Label Serangan --}}
                            <span class="text-base sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl font-black uppercase tracking-wider drop-shadow-md leading-tight text-center">
                                PUKULAN
                            </span>

                            {{-- Poin Badge --}}
                            <span class="mt-1 sm:mt-1.5 px-2.5 sm:px-4 py-0.5 sm:py-1 rounded-full bg-white/25 text-white text-[10px] sm:text-xs md:text-sm lg:text-base font-black tracking-widest border border-white/40 shadow-sm shrink-0">
                                +1 POIN
                            </span>
                        </button>

                        {{-- 2. TENDANGAN MERAH (+2) --}}
                        <button type="button"
                            onclick="kirimNilaiTanding('Merah', 'Tendangan', 2, this)"
                            class="strike-btn w-full min-h-[130px] sm:min-h-[150px] md:min-h-[170px] lg:min-h-[200px] py-4 sm:py-5 px-2 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-rose-600 via-rose-700 to-red-950 hover:from-rose-500 hover:to-red-900 active:from-rose-400 text-white border-2 sm:border-3 border-rose-400/90 shadow-[0_10px_25px_rgba(225,29,72,0.45)] flex flex-col items-center justify-center cursor-pointer group relative overflow-hidden">
                            
                            {{-- Frosted Icon Capsule --}}
                            <div class="w-12 h-12 sm:w-14 sm:h-14 md:w-18 md:h-18 lg:w-22 lg:h-22 rounded-xl sm:rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center shadow-inner mb-1.5 sm:mb-2 group-active:scale-110 transition-transform shrink-0">
                                <span class="text-2xl sm:text-3xl md:text-5xl lg:text-6xl drop-shadow select-none">🦶</span>
                            </div>

                            {{-- Label Serangan --}}
                            <span class="text-base sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl font-black uppercase tracking-wider drop-shadow-md leading-tight text-center">
                                TENDANGAN
                            </span>

                            {{-- Poin Badge --}}
                            <span class="mt-1 sm:mt-1.5 px-2.5 sm:px-4 py-0.5 sm:py-1 rounded-full bg-white/25 text-white text-[10px] sm:text-xs md:text-sm lg:text-base font-black tracking-widest border border-white/40 shadow-sm shrink-0">
                                +2 POIN
                            </span>
                        </button>
                    </div>

                    {{-- Feedback Bar Merah --}}
                    <div id="feedback-merah" class="shrink-0 text-center py-1 sm:py-1.5 px-2.5 sm:px-3 rounded-lg sm:rounded-xl bg-red-950/70 border border-red-500/40 text-[11px] sm:text-xs font-bold text-red-100 truncate shadow-inner">
                        Siap menerima input nilai Sudut Merah
                    </div>
                </div>

            </div>

            {{-- 5. RIWAYAT LOG KLIK JURI TERAKHIR (HORIZONTAL DRAWER RESPONSIF) --}}
            <div class="bg-slate-900/90 border border-slate-700/80 rounded-xl sm:rounded-2xl p-1.5 sm:p-2.5 shadow-md shrink-0">
                <div class="flex items-center justify-between text-[10px] sm:text-xs font-black uppercase tracking-wider text-slate-400 mb-1 px-1">
                    <span class="truncate">Riwayat Penilaian Juri {{ $no_juri }}</span>
                    <span class="text-[9px] sm:text-[10px] text-slate-500 font-normal shrink-0 ml-1">Maks. 2 Dtk Konsensus</span>
                </div>
                <div id="juri-history-list" class="flex gap-1.5 sm:gap-2 overflow-x-auto pb-0.5 scrollbar-thin">
                    @forelse ($recentLogs as $log)
                        @php
                            $isSah = ($log->status_input === 'Berhasil');
                            $isBiru = ($log->sudut === 'Biru');
                        @endphp
                        <div id="log-card-{{ $log->id }}"
                            class="shrink-0 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg sm:rounded-xl border text-[10px] sm:text-[11px] font-bold flex items-center gap-1.5 sm:gap-2 {{ $isSah ? 'bg-emerald-950/60 border-emerald-500/50 text-emerald-300' : 'bg-slate-800/80 border-slate-700 text-slate-300' }}">
                            <span class="px-1.5 py-0.5 rounded font-black text-[8px] sm:text-[9px] uppercase {{ $isBiru ? 'bg-blue-600 text-white' : 'bg-red-600 text-white' }}">
                                {{ $log->sudut }}
                            </span>
                            <span>{{ $log->jenis_serangan }} (+{{ $log->poin }})</span>
                            <span class="text-[9px] sm:text-[10px] opacity-75 font-mono">{{ $log->created_at ? $log->created_at->format('H:i:s') : '-' }}</span>
                            <span class="status-badge px-1.5 py-0.5 rounded text-[8px] sm:text-[9px] font-black uppercase {{ $isSah ? 'bg-emerald-500/30 text-emerald-300' : 'bg-slate-700 text-slate-400' }}">
                                {{ $isSah ? 'POIN SAH' : ($log->keterangan ?? $log->status_input) }}
                            </span>
                        </div>
                    @empty
                        <div id="empty-history-text" class="text-xs text-slate-500 py-0.5 px-2 italic">
                            Belum ada riwayat input penilaian pada partai ini.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- JAVASCRIPT REAL-TIME JURI TANDING --}}
        <script>
            (function() {
                const partaiId = "{{ $partai->id }}";
                const noJuri = parseInt("{{ $no_juri }}");

                // 1. Audio Synthesizer (Bip saat klik tombol)
                let audioCtx = null;
                function playBeep(freq = 600, duration = 0.08) {
                    try {
                        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        if (audioCtx.state === 'suspended') audioCtx.resume();
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
                        gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + duration);
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        osc.start();
                        osc.stop(audioCtx.currentTime + duration);
                    } catch (e) {
                        // Silently continue if audio not permitted
                    }
                }

                // 2. Kirim Input Penilaian ke Server (Non-blocking, mendukung input bersamaan & beruntun)
                window.kirimNilaiTanding = function(sudut, jenisSerangan, poin, btnElement) {
                    // Filter micro-bounce touchscreen (< 60ms) hanya pada tombol fisik yang sama
                    const nowTime = Date.now();
                    if (btnElement && btnElement._lastTouchTime && (nowTime - btnElement._lastTouchTime < 60)) {
                        return;
                    }
                    if (btnElement) btnElement._lastTouchTime = nowTime;

                    // Haptic & Sound seketika
                    if (navigator.vibrate) navigator.vibrate(35);
                    playBeep(sudut === 'Biru' ? 750 : 550, 0.08);

                    // Visual Feedback pada Tombol: re-trigger animasi agar selalu terlihat meski ditekan beruntun
                    if (btnElement) {
                        btnElement.classList.remove('brightness-150', 'scale-95');
                        void btnElement.offsetWidth;
                        btnElement.classList.add('brightness-150', 'scale-95');
                        setTimeout(() => {
                            btnElement.classList.remove('brightness-150', 'scale-95');
                        }, 120);
                    }

                    const feedbackEl = document.getElementById(`feedback-${sudut.toLowerCase()}`);
                    if (feedbackEl) {
                        feedbackEl.innerText = `Input: ${jenisSerangan} ${sudut} (+${poin})...`;
                    }

                    // Kirim request ke server secara asinkron tanpa memblokir tombol lain atau klik berikutnya
                    fetch(`/dewan/tanding/${partaiId}/input-juri`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            no_juri: noJuri,
                            sudut: sudut,
                            jenis_serangan: jenisSerangan
                        })
                    })
                    .then(async res => {
                        if (!res.ok) {
                            const errData = await res.json().catch(() => ({}));
                            if (feedbackEl) {
                                feedbackEl.innerText = errData.message || `Gagal kirim (${res.status})`;
                                setTimeout(() => {
                                    feedbackEl.innerText = `Siap menerima input nilai Sudut ${sudut}`;
                                }, 3000);
                            }
                            return;
                        }

                        const data = await res.json();

                        if (data.success) {
                            if (data.is_poin_sah) {
                                playBeep(1000, 0.2); // Nada tinggi untuk poin sah!
                                if (feedbackEl) {
                                    feedbackEl.className = `shrink-0 text-center py-1 sm:py-1.5 px-2.5 sm:px-3 rounded-lg sm:rounded-xl bg-emerald-900 border border-emerald-400 text-[11px] sm:text-xs font-black text-emerald-200 truncate consensus-flash`;
                                    feedbackEl.innerText = `POIN SAH MASUK! ${jenisSerangan} ${sudut} (+${poin})`;
                                    setTimeout(() => {
                                        const isB = (sudut.toLowerCase() === 'biru');
                                        feedbackEl.className = isB
                                            ? `shrink-0 text-center py-1 sm:py-1.5 px-2.5 sm:px-3 rounded-lg sm:rounded-xl bg-blue-950/70 border border-blue-500/40 text-[11px] sm:text-xs font-bold text-blue-100 truncate shadow-inner`
                                            : `shrink-0 text-center py-1 sm:py-1.5 px-2.5 sm:px-3 rounded-lg sm:rounded-xl bg-red-950/70 border border-red-500/40 text-[11px] sm:text-xs font-bold text-red-100 truncate shadow-inner`;
                                        feedbackEl.innerText = `Siap menerima input nilai Sudut ${sudut}`;
                                    }, 2000);
                                }
                            } else {
                                if (feedbackEl) {
                                    feedbackEl.innerText = `Input ${jenisSerangan} (+${poin}) tercatat. Menunggu juri lain (maks. 2 dtk)...`;
                                    setTimeout(() => {
                                        feedbackEl.innerText = `Siap menerima input nilai Sudut ${sudut}`;
                                    }, 2000);
                                }
                            }

                            // Tambahkan ke drawer history lokal
                            if (data.log) {
                                appendToHistory(data.log, data.is_poin_sah);
                            }
                        } else {
                            if (feedbackEl) feedbackEl.innerText = data.message || 'Gagal mengirim nilai';
                        }
                    })
                    .catch(err => {
                        console.error("Gagal mengirim nilai juri:", err);
                        if (feedbackEl) feedbackEl.innerText = 'Koneksi error ke server!';
                    });
                };

                // Helper: Append ke list history juri
                function appendToHistory(log, isSah) {
                    const list = document.getElementById('juri-history-list');
                    const emptyText = document.getElementById('empty-history-text');
                    if (emptyText) emptyText.remove();
                    if (!list) return;

                    const isBiru = (log.sudut === 'Biru');
                    const card = document.createElement('div');
                    card.id = `log-card-${log.id}`;
                    card.className = `shrink-0 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg sm:rounded-xl border text-[10px] sm:text-[11px] font-bold flex items-center gap-1.5 sm:gap-2 ${isSah ? 'bg-emerald-950/60 border-emerald-500/50 text-emerald-300' : 'bg-slate-800/80 border-slate-700 text-slate-300'}`;
                    card.innerHTML = `
                        <span class="px-1.5 py-0.5 rounded font-black text-[8px] sm:text-[9px] uppercase ${isBiru ? 'bg-blue-600 text-white' : 'bg-red-600 text-white'}">
                            ${log.sudut}
                        </span>
                        <span>${log.jenis_serangan} (+${log.poin})</span>
                        <span class="text-[9px] sm:text-[10px] opacity-75 font-mono">${log.time || '-'}</span>
                        <span class="status-badge px-1.5 py-0.5 rounded text-[8px] sm:text-[9px] font-black uppercase ${isSah ? 'bg-emerald-500/30 text-emerald-300' : 'bg-slate-700 text-slate-400'}">
                            ${isSah ? 'POIN SAH' : (log.keterangan || log.status_input)}
                        </span>
                    `;
                    list.prepend(card);
                }

                // 4. KONTROL VERIFIKASI JURI MODAL (VAR)
                let activeVerifikasiId = {{ $initialVerifId ?: 'null' }};

                window.showVerifikasiModalJuri = function(verifId) {
                    activeVerifikasiId = verifId;
                    const modal = document.getElementById('modal-verifikasi-juri');
                    if (modal) modal.classList.remove('hidden');
                    playBeep(750, 0.25);
                    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
                };

                window.hideVerifikasiModalJuri = function() {
                    const modal = document.getElementById('modal-verifikasi-juri');
                    if (modal) modal.classList.add('hidden');
                };

                window.submitVerifikasiJuri = async function(pilihan) {
                    // Ketika diklik, modal langsung tertutup di halaman juri
                    hideVerifikasiModalJuri();
                    playBeep(pilihan === 'Biru' ? 520 : (pilihan === 'Merah' ? 780 : 400), 0.15);
                    if (navigator.vibrate) navigator.vibrate(80);

                    if (!activeVerifikasiId) return;

                    const fbEl = (pilihan === 'Biru') ? document.getElementById('feedback-biru') : document.getElementById('feedback-merah');
                    if (fbEl) {
                        const originalText = fbEl.innerText;
                        fbEl.innerText = `Pilihan verifikasi (${pilihan}) telah dikirim ke Dewan`;
                        fbEl.className = "shrink-0 text-center py-1 sm:py-1.5 px-2.5 sm:px-3 rounded-lg sm:rounded-xl bg-purple-900 border border-purple-400 text-[11px] sm:text-xs font-black text-white truncate shadow-inner animate-pulse";
                        setTimeout(() => {
                            fbEl.innerText = originalText;
                            fbEl.className = (pilihan === 'Biru') ?
                                "shrink-0 text-center py-1 sm:py-1.5 px-2.5 sm:px-3 rounded-lg sm:rounded-xl bg-blue-950/70 border border-blue-500/40 text-[11px] sm:text-xs font-bold text-blue-100 truncate shadow-inner" :
                                "shrink-0 text-center py-1 sm:py-1.5 px-2.5 sm:px-3 rounded-lg sm:rounded-xl bg-red-950/70 border border-red-500/40 text-[11px] sm:text-xs font-bold text-red-100 truncate shadow-inner";
                        }, 3500);
                    }

                    try {
                        const url = `/juri/tanding/verifikasi/${activeVerifikasiId}/jawaban`;
                        await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({
                                no_juri: noJuri,
                                pilihan_sudut: pilihan
                            })
                        });
                    } catch (err) {
                        console.error("Gagal mengirim pilihan verifikasi:", err);
                    }
                };

                // 5. Listen Reverb Real-Time
                document.addEventListener('DOMContentLoaded', function() {
                    if (window.Echo) {
                        const channelName = `partai.tanding.${partaiId}`;
                        const channel = window.Echo.channel(channelName);
                        console.log("Juri Tanding terhubung ke Channel:", channelName);

                        // A. Babak Sync
                        channel.listen('TimerTandingUpdated', function(e) {
                            if (e.babakAktif) {
                                const bb = document.getElementById('juri-babak-badge');
                                if (bb) bb.innerText = `BABAK ${e.babakAktif}`;
                            }
                        });

                        // B. Active / Pause Juri Sync
                        channel.listen('ToggleKontrolJuriTanding', function(e) {
                            console.log("Toggle Juri:", e);
                            if (parseInt(e.noJuri) === noJuri) {
                                const modal = document.getElementById('modal-paused-juri');
                                const badge = document.getElementById('badge-status-juri');
                                if (e.isActive) {
                                    if (modal) modal.classList.add('hidden');
                                    if (badge) {
                                        badge.className = "px-3 py-1 rounded-lg text-xs font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30";
                                        badge.innerText = "AKTIF";
                                    }
                                } else {
                                    if (modal) modal.classList.remove('hidden');
                                    if (badge) {
                                        badge.className = "px-3 py-1 rounded-lg text-xs font-black uppercase tracking-wider bg-amber-500/20 text-amber-400 border border-amber-500/30";
                                        badge.innerText = "PAUSED";
                                    }
                                }
                            }
                        });

                        // C. Juri Input & Consensus Sah Notification
                        channel.listen('JuriTandingInputReceived', function(e) {
                            if (e.poinSah) {
                                const isMeInvolved = (parseInt(e.noJuri) === noJuri || parseInt(e.partnerNoJuri) === noJuri);
                                if (isMeInvolved) {
                                    playBeep(1000, 0.2); // Notifikasi suara poin sah
                                    // Update kartu history yang menunggu pasangan (1 per konsensus)
                                    const badges = document.querySelectorAll('.status-badge');
                                    for (let b of badges) {
                                        if (b.innerText.includes('Menunggu') || b.innerText.includes('Belum')) {
                                            b.className = "status-badge px-1.5 py-0.5 rounded text-[8px] sm:text-[9px] font-black uppercase bg-emerald-500/30 text-emerald-300";
                                            b.innerText = "POIN SAH";
                                            break;
                                        }
                                    }
                                }
                            }
                        });

                        // D. Global Gelanggang Refresh
                        window.Echo.channel(`gelanggang.global.tanding.A`)
                            .listen('RefreshDisplay', function() {
                                window.location.reload();
                            });

                        // E. Realtime Verifikasi VAR Juri
                        channel.listen('VerifikasiTandingUpdated', function(e) {
                            console.log("Realtime Verifikasi received on Juri:", e);
                            if (e.action === 'start') {
                                showVerifikasiModalJuri(e.verifikasiId);
                            } else if (e.action === 'close') {
                                hideVerifikasiModalJuri();
                            }
                        });
                    }
                });
            })();
        </script>
    @endif
@endsection

