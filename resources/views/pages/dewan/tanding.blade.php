@extends('layouts.dewan')

@section('content')
    {{-- 1. TOP BAR NAVIGASI & INFORMASI PARTAI --}}
    <div class="bg-white rounded-xl p-2.5 mb-2.5 border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-2">
        <div class="flex items-center gap-2.5 w-full md:w-auto">
            <a href="{{ route('admin.manajemen.tanding.index') }}"
                class="px-2.5 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold transition-all flex items-center gap-1 shrink-0 shadow-sm">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali
            </a>

            <div class="h-5 w-[1px] bg-gray-200 hidden sm:block"></div>

            <div>
                <div class="flex items-center gap-1.5">
                    <span class="px-2 py-0.5 rounded bg-gray-900 text-white font-black text-[11px] tracking-wider">
                        PARTAI #{{ $partai->no_partai }}
                    </span>
                    <span class="px-2 py-0.5 rounded bg-primary-50 text-primary-700 font-bold text-[11px] uppercase border border-primary-200">
                        {{ $partai->kelas }}
                    </span>
                    <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-700 font-bold text-[11px] uppercase border border-purple-200">
                        {{ $partai->fase_babak }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Status Pertandingan --}}
        <div class="flex items-center gap-2 w-full md:w-auto justify-end">
            <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold uppercase {{ $partai->status_pertandingan === 'Selesai' ? 'bg-gray-100 text-gray-600 border border-gray-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }} flex items-center gap-1.5 shadow-2xs">
                <span class="w-2 h-2 rounded-full {{ $partai->status_pertandingan === 'Selesai' ? 'bg-gray-400' : 'bg-emerald-500 animate-pulse' }}"></span>
                {{ $partai->status_pertandingan === 'Selesai' ? 'Partai Selesai' : 'Pertandingan Berlangsung' }}
            </span>
        </div>
    </div>

    {{-- 2. PAPAN SKOR UTAMA (SUDUT BIRU - TIMER & BABAK - SUDUT MERAH) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-2.5 mb-2.5">
        {{-- SUDUT BIRU --}}
        <div class="lg:col-span-4 bg-white rounded-xl border-2 border-blue-500 shadow-sm p-3 relative overflow-hidden flex flex-col justify-between">
            <div class="absolute top-0 right-0 w-20 h-20 bg-blue-50 rounded-full -mr-8 -mt-8 opacity-70"></div>

            <div>
                <div class="flex items-center justify-between mb-1.5 relative">
                    <span class="px-2 py-0.5 rounded bg-blue-600 text-white text-[10px] font-black uppercase tracking-wider shadow-sm">
                        SUDUT BIRU
                    </span>
                    <span id="badge-disq-biru" class="{{ $scores['biru']['is_disq'] ? '' : 'hidden' }} px-1.5 py-0.5 rounded bg-red-600 text-white text-[9px] font-black uppercase animate-pulse">
                        DISKUALIFIKASI
                    </span>
                </div>

                <h2 class="text-base md:text-lg font-black text-gray-900 leading-tight truncate">
                    {{ $partai->pesertaBiru ? $partai->pesertaBiru->nama : 'ATLET BELUM DITENTUKAN' }}
                </h2>
                <p class="text-[11px] font-bold text-blue-600 uppercase mt-0.5 truncate">
                    {{ $partai->pesertaBiru ? $partai->pesertaBiru->kontingen : '-' }}
                </p>
            </div>

            {{-- Skor & Detail Angka --}}
            <div class="my-1 text-center">
                <div id="display-score-biru" class="text-4xl lg:text-5xl font-black {{ $scores['biru']['is_disq'] ? 'text-red-600' : 'text-blue-600' }} font-mono tracking-tight">
                    @if ($scores['biru']['is_disq'])
                        <span class="text-3xl md:text-4xl text-red-600 font-black tracking-normal">DISQ</span>
                    @else
                        {{ $scores['biru']['total'] }}
                    @endif
                </div>
                <div id="disq-banner-biru" class="{{ $scores['biru']['is_disq'] ? '' : 'hidden' }} mt-0.5">
                    <span class="px-2 py-0.5 rounded-full bg-red-600 text-white text-[9px] font-black uppercase tracking-wider animate-pulse">
                        DISKUALIFIKASI
                    </span>
                </div>
                <p id="label-score-biru" class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider mt-0.5 {{ $scores['biru']['is_disq'] ? 'hidden' : '' }}">Total Poin</p>
            </div>

            {{-- Rincian Poin & Pelanggaran --}}
            <div class="pt-2 border-t border-gray-100 space-y-1.5">
                <div class="grid grid-cols-3 gap-1 text-center">
                    <div class="p-1 bg-blue-50/70 rounded-lg border border-blue-100">
                        <p class="text-[8px] font-bold text-blue-400 uppercase">Poin Masuk</p>
                        <p id="stat-poin-biru" class="text-xs font-black text-blue-700 font-mono">{{ $scores['biru']['poin_masuk'] }}</p>
                    </div>
                    <div class="p-1 bg-blue-50/70 rounded-lg border border-blue-100">
                        <p class="text-[8px] font-bold text-blue-400 uppercase">Jatuhan (+3)</p>
                        <p id="stat-jatuhan-biru" class="text-xs font-black text-blue-700 font-mono">{{ $scores['biru']['jatuhan_count'] }} ({{ $scores['biru']['poin_jatuhan'] }})</p>
                    </div>
                    <div class="p-1 bg-red-50/70 rounded-lg border border-red-100">
                        <p class="text-[8px] font-bold text-red-400 uppercase">Hukuman</p>
                        <p id="stat-minus-biru" class="text-xs font-black text-red-600 font-mono">-{{ $scores['biru']['poin_minus'] }}</p>
                    </div>
                </div>

                {{-- Status Binaan, Teguran, Peringatan --}}
                <div id="pelanggaran-tags-biru" class="flex flex-wrap gap-1 items-center min-h-[18px]">
                    @if ($scores['biru']['binaan_count'] > 0)
                        <span class="px-1.5 py-0.5 rounded bg-yellow-50 text-yellow-700 border border-yellow-200 text-[9px] font-bold">
                            Binaan: {{ $scores['biru']['binaan_count'] }}x
                        </span>
                    @endif
                    @foreach ($scores['biru']['teguran_list'] as $teg)
                        <span class="px-1.5 py-0.5 rounded bg-orange-50 text-orange-700 border border-orange-200 text-[9px] font-bold">
                            {{ $teg }}
                        </span>
                    @endforeach
                    @foreach ($scores['biru']['peringatan_list'] as $per)
                        <span class="px-1.5 py-0.5 rounded bg-red-100 text-red-800 border border-red-200 text-[9px] font-black">
                            {{ $per }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- CENTER: TIMER & KONTROL BABAK --}}
        <div class="lg:col-span-4 bg-gray-900 rounded-xl p-3 text-white shadow-md flex flex-col justify-between items-center text-center relative overflow-hidden">
            <div class="w-full flex justify-between items-center border-b border-gray-800 pb-1.5">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-wider">DIGITAL MATCH TIMER</span>
                <span id="badge-babak-display" class="px-2 py-0.5 rounded bg-primary-600 text-white text-[10px] font-black uppercase tracking-wider">
                    BABAK {{ $partai->babak_aktif ?: 1 }}
                </span>
            </div>

            {{-- Timer Display --}}
            <div class="my-1">
                <div id="timer-display" class="text-4xl lg:text-5xl font-mono font-black tracking-wider text-white tabular-nums drop-shadow-sm">
                    @php
                        $detik = $partai->waktu_timer ?? 120;
                        $m = floor($detik / 60);
                        $s = $detik % 60;
                        echo sprintf('%02d:%02d', $m, $s);
                    @endphp
                </div>
                <div class="flex items-center justify-center gap-1.5 mt-1">
                    <span id="timer-status-dot" class="w-1.5 h-1.5 rounded-full {{ $partai->status_timer === 'running' ? 'bg-emerald-400 animate-ping' : 'bg-gray-500' }}"></span>
                    <span id="timer-status-text" class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                        {{ strtoupper($partai->status_timer ?? 'STOP') }}
                    </span>
                </div>
            </div>

            {{-- Tombol Kontrol Timer --}}
            <div class="w-full space-y-1.5">
                <div class="grid grid-cols-3 gap-1.5">
                    <button type="button" onclick="startTimer()" id="btn-timer-start"
                        class="bg-emerald-600 hover:bg-emerald-500 text-white py-1.5 px-2 rounded-lg font-black text-xs uppercase tracking-wide transition-all active:scale-95 shadow-sm">
                        Start
                    </button>
                    <button type="button" onclick="pauseTimer()" id="btn-timer-pause"
                        class="bg-amber-600 hover:bg-amber-500 text-white py-1.5 px-2 rounded-lg font-black text-xs uppercase tracking-wide transition-all active:scale-95 shadow-sm">
                        Pause
                    </button>
                    <button type="button" onclick="resetTimer()" id="btn-timer-reset"
                        class="bg-red-900/60 hover:bg-red-800 text-red-200 py-1.5 px-2 rounded-lg font-black text-xs uppercase tracking-wide transition-all active:scale-95">
                        Reset
                    </button>
                </div>
                
                <div class="flex items-center justify-center gap-1.5 pt-0.5 text-[10px]">
                    <button type="button" onclick="adjustTimer(-10)" class="text-gray-400 hover:text-white px-1.5 py-0.5 rounded bg-gray-800">
                        -10s
                    </button>
                    <button type="button" onclick="adjustTimer(10)" class="text-gray-400 hover:text-white px-1.5 py-0.5 rounded bg-gray-800">
                        +10s
                    </button>
                    <button type="button" onclick="setTimerDirect(120)" class="text-gray-400 hover:text-white px-1.5 py-0.5 rounded bg-gray-800">
                        2 Menit
                    </button>
                </div>
            </div>
        </div>

        {{-- SUDUT MERAH --}}
        <div class="lg:col-span-4 bg-white rounded-xl border-2 border-red-500 shadow-sm p-3 relative overflow-hidden flex flex-col justify-between">
            <div class="absolute top-0 right-0 w-20 h-20 bg-red-50 rounded-full -mr-8 -mt-8 opacity-70"></div>

            <div>
                <div class="flex items-center justify-between mb-1.5 relative">
                    <span class="px-2 py-0.5 rounded bg-red-600 text-white text-[10px] font-black uppercase tracking-wider shadow-sm">
                        SUDUT MERAH
                    </span>
                    <span id="badge-disq-merah" class="{{ $scores['merah']['is_disq'] ? '' : 'hidden' }} px-1.5 py-0.5 rounded bg-red-600 text-white text-[9px] font-black uppercase animate-pulse">
                        DISKUALIFIKASI
                    </span>
                </div>

                <h2 class="text-base md:text-lg font-black text-gray-900 leading-tight truncate">
                    {{ $partai->pesertaMerah ? $partai->pesertaMerah->nama : 'ATLET BELUM DITENTUKAN' }}
                </h2>
                <p class="text-[11px] font-bold text-red-600 uppercase mt-0.5 truncate">
                    {{ $partai->pesertaMerah ? $partai->pesertaMerah->kontingen : '-' }}
                </p>
            </div>

            {{-- Skor & Detail Angka --}}
            <div class="my-1 text-center">
                <div id="display-score-merah" class="text-4xl lg:text-5xl font-black text-red-600 font-mono tracking-tight">
                    @if ($scores['merah']['is_disq'])
                        <span class="text-3xl md:text-4xl text-red-600 font-black tracking-normal">DISQ</span>
                    @else
                        {{ $scores['merah']['total'] }}
                    @endif
                </div>
                <div id="disq-banner-merah" class="{{ $scores['merah']['is_disq'] ? '' : 'hidden' }} mt-0.5">
                    <span class="px-2 py-0.5 rounded-full bg-red-600 text-white text-[9px] font-black uppercase tracking-wider animate-pulse">
                        DISKUALIFIKASI
                    </span>
                </div>
                <p id="label-score-merah" class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider mt-0.5 {{ $scores['merah']['is_disq'] ? 'hidden' : '' }}">Total Poin</p>
            </div>

            {{-- Rincian Poin & Pelanggaran --}}
            <div class="pt-2 border-t border-gray-100 space-y-1.5">
                <div class="grid grid-cols-3 gap-1 text-center">
                    <div class="p-1 bg-red-50/70 rounded-lg border border-red-100">
                        <p class="text-[8px] font-bold text-red-400 uppercase">Poin Masuk</p>
                        <p id="stat-poin-merah" class="text-xs font-black text-red-700 font-mono">{{ $scores['merah']['poin_masuk'] }}</p>
                    </div>
                    <div class="p-1 bg-red-50/70 rounded-lg border border-red-100">
                        <p class="text-[8px] font-bold text-red-400 uppercase">Jatuhan (+3)</p>
                        <p id="stat-jatuhan-merah" class="text-xs font-black text-red-700 font-mono">{{ $scores['merah']['jatuhan_count'] }} ({{ $scores['merah']['poin_jatuhan'] }})</p>
                    </div>
                    <div class="p-1 bg-red-50/70 rounded-lg border border-red-100">
                        <p class="text-[8px] font-bold text-red-400 uppercase">Hukuman</p>
                        <p id="stat-minus-merah" class="text-xs font-black text-red-600 font-mono">-{{ $scores['merah']['poin_minus'] }}</p>
                    </div>
                </div>

                {{-- Status Binaan, Teguran, Peringatan --}}
                <div id="pelanggaran-tags-merah" class="flex flex-wrap gap-1 items-center min-h-[18px]">
                    @if ($scores['merah']['binaan_count'] > 0)
                        <span class="px-1.5 py-0.5 rounded bg-yellow-50 text-yellow-700 border border-yellow-200 text-[9px] font-bold">
                            Binaan: {{ $scores['merah']['binaan_count'] }}x
                        </span>
                    @endif
                    @foreach ($scores['merah']['teguran_list'] as $teg)
                        <span class="px-1.5 py-0.5 rounded bg-orange-50 text-orange-700 border border-orange-200 text-[9px] font-bold">
                            {{ $teg }}
                        </span>
                    @endforeach
                    @foreach ($scores['merah']['peringatan_list'] as $per)
                        <span class="px-1.5 py-0.5 rounded bg-red-100 text-red-800 border border-red-200 text-[9px] font-black">
                            {{ $per }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- 3. KONTROL AKSI DEWAN & STATUS JURI --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-2.5 mb-2.5">
        {{-- KONTROL SUDUT BIRU --}}
        <div class="lg:col-span-5 bg-white rounded-xl p-2.5 border border-blue-200 shadow-sm space-y-1.5 flex flex-col justify-between">
            <div class="flex items-center justify-between border-b border-blue-100 pb-1.5">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600 shadow-sm"></span>
                    <h3 class="text-xs font-black text-blue-900 uppercase tracking-wide">AKSI DEWAN: SUDUT BIRU</h3>
                </div>
                <span class="text-[9px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded-md border border-blue-200">
                    Babak Aktif: <strong id="label-babak-biru">Babak {{ $partai->babak_aktif ?: 1 }}</strong>
                </span>
            </div>

            {{-- 1. Jatuhan (+3 Poin) --}}
            <div class="p-1.5 rounded-lg bg-blue-50/70 border border-blue-100 flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                    <span class="text-xs font-black text-blue-950 uppercase">Jatuhan (+3)</span>
                    <span class="text-[9px] text-blue-600/70 hidden sm:inline">(Babak Aktif)</span>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" onclick="addJatuhan('Biru')"
                        class="px-2.5 py-1 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase transition-all shadow-sm active:scale-95">
                        + JATUHAN
                    </button>
                    <button type="button" id="btn-undo-jatuhan-biru" onclick="undoJatuhan('Biru')"
                        {{ $scores['biru']['can_undo_jatuhan'] ? '' : 'disabled' }}
                        class="px-2 py-1 rounded-lg text-xs font-bold transition-all border {{ $scores['biru']['can_undo_jatuhan'] ? 'bg-white hover:bg-gray-100 text-gray-700 border-gray-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}"
                        title="Batalkan Jatuhan Babak Ini">
                        Undo
                    </button>
                </div>
            </div>

            {{-- 2. Binaan (0 Poin - Babak Ini) --}}
            <div class="p-1.5 rounded-lg bg-amber-50/40 border border-amber-200/80 space-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <span class="text-xs font-black text-amber-950 uppercase">Binaan</span>
                        <span class="text-[9px] text-amber-700/80">(0 Poin - Babak Ini)</span>
                    </div>
                    <button type="button" id="btn-undo-binaan-biru" onclick="undoPelanggaran('Biru', 'binaan')"
                        {{ $scores['biru']['can_undo_binaan'] ? '' : 'disabled' }}
                        class="text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 {{ $scores['biru']['can_undo_binaan'] ? 'bg-amber-100 hover:bg-amber-200 text-amber-900 border-amber-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                        Undo Binaan
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-1.5">
                    <button type="button" id="btn-binaan-1-biru" onclick="addPelanggaran('Biru', 'Binaan 1')"
                        class="py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-center gap-1 {{ $scores['biru']['has_binaan_1'] ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm' }}">
                        <span id="check-binaan-1-biru" class="{{ $scores['biru']['has_binaan_1'] ? '' : 'hidden' }} text-amber-900 font-black">✓</span>
                        Binaan 1
                    </button>
                    <button type="button" id="btn-binaan-2-biru" onclick="addPelanggaran('Biru', 'Binaan 2')"
                        class="py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-center gap-1 {{ $scores['biru']['has_binaan_2'] ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm' }}">
                        <span id="check-binaan-2-biru" class="{{ $scores['biru']['has_binaan_2'] ? '' : 'hidden' }} text-amber-900 font-black">✓</span>
                        Binaan 2
                    </button>
                </div>
            </div>

            {{-- 3. Teguran (Poin Minus - Babak Ini) --}}
            <div class="p-1.5 rounded-lg bg-orange-50/50 border border-orange-200/80 space-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                        <span class="text-xs font-black text-orange-950 uppercase">Teguran</span>
                        <span class="text-[9px] text-orange-700/80">(Babak Ini)</span>
                    </div>
                    <button type="button" id="btn-undo-teguran-biru" onclick="undoPelanggaran('Biru', 'teguran')"
                        {{ $scores['biru']['can_undo_teguran'] ? '' : 'disabled' }}
                        class="text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 {{ $scores['biru']['can_undo_teguran'] ? 'bg-orange-100 hover:bg-orange-200 text-orange-900 border-orange-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                        Undo Teguran
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-1.5">
                    <button type="button" id="btn-teguran-1-biru" onclick="addPelanggaran('Biru', 'Teguran 1')"
                        class="py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between {{ $scores['biru']['has_teguran_1'] ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-teguran-1-biru" class="{{ $scores['biru']['has_teguran_1'] ? '' : 'hidden' }} text-orange-950 font-black">✓</span>
                            Teguran 1
                        </span>
                        <span class="px-1 py-0.5 rounded bg-orange-200 text-orange-950 font-mono text-[9px] font-black">-1</span>
                    </button>
                    <button type="button" id="btn-teguran-2-biru" onclick="addPelanggaran('Biru', 'Teguran 2')"
                        class="py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between {{ $scores['biru']['has_teguran_2'] ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-teguran-2-biru" class="{{ $scores['biru']['has_teguran_2'] ? '' : 'hidden' }} text-orange-950 font-black">✓</span>
                            Teguran 2
                        </span>
                        <span class="px-1 py-0.5 rounded bg-orange-200 text-orange-950 font-mono text-[9px] font-black">-2</span>
                    </button>
                </div>
            </div>

            {{-- 4. Peringatan (Hukuman Berat - Semua Babak Sekaligus) --}}
            <div class="p-1.5 rounded-lg bg-red-50/50 border border-red-200/80 space-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                        <span class="text-xs font-black text-red-950 uppercase">Peringatan</span>
                        <span class="text-[9px] text-red-700/80">(Semua Babak)</span>
                    </div>
                    <button type="button" id="btn-undo-peringatan-biru" onclick="undoPelanggaran('Biru', 'peringatan')"
                        {{ $scores['biru']['can_undo_peringatan'] ? '' : 'disabled' }}
                        class="text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 {{ $scores['biru']['can_undo_peringatan'] ? 'bg-red-100 hover:bg-red-200 text-red-900 border-red-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                        Undo Peringatan
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-1.5">
                    <button type="button" id="btn-peringatan-1-biru" onclick="addPelanggaran('Biru', 'Peringatan 1')"
                        class="py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between {{ $scores['biru']['has_peringatan_1'] ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-peringatan-1-biru" class="{{ $scores['biru']['has_peringatan_1'] ? '' : 'hidden' }} text-red-950 font-black">✓</span>
                            Peringatan 1
                        </span>
                        <span class="px-1 py-0.5 rounded bg-red-200 text-red-950 font-mono text-[9px] font-black">-5</span>
                    </button>
                    <button type="button" id="btn-peringatan-2-biru" onclick="addPelanggaran('Biru', 'Peringatan 2')"
                        class="py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between {{ $scores['biru']['has_peringatan_2'] ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-peringatan-2-biru" class="{{ $scores['biru']['has_peringatan_2'] ? '' : 'hidden' }} text-red-950 font-black">✓</span>
                            Peringatan 2
                        </span>
                        <span class="px-1 py-0.5 rounded bg-red-200 text-red-950 font-mono text-[9px] font-black">-10</span>
                    </button>
                </div>
            </div>

            {{-- 5. Diskualifikasi (DISQ) --}}
            <div class="p-1.5 rounded-lg bg-red-100/60 border border-red-300 flex items-center justify-between gap-1.5">
                <button type="button" id="btn-disq-biru" onclick="addPelanggaran('Biru', 'Peringatan 3')"
                    {{ $scores['biru']['is_disq'] ? 'disabled' : '' }}
                    class="flex-1 py-1.5 px-2 rounded-lg font-black text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 shadow-sm {{ $scores['biru']['is_disq'] ? 'bg-red-900 text-white opacity-80 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 active:scale-98 text-white' }}">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span id="label-disq-biru">{{ $scores['biru']['is_disq'] ? 'TELAH DIDISKUALIFIKASI' : 'DISKUALIFIKASI (DISQ)' }}</span>
                </button>
                <button type="button" id="btn-undo-disq-biru" onclick="undoPelanggaran('Biru', 'disq')"
                    {{ $scores['biru']['can_undo_disq'] ? '' : 'disabled' }}
                    class="shrink-0 text-[10px] font-bold px-2 py-1.5 rounded-lg border transition-all flex items-center gap-1 {{ $scores['biru']['can_undo_disq'] ? 'bg-red-200 hover:bg-red-300 text-red-950 border-red-400 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                    Undo DISQ
                </button>
            </div>
        </div>

        {{-- KONTROL STATUS JURI & OPERASIONAL PERTANDINGAN (VERTIKAL DI TENGAH) --}}
        <div class="lg:col-span-2 bg-white rounded-xl p-2.5 border border-gray-200 shadow-sm flex flex-col justify-between space-y-2">
            {{-- 1. STATUS JURI --}}
            <div class="space-y-1.5">
                @php
                    $anyJuriActive = $statusJuri->contains(fn($j) => $j->is_active);
                @endphp
                <div class="flex items-center justify-between pb-1 border-b border-gray-100">
                    <div class="flex items-center gap-1.5">
                        <span id="dot-header-status-juri" class="w-2.5 h-2.5 rounded-full {{ $anyJuriActive ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-wide">Status Juri</h3>
                    </div>
                    <span id="text-header-status-juri" class="text-[10px] font-black uppercase {{ $anyJuriActive ? 'text-emerald-600' : 'text-gray-400' }}">
                        {{ $anyJuriActive ? 'Aktif' : 'Jeda' }}
                    </span>
                </div>

                {{-- SATU TOMBOL KONTROL UNTUK SEMUA JURI --}}
                <button type="button" onclick="toggleAllJuri()" id="btn-toggle-all-juri"
                    class="w-full py-1.5 px-2 rounded-lg text-xs font-black transition-all shadow-sm flex items-center justify-center gap-1.5 active:scale-98 cursor-pointer {{ $anyJuriActive ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                    <span id="btn-toggle-all-juri-icon">
                        @if($anyJuriActive)
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                            </svg>
                        @else
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                            </svg>
                        @endif
                    </span>
                    <span id="btn-toggle-all-juri-text">{{ $anyJuriActive ? 'Jeda Semua Juri' : 'Aktifkan Semua Juri' }}</span>
                </button>
            </div>

            {{-- 2. KONTROL BABAK (VERTIKAL) --}}
            <div class="space-y-1.5 pt-1.5 border-t border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black text-gray-700 uppercase tracking-wide">Pilih Babak</span>
                    <span id="badge-babak-aktif" class="text-[9px] font-black uppercase text-primary-700 bg-primary-50 px-1.5 py-0.2 rounded border border-primary-200">
                        Babak {{ $partai->babak_aktif ?: 1 }}
                    </span>
                </div>
                <div class="flex flex-col gap-1">
                    @foreach ([1, 2, 3] as $b)
                        <button type="button" onclick="changeBabak({{ $b }})"
                            id="btn-babak-{{ $b }}"
                            class="w-full py-1.5 px-2 rounded-lg text-xs font-black transition-all text-center cursor-pointer active:scale-98 {{ ($partai->babak_aktif ?: 1) == $b ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200' }}">
                            Babak {{ $b }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 3. TOMBOL VERIFIKASI JURI (VAR TANDING) --}}
            <div class="space-y-1.5 pt-1.5 border-t border-gray-100">
                <button type="button" onclick="openModalVerifikasiDewan()"
                    class="w-full py-1.5 px-2 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 active:scale-98 text-white font-black text-xs uppercase tracking-wider flex items-center justify-center gap-1.5 shadow-md shadow-purple-600/20 transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>Verifikasi Juri</span>
                </button>

                {{-- 4. TOMBOL KEPUTUSAN PEMENANG --}}
                <button type="button" onclick="openModalKeputusanPemenang()"
                    class="w-full py-1.5 px-2 rounded-lg bg-gradient-to-r from-amber-500 via-amber-600 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 active:scale-98 text-white font-black text-xs uppercase tracking-wider flex items-center justify-center gap-1.5 shadow-md shadow-amber-500/20 transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    <span>Keputusan Pemenang</span>
                </button>

                {{-- 5. TOMBOL SELESAIKAN PERTANDINGAN --}}
                <button type="button" onclick="openSelesaiModal()"
                    class="w-full py-1.5 px-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white font-black text-xs uppercase tracking-wider flex items-center justify-center gap-1.5 shadow-md shadow-emerald-600/20 transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-emerald-200 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span>Selesai Partai</span>
                </button>
            </div>
        </div>

        {{-- KONTROL SUDUT MERAH --}}
        <div class="lg:col-span-5 bg-white rounded-xl p-2.5 border border-red-200 shadow-sm space-y-1.5 flex flex-col justify-between">
            <div class="flex items-center justify-between border-b border-red-100 pb-1.5">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-600 shadow-sm"></span>
                    <h3 class="text-xs font-black text-red-900 uppercase tracking-wide">AKSI DEWAN: SUDUT MERAH</h3>
                </div>
                <span class="text-[9px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded-md border border-red-200">
                    Babak Aktif: <strong id="label-babak-merah">Babak {{ $partai->babak_aktif ?: 1 }}</strong>
                </span>
            </div>

            {{-- 1. Jatuhan (+3 Poin) --}}
            <div class="p-1.5 rounded-lg bg-red-50/70 border border-red-100 flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                    <span class="text-xs font-black text-red-950 uppercase">Jatuhan (+3)</span>
                    <span class="text-[9px] text-red-600/70 hidden sm:inline">(Babak Aktif)</span>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" onclick="addJatuhan('Merah')"
                        class="px-2.5 py-1 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase transition-all shadow-sm active:scale-95">
                        + JATUHAN
                    </button>
                    <button type="button" id="btn-undo-jatuhan-merah" onclick="undoJatuhan('Merah')"
                        {{ $scores['merah']['can_undo_jatuhan'] ? '' : 'disabled' }}
                        class="px-2 py-1 rounded-lg text-xs font-bold transition-all border {{ $scores['merah']['can_undo_jatuhan'] ? 'bg-white hover:bg-gray-100 text-gray-700 border-gray-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}"
                        title="Batalkan Jatuhan Babak Ini">
                        Undo
                    </button>
                </div>
            </div>

            {{-- 2. Binaan (0 Poin - Babak Ini) --}}
            <div class="p-1.5 rounded-lg bg-amber-50/40 border border-amber-200/80 space-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <span class="text-xs font-black text-amber-950 uppercase">Binaan</span>
                        <span class="text-[9px] text-amber-700/80">(0 Poin - Babak Ini)</span>
                    </div>
                    <button type="button" id="btn-undo-binaan-merah" onclick="undoPelanggaran('Merah', 'binaan')"
                        {{ $scores['merah']['can_undo_binaan'] ? '' : 'disabled' }}
                        class="text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 {{ $scores['merah']['can_undo_binaan'] ? 'bg-amber-100 hover:bg-amber-200 text-amber-900 border-amber-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                        Undo Binaan
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-1.5">
                    <button type="button" id="btn-binaan-1-merah" onclick="addPelanggaran('Merah', 'Binaan 1')"
                        class="py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-center gap-1 {{ $scores['merah']['has_binaan_1'] ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm' }}">
                        <span id="check-binaan-1-merah" class="{{ $scores['merah']['has_binaan_1'] ? '' : 'hidden' }} text-amber-900 font-black">✓</span>
                        Binaan 1
                    </button>
                    <button type="button" id="btn-binaan-2-merah" onclick="addPelanggaran('Merah', 'Binaan 2')"
                        class="py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-center gap-1 {{ $scores['merah']['has_binaan_2'] ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm' }}">
                        <span id="check-binaan-2-merah" class="{{ $scores['merah']['has_binaan_2'] ? '' : 'hidden' }} text-amber-900 font-black">✓</span>
                        Binaan 2
                    </button>
                </div>
            </div>

            {{-- 3. Teguran (Poin Minus - Babak Ini) --}}
            <div class="p-1.5 rounded-lg bg-orange-50/50 border border-orange-200/80 space-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                        <span class="text-xs font-black text-orange-950 uppercase">Teguran</span>
                        <span class="text-[9px] text-orange-700/80">(Babak Ini)</span>
                    </div>
                    <button type="button" id="btn-undo-teguran-merah" onclick="undoPelanggaran('Merah', 'teguran')"
                        {{ $scores['merah']['can_undo_teguran'] ? '' : 'disabled' }}
                        class="text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 {{ $scores['merah']['can_undo_teguran'] ? 'bg-orange-100 hover:bg-orange-200 text-orange-900 border-orange-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                        Undo Teguran
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-1.5">
                    <button type="button" id="btn-teguran-1-merah" onclick="addPelanggaran('Merah', 'Teguran 1')"
                        class="py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between {{ $scores['merah']['has_teguran_1'] ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-teguran-1-merah" class="{{ $scores['merah']['has_teguran_1'] ? '' : 'hidden' }} text-orange-950 font-black">✓</span>
                            Teguran 1
                        </span>
                        <span class="px-1 py-0.5 rounded bg-orange-200 text-orange-950 font-mono text-[9px] font-black">-1</span>
                    </button>
                    <button type="button" id="btn-teguran-2-merah" onclick="addPelanggaran('Merah', 'Teguran 2')"
                        class="py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between {{ $scores['merah']['has_teguran_2'] ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-teguran-2-merah" class="{{ $scores['merah']['has_teguran_2'] ? '' : 'hidden' }} text-orange-950 font-black">✓</span>
                            Teguran 2
                        </span>
                        <span class="px-1 py-0.5 rounded bg-orange-200 text-orange-950 font-mono text-[9px] font-black">-2</span>
                    </button>
                </div>
            </div>

            {{-- 4. Peringatan (Hukuman Berat - Semua Babak Sekaligus) --}}
            <div class="p-1.5 rounded-lg bg-red-50/50 border border-red-200/80 space-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                        <span class="text-xs font-black text-red-950 uppercase">Peringatan</span>
                        <span class="text-[9px] text-red-700/80">(Semua Babak)</span>
                    </div>
                    <button type="button" id="btn-undo-peringatan-merah" onclick="undoPelanggaran('Merah', 'peringatan')"
                        {{ $scores['merah']['can_undo_peringatan'] ? '' : 'disabled' }}
                        class="text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 {{ $scores['merah']['can_undo_peringatan'] ? 'bg-red-100 hover:bg-red-200 text-red-900 border-red-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                        Undo Peringatan
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-1.5">
                    <button type="button" id="btn-peringatan-1-merah" onclick="addPelanggaran('Merah', 'Peringatan 1')"
                        class="py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between {{ $scores['merah']['has_peringatan_1'] ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-peringatan-1-merah" class="{{ $scores['merah']['has_peringatan_1'] ? '' : 'hidden' }} text-red-950 font-black">✓</span>
                            Peringatan 1
                        </span>
                        <span class="px-1 py-0.5 rounded bg-red-200 text-red-950 font-mono text-[9px] font-black">-5</span>
                    </button>
                    <button type="button" id="btn-peringatan-2-merah" onclick="addPelanggaran('Merah', 'Peringatan 2')"
                        class="py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between {{ $scores['merah']['has_peringatan_2'] ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-peringatan-2-merah" class="{{ $scores['merah']['has_peringatan_2'] ? '' : 'hidden' }} text-red-950 font-black">✓</span>
                            Peringatan 2
                        </span>
                        <span class="px-1 py-0.5 rounded bg-red-200 text-red-950 font-mono text-[9px] font-black">-10</span>
                    </button>
                </div>
            </div>

            {{-- 5. Diskualifikasi (DISQ) --}}
            <div class="p-1.5 rounded-lg bg-red-100/60 border border-red-300 flex items-center justify-between gap-1.5">
                <button type="button" id="btn-disq-merah" onclick="addPelanggaran('Merah', 'Peringatan 3')"
                    {{ $scores['merah']['is_disq'] ? 'disabled' : '' }}
                    class="flex-1 py-1.5 px-2 rounded-lg font-black text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 shadow-sm {{ $scores['merah']['is_disq'] ? 'bg-red-900 text-white opacity-80 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 active:scale-98 text-white' }}">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span id="label-disq-merah">{{ $scores['merah']['is_disq'] ? 'TELAH DIDISKUALIFIKASI' : 'DISKUALIFIKASI (DISQ)' }}</span>
                </button>
                <button type="button" id="btn-undo-disq-merah" onclick="undoPelanggaran('Merah', 'disq')"
                    {{ $scores['merah']['can_undo_disq'] ? '' : 'disabled' }}
                    class="shrink-0 text-[10px] font-bold px-2 py-1.5 rounded-lg border transition-all flex items-center gap-1 {{ $scores['merah']['can_undo_disq'] ? 'bg-red-200 hover:bg-red-300 text-red-950 border-red-400 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                    Undo DISQ
                </button>
            </div>
        </div>
    </div>

    {{-- 6. TIMELINE LOG RIWAYAT POIN & PELANGGARAN (DIBAGI DUA SUDUT: BIRU & MERAH) --}}
    <div class="grid grid-cols-2 gap-2.5 items-start">
        {{-- LOG SUDUT BIRU --}}
        <div class="bg-white rounded-xl p-2.5 border border-blue-200 shadow-sm flex flex-col h-64 justify-start">
            <div class="flex items-center justify-between border-b border-blue-100 pb-1.5 mb-1.5 shrink-0">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600 shadow-sm"></span>
                    <h3 class="text-xs font-black text-blue-900 uppercase tracking-wide">Riwayat Log Sudut Biru</h3>
                </div>
                <span class="text-[10px] font-bold text-blue-600 truncate max-w-[140px]">
                    {{ $partai->pesertaBiru ? $partai->pesertaBiru->nama : 'Sudut Biru' }}
                </span>
            </div>

            <div class="flex-1 overflow-y-auto overflow-x-auto min-h-0">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="sticky top-0 bg-blue-50/90 backdrop-blur-xs z-10">
                        <tr class="border-b border-blue-100 text-blue-800 uppercase tracking-wider font-bold text-[10px]">
                            <th class="px-2 py-1 w-16 text-center">Waktu</th>
                            <th class="px-2 py-1 w-16 text-center">Babak</th>
                            <th class="px-2 py-1">Aksi / Event</th>
                            <th class="px-2 py-1">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="timeline-body-biru" class="divide-y divide-gray-100 text-[11px]">
                        @php $biruEvents = collect($timeline)->where('sudut', 'Biru'); @endphp
                        @forelse ($biruEvents as $ev)
                            @php
                                $idDetail = 'detail-' . ($ev['id'] ?? uniqid());
                                $hasDetails = !empty($ev['jurist_details']);
                                $isPoin = (($ev['type'] ?? '') === 'poin');
                                $isJuri = (($ev['type'] ?? '') === 'juri_input');
                            @endphp
                            <tr class="hover:bg-blue-50/40">
                                <td class="px-2 py-1 text-center text-gray-400 font-mono">{{ $ev['time'] }}</td>
                                <td class="px-2 py-1 text-center font-bold text-gray-700">Babak {{ $ev['babak'] }}</td>
                                <td class="px-2 py-1">
                                    @if ($isPoin)
                                        <div class="flex items-center gap-1 flex-wrap">
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-blue-600 text-white font-black text-[10px] shadow-sm">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                {{ $ev['badge'] }}
                                            </span>
                                            @if ($hasDetails)
                                                <button type="button" onclick="toggleDetail('{{ $idDetail }}')" class="text-[10px] text-blue-600 hover:text-blue-800 font-bold underline cursor-pointer">
                                                    ▼ Rincian Juri
                                                </button>
                                            @endif
                                        </div>
                                    @elseif ($isJuri)
                                        @php
                                            $isBerhasil = (($ev['status_input'] ?? '') === 'Berhasil');
                                            $isBelum = (($ev['status_input'] ?? '') === 'Belum Proses');
                                            $badgeClass = $isBerhasil ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($isBelum ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-slate-100 text-slate-600 border-slate-200');
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $badgeClass }}">
                                            @if ($isBerhasil)
                                                <svg class="w-2.5 h-2.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            @endif
                                            {{ $ev['badge'] }}
                                        </span>
                                    @elseif (($ev['type'] ?? '') === 'jatuhan')
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-black bg-blue-700 text-white">
                                            {{ $ev['badge'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                            {{ $ev['badge'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2 py-1 text-gray-700 font-medium">
                                    {{ $ev['keterangan'] }}
                                    @if ($isPoin && $hasDetails)
                                        <div id="{{ $idDetail }}" class="hidden mt-1 p-1.5 rounded-lg bg-blue-50/80 border border-blue-200 text-[10px] space-y-0.5 shadow-xs">
                                            <div class="font-bold text-[9px] text-blue-900 uppercase tracking-wider mb-0.5">Waktu Input Juri Konsensus:</div>
                                            @foreach($ev['jurist_details'] as $jd)
                                                <div class="flex items-center justify-between text-blue-950 font-medium">
                                                    <span>• Juri {{ $jd['no_juri'] }} ({{ $jd['jenis'] ?? 'Serangan' }}): Sah</span>
                                                    <span class="font-mono text-gray-500 font-bold">{{ $jd['time'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-gray-400 italic text-[11px]">
                                    Belum ada riwayat aktivitas Sudut Biru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- LOG SUDUT MERAH --}}
        <div class="bg-white rounded-xl p-2.5 border border-red-200 shadow-sm flex flex-col h-64 justify-start">
            <div class="flex items-center justify-between border-b border-red-100 pb-1.5 mb-1.5 shrink-0">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-600 shadow-sm"></span>
                    <h3 class="text-xs font-black text-red-900 uppercase tracking-wide">Riwayat Log Sudut Merah</h3>
                </div>
                <span class="text-[10px] font-bold text-red-600 truncate max-w-[140px]">
                    {{ $partai->pesertaMerah ? $partai->pesertaMerah->nama : 'Sudut Merah' }}
                </span>
            </div>

            <div class="flex-1 overflow-y-auto overflow-x-auto min-h-0">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="sticky top-0 bg-red-50/90 backdrop-blur-xs z-10">
                        <tr class="border-b border-red-100 text-red-800 uppercase tracking-wider font-bold text-[10px]">
                            <th class="px-2 py-1 w-16 text-center">Waktu</th>
                            <th class="px-2 py-1 w-16 text-center">Babak</th>
                            <th class="px-2 py-1">Aksi / Event</th>
                            <th class="px-2 py-1">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="timeline-body-merah" class="divide-y divide-gray-100 text-[11px]">
                        @php $merahEvents = collect($timeline)->where('sudut', 'Merah'); @endphp
                        @forelse ($merahEvents as $ev)
                            @php
                                $idDetail = 'detail-' . ($ev['id'] ?? uniqid());
                                $hasDetails = !empty($ev['jurist_details']);
                                $isPoin = (($ev['type'] ?? '') === 'poin');
                                $isJuri = (($ev['type'] ?? '') === 'juri_input');
                            @endphp
                            <tr class="hover:bg-red-50/40">
                                <td class="px-2 py-1 text-center text-gray-400 font-mono">{{ $ev['time'] }}</td>
                                <td class="px-2 py-1 text-center font-bold text-gray-700">Babak {{ $ev['babak'] }}</td>
                                <td class="px-2 py-1">
                                    @if ($isPoin)
                                        <div class="flex items-center gap-1 flex-wrap">
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-red-600 text-white font-black text-[10px] shadow-sm">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                {{ $ev['badge'] }}
                                            </span>
                                            @if ($hasDetails)
                                                <button type="button" onclick="toggleDetail('{{ $idDetail }}')" class="text-[10px] text-red-600 hover:text-red-800 font-bold underline cursor-pointer">
                                                    ▼ Rincian Juri
                                                </button>
                                            @endif
                                        </div>
                                    @elseif ($isJuri)
                                        @php
                                            $isBerhasil = (($ev['status_input'] ?? '') === 'Berhasil');
                                            $isBelum = (($ev['status_input'] ?? '') === 'Belum Proses');
                                            $badgeClass = $isBerhasil ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($isBelum ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-slate-100 text-slate-600 border-slate-200');
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $badgeClass }}">
                                            @if ($isBerhasil)
                                                <svg class="w-2.5 h-2.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            @endif
                                            {{ $ev['badge'] }}
                                        </span>
                                    @elseif (($ev['type'] ?? '') === 'jatuhan')
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-black bg-red-700 text-white">
                                            {{ $ev['badge'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                            {{ $ev['badge'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2 py-1 text-gray-700 font-medium">
                                    {{ $ev['keterangan'] }}
                                    @if ($isPoin && $hasDetails)
                                        <div id="{{ $idDetail }}" class="hidden mt-1 p-1.5 rounded-lg bg-red-50/80 border border-red-200 text-[10px] space-y-0.5 shadow-xs">
                                            <div class="font-bold text-[9px] text-red-900 uppercase tracking-wider mb-0.5">Waktu Input Juri Konsensus:</div>
                                            @foreach($ev['jurist_details'] as $jd)
                                                <div class="flex items-center justify-between text-red-950 font-medium">
                                                    <span>• Juri {{ $jd['no_juri'] }} ({{ $jd['jenis'] ?? 'Serangan' }}): Sah</span>
                                                    <span class="font-mono text-gray-500 font-bold">{{ $jd['time'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-gray-400 italic text-[11px]">
                                    Belum ada riwayat aktivitas Sudut Merah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL SELESAIKAN PERTANDINGAN (OTOMATIS BERDASARKAN 5 KRITERIA) --}}
    <div id="modal-selesai" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden transition-all duration-300">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl border border-gray-100 transform transition-all animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 font-black text-lg shadow-xs">
                        🏁
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900 tracking-tight">Selesaikan Pertandingan</h3>
                        <p class="text-[11px] text-gray-500 font-medium">Pemenang Ditetapkan Otomatis Berdasarkan 5 Kriteria</p>
                    </div>
                </div>
                <button type="button" onclick="closeSelesaiModal()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @php
                $sudutMenang = $evaluasiPemenang['sudut'] ?? null;
                $isMerah = (strtolower($sudutMenang) === 'merah');
                $isBiru = (strtolower($sudutMenang) === 'biru');
                $isSeri = (!$isMerah && !$isBiru);
            @endphp

            <form action="{{ route('admin.dewan.tanding.selesai-partai', $partai->id) }}" method="POST">
                @csrf
                <input type="hidden" name="id_pemenang" id="selesai-id-pemenang" value="{{ $evaluasiPemenang['id_pemenang'] ?? '' }}">
                <input type="hidden" name="alasan_pemenang" id="selesai-alasan-pemenang" value="{{ $evaluasiPemenang['alasan'] ?? '' }}">

                <div class="mb-5">
                    {{-- Card Pemenang Otomatis --}}
                    <div id="card-pemenang-otomatis" class="p-4 rounded-xl border-2 transition-all {{ $isMerah ? 'border-red-500 bg-red-50/70' : ($isBiru ? 'border-blue-500 bg-blue-50/70' : 'border-amber-400 bg-amber-50/70') }}">
                        <div class="flex items-center justify-between mb-2">
                            <span id="badge-pemenang-otomatis" class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full shadow-xs {{ $isMerah ? 'bg-red-600 text-white' : ($isBiru ? 'bg-blue-600 text-white' : 'bg-amber-600 text-white') }}">
                                {{ $isMerah ? 'PEMENANG: SUDUT MERAH' : ($isBiru ? 'PEMENANG: SUDUT BIRU' : 'HASIL SERI / IMBANG') }}
                            </span>
                            <span id="tahap-pemenang-otomatis" class="text-[10px] font-extrabold text-gray-500 bg-white/80 px-2 py-0.5 rounded border border-gray-200">
                                Kriteria Tahap {{ $evaluasiPemenang['kriteriaTahap'] ?? 1 }}
                            </span>
                        </div>

                        <div class="my-2">
                            <h4 id="nama-pemenang-otomatis" class="text-base font-black {{ $isMerah ? 'text-red-900' : ($isBiru ? 'text-blue-900' : 'text-amber-900') }}">
                                @if($isMerah)
                                    {{ $evaluasiPemenang['namaMerah'] ?? 'SUDUT MERAH' }}
                                @elseif($isBiru)
                                    {{ $evaluasiPemenang['namaBiru'] ?? 'SUDUT BIRU' }}
                                @else
                                    HASIL MASIH IMBANG / SERI
                                @endif
                            </h4>
                            <p id="kontingen-pemenang-otomatis" class="text-xs font-semibold {{ $isMerah ? 'text-red-700' : ($isBiru ? 'text-blue-700' : 'text-amber-700') }}">
                                @if($isMerah)
                                    {{ $evaluasiPemenang['kontingenMerah'] ?? '-' }}
                                @elseif($isBiru)
                                    {{ $evaluasiPemenang['kontingenBiru'] ?? '-' }}
                                @else
                                    Seluruh 5 Kriteria Poin Bernilai Sama
                                @endif
                            </p>
                        </div>

                        <div class="mt-3 pt-2.5 border-t {{ $isMerah ? 'border-red-200' : ($isBiru ? 'border-blue-200' : 'border-amber-200') }} flex items-center justify-between text-xs">
                            <span class="text-gray-500 font-medium">Alasan Kemenangan:</span>
                            <span id="alasan-pemenang-otomatis" class="font-bold {{ $isMerah ? 'text-red-900' : ($isBiru ? 'text-blue-900' : 'text-amber-900') }}">
                                {{ $evaluasiPemenang['alasan'] ?? '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- Warning jika masih seri --}}
                    <div id="warning-seri-selesai" class="{{ $isSeri ? '' : 'hidden' }} mt-3 p-3 rounded-xl bg-amber-100/70 border border-amber-300 text-amber-900 text-xs flex items-start gap-2">
                        <span class="text-base">⚠️</span>
                        <div>
                            <p class="font-bold">Pertandingan belum memiliki pemenang.</p>
                            <p class="text-[11px] text-amber-800 mt-0.5">Total skor dan semua kriteria tie-break masih bernilai sama. Selesaikan babak atau tambah poin/hukuman sah sebelum mengunci partai.</p>
                        </div>
                    </div>

                    <div class="mt-3 p-2.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-2 text-[11px] text-slate-600">
                        <span class="text-sm">⚖️</span>
                        <span>Sistem otomatis mengunci skor dan mengupdate bagan partai lanjutan sesuai atlet pemenang di atas.</span>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeSelesaiModal()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-all">
                        Batal
                    </button>
                    <button type="submit" id="btn-submit-selesai" {{ $isSeri ? 'disabled' : '' }}
                        class="px-4 py-2 {{ $isSeri ? 'bg-gray-300 text-gray-500 cursor-not-allowed opacity-60' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm' }} rounded-xl text-xs font-bold transition-all">
                        Kunci & Tetapkan Pemenang
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL VERIFIKASI DEWAN (VAR TANDING) --}}
    <div id="modal-verifikasi-dewan" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm hidden transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-xl overflow-hidden flex flex-col transform transition-all duration-300 animate-in fade-in zoom-in-95">
            
            {{-- Header Modal --}}
            <div class="bg-gradient-to-r from-purple-700 via-indigo-700 to-slate-900 text-white p-4 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-white/20 border border-white/30 flex items-center justify-center text-lg shadow-inner">
                        📹
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider">SISTEM VERIFIKASI JURI (VAR)</h3>
                        <p class="text-[10px] text-purple-200">Pantau respon Juri 1, 2, dan 3 secara real-time</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span id="dewan-verif-babak" class="px-2.5 py-1 bg-white/20 text-white rounded-lg text-xs font-black uppercase tracking-wider border border-white/30">
                        BABAK {{ $partai->babak_aktif ?: 1 }}
                    </span>
                    <button type="button" onclick="closeVerifikasiDewan()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs font-bold transition-all cursor-pointer">
                        ✕
                    </button>
                </div>
            </div>

            {{-- Body Modal --}}
            <div class="p-4 space-y-4 bg-slate-50/50">
                
                {{-- Status 3 Juri Cards --}}
                <div class="grid grid-cols-3 gap-3">
                    @for ($j = 1; $j <= 3; $j++)
                        <div id="dewan-card-juri-{{ $j }}" class="p-3 rounded-xl border bg-white shadow-sm flex flex-col items-center justify-between text-center min-h-[110px] transition-all border-slate-200">
                            <span class="text-[11px] font-black text-slate-500 uppercase tracking-widest mb-1">JURI {{ $j }}</span>
                            
                            {{-- Icon status --}}
                            <div id="dewan-icon-juri-{{ $j }}" class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-base font-black my-1 text-slate-400 transition-all">
                                ⏳
                            </div>
                            
                            {{-- Pilihan badge --}}
                            <span id="dewan-badge-juri-{{ $j }}" class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-500 border border-slate-200">
                                Menunggu...
                            </span>
                        </div>
                    @endfor
                </div>

                {{-- Banner Hasil Konsensus Realtime --}}
                <div id="dewan-banner-hasil" class="p-3.5 rounded-xl border bg-slate-100 border-slate-200 text-center transition-all">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 block mb-0.5">STATUS HASIL KONSENSUS</span>
                    <h4 id="dewan-text-hasil" class="text-base font-black text-slate-700">
                        ⏳ Menunggu respon dari juri (Minimal 2 juri sepakat)...
                    </h4>
                </div>

                {{-- Aksi Eksekusi Hasil (Jika Diperlukan oleh Dewan) --}}
                <div id="dewan-aksi-eksekusi" class="p-3 rounded-xl bg-white border border-slate-200 space-y-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block">Tindakan Keputusan (Opsional):</span>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 text-center">
                        <button type="button" onclick="eksekusiKeputusanVerifikasi('jatuhan_biru')"
                            class="px-2 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 border border-blue-200 text-[10px] font-black transition-all">
                            Jatuhan Biru (+3)
                        </button>
                        <button type="button" onclick="eksekusiKeputusanVerifikasi('jatuhan_merah')"
                            class="px-2 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 hover:text-white text-red-700 border border-red-200 text-[10px] font-black transition-all">
                            Jatuhan Merah (+3)
                        </button>
                        <button type="button" onclick="eksekusiKeputusanVerifikasi('sah_biru_tendangan')"
                            class="px-2 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 border border-indigo-200 text-[10px] font-black transition-all">
                            Tendangan Biru (+2)
                        </button>
                        <button type="button" onclick="eksekusiKeputusanVerifikasi('sah_merah_tendangan')"
                            class="px-2 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-700 border border-rose-200 text-[10px] font-black transition-all">
                            Tendangan Merah (+2)
                        </button>
                    </div>
                </div>

            </div>

            {{-- Footer Modal --}}
            <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-400">Modal verifikasi di display & juri dikontrol oleh Dewan.</span>
                <button type="button" onclick="closeVerifikasiDewan()"
                    class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs uppercase tracking-wider flex items-center gap-1.5 shadow-md transition-all cursor-pointer">
                    <span>Tutup Verifikasi</span>
                </button>
            </div>

        </div>
    </div>

    {{-- MODAL KEPUTUSAN PEMENANG DEWAN --}}
    <div id="modal-pemenang-dewan" class="{{ ($partai->status_pemenang ?? false) ? '' : 'hidden' }} fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl border border-amber-200/80 w-full max-w-2xl max-h-[92vh] overflow-y-auto flex flex-col transform transition-all duration-300 animate-in fade-in zoom-in-95">
            
            {{-- Header Modal --}}
            <div class="bg-gradient-to-r from-amber-600 via-yellow-600 to-amber-700 text-white p-4 flex items-center justify-between shrink-0 shadow-md">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-white/20 border border-white/30 flex items-center justify-center text-xl shadow-inner">
                        🏆
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider">KEPUTUSAN PEMENANG PERTANDINGAN</h3>
                        <p class="text-[11px] text-amber-100 font-medium">Partai {{ $partai->no_partai }} • {{ $partai->kelas }} • Kriteria IPSI (5 Tahapan)</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalKeputusanPemenang()" class="w-8 h-8 rounded-lg bg-black/20 hover:bg-black/40 text-white flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body Modal --}}
            <div class="p-4 space-y-4 text-slate-800 text-xs">
                
                {{-- Banner Pemenang Utama --}}
                @php
                    $isMerah = ($partai->sudut_pemenang === 'Merah');
                    $isBiru = ($partai->sudut_pemenang === 'Biru');
                    $pBoxClass = $isMerah ? 'bg-gradient-to-r from-red-600 to-red-800 text-white border-red-500' : ($isBiru ? 'bg-gradient-to-r from-blue-600 to-blue-800 text-white border-blue-500' : 'bg-gradient-to-r from-amber-600 to-yellow-700 text-white border-amber-500');
                    $pBadgeText = $isMerah ? 'PEMENANG: SUDUT MERAH' : ($isBiru ? 'PEMENANG: SUDUT BIRU' : 'STATUS: HASIL IMBANG / SERI');
                    $pNama = $isMerah ? ($partai->pesertaMerah->nama ?? 'SUDUT MERAH') : ($isBiru ? ($partai->pesertaBiru->nama ?? 'SUDUT BIRU') : 'HASIL IMBANG / SERI');
                    $pKontingen = $isMerah ? ($partai->pesertaMerah->kontingen ?? '-') : ($isBiru ? ($partai->pesertaBiru->kontingen ?? '-') : 'Seluruh 5 Kriteria Poin Sama');
                @endphp
                <div id="dewan-pemenang-box" class="p-4 rounded-xl border-2 {{ $pBoxClass }} shadow-lg flex flex-col items-center justify-center text-center gap-1.5 transition-all">
                    <span id="dewan-pemenang-badge" class="px-3 py-1 bg-white {{ $isMerah ? 'text-red-700' : ($isBiru ? 'text-blue-700' : 'text-amber-800') }} font-black text-xs uppercase rounded-full shadow">
                        {{ $pBadgeText }}
                    </span>
                    <h2 id="dewan-pemenang-nama" class="text-xl sm:text-2xl font-black tracking-wide text-white drop-shadow">
                        {{ $pNama }}
                    </h2>
                    <p id="dewan-pemenang-kontingen" class="text-xs sm:text-sm font-bold text-amber-200">
                        {{ $pKontingen }}
                    </p>
                    <div class="mt-1 px-3 py-1 bg-black/25 backdrop-blur-sm rounded-lg border border-white/20 text-white font-semibold text-xs flex items-center gap-1.5">
                        <span>Alasan:</span>
                        <span id="dewan-pemenang-alasan" class="font-bold underline">{{ $partai->alasan_pemenang ?? '-' }}</span>
                    </div>
                </div>

                {{-- Tabel Perbandingan 5 Kriteria IPSI --}}
                <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                    <div class="bg-slate-100 px-3 py-2 border-b border-slate-200 font-black text-[11px] uppercase tracking-wider text-slate-700 flex items-center justify-between">
                        <span>Rincian Evaluasi 5 Kriteria Penentuan Pemenang</span>
                        <span class="text-[10px] text-slate-500 font-medium">Berdasarkan Regulasi IPSI</span>
                    </div>
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold text-[11px]">
                                <th class="p-2.5">Tahap & Kriteria</th>
                                <th class="p-2.5 text-center text-red-600 w-28 bg-red-50/60 font-black">Sudut Merah</th>
                                <th class="p-2.5 text-center text-blue-600 w-28 bg-blue-50/60 font-black">Sudut Biru</th>
                                <th class="p-2.5 text-slate-600 text-[11px]">Aturan Evaluasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            {{-- 1. Total Nilai --}}
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-2.5 font-bold text-slate-900">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center text-[10px] font-black">1</span>
                                        <span>Total Skor (Nilai Bersih)</span>
                                    </div>
                                </td>
                                <td class="p-2.5 text-center font-black text-red-700 bg-red-50/30 text-sm">
                                    <span id="dewan-skor-merah">{{ $scores['merah']['total'] ?? 0 }}</span>
                                </td>
                                <td class="p-2.5 text-center font-black text-blue-700 bg-blue-50/30 text-sm">
                                    <span id="dewan-skor-biru">{{ $scores['biru']['total'] ?? 0 }}</span>
                                </td>
                                <td class="p-2.5 text-slate-500 text-[11px]">Skor tertinggi otomatis menang</td>
                            </tr>

                            {{-- 2. Nilai Hukuman --}}
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-2.5 font-bold text-slate-900">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center text-[10px] font-black">2</span>
                                        <span>Nilai Hukuman</span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-normal pl-6.5">(T1×1) + (T2×2) + (P1×5) + (P2×10)</p>
                                </td>
                                <td class="p-2.5 text-center font-black text-red-700 bg-red-50/30 text-sm">
                                    <span id="dewan-hukuman-merah">0</span>
                                </td>
                                <td class="p-2.5 text-center font-black text-blue-700 bg-blue-50/30 text-sm">
                                    <span id="dewan-hukuman-biru">0</span>
                                </td>
                                <td class="p-2.5 text-slate-500 text-[11px]">Jika skor sama, hukuman <strong>lebih rendah</strong> menang</td>
                            </tr>

                            {{-- 3. Nilai Jatuhan --}}
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-2.5 font-bold text-slate-900">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center text-[10px] font-black">3</span>
                                        <span>Nilai Jatuhan (3 Poin)</span>
                                    </div>
                                </td>
                                <td class="p-2.5 text-center font-black text-red-700 bg-red-50/30 text-sm">
                                    <span id="dewan-jatuhan-merah">{{ ($scores['merah']['jatuhan_count'] ?? 0) * 3 }}</span>
                                </td>
                                <td class="p-2.5 text-center font-black text-blue-700 bg-blue-50/30 text-sm">
                                    <span id="dewan-jatuhan-biru">{{ ($scores['biru']['jatuhan_count'] ?? 0) * 3 }}</span>
                                </td>
                                <td class="p-2.5 text-slate-500 text-[11px]">Nilai jatuhan <strong>lebih tinggi</strong> menang</td>
                            </tr>

                            {{-- 4. Nilai Tendangan --}}
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-2.5 font-bold text-slate-900">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center text-[10px] font-black">4</span>
                                        <span>Jumlah Tendangan (2 Poin)</span>
                                    </div>
                                </td>
                                <td class="p-2.5 text-center font-black text-red-700 bg-red-50/30 text-sm">
                                    <span id="dewan-tendangan-merah">0</span>
                                </td>
                                <td class="p-2.5 text-center font-black text-blue-700 bg-blue-50/30 text-sm">
                                    <span id="dewan-tendangan-biru">0</span>
                                </td>
                                <td class="p-2.5 text-slate-500 text-[11px]">Jumlah tendangan <strong>lebih banyak</strong> menang</td>
                            </tr>

                            {{-- 5. Nilai Pukulan --}}
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-2.5 font-bold text-slate-900">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center text-[10px] font-black">5</span>
                                        <span>Jumlah Pukulan (1 Poin)</span>
                                    </div>
                                </td>
                                <td class="p-2.5 text-center font-black text-red-700 bg-red-50/30 text-sm">
                                    <span id="dewan-pukulan-merah">0</span>
                                </td>
                                <td class="p-2.5 text-center font-black text-blue-700 bg-blue-50/30 text-sm">
                                    <span id="dewan-pukulan-biru">0</span>
                                </td>
                                <td class="p-2.5 text-slate-500 text-[11px]">Jumlah pukulan <strong>lebih banyak</strong> menang</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- Footer Modal --}}
            <div class="p-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0">
                <span class="text-[11px] text-slate-500">Tampilan modal ini tersinkronisasi otomatis dengan Layar Display Penonton.</span>
                <button type="button" onclick="closeModalKeputusanPemenang()"
                    class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs uppercase tracking-wider flex items-center gap-1.5 shadow-md transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Tutup Keputusan Pemenang</span>
                </button>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script>
    const partaiId = {{ $partai->id }};
    const csrfToken = "{{ csrf_token() }}";
    let timerSeconds = {{ $partai->waktu_timer ?? 120 }};
    let timerStatus = "{{ $partai->status_timer ?? 'stop' }}";
    let timerInterval = null;

    // Helper POST AJAX
    async function sendPost(url, data = {}) {
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            return await res.json();
        } catch (err) {
            console.error('Error post:', err);
            return { success: false, message: err.message };
        }
    }

    // Toast Alert
    function showToast(icon, title) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: title,
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });
    }

    // 1. KONTROL JURI ACTIVE / PAUSE
    let areAllJuriActive = {{ $anyJuriActive ? 'true' : 'false' }};

    async function toggleAllJuri(action) {
        const targetAction = action || (areAllJuriActive ? 'pause' : 'active');
        const url = `/admin/dewan/tanding/${partaiId}/toggle-all-juri`;
        const res = await sendPost(url, { action: targetAction });
        if (res.success) {
            updateAllJuriStatusUI(res.is_active);
            showToast('success', res.message);
        }
    }

    function updateAllJuriStatusUI(isActive) {
        areAllJuriActive = isActive;
        
        // Update Status Dot & Teks di Header
        const headerDot = document.getElementById('dot-header-status-juri');
        const headerText = document.getElementById('text-header-status-juri');

        if (headerDot) {
            headerDot.className = isActive 
                ? "w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse" 
                : "w-2.5 h-2.5 rounded-full bg-gray-400";
        }
        if (headerText) {
            headerText.innerText = isActive ? "Aktif" : "Jeda";
            headerText.className = isActive 
                ? "text-[10px] font-black uppercase text-emerald-600" 
                : "text-[10px] font-black uppercase text-gray-400";
        }

        // Update Tombol Kontrol
        const btn = document.getElementById('btn-toggle-all-juri');
        const icon = document.getElementById('btn-toggle-all-juri-icon');
        const btnText = document.getElementById('btn-toggle-all-juri-text');
        if (btn && btnText) {
            if (isActive) {
                btn.className = "w-full py-2 px-2.5 rounded-lg text-xs font-black transition-all shadow-sm flex items-center justify-center gap-1.5 active:scale-98 cursor-pointer bg-amber-500 hover:bg-amber-600 text-white";
                btnText.innerText = "Jeda Semua Juri";
                if (icon) {
                    icon.innerHTML = `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" /></svg>`;
                }
            } else {
                btn.className = "w-full py-2 px-3 rounded-xl text-xs font-black transition-all shadow-sm flex items-center justify-center gap-1.5 active:scale-98 cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white";
                btnText.innerText = "Aktifkan Semua Juri";
                if (icon) {
                    icon.innerHTML = `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" /></svg>`;
                }
            }
        }
    }

    // 2. KONTROL JATUHAN
    async function addJatuhan(sudut) {
        const url = `/admin/dewan/tanding/${partaiId}/jatuhan`;
        const res = await sendPost(url, { sudut: sudut });
        if (res.success) {
            applyScores(res.scores);
            showToast('success', res.message);
            refreshStatus();
        }
    }

    async function undoJatuhan(sudut) {
        const url = `/admin/dewan/tanding/${partaiId}/jatuhan/undo`;
        const res = await sendPost(url, { sudut: sudut });
        if (res.success) {
            applyScores(res.scores);
            showToast('info', res.message);
            refreshStatus();
        }
    }

    // 3. KONTROL PELANGGARAN & UNDO
    async function addPelanggaran(sudut, jenis) {
        const url = `/admin/dewan/tanding/${partaiId}/pelanggaran`;
        const res = await sendPost(url, { sudut: sudut, jenis_pelanggaran: jenis });
        if (res.success) {
            applyScores(res.scores);
            showToast('warning', res.message);
            refreshStatus();
        } else {
            showToast('error', res.message || 'Gagal menambahkan pelanggaran');
        }
    }

    async function undoPelanggaran(sudut, kategori = null) {
        const url = `/admin/dewan/tanding/${partaiId}/pelanggaran/undo`;
        const res = await sendPost(url, { sudut: sudut, kategori: kategori });
        if (res.success) {
            applyScores(res.scores);
            showToast('info', res.message);
            refreshStatus();
        } else {
            showToast('error', res.message || 'Tidak ada pelanggaran yang dapat dibatalkan.');
        }
    }

    // 4. KONTROL VERIFIKASI JURI (VAR TANDING SISTEM MODAL)
    let currentVerifikasiId = {{ (isset($verifikasiAktif) && $verifikasiAktif) ? $verifikasiAktif->id : 'null' }};

    async function openModalVerifikasiDewan() {
        const modal = document.getElementById('modal-verifikasi-dewan');
        if (modal) modal.classList.remove('hidden');

        resetModalVerifikasiDewan();

        const url = `/admin/dewan/tanding/${partaiId}/start-verifikasi`;
        const res = await sendPost(url, { keterangan: 'Verifikasi Juri (VAR)' });
        if (res && res.success) {
            currentVerifikasiId = res.verifikasi.id;
            showToast('info', 'Sesi verifikasi dibuka. Menunggu respon 3 juri...');
        } else {
            showToast('error', res.message || 'Gagal memulai verifikasi');
        }
    }

    function resetModalVerifikasiDewan() {
        for (let j = 1; j <= 3; j++) {
            const badge = document.getElementById(`dewan-badge-juri-${j}`);
            const icon = document.getElementById(`dewan-icon-juri-${j}`);
            const card = document.getElementById(`dewan-card-juri-${j}`);
            if (badge) {
                badge.innerText = 'Menunggu...';
                badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-500 border border-slate-200';
            }
            if (icon) {
                icon.innerText = '⏳';
                icon.className = 'w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-base font-black my-1 text-slate-400 transition-all';
            }
            if (card) {
                card.className = 'p-3 rounded-xl border bg-white shadow-sm flex flex-col items-center justify-between text-center min-h-[110px] transition-all border-slate-200';
            }
        }
        const banner = document.getElementById('dewan-banner-hasil');
        const textHasil = document.getElementById('dewan-text-hasil');
        if (banner && textHasil) {
            banner.className = 'p-3.5 rounded-xl border bg-slate-100 border-slate-200 text-center transition-all';
            textHasil.innerText = '⏳ Menunggu respon dari juri (Minimal 2 juri sepakat)...';
            textHasil.className = 'text-base font-black text-slate-700';
        }
    }

    function updateDewanVerifikasiUI(jawaban, hasil) {
        if (jawaban) {
            for (let j = 1; j <= 3; j++) {
                const pil = jawaban[j];
                const badge = document.getElementById(`dewan-badge-juri-${j}`);
                const icon = document.getElementById(`dewan-icon-juri-${j}`);
                const card = document.getElementById(`dewan-card-juri-${j}`);
                if (!badge || !icon || !card) continue;

                if (pil === 'Biru') {
                    badge.innerText = 'Sudut Biru';
                    badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-600 text-white border border-blue-400 shadow-sm';
                    icon.innerText = '🔵';
                    icon.className = 'w-10 h-10 rounded-full bg-blue-50 border border-blue-200 flex items-center justify-center text-base font-black my-1 text-blue-600 transition-all';
                    card.className = 'p-3 rounded-xl border-2 border-blue-400 bg-blue-50/50 shadow-sm flex flex-col items-center justify-between text-center min-h-[110px] transition-all';
                } else if (pil === 'Merah') {
                    badge.innerText = 'Sudut Merah';
                    badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-600 text-white border border-red-400 shadow-sm';
                    icon.innerText = '🔴';
                    icon.className = 'w-10 h-10 rounded-full bg-red-50 border border-red-200 flex items-center justify-center text-base font-black my-1 text-red-600 transition-all';
                    card.className = 'p-3 rounded-xl border-2 border-red-400 bg-red-50/50 shadow-sm flex flex-col items-center justify-between text-center min-h-[110px] transition-all';
                } else if (pil === 'Tidak Valid' || pil === 'Batal') {
                    badge.innerText = 'Tidak Valid';
                    badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500 text-white border border-amber-400 shadow-sm';
                    icon.innerText = '⚪';
                    icon.className = 'w-10 h-10 rounded-full bg-amber-50 border border-amber-200 flex items-center justify-center text-base font-black my-1 text-amber-600 transition-all';
                    card.className = 'p-3 rounded-xl border-2 border-amber-400 bg-amber-50/50 shadow-sm flex flex-col items-center justify-between text-center min-h-[110px] transition-all';
                }
            }
        }

        if (hasil) {
            const banner = document.getElementById('dewan-banner-hasil');
            const textHasil = document.getElementById('dewan-text-hasil');
            if (!banner || !textHasil) return;

            if (hasil === 'Biru') {
                banner.className = 'p-3.5 rounded-xl border-2 border-blue-500 bg-blue-50 text-center transition-all shadow-md';
                textHasil.innerHTML = '🎉 KONSENSUS SAH: <span class="text-blue-700 underline font-black">SUDUT BIRU</span> (Tampil di Display)';
                textHasil.className = 'text-base font-black text-blue-900';
            } else if (hasil === 'Merah') {
                banner.className = 'p-3.5 rounded-xl border-2 border-red-500 bg-red-50 text-center transition-all shadow-md';
                textHasil.innerHTML = '🎉 KONSENSUS SAH: <span class="text-red-700 underline font-black">SUDUT MERAH</span> (Tampil di Display)';
                textHasil.className = 'text-base font-black text-red-900';
            } else {
                banner.className = 'p-3.5 rounded-xl border-2 border-amber-500 bg-amber-50 text-center transition-all shadow-md';
                textHasil.innerHTML = '⚠️ KONSENSUS: <span class="text-amber-800 underline font-black">TIDAK VALID / BATAL</span> (Tampil di Display)';
                textHasil.className = 'text-base font-black text-amber-900';
            }
        }
    }

    async function closeVerifikasiDewan(keputusan = 'tutup') {
        if (!currentVerifikasiId) {
            const modal = document.getElementById('modal-verifikasi-dewan');
            if (modal) modal.classList.add('hidden');
            return;
        }
        const url = `/admin/dewan/tanding/verifikasi/${currentVerifikasiId}/selesai`;
        await sendPost(url, { keputusan: keputusan });
        const modal = document.getElementById('modal-verifikasi-dewan');
        if (modal) modal.classList.add('hidden');
        currentVerifikasiId = null;
        showToast('info', 'Sesi verifikasi ditutup di dewan dan display.');
    }

    async function eksekusiKeputusanVerifikasi(keputusan) {
        if (!currentVerifikasiId) return;
        await closeVerifikasiDewan(keputusan);
    }

    // 4.5 KONTROL KEPUTUSAN PEMENANG
    async function openModalKeputusanPemenang() {
        showToast('info', 'Menghitung keputusan pemenang...');
        const url = `/admin/dewan/tanding/${partaiId}/keputusan-pemenang`;
        const res = await sendPost(url);

        if (res && res.success) {
            updateDewanPemenangUI(res.data);
            const modal = document.getElementById('modal-pemenang-dewan');
            if (modal) modal.classList.remove('hidden');
            showToast('success', res.message);
        } else {
            showToast('error', res.message || 'Gagal menghitung pemenang');
        }
    }

    async function closeModalKeputusanPemenang() {
        const url = `/admin/dewan/tanding/${partaiId}/close-pemenang`;
        await sendPost(url);
        const modal = document.getElementById('modal-pemenang-dewan');
        if (modal) modal.classList.add('hidden');
        showToast('info', 'Modal keputusan pemenang ditutup di dewan dan display.');
    }

    function updateDewanPemenangUI(data) {
        if (!data) return;
        const box = document.getElementById('dewan-pemenang-box');
        const badge = document.getElementById('dewan-pemenang-badge');
        const nama = document.getElementById('dewan-pemenang-nama');
        const kontingen = document.getElementById('dewan-pemenang-kontingen');
        const alasan = document.getElementById('dewan-pemenang-alasan');

        if (data.pemenang === 'Merah') {
            if (box) box.className = "p-4 rounded-xl border-2 border-red-500 bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg flex flex-col items-center justify-center text-center gap-1.5 transition-all";
            if (badge) {
                badge.innerText = "PEMENANG: SUDUT MERAH";
                badge.className = "px-3 py-1 bg-white text-red-700 font-black text-xs uppercase rounded-full shadow";
            }
            if (nama) nama.innerText = data.pesertaPemenang ? data.pesertaPemenang.nama : (data.namaMerah || 'SUDUT MERAH');
            if (kontingen) kontingen.innerText = data.pesertaPemenang ? data.pesertaPemenang.kontingen : (data.kontingenMerah || '-');
        } else if (data.pemenang === 'Biru') {
            if (box) box.className = "p-4 rounded-xl border-2 border-blue-500 bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg flex flex-col items-center justify-center text-center gap-1.5 transition-all";
            if (badge) {
                badge.innerText = "PEMENANG: SUDUT BIRU";
                badge.className = "px-3 py-1 bg-white text-blue-700 font-black text-xs uppercase rounded-full shadow";
            }
            if (nama) nama.innerText = data.pesertaPemenang ? data.pesertaPemenang.nama : (data.namaBiru || 'SUDUT BIRU');
            if (kontingen) kontingen.innerText = data.pesertaPemenang ? data.pesertaPemenang.kontingen : (data.kontingenBiru || '-');
        } else {
            if (box) box.className = "p-4 rounded-xl border-2 border-amber-500 bg-gradient-to-r from-amber-600 to-yellow-700 text-white shadow-lg flex flex-col items-center justify-center text-center gap-1.5 transition-all";
            if (badge) {
                badge.innerText = "HASIL IMBANG / SERI";
                badge.className = "px-3 py-1 bg-white text-amber-800 font-black text-xs uppercase rounded-full shadow";
            }
            if (nama) nama.innerText = "HASIL IMBANG / SERI";
            if (kontingen) kontingen.innerText = "Seluruh 5 Kriteria Poin Sama";
        }

        if (alasan) alasan.innerText = data.alasan || '-';

        // Update rows
        const elSkorM = document.getElementById('dewan-skor-merah');
        const elSkorB = document.getElementById('dewan-skor-biru');
        if (elSkorM) elSkorM.innerText = data.totalMerah ?? 0;
        if (elSkorB) elSkorB.innerText = data.totalBiru ?? 0;

        const elHukM = document.getElementById('dewan-hukuman-merah');
        const elHukB = document.getElementById('dewan-hukuman-biru');
        if (elHukM) elHukM.innerText = data.hukumanMerah ?? 0;
        if (elHukB) elHukB.innerText = data.hukumanBiru ?? 0;

        const elJatM = document.getElementById('dewan-jatuhan-merah');
        const elJatB = document.getElementById('dewan-jatuhan-biru');
        if (elJatM) elJatM.innerText = data.jatuhanMerah ?? 0;
        if (elJatB) elJatB.innerText = data.jatuhanBiru ?? 0;

        const elTenM = document.getElementById('dewan-tendangan-merah');
        const elTenB = document.getElementById('dewan-tendangan-biru');
        if (elTenM) elTenM.innerText = data.tendanganMerah ?? 0;
        if (elTenB) elTenB.innerText = data.tendanganBiru ?? 0;

        const elPukM = document.getElementById('dewan-pukulan-merah');
        const elPukB = document.getElementById('dewan-pukulan-biru');
        if (elPukM) elPukM.innerText = data.pukulanMerah ?? 0;
        if (elPukB) elPukB.innerText = data.pukulanBiru ?? 0;
    }

    // 5. KONTROL BABAK
    async function changeBabak(babak) {
        const url = `/admin/dewan/tanding/${partaiId}/update-babak`;
        const res = await sendPost(url, { babak_aktif: babak });
        if (res.success) {
            document.querySelectorAll('[id^="btn-babak-"]').forEach((btn, idx) => {
                if ((idx + 1) === babak) {
                    btn.className = "w-full py-1.5 px-2 rounded-lg text-xs font-black transition-all text-center cursor-pointer active:scale-98 bg-primary-600 text-white shadow-sm";
                } else {
                    btn.className = "w-full py-1.5 px-2 rounded-lg text-xs font-black transition-all text-center cursor-pointer active:scale-98 bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200";
                }
            });
            const badgeBabak = document.getElementById('badge-babak-aktif');
            if (badgeBabak) badgeBabak.innerText = `Babak ${babak}`;
            document.getElementById('badge-babak-display').innerText = `BABAK ${babak}`;
            const lblBabakBiru = document.getElementById('label-babak-biru');
            if (lblBabakBiru) lblBabakBiru.innerText = `Babak ${babak}`;
            const lblBabakMerah = document.getElementById('label-babak-merah');
            if (lblBabakMerah) lblBabakMerah.innerText = `Babak ${babak}`;

            timerSeconds = 120;
            pauseTimer();
            updateTimerDisplay();

            if (res.scores) {
                applyScores(res.scores);
            }
            showToast('success', res.message);
            refreshStatus();
        }
    }

    // 6. TIMER LOGIC
    function updateTimerDisplay() {
        const m = Math.floor(timerSeconds / 60);
        const s = timerSeconds % 60;
        document.getElementById('timer-display').innerText = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }

    function startTimer() {
        if (timerInterval) clearInterval(timerInterval);
        timerStatus = 'running';
        document.getElementById('timer-status-text').innerText = 'RUNNING';
        document.getElementById('timer-status-dot').className = 'w-2 h-2 rounded-full bg-emerald-400 animate-ping';

        sendPost(`/admin/dewan/tanding/${partaiId}/update-timer`, {
            waktu_timer: timerSeconds,
            status_timer: 'running'
        });

        timerInterval = setInterval(() => {
            if (timerSeconds > 0) {
                timerSeconds--;
                updateTimerDisplay();
            } else {
                clearInterval(timerInterval);
                timerStatus = 'stop';
                document.getElementById('timer-status-text').innerText = 'STOP';
                document.getElementById('timer-status-dot').className = 'w-2 h-2 rounded-full bg-red-500';
                sendPost(`/admin/dewan/tanding/${partaiId}/update-timer`, {
                    waktu_timer: 0,
                    status_timer: 'stop'
                });
                Swal.fire({
                    title: 'WAKTU BABAK HABIS!',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4f46e5'
                });
            }
        }, 1000);
    }

    function pauseTimer() {
        if (timerInterval) clearInterval(timerInterval);
        timerStatus = 'pause';
        document.getElementById('timer-status-text').innerText = 'PAUSE';
        document.getElementById('timer-status-dot').className = 'w-2 h-2 rounded-full bg-amber-500';
        sendPost(`/admin/dewan/tanding/${partaiId}/update-timer`, {
            waktu_timer: timerSeconds,
            status_timer: 'pause'
        });
    }

    function resetTimer() {
        pauseTimer();
        timerSeconds = 120;
        updateTimerDisplay();
        document.getElementById('timer-status-text').innerText = 'STOP';
        document.getElementById('timer-status-dot').className = 'w-2 h-2 rounded-full bg-gray-500';
        sendPost(`/admin/dewan/tanding/${partaiId}/update-timer`, {
            waktu_timer: 120,
            status_timer: 'stop'
        });
    }

    function adjustTimer(delta) {
        timerSeconds = Math.max(0, timerSeconds + delta);
        updateTimerDisplay();
        sendPost(`/admin/dewan/tanding/${partaiId}/update-timer`, {
            waktu_timer: timerSeconds,
            status_timer: timerStatus
        });
    }

    function setTimerDirect(sec) {
        timerSeconds = sec;
        updateTimerDisplay();
        sendPost(`/admin/dewan/tanding/${partaiId}/update-timer`, {
            waktu_timer: sec,
            status_timer: timerStatus
        });
    }

    // Helper Toggle Tombol Aktif / Inaktif
    function toggleBtnState(btnId, isEnabled, activeClasses, inactiveClasses) {
        const btn = document.getElementById(btnId);
        if (!btn) return;
        btn.disabled = !isEnabled;
        btn.className = isEnabled ? activeClasses : inactiveClasses;
    }

    function toggleCheckmark(checkId, isVisible) {
        const el = document.getElementById(checkId);
        if (el) {
            if (isVisible) el.classList.remove('hidden');
            else el.classList.add('hidden');
        }
    }

    // Helper Render Status Tags Pelanggaran (Binaan, Teguran, Peringatan)
    function renderPelanggaranTagsHtml(sudutData) {
        if (!sudutData) return '';
        let html = '';

        const binaanCount = Number(sudutData.binaan_count || 0);
        if (binaanCount > 0) {
            html += `<span class="px-1.5 py-0.5 rounded bg-yellow-50 text-yellow-700 border border-yellow-200 text-[9px] font-bold">Binaan: ${binaanCount}x</span>`;
        }

        const teguranList = Array.isArray(sudutData.teguran_list)
            ? sudutData.teguran_list
            : (sudutData.teguran_list ? Object.values(sudutData.teguran_list) : []);
        teguranList.forEach(teg => {
            if (teg) {
                html += `<span class="px-1.5 py-0.5 rounded bg-orange-50 text-orange-700 border border-orange-200 text-[9px] font-bold">${teg}</span>`;
            }
        });

        const peringatanList = Array.isArray(sudutData.peringatan_list)
            ? sudutData.peringatan_list
            : (sudutData.peringatan_list ? Object.values(sudutData.peringatan_list) : []);
        peringatanList.forEach(per => {
            if (per) {
                html += `<span class="px-1.5 py-0.5 rounded bg-red-100 text-red-800 border border-red-200 text-[9px] font-black">${per}</span>`;
            }
        });

        return html;
    }

    // 7. HELPER UPDATE SKOR & STATUS UI
    function applyScores(scores) {
        // --- 1. SKOR UTAMA SUDUT BIRU ---
        const scoreBiruEl = document.getElementById('display-score-biru');
        const bannerBiru = document.getElementById('disq-banner-biru');
        const labelBiru = document.getElementById('label-score-biru');

        if (scores.biru.is_disq) {
            scoreBiruEl.innerHTML = '<span class="text-3xl md:text-4xl text-red-600 font-black tracking-normal">DISQ</span>';
            scoreBiruEl.className = "text-4xl lg:text-5xl font-black text-red-600 font-mono tracking-tight";
            if (bannerBiru) bannerBiru.classList.remove('hidden');
            if (labelBiru) labelBiru.classList.add('hidden');
        } else {
            scoreBiruEl.innerText = scores.biru.total;
            scoreBiruEl.className = "text-4xl lg:text-5xl font-black text-blue-600 font-mono tracking-tight";
            if (bannerBiru) bannerBiru.classList.add('hidden');
            if (labelBiru) labelBiru.classList.remove('hidden');
        }

        document.getElementById('stat-poin-biru').innerText = scores.biru.poin_masuk;
        document.getElementById('stat-jatuhan-biru').innerText = `${scores.biru.jatuhan_count} (${scores.biru.poin_jatuhan})`;
        document.getElementById('stat-minus-biru').innerText = `-${scores.biru.poin_minus}`;

        // --- 2. SKOR UTAMA SUDUT MERAH ---
        const scoreMerahEl = document.getElementById('display-score-merah');
        const bannerMerah = document.getElementById('disq-banner-merah');
        const labelMerah = document.getElementById('label-score-merah');

        if (scores.merah.is_disq) {
            scoreMerahEl.innerHTML = '<span class="text-3xl md:text-4xl text-red-600 font-black tracking-normal">DISQ</span>';
            scoreMerahEl.className = "text-4xl lg:text-5xl font-black text-red-600 font-mono tracking-tight";
            if (bannerMerah) bannerMerah.classList.remove('hidden');
            if (labelMerah) labelMerah.classList.add('hidden');
        } else {
            scoreMerahEl.innerText = scores.merah.total;
            scoreMerahEl.className = "text-4xl lg:text-5xl font-black text-red-600 font-mono tracking-tight";
            if (bannerMerah) bannerMerah.classList.add('hidden');
            if (labelMerah) labelMerah.classList.remove('hidden');
        }

        document.getElementById('stat-poin-merah').innerText = scores.merah.poin_masuk;
        document.getElementById('stat-jatuhan-merah').innerText = `${scores.merah.jatuhan_count} (${scores.merah.poin_jatuhan})`;
        document.getElementById('stat-minus-merah').innerText = `-${scores.merah.poin_minus}`;

        // --- 3. STATUS TOMBOL UNDO SUDUT BIRU (AKTIF / INAKTIF) ---
        toggleBtnState(
            'btn-undo-jatuhan-biru',
            scores.biru.can_undo_jatuhan,
            'px-2 py-1 rounded-lg text-xs font-bold transition-all border bg-white hover:bg-gray-100 text-gray-700 border-gray-300 shadow-sm cursor-pointer',
            'px-2 py-1 rounded-lg text-xs font-bold transition-all border bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-binaan-biru',
            scores.biru.can_undo_binaan,
            'text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 bg-amber-100 hover:bg-amber-200 text-amber-900 border-amber-300 shadow-sm cursor-pointer',
            'text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-teguran-biru',
            scores.biru.can_undo_teguran,
            'text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 bg-orange-100 hover:bg-orange-200 text-orange-900 border-orange-300 shadow-sm cursor-pointer',
            'text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-peringatan-biru',
            scores.biru.can_undo_peringatan,
            'text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 bg-red-100 hover:bg-red-200 text-red-900 border-red-300 shadow-sm cursor-pointer',
            'text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-disq-biru',
            scores.biru.can_undo_disq,
            'shrink-0 text-[10px] font-bold px-2 py-1.5 rounded-lg border transition-all flex items-center gap-1 bg-red-200 hover:bg-red-300 text-red-950 border-red-400 shadow-sm cursor-pointer',
            'shrink-0 text-[10px] font-bold px-2 py-1.5 rounded-lg border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        // --- 4. STATUS TOMBOL UNDO SUDUT MERAH (AKTIF / INAKTIF) ---
        toggleBtnState(
            'btn-undo-jatuhan-merah',
            scores.merah.can_undo_jatuhan,
            'px-2 py-1 rounded-lg text-xs font-bold transition-all border bg-white hover:bg-gray-100 text-gray-700 border-gray-300 shadow-sm cursor-pointer',
            'px-2 py-1 rounded-lg text-xs font-bold transition-all border bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-binaan-merah',
            scores.merah.can_undo_binaan,
            'text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 bg-amber-100 hover:bg-amber-200 text-amber-900 border-amber-300 shadow-sm cursor-pointer',
            'text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-teguran-merah',
            scores.merah.can_undo_teguran,
            'text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 bg-orange-100 hover:bg-orange-200 text-orange-900 border-orange-300 shadow-sm cursor-pointer',
            'text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-peringatan-merah',
            scores.merah.can_undo_peringatan,
            'text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 bg-red-100 hover:bg-red-200 text-red-900 border-red-300 shadow-sm cursor-pointer',
            'text-[9px] font-bold px-1.5 py-0.5 rounded border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-disq-merah',
            scores.merah.can_undo_disq,
            'shrink-0 text-[10px] font-bold px-2 py-1.5 rounded-lg border transition-all flex items-center gap-1 bg-red-200 hover:bg-red-300 text-red-950 border-red-400 shadow-sm cursor-pointer',
            'shrink-0 text-[10px] font-bold px-2 py-1.5 rounded-lg border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        // --- 5. CHECKMARKS & HIGHLIGHTS TOMBOL BIRU ---
        toggleCheckmark('check-binaan-1-biru', scores.biru.has_binaan_1);
        toggleCheckmark('check-binaan-2-biru', scores.biru.has_binaan_2);
        toggleCheckmark('check-teguran-1-biru', scores.biru.has_teguran_1);
        toggleCheckmark('check-teguran-2-biru', scores.biru.has_teguran_2);
        toggleCheckmark('check-peringatan-1-biru', scores.biru.has_peringatan_1);
        toggleCheckmark('check-peringatan-2-biru', scores.biru.has_peringatan_2);

        const btnBinaan1B = document.getElementById('btn-binaan-1-biru');
        if (btnBinaan1B) btnBinaan1B.className = `py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-center gap-1 ${scores.biru.has_binaan_1 ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm'}`;
        const btnBinaan2B = document.getElementById('btn-binaan-2-biru');
        if (btnBinaan2B) btnBinaan2B.className = `py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-center gap-1 ${scores.biru.has_binaan_2 ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm'}`;

        const btnTeguran1B = document.getElementById('btn-teguran-1-biru');
        if (btnTeguran1B) btnTeguran1B.className = `py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between ${scores.biru.has_teguran_1 ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm'}`;
        const btnTeguran2B = document.getElementById('btn-teguran-2-biru');
        if (btnTeguran2B) btnTeguran2B.className = `py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between ${scores.biru.has_teguran_2 ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm'}`;

        const btnPer1B = document.getElementById('btn-peringatan-1-biru');
        if (btnPer1B) btnPer1B.className = `py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between ${scores.biru.has_peringatan_1 ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm'}`;
        const btnPer2B = document.getElementById('btn-peringatan-2-biru');
        if (btnPer2B) btnPer2B.className = `py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between ${scores.biru.has_peringatan_2 ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm'}`;

        const btnDisqB = document.getElementById('btn-disq-biru');
        const lblDisqB = document.getElementById('label-disq-biru');
        if (btnDisqB) {
            btnDisqB.disabled = scores.biru.is_disq;
            btnDisqB.className = `flex-1 py-1.5 px-2 rounded-lg font-black text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 shadow-sm ${scores.biru.is_disq ? 'bg-red-900 text-white opacity-80 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 active:scale-98 text-white'}`;
        }
        if (lblDisqB) lblDisqB.innerText = scores.biru.is_disq ? 'TELAH DIDISKUALIFIKASI' : 'DISKUALIFIKASI (DISQ)';

        // --- 6. CHECKMARKS & HIGHLIGHTS TOMBOL MERAH ---
        toggleCheckmark('check-binaan-1-merah', scores.merah.has_binaan_1);
        toggleCheckmark('check-binaan-2-merah', scores.merah.has_binaan_2);
        toggleCheckmark('check-teguran-1-merah', scores.merah.has_teguran_1);
        toggleCheckmark('check-teguran-2-merah', scores.merah.has_teguran_2);
        toggleCheckmark('check-peringatan-1-merah', scores.merah.has_peringatan_1);
        toggleCheckmark('check-peringatan-2-merah', scores.merah.has_peringatan_2);

        const btnBinaan1M = document.getElementById('btn-binaan-1-merah');
        if (btnBinaan1M) btnBinaan1M.className = `py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-center gap-1 ${scores.merah.has_binaan_1 ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm'}`;
        const btnBinaan2M = document.getElementById('btn-binaan-2-merah');
        if (btnBinaan2M) btnBinaan2M.className = `py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-center gap-1 ${scores.merah.has_binaan_2 ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm'}`;

        const btnTeguran1M = document.getElementById('btn-teguran-1-merah');
        if (btnTeguran1M) btnTeguran1M.className = `py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between ${scores.merah.has_teguran_1 ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm'}`;
        const btnTeguran2M = document.getElementById('btn-teguran-2-merah');
        if (btnTeguran2M) btnTeguran2M.className = `py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between ${scores.merah.has_teguran_2 ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm'}`;

        const btnPer1M = document.getElementById('btn-peringatan-1-merah');
        if (btnPer1M) btnPer1M.className = `py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between ${scores.merah.has_peringatan_1 ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm'}`;
        const btnPer2M = document.getElementById('btn-peringatan-2-merah');
        if (btnPer2M) btnPer2M.className = `py-1 px-2 rounded-lg border text-xs font-bold transition-all flex items-center justify-between ${scores.merah.has_peringatan_2 ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm'}`;

        const btnDisqM = document.getElementById('btn-disq-merah');
        const lblDisqM = document.getElementById('label-disq-merah');
        if (btnDisqM) {
            btnDisqM.disabled = scores.merah.is_disq;
            btnDisqM.className = `flex-1 py-1.5 px-2 rounded-lg font-black text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 shadow-sm ${scores.merah.is_disq ? 'bg-red-900 text-white opacity-80 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 active:scale-98 text-white'}`;
        }
        if (lblDisqM) lblDisqM.innerText = scores.merah.is_disq ? 'TELAH DIDISKUALIFIKASI' : 'DISKUALIFIKASI (DISQ)';

        // --- 7. MODAL SELESAI SKOR PREVIEW ---
        document.querySelectorAll('.score-preview-biru').forEach(el => el.innerText = `${scores.biru.is_disq ? 'DISQ' : scores.biru.total + ' Poin'}`);
        document.querySelectorAll('.score-preview-merah').forEach(el => el.innerText = `${scores.merah.is_disq ? 'DISQ' : scores.merah.total + ' Poin'}`);

        // --- 8. STATUS TAGS PELANGGARAN (BINAAN, TEGURAN, PERINGATAN) ---
        const tagsBiruEl = document.getElementById('pelanggaran-tags-biru');
        if (tagsBiruEl && scores.biru) {
            tagsBiruEl.innerHTML = renderPelanggaranTagsHtml(scores.biru);
        }

        const tagsMerahEl = document.getElementById('pelanggaran-tags-merah');
        if (tagsMerahEl && scores.merah) {
            tagsMerahEl.innerHTML = renderPelanggaranTagsHtml(scores.merah);
        }
    }

        // Helper Expand / Collapse Rincian Juri
    window.toggleDetail = function(id) {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('hidden');
    };

    // Helper render baris HTML timeline (konsisten untuk initial, polling, dan realtime)
    function renderTimelineRowHtml(ev, isBiru) {
        const idDetail = `detail-${ev.id || Math.random().toString(36).substr(2, 9)}`;
        let badgeHtml = '';
        let keteranganHtml = ev.keterangan || '';
        const isPoin = (ev.type === 'poin');
        const isJuri = (ev.type === 'juri_input');

        if (isPoin) {
            const hasDetails = ev.jurist_details && ev.jurist_details.length > 0;
            badgeHtml = `
                <div class="flex items-center gap-1 flex-wrap">
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded ${isBiru ? 'bg-blue-600 text-white' : 'bg-red-600 text-white'} font-black text-[10px] shadow-sm">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        ${ev.badge}
                    </span>
                    ${hasDetails ? `<button type="button" onclick="toggleDetail('${idDetail}')" class="text-[10px] ${isBiru ? 'text-blue-600 hover:text-blue-800' : 'text-red-600 hover:text-red-800'} font-bold underline cursor-pointer">▼ Rincian Juri</button>` : ''}
                </div>
            `;
            if (hasDetails) {
                const subRows = ev.jurist_details.map(jd => `
                    <div class="flex items-center justify-between text-slate-800 font-medium">
                        <span>• Juri ${jd.no_juri} (${jd.jenis || 'Serangan'}): Sah</span>
                        <span class="font-mono text-gray-500 font-bold">${jd.time}</span>
                    </div>
                `).join('');
                keteranganHtml += `
                    <div id="${idDetail}" class="hidden mt-1 p-1.5 rounded-lg ${isBiru ? 'bg-blue-50/80 border-blue-200' : 'bg-red-50/80 border-red-200'} border text-[10px] space-y-0.5 shadow-xs">
                        <div class="font-bold text-[9px] ${isBiru ? 'text-blue-900' : 'text-red-900'} uppercase tracking-wider mb-0.5">Waktu Input Juri Konsensus:</div>
                        ${subRows}
                    </div>
                `;
            }
        } else if (isJuri) {
            const isBerhasil = (ev.status_input === 'Berhasil');
            const isBelum = (ev.status_input === 'Belum Proses');
            const badgeClass = isBerhasil ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : (isBelum ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-slate-100 text-slate-600 border-slate-200');
            badgeHtml = `
                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold border ${badgeClass}">
                    ${isBerhasil ? '<svg class="w-2.5 h-2.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>' : ''}
                    ${ev.badge}
                </span>
            `;
        } else if (ev.type === 'jatuhan') {
            badgeHtml = `<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-black ${isBiru ? 'bg-blue-700 text-white' : 'bg-red-700 text-white'}">${ev.badge}</span>`;
        } else {
            badgeHtml = `<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">${ev.badge}</span>`;
        }

        return `
            <tr class="${isBiru ? 'hover:bg-blue-50/40' : 'hover:bg-red-50/40'}">
                <td class="px-2 py-1 text-center text-gray-400 font-mono">${ev.time}</td>
                <td class="px-2 py-1 text-center font-bold text-gray-700">Babak ${ev.babak}</td>
                <td class="px-2 py-1">${badgeHtml}</td>
                <td class="px-2 py-1 text-gray-700 font-medium">${keteranganHtml}</td>
            </tr>
        `;
    }

    // Refresh Timeline & Scores secara berkala (polling tiap 5 detik)
    async function refreshStatus() {
        try {
            const res = await fetch(`/admin/dewan/tanding/${partaiId}/status-data`);
            const data = await res.json();
            if (data.success) {
                applyScores(data.scores);

                // Update Status Juri & Tombol Kontrol
                if (data.status_juri) {
                    const anyActive = data.status_juri.some(j => j.is_active);
                    updateAllJuriStatusUI(anyActive);
                }

                // Render Timeline Sudut Biru & Sudut Merah
                const tbodyBiru = document.getElementById('timeline-body-biru');
                const tbodyMerah = document.getElementById('timeline-body-merah');

                if (data.timeline) {
                    const biruEvents = data.timeline.filter(ev => ev.sudut === 'Biru');
                    const merahEvents = data.timeline.filter(ev => ev.sudut === 'Merah');

                    if (tbodyBiru) {
                        if (biruEvents.length > 0) {
                            tbodyBiru.innerHTML = biruEvents.map(ev => renderTimelineRowHtml(ev, true)).join('');
                        } else {
                            tbodyBiru.innerHTML = `<tr><td colspan="4" class="px-3 py-4 text-center text-gray-400 italic text-[11px]">Belum ada riwayat aktivitas Sudut Biru.</td></tr>`;
                        }
                    }

                    if (tbodyMerah) {
                        if (merahEvents.length > 0) {
                            tbodyMerah.innerHTML = merahEvents.map(ev => renderTimelineRowHtml(ev, false)).join('');
                        } else {
                            tbodyMerah.innerHTML = `<tr><td colspan="4" class="px-3 py-4 text-center text-gray-400 italic text-[11px]">Belum ada riwayat aktivitas Sudut Merah.</td></tr>`;
                        }
                    }
                }
            }
        } catch (e) {
            console.error('Polling error:', e);
        }
    }

    setInterval(refreshStatus, 5000);

    // --- REALTIME WEBSOCKET (REVERB / ECHO) LISTENERS ---
    function insertRealtimeRow(ev, isBiru) {
        const tbody = document.getElementById(isBiru ? 'timeline-body-biru' : 'timeline-body-merah');
        if (!tbody) return;

        // Bersihkan pesan kosong jika ada
        const emptyRow = tbody.querySelector('td[colspan="4"]');
        if (emptyRow) tbody.innerHTML = '';

        const tempContainer = document.createElement('tbody');
        tempContainer.innerHTML = renderTimelineRowHtml(ev, isBiru);
        const newTr = tempContainer.firstElementChild;
        if (newTr) {
            newTr.classList.add('bg-amber-100/90', 'transition-all', 'duration-700');
            tbody.insertBefore(newTr, tbody.firstChild);
            const scrollBox = tbody.closest('.overflow-y-auto');
            if (scrollBox) scrollBox.scrollTop = 0;
            setTimeout(() => {
                newTr.classList.remove('bg-amber-100/90');
            }, 2000);
        }
    }

    function prependRealtimeTimelineLog(e) {
        const isBiru = (e.sudut.toLowerCase() === 'biru');

        if (e.poinSah) {
            // 1. Sisipkan baris input individual Juri yang baru saja klik (Berhasil)
            const juriRow = {
                id: `rt-juri-${Date.now()}-${e.noJuri}`,
                type: 'juri_input',
                status_input: 'Berhasil',
                badge: `Juri ${e.noJuri}: ${e.jenisSerangan}`,
                sudut: e.sudut,
                babak: e.babak || babakAktif,
                keterangan: e.keterangan,
                time: e.time || new Date().toTimeString().split(' ')[0]
            };
            insertRealtimeRow(juriRow, isBiru);

            // 2. Sisipkan baris ringkasan Poin Sah (bisa diexpand)
            const poinVal = (e.jenisSerangan === 'Tendangan') ? 2 : 1;
            const poinRow = {
                id: `rt-ps-${Date.now()}`,
                type: 'poin',
                badge: `${e.jenisSerangan} (+${poinVal})`,
                sudut: e.sudut,
                babak: e.babak || babakAktif,
                keterangan: e.keterangan,
                time: e.time || new Date().toTimeString().split(' ')[0],
                jurist_details: e.juristDetails || []
            };
            insertRealtimeRow(poinRow, isBiru);
        } else {
            // Input Juri biasa (Menunggu Pasangan)
            const juriRow = {
                id: `rt-juri-${Date.now()}-${e.noJuri}`,
                type: 'juri_input',
                status_input: 'Belum Proses',
                badge: `Juri ${e.noJuri}: ${e.jenisSerangan}`,
                sudut: e.sudut,
                babak: e.babak || babakAktif,
                keterangan: e.keterangan || `Input Juri ${e.noJuri} (Menunggu Pasangan)`,
                time: e.time || new Date().toTimeString().split(' ')[0]
            };
            insertRealtimeRow(juriRow, isBiru);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (window.Echo) {
            const channel = window.Echo.channel(`partai.tanding.${partaiId}`);
            console.log(`Dewan Tanding terhubung ke Reverb: partai.tanding.${partaiId}`);

            // 1. Realtime Log Input Juri & Consensus
            channel.listen('JuriTandingInputReceived', function(e) {
                console.log("Realtime Juri Input Tanding:", e);
                prependRealtimeTimelineLog(e);
                if (e.scores) {
                    applyScores(e.scores);
                }
            });

            // 2. Realtime Score Updated
            channel.listen('TandingScoreUpdated', function(e) {
                console.log("Realtime Score Updated:", e);
                if (e.scores) {
                    applyScores(e.scores);
                }
                if (e.babakAktif) {
                    babakAktif = parseInt(e.babakAktif);
                    document.querySelectorAll('[id^="btn-babak-"]').forEach((btn, idx) => {
                        if ((idx + 1) === babakAktif) {
                            btn.className = "w-full py-1.5 px-2 rounded-lg text-xs font-black transition-all text-center cursor-pointer active:scale-98 bg-primary-600 text-white shadow-sm";
                        } else {
                            btn.className = "w-full py-1.5 px-2 rounded-lg text-xs font-black transition-all text-center cursor-pointer active:scale-98 bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200";
                        }
                    });
                    const badgeBabak = document.getElementById('badge-babak-aktif');
                    if (badgeBabak) badgeBabak.innerText = `Babak ${babakAktif}`;
                }
            });

            // 3. Realtime Status Kontrol Juri Toggle
            channel.listen('ToggleKontrolJuriTanding', function(e) {
                console.log("Realtime Toggle Kontrol Juri:", e);
                updateAllJuriStatusUI(e.isActive);
            });

            // 4. Realtime Verifikasi VAR Dewan
            channel.listen('VerifikasiTandingUpdated', function(e) {
                console.log("Realtime Verifikasi Tanding on Dewan:", e);
                if (e.action === 'start') {
                    currentVerifikasiId = e.verifikasiId;
                    const modal = document.getElementById('modal-verifikasi-dewan');
                    if (modal) modal.classList.remove('hidden');
                    resetModalVerifikasiDewan();
                } else if (e.action === 'vote') {
                    currentVerifikasiId = e.verifikasiId;
                    const modal = document.getElementById('modal-verifikasi-dewan');
                    if (modal) modal.classList.remove('hidden');
                    updateDewanVerifikasiUI(e.jawaban, e.hasil);
                } else if (e.action === 'close') {
                    const modal = document.getElementById('modal-verifikasi-dewan');
                    if (modal) modal.classList.add('hidden');
                    currentVerifikasiId = null;
                }
            });

            // 5. Realtime Keputusan Pemenang Dewan
            channel.listen('KeputusanPemenangTandingUpdated', function(e) {
                console.log("Realtime Keputusan Pemenang on Dewan:", e);
                if (e.action === 'show') {
                    updateDewanPemenangUI(e);
                    const modal = document.getElementById('modal-pemenang-dewan');
                    if (modal) modal.classList.remove('hidden');
                } else if (e.action === 'close') {
                    const modal = document.getElementById('modal-pemenang-dewan');
                    if (modal) modal.classList.add('hidden');
                }
            });
        }
    });

    setInterval(refreshStatus, 5000);

    // Modal Selesai Otomatis Berdasarkan 5 Kriteria
    async function openSelesaiModal() {
        const modal = document.getElementById('modal-selesai');
        if (modal) modal.classList.remove('hidden');

        try {
            const res = await fetch(`/admin/dewan/tanding/${partaiId}/evaluasi-pemenang`);
            const json = await res.json();
            if (json.success && json.data) {
                updateModalSelesaiUI(json.data);
            }
        } catch (e) {
            console.error('Gagal mengambil data evaluasi pemenang:', e);
        }
    }

    function updateModalSelesaiUI(data) {
        const card = document.getElementById('card-pemenang-otomatis');
        const badge = document.getElementById('badge-pemenang-otomatis');
        const tahap = document.getElementById('tahap-pemenang-otomatis');
        const nama = document.getElementById('nama-pemenang-otomatis');
        const kontingen = document.getElementById('kontingen-pemenang-otomatis');
        const alasan = document.getElementById('alasan-pemenang-otomatis');
        const warning = document.getElementById('warning-seri-selesai');
        const btnSubmit = document.getElementById('btn-submit-selesai');
        const inputId = document.getElementById('selesai-id-pemenang');
        const inputAlasan = document.getElementById('selesai-alasan-pemenang');

        if (inputId) inputId.value = data.id_pemenang || '';
        if (inputAlasan) inputAlasan.value = data.alasan || '';
        if (tahap) tahap.innerText = `Kriteria Tahap ${data.kriteriaTahap || 1}`;
        if (alasan) alasan.innerText = data.alasan || '-';

        const sudut = (data.sudut || data.pemenang || '').toLowerCase();
        const isMerah = (sudut === 'merah');
        const isBiru = (sudut === 'biru');
        const isSeri = (!isMerah && !isBiru);

        if (isMerah) {
            if (card) card.className = "p-4 rounded-xl border-2 transition-all border-red-500 bg-red-50/70";
            if (badge) {
                badge.className = "text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full shadow-xs bg-red-600 text-white";
                badge.innerText = "PEMENANG: SUDUT MERAH";
            }
            if (nama) {
                nama.className = "text-base font-black text-red-900";
                nama.innerText = data.pesertaPemenang ? data.pesertaPemenang.nama : (data.namaMerah || 'SUDUT MERAH');
            }
            if (kontingen) {
                kontingen.className = "text-xs font-semibold text-red-700";
                kontingen.innerText = data.pesertaPemenang ? data.pesertaPemenang.kontingen : (data.kontingenMerah || '-');
            }
            if (warning) warning.classList.add('hidden');
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.className = "px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer";
            }
        } else if (isBiru) {
            if (card) card.className = "p-4 rounded-xl border-2 transition-all border-blue-500 bg-blue-50/70";
            if (badge) {
                badge.className = "text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full shadow-xs bg-blue-600 text-white";
                badge.innerText = "PEMENANG: SUDUT BIRU";
            }
            if (nama) {
                nama.className = "text-base font-black text-blue-900";
                nama.innerText = data.pesertaPemenang ? data.pesertaPemenang.nama : (data.namaBiru || 'SUDUT BIRU');
            }
            if (kontingen) {
                kontingen.className = "text-xs font-semibold text-blue-700";
                kontingen.innerText = data.pesertaPemenang ? data.pesertaPemenang.kontingen : (data.kontingenBiru || '-');
            }
            if (warning) warning.classList.add('hidden');
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.className = "px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer";
            }
        } else {
            if (card) card.className = "p-4 rounded-xl border-2 transition-all border-amber-400 bg-amber-50/70";
            if (badge) {
                badge.className = "text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full shadow-xs bg-amber-600 text-white";
                badge.innerText = "HASIL SERI / IMBANG";
            }
            if (nama) {
                nama.className = "text-base font-black text-amber-900";
                nama.innerText = "HASIL MASIH IMBANG / SERI";
            }
            if (kontingen) {
                kontingen.className = "text-xs font-semibold text-amber-700";
                kontingen.innerText = "Seluruh 5 Kriteria Poin Bernilai Sama";
            }
            if (warning) warning.classList.remove('hidden');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.className = "px-4 py-2 bg-gray-300 text-gray-500 cursor-not-allowed opacity-60 rounded-xl text-xs font-bold transition-all";
            }
        }
    }

    function closeSelesaiModal() {
        document.getElementById('modal-selesai').classList.add('hidden');
    }
</script>
@endpush
