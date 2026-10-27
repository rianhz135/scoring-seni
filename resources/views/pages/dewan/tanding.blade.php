@extends('layouts.dewan')

@section('content')
    {{-- 1. TOP BAR NAVIGASI & INFORMASI PARTAI --}}
    <div class="bg-white rounded-2xl p-4 mb-4 border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-3">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <a href="{{ route('admin.manajemen.tanding.index') }}"
                class="px-3 py-2 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke Manajemen
            </a>

            <div class="h-6 w-[1px] bg-gray-200 hidden sm:block"></div>

            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-lg bg-gray-900 text-white font-black text-xs tracking-wider">
                        PARTAI #{{ $partai->no_partai }}
                    </span>
                    <span class="px-2 py-0.5 rounded-md bg-primary-50 text-primary-700 font-bold text-xs uppercase border border-primary-200">
                        {{ $partai->kelas }}
                    </span>
                    <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 font-bold text-xs uppercase border border-purple-200">
                        {{ $partai->fase_babak }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Kontrol Babak & Status --}}
        <div class="flex items-center gap-2.5 w-full md:w-auto justify-end">
            <div class="flex items-center bg-gray-100 p-1 rounded-xl border border-gray-200">
                <span class="text-[10px] font-bold text-gray-400 uppercase px-2">Babak:</span>
                @foreach ([1, 2, 3] as $b)
                    <button type="button" onclick="changeBabak({{ $b }})"
                        id="btn-babak-{{ $b }}"
                        class="px-3 py-1 rounded-lg text-xs font-black transition-all {{ ($partai->babak_aktif ?: 1) == $b ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-200' }}">
                        {{ $b }}
                    </button>
                @endforeach
            </div>

            <button type="button" onclick="openSelesaiModal()"
                class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4 text-emerald-200" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Selesaikan Partai
            </button>
        </div>
    </div>

    {{-- 2. PAPAN SKOR UTAMA (SUDUT BIRU - TIMER & BABAK - SUDUT MERAH) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-4">
        {{-- SUDUT BIRU --}}
        <div class="lg:col-span-4 bg-white rounded-2xl border-2 border-blue-500 shadow-sm p-5 relative overflow-hidden flex flex-col justify-between">
            <div class="absolute top-0 right-0 w-28 h-28 bg-blue-50 rounded-full -mr-10 -mt-10 opacity-70"></div>

            <div>
                <div class="flex items-center justify-between mb-3 relative">
                    <span class="px-3 py-1 rounded-lg bg-blue-600 text-white text-xs font-black uppercase tracking-wider shadow-sm">
                        SUDUT BIRU
                    </span>
                    <span id="badge-disq-biru" class="{{ $scores['biru']['is_disq'] ? '' : 'hidden' }} px-2 py-0.5 rounded-md bg-red-600 text-white text-[10px] font-black uppercase animate-pulse">
                        DISKUALIFIKASI
                    </span>
                </div>

                <h2 class="text-xl font-black text-gray-900 leading-tight">
                    {{ $partai->pesertaBiru ? $partai->pesertaBiru->nama : 'ATLET BELUM DITENTUKAN' }}
                </h2>
                <p class="text-xs font-bold text-blue-600 uppercase mt-0.5">
                    {{ $partai->pesertaBiru ? $partai->pesertaBiru->kontingen : '-' }}
                </p>
            </div>

            {{-- Skor & Detail Angka --}}
            <div class="my-4 text-center">
                <div id="display-score-biru" class="text-7xl font-black {{ $scores['biru']['is_disq'] ? 'text-red-600' : 'text-blue-600' }} font-mono tracking-tighter">
                    @if ($scores['biru']['is_disq'])
                        <span class="text-5xl md:text-6xl text-red-600 font-black tracking-normal">DISQ</span>
                    @else
                        {{ $scores['biru']['total'] }}
                    @endif
                </div>
                <div id="disq-banner-biru" class="{{ $scores['biru']['is_disq'] ? '' : 'hidden' }} mt-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-red-600 text-white text-[10px] font-black uppercase tracking-wider animate-pulse">
                        DISKUALIFIKASI
                    </span>
                </div>
                <p id="label-score-biru" class="text-[11px] font-extrabold text-gray-400 uppercase tracking-widest mt-1 {{ $scores['biru']['is_disq'] ? 'hidden' : '' }}">Total Poin Bersih</p>
            </div>

            {{-- Rincian Poin & Pelanggaran --}}
            <div class="pt-3 border-t border-gray-100 space-y-2">
                <div class="grid grid-cols-3 gap-1.5 text-center">
                    <div class="p-2 bg-blue-50/70 rounded-xl border border-blue-100">
                        <p class="text-[9px] font-bold text-blue-400 uppercase">Poin Masuk</p>
                        <p id="stat-poin-biru" class="text-sm font-black text-blue-700 font-mono">{{ $scores['biru']['poin_masuk'] }}</p>
                    </div>
                    <div class="p-2 bg-blue-50/70 rounded-xl border border-blue-100">
                        <p class="text-[9px] font-bold text-blue-400 uppercase">Jatuhan (+3)</p>
                        <p id="stat-jatuhan-biru" class="text-sm font-black text-blue-700 font-mono">{{ $scores['biru']['jatuhan_count'] }} ({{ $scores['biru']['poin_jatuhan'] }})</p>
                    </div>
                    <div class="p-2 bg-red-50/70 rounded-xl border border-red-100">
                        <p class="text-[9px] font-bold text-red-400 uppercase">Hukuman</p>
                        <p id="stat-minus-biru" class="text-sm font-black text-red-600 font-mono">-{{ $scores['biru']['poin_minus'] }}</p>
                    </div>
                </div>

                {{-- Status Binaan, Teguran, Peringatan --}}
                <div id="pelanggaran-tags-biru" class="flex flex-wrap gap-1 items-center min-h-[22px]">
                    @if ($scores['biru']['binaan_count'] > 0)
                        <span class="px-2 py-0.5 rounded bg-yellow-50 text-yellow-700 border border-yellow-200 text-[10px] font-bold">
                            Binaan: {{ $scores['biru']['binaan_count'] }}x
                        </span>
                    @endif
                    @foreach ($scores['biru']['teguran_list'] as $teg)
                        <span class="px-2 py-0.5 rounded bg-orange-50 text-orange-700 border border-orange-200 text-[10px] font-bold">
                            {{ $teg }}
                        </span>
                    @endforeach
                    @foreach ($scores['biru']['peringatan_list'] as $per)
                        <span class="px-2 py-0.5 rounded bg-red-100 text-red-800 border border-red-200 text-[10px] font-black">
                            {{ $per }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- CENTER: TIMER & KONTROL BABAK --}}
        <div class="lg:col-span-4 bg-gray-900 rounded-2xl p-6 text-white shadow-xl flex flex-col justify-between items-center text-center relative overflow-hidden">
            <div class="w-full flex justify-between items-center border-b border-gray-800 pb-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">DIGITAL MATCH TIMER</span>
                <span id="badge-babak-display" class="px-2.5 py-0.5 rounded bg-primary-600 text-white text-[11px] font-black uppercase tracking-wider">
                    BABAK {{ $partai->babak_aktif ?: 1 }}
                </span>
            </div>

            {{-- Timer Display --}}
            <div class="my-4">
                <div id="timer-display" class="text-7xl font-mono font-black tracking-wider text-white tabular-nums drop-shadow-md">
                    @php
                        $detik = $partai->waktu_timer ?? 120;
                        $m = floor($detik / 60);
                        $s = $detik % 60;
                        echo sprintf('%02d:%02d', $m, $s);
                    @endphp
                </div>
                <div class="flex items-center justify-center gap-2 mt-2">
                    <span id="timer-status-dot" class="w-2 h-2 rounded-full {{ $partai->status_timer === 'running' ? 'bg-emerald-400 animate-ping' : 'bg-gray-500' }}"></span>
                    <span id="timer-status-text" class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                        {{ strtoupper($partai->status_timer ?? 'STOP') }}
                    </span>
                </div>
            </div>

            {{-- Tombol Kontrol Timer --}}
            <div class="w-full space-y-2">
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="startTimer()" id="btn-timer-start"
                        class="bg-emerald-600 hover:bg-emerald-500 text-white py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition-all active:scale-95 shadow-md">
                        Start
                    </button>
                    <button type="button" onclick="pauseTimer()" id="btn-timer-pause"
                        class="bg-amber-600 hover:bg-amber-500 text-white py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition-all active:scale-95 shadow-md">
                        Pause
                    </button>
                    <button type="button" onclick="resetTimer()" id="btn-timer-reset"
                        class="bg-red-900/60 hover:bg-red-800 text-red-200 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition-all active:scale-95">
                        Reset
                    </button>
                </div>
                
                <div class="flex items-center justify-center gap-2 pt-1 text-[11px]">
                    <button type="button" onclick="adjustTimer(-10)" class="text-gray-400 hover:text-white px-2 py-0.5 rounded bg-gray-800">
                        -10s
                    </button>
                    <button type="button" onclick="adjustTimer(10)" class="text-gray-400 hover:text-white px-2 py-0.5 rounded bg-gray-800">
                        +10s
                    </button>
                    <button type="button" onclick="setTimerDirect(120)" class="text-gray-400 hover:text-white px-2 py-0.5 rounded bg-gray-800">
                        2 Menit
                    </button>
                </div>
            </div>
        </div>

        {{-- SUDUT MERAH --}}
        <div class="lg:col-span-4 bg-white rounded-2xl border-2 border-red-500 shadow-sm p-5 relative overflow-hidden flex flex-col justify-between">
            <div class="absolute top-0 right-0 w-28 h-28 bg-red-50 rounded-full -mr-10 -mt-10 opacity-70"></div>

            <div>
                <div class="flex items-center justify-between mb-3 relative">
                    <span class="px-3 py-1 rounded-lg bg-red-600 text-white text-xs font-black uppercase tracking-wider shadow-sm">
                        SUDUT MERAH
                    </span>
                    <span id="badge-disq-merah" class="{{ $scores['merah']['is_disq'] ? '' : 'hidden' }} px-2 py-0.5 rounded-md bg-red-600 text-white text-[10px] font-black uppercase animate-pulse">
                        DISKUALIFIKASI
                    </span>
                </div>

                <h2 class="text-xl font-black text-gray-900 leading-tight">
                    {{ $partai->pesertaMerah ? $partai->pesertaMerah->nama : 'ATLET BELUM DITENTUKAN' }}
                </h2>
                <p class="text-xs font-bold text-red-600 uppercase mt-0.5">
                    {{ $partai->pesertaMerah ? $partai->pesertaMerah->kontingen : '-' }}
                </p>
            </div>

            {{-- Skor & Detail Angka --}}
            <div class="my-4 text-center">
                <div id="display-score-merah" class="text-7xl font-black text-red-600 font-mono tracking-tighter">
                    @if ($scores['merah']['is_disq'])
                        <span class="text-5xl md:text-6xl text-red-600 font-black tracking-normal">DISQ</span>
                    @else
                        {{ $scores['merah']['total'] }}
                    @endif
                </div>
                <div id="disq-banner-merah" class="{{ $scores['merah']['is_disq'] ? '' : 'hidden' }} mt-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-red-600 text-white text-[10px] font-black uppercase tracking-wider animate-pulse">
                        DISKUALIFIKASI
                    </span>
                </div>
                <p id="label-score-merah" class="text-[11px] font-extrabold text-gray-400 uppercase tracking-widest mt-1 {{ $scores['merah']['is_disq'] ? 'hidden' : '' }}">Total Poin Bersih</p>
            </div>

            {{-- Rincian Poin & Pelanggaran --}}
            <div class="pt-3 border-t border-gray-100 space-y-2">
                <div class="grid grid-cols-3 gap-1.5 text-center">
                    <div class="p-2 bg-red-50/70 rounded-xl border border-red-100">
                        <p class="text-[9px] font-bold text-red-400 uppercase">Poin Masuk</p>
                        <p id="stat-poin-merah" class="text-sm font-black text-red-700 font-mono">{{ $scores['merah']['poin_masuk'] }}</p>
                    </div>
                    <div class="p-2 bg-red-50/70 rounded-xl border border-red-100">
                        <p class="text-[9px] font-bold text-red-400 uppercase">Jatuhan (+3)</p>
                        <p id="stat-jatuhan-merah" class="text-sm font-black text-red-700 font-mono">{{ $scores['merah']['jatuhan_count'] }} ({{ $scores['merah']['poin_jatuhan'] }})</p>
                    </div>
                    <div class="p-2 bg-red-50/70 rounded-xl border border-red-100">
                        <p class="text-[9px] font-bold text-red-400 uppercase">Hukuman</p>
                        <p id="stat-minus-merah" class="text-sm font-black text-red-600 font-mono">-{{ $scores['merah']['poin_minus'] }}</p>
                    </div>
                </div>

                {{-- Status Binaan, Teguran, Peringatan --}}
                <div id="pelanggaran-tags-merah" class="flex flex-wrap gap-1 items-center min-h-[22px]">
                    @if ($scores['merah']['binaan_count'] > 0)
                        <span class="px-2 py-0.5 rounded bg-yellow-50 text-yellow-700 border border-yellow-200 text-[10px] font-bold">
                            Binaan: {{ $scores['merah']['binaan_count'] }}x
                        </span>
                    @endif
                    @foreach ($scores['merah']['teguran_list'] as $teg)
                        <span class="px-2 py-0.5 rounded bg-orange-50 text-orange-700 border border-orange-200 text-[10px] font-bold">
                            {{ $teg }}
                        </span>
                    @endforeach
                    @foreach ($scores['merah']['peringatan_list'] as $per)
                        <span class="px-2 py-0.5 rounded bg-red-100 text-red-800 border border-red-200 text-[10px] font-black">
                            {{ $per }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- 3. KONTROL STATUS JURI (1, 2, 3): SET ACTIVE / PAUSE --}}
    <div class="bg-white rounded-2xl p-4 mb-4 border border-gray-200 shadow-sm">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center font-bold text-xs">
                    <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 leading-tight">Kontrol Status Juri Tanding</h3>
                    <p class="text-[11px] text-gray-400">Atur kesiapan dan kontrol input juri (Active / Pause) secara real-time.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="toggleAllJuri('active')"
                    class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-all flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Aktifkan Semua
                </button>
                <button type="button" onclick="toggleAllJuri('pause')"
                    class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-all flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Jeda (Pause) Semua
                </button>
            </div>
        </div>

        {{-- 3 Kartu Juri --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @for ($i = 1; $i <= 3; $i++)
                @php
                    $juriData = $statusJuri->firstWhere('no_juri', $i);
                    $isActive = $juriData ? $juriData->is_active : true;
                @endphp
                <div id="card-juri-{{ $i }}"
                    class="p-3.5 rounded-xl border transition-all flex items-center justify-between {{ $isActive ? 'bg-emerald-50/50 border-emerald-200' : 'bg-gray-50 border-gray-200 opacity-80' }}">
                    <div class="flex items-center gap-3">
                        <div id="icon-juri-{{ $i }}"
                            class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm {{ $isActive ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-300 text-gray-700' }}">
                            J{{ $i }}
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-900 leading-tight">Juri {{ $i }}</p>
                            <span id="status-badge-juri-{{ $i }}"
                                class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase mt-0.5 {{ $isActive ? 'text-emerald-700' : 'text-gray-500' }}">
                                <span id="status-dot-juri-{{ $i }}" class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                <span id="status-text-juri-{{ $i }}">{{ $isActive ? 'ACTIVE' : 'PAUSED' }}</span>
                            </span>
                        </div>
                    </div>

                    <button type="button" onclick="toggleJuri({{ $i }})"
                        id="btn-toggle-juri-{{ $i }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all shadow-sm {{ $isActive ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                        {{ $isActive ? 'Jeda Juri' : 'Aktifkan' }}
                    </button>
                </div>
            @endfor
        </div>
    </div>

    {{-- 4. KONTROL AKSI DEWAN: JATUHAN, BINAAN, TEGURAN, PERINGATAN, DISQUALIFIKASI --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        {{-- KONTROL SUDUT BIRU --}}
        <div class="bg-white rounded-2xl p-4 border border-blue-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between border-b border-blue-100 pb-2">
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-full bg-blue-600 shadow-sm"></span>
                    <h3 class="text-sm font-black text-blue-900 uppercase tracking-wide">AKSI DEWAN: SUDUT BIRU</h3>
                </div>
                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-200">
                    Babak Aktif: <strong id="label-babak-biru">Babak {{ $partai->babak_aktif ?: 1 }}</strong>
                </span>
            </div>

            {{-- 1. Jatuhan (+3 Poin) --}}
            <div class="p-3 rounded-xl bg-blue-50/70 border border-blue-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-black text-blue-950 uppercase tracking-wide">Jatuhan (+3 Poin)</p>
                    <p class="text-[10px] text-blue-600/80">Poin mutlak +3 (Hanya babak aktif)</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="addJatuhan('Biru')"
                        class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase transition-all shadow-sm active:scale-95">
                        + JATUHAN
                    </button>
                    <button type="button" id="btn-undo-jatuhan-biru" onclick="undoJatuhan('Biru')"
                        {{ $scores['biru']['can_undo_jatuhan'] ? '' : 'disabled' }}
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all border {{ $scores['biru']['can_undo_jatuhan'] ? 'bg-white hover:bg-gray-100 text-gray-700 border-gray-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}"
                        title="Batalkan Jatuhan Babak Ini">
                        Undo
                    </button>
                </div>
            </div>

            {{-- 2. Binaan (0 Poin - Babak Ini) --}}
            <div class="p-3 rounded-xl bg-amber-50/40 border border-amber-200/80">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <p class="text-xs font-black text-amber-950 uppercase tracking-wide">Binaan (0 Poin)</p>
                        <span class="text-[10px] text-amber-700/80 font-bold">(Babak Ini)</span>
                    </div>
                    <button type="button" id="btn-undo-binaan-biru" onclick="undoPelanggaran('Biru', 'binaan')"
                        {{ $scores['biru']['can_undo_binaan'] ? '' : 'disabled' }}
                        class="text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 {{ $scores['biru']['can_undo_binaan'] ? 'bg-amber-100 hover:bg-amber-200 text-amber-900 border-amber-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                        Undo Binaan
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" id="btn-binaan-1-biru" onclick="addPelanggaran('Biru', 'Binaan 1')"
                        class="py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-center gap-1.5 {{ $scores['biru']['has_binaan_1'] ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm' }}">
                        <span id="check-binaan-1-biru" class="{{ $scores['biru']['has_binaan_1'] ? '' : 'hidden' }} text-amber-900 font-black">✓</span>
                        Binaan 1
                    </button>
                    <button type="button" id="btn-binaan-2-biru" onclick="addPelanggaran('Biru', 'Binaan 2')"
                        class="py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-center gap-1.5 {{ $scores['biru']['has_binaan_2'] ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm' }}">
                        <span id="check-binaan-2-biru" class="{{ $scores['biru']['has_binaan_2'] ? '' : 'hidden' }} text-amber-900 font-black">✓</span>
                        Binaan 2
                    </button>
                </div>
            </div>

            {{-- 3. Teguran (Poin Minus - Babak Ini) --}}
            <div class="p-3 rounded-xl bg-orange-50/50 border border-orange-200">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                        <p class="text-xs font-black text-orange-950 uppercase tracking-wide">Teguran (Poin Minus)</p>
                        <span class="text-[10px] text-orange-700/80 font-bold">(Babak Ini)</span>
                    </div>
                    <button type="button" id="btn-undo-teguran-biru" onclick="undoPelanggaran('Biru', 'teguran')"
                        {{ $scores['biru']['can_undo_teguran'] ? '' : 'disabled' }}
                        class="text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 {{ $scores['biru']['can_undo_teguran'] ? 'bg-orange-100 hover:bg-orange-200 text-orange-900 border-orange-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                        Undo Teguran
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" id="btn-teguran-1-biru" onclick="addPelanggaran('Biru', 'Teguran 1')"
                        class="py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between {{ $scores['biru']['has_teguran_1'] ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-teguran-1-biru" class="{{ $scores['biru']['has_teguran_1'] ? '' : 'hidden' }} text-orange-950 font-black">✓</span>
                            Teguran 1
                        </span>
                        <span class="px-1.5 py-0.5 rounded bg-orange-200 text-orange-950 font-mono text-[10px] font-black">-1</span>
                    </button>
                    <button type="button" id="btn-teguran-2-biru" onclick="addPelanggaran('Biru', 'Teguran 2')"
                        class="py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between {{ $scores['biru']['has_teguran_2'] ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-teguran-2-biru" class="{{ $scores['biru']['has_teguran_2'] ? '' : 'hidden' }} text-orange-950 font-black">✓</span>
                            Teguran 2
                        </span>
                        <span class="px-1.5 py-0.5 rounded bg-orange-200 text-orange-950 font-mono text-[10px] font-black">-2</span>
                    </button>
                </div>
            </div>

            {{-- 4. Peringatan (Hukuman Berat - Semua Babak Sekaligus) --}}
            <div class="p-3 rounded-xl bg-red-50/50 border border-red-200">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-red-600"></span>
                        <p class="text-xs font-black text-red-950 uppercase tracking-wide">Peringatan</p>
                        <span class="text-[10px] text-red-700/80 font-bold">(Akumulatif Semua Babak)</span>
                    </div>
                    <button type="button" id="btn-undo-peringatan-biru" onclick="undoPelanggaran('Biru', 'peringatan')"
                        {{ $scores['biru']['can_undo_peringatan'] ? '' : 'disabled' }}
                        class="text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 {{ $scores['biru']['can_undo_peringatan'] ? 'bg-red-100 hover:bg-red-200 text-red-900 border-red-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                        Undo Peringatan
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" id="btn-peringatan-1-biru" onclick="addPelanggaran('Biru', 'Peringatan 1')"
                        class="py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between {{ $scores['biru']['has_peringatan_1'] ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-peringatan-1-biru" class="{{ $scores['biru']['has_peringatan_1'] ? '' : 'hidden' }} text-red-950 font-black">✓</span>
                            Peringatan 1
                        </span>
                        <span class="px-1.5 py-0.5 rounded bg-red-200 text-red-950 font-mono text-[10px] font-black">-5</span>
                    </button>
                    <button type="button" id="btn-peringatan-2-biru" onclick="addPelanggaran('Biru', 'Peringatan 2')"
                        class="py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between {{ $scores['biru']['has_peringatan_2'] ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-peringatan-2-biru" class="{{ $scores['biru']['has_peringatan_2'] ? '' : 'hidden' }} text-red-950 font-black">✓</span>
                            Peringatan 2
                        </span>
                        <span class="px-1.5 py-0.5 rounded bg-red-200 text-red-950 font-mono text-[10px] font-black">-10</span>
                    </button>
                </div>
            </div>

            {{-- 5. Diskualifikasi (DISQ) --}}
            <div class="p-3 rounded-xl bg-red-100/60 border border-red-300">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-700 animate-pulse"></span>
                        <p class="text-xs font-black text-red-950 uppercase tracking-wide">Diskualifikasi (DISQ)</p>
                    </div>
                    <button type="button" id="btn-undo-disq-biru" onclick="undoPelanggaran('Biru', 'disq')"
                        {{ $scores['biru']['can_undo_disq'] ? '' : 'disabled' }}
                        class="text-[10px] font-bold px-2.5 py-1 rounded-lg border transition-all flex items-center gap-1 {{ $scores['biru']['can_undo_disq'] ? 'bg-red-200 hover:bg-red-300 text-red-950 border-red-400 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                        Undo DISQ
                    </button>
                </div>
                <div>
                    <button type="button" id="btn-disq-biru" onclick="addPelanggaran('Biru', 'Peringatan 3')"
                        {{ $scores['biru']['is_disq'] ? 'disabled' : '' }}
                        class="w-full py-2 px-3 rounded-xl font-black text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 shadow-sm {{ $scores['biru']['is_disq'] ? 'bg-red-900 text-white opacity-80 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 active:scale-98 text-white' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span id="label-disq-biru">{{ $scores['biru']['is_disq'] ? 'PESERTA BIRU TELAH DIDISKUALIFIKASI' : 'DISKUALIFIKASI (DISQ)' }}</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- KONTROL SUDUT MERAH --}}
        <div class="bg-white rounded-2xl p-4 border border-red-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between border-b border-red-100 pb-2">
                <div class="flex items-center gap-2">
                    <span class="w-3.5 h-3.5 rounded-full bg-red-600 shadow-sm"></span>
                    <h3 class="text-sm font-black text-red-900 uppercase tracking-wide">AKSI DEWAN: SUDUT MERAH</h3>
                </div>
                <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full border border-red-200">
                    Babak Aktif: <strong id="label-babak-merah">Babak {{ $partai->babak_aktif ?: 1 }}</strong>
                </span>
            </div>

            {{-- 1. Jatuhan (+3 Poin) --}}
            <div class="p-3 rounded-xl bg-red-50/70 border border-red-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-black text-red-950 uppercase tracking-wide">Jatuhan (+3 Poin)</p>
                    <p class="text-[10px] text-red-600/80">Poin mutlak +3 (Hanya babak aktif)</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="addJatuhan('Merah')"
                        class="px-3.5 py-1.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase transition-all shadow-sm active:scale-95">
                        + JATUHAN
                    </button>
                    <button type="button" id="btn-undo-jatuhan-merah" onclick="undoJatuhan('Merah')"
                        {{ $scores['merah']['can_undo_jatuhan'] ? '' : 'disabled' }}
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all border {{ $scores['merah']['can_undo_jatuhan'] ? 'bg-white hover:bg-gray-100 text-gray-700 border-gray-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}"
                        title="Batalkan Jatuhan Babak Ini">
                        Undo
                    </button>
                </div>
            </div>

            {{-- 2. Binaan (0 Poin - Babak Ini) --}}
            <div class="p-3 rounded-xl bg-amber-50/40 border border-amber-200/80">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <p class="text-xs font-black text-amber-950 uppercase tracking-wide">Binaan (0 Poin)</p>
                        <span class="text-[10px] text-amber-700/80 font-bold">(Babak Ini)</span>
                    </div>
                    <button type="button" id="btn-undo-binaan-merah" onclick="undoPelanggaran('Merah', 'binaan')"
                        {{ $scores['merah']['can_undo_binaan'] ? '' : 'disabled' }}
                        class="text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 {{ $scores['merah']['can_undo_binaan'] ? 'bg-amber-100 hover:bg-amber-200 text-amber-900 border-amber-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                        Undo Binaan
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" id="btn-binaan-1-merah" onclick="addPelanggaran('Merah', 'Binaan 1')"
                        class="py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-center gap-1.5 {{ $scores['merah']['has_binaan_1'] ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm' }}">
                        <span id="check-binaan-1-merah" class="{{ $scores['merah']['has_binaan_1'] ? '' : 'hidden' }} text-amber-900 font-black">✓</span>
                        Binaan 1
                    </button>
                    <button type="button" id="btn-binaan-2-merah" onclick="addPelanggaran('Merah', 'Binaan 2')"
                        class="py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-center gap-1.5 {{ $scores['merah']['has_binaan_2'] ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm' }}">
                        <span id="check-binaan-2-merah" class="{{ $scores['merah']['has_binaan_2'] ? '' : 'hidden' }} text-amber-900 font-black">✓</span>
                        Binaan 2
                    </button>
                </div>
            </div>

            {{-- 3. Teguran (Poin Minus - Babak Ini) --}}
            <div class="p-3 rounded-xl bg-orange-50/50 border border-orange-200">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                        <p class="text-xs font-black text-orange-950 uppercase tracking-wide">Teguran (Poin Minus)</p>
                        <span class="text-[10px] text-orange-700/80 font-bold">(Babak Ini)</span>
                    </div>
                    <button type="button" id="btn-undo-teguran-merah" onclick="undoPelanggaran('Merah', 'teguran')"
                        {{ $scores['merah']['can_undo_teguran'] ? '' : 'disabled' }}
                        class="text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 {{ $scores['merah']['can_undo_teguran'] ? 'bg-orange-100 hover:bg-orange-200 text-orange-900 border-orange-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                        Undo Teguran
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" id="btn-teguran-1-merah" onclick="addPelanggaran('Merah', 'Teguran 1')"
                        class="py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between {{ $scores['merah']['has_teguran_1'] ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-teguran-1-merah" class="{{ $scores['merah']['has_teguran_1'] ? '' : 'hidden' }} text-orange-950 font-black">✓</span>
                            Teguran 1
                        </span>
                        <span class="px-1.5 py-0.5 rounded bg-orange-200 text-orange-950 font-mono text-[10px] font-black">-1</span>
                    </button>
                    <button type="button" id="btn-teguran-2-merah" onclick="addPelanggaran('Merah', 'Teguran 2')"
                        class="py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between {{ $scores['merah']['has_teguran_2'] ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-teguran-2-merah" class="{{ $scores['merah']['has_teguran_2'] ? '' : 'hidden' }} text-orange-950 font-black">✓</span>
                            Teguran 2
                        </span>
                        <span class="px-1.5 py-0.5 rounded bg-orange-200 text-orange-950 font-mono text-[10px] font-black">-2</span>
                    </button>
                </div>
            </div>

            {{-- 4. Peringatan (Hukuman Berat - Semua Babak Sekaligus) --}}
            <div class="p-3 rounded-xl bg-red-50/50 border border-red-200">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-red-600"></span>
                        <p class="text-xs font-black text-red-950 uppercase tracking-wide">Peringatan</p>
                        <span class="text-[10px] text-red-700/80 font-bold">(Akumulatif Semua Babak)</span>
                    </div>
                    <button type="button" id="btn-undo-peringatan-merah" onclick="undoPelanggaran('Merah', 'peringatan')"
                        {{ $scores['merah']['can_undo_peringatan'] ? '' : 'disabled' }}
                        class="text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 {{ $scores['merah']['can_undo_peringatan'] ? 'bg-red-100 hover:bg-red-200 text-red-900 border-red-300 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                        Undo Peringatan
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" id="btn-peringatan-1-merah" onclick="addPelanggaran('Merah', 'Peringatan 1')"
                        class="py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between {{ $scores['merah']['has_peringatan_1'] ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-peringatan-1-merah" class="{{ $scores['merah']['has_peringatan_1'] ? '' : 'hidden' }} text-red-950 font-black">✓</span>
                            Peringatan 1
                        </span>
                        <span class="px-1.5 py-0.5 rounded bg-red-200 text-red-950 font-mono text-[10px] font-black">-5</span>
                    </button>
                    <button type="button" id="btn-peringatan-2-merah" onclick="addPelanggaran('Merah', 'Peringatan 2')"
                        class="py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between {{ $scores['merah']['has_peringatan_2'] ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm' }}">
                        <span class="flex items-center gap-1">
                            <span id="check-peringatan-2-merah" class="{{ $scores['merah']['has_peringatan_2'] ? '' : 'hidden' }} text-red-950 font-black">✓</span>
                            Peringatan 2
                        </span>
                        <span class="px-1.5 py-0.5 rounded bg-red-200 text-red-950 font-mono text-[10px] font-black">-10</span>
                    </button>
                </div>
            </div>

            {{-- 5. Diskualifikasi (DISQ) --}}
            <div class="p-3 rounded-xl bg-red-100/60 border border-red-300">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-700 animate-pulse"></span>
                        <p class="text-xs font-black text-red-950 uppercase tracking-wide">Diskualifikasi (DISQ)</p>
                    </div>
                    <button type="button" id="btn-undo-disq-merah" onclick="undoPelanggaran('Merah', 'disq')"
                        {{ $scores['merah']['can_undo_disq'] ? '' : 'disabled' }}
                        class="text-[10px] font-bold px-2.5 py-1 rounded-lg border transition-all flex items-center gap-1 {{ $scores['merah']['can_undo_disq'] ? 'bg-red-200 hover:bg-red-300 text-red-950 border-red-400 shadow-sm cursor-pointer' : 'bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none' }}">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6-6M3 10l6 6"/></svg>
                        Undo DISQ
                    </button>
                </div>
                <div>
                    <button type="button" id="btn-disq-merah" onclick="addPelanggaran('Merah', 'Peringatan 3')"
                        {{ $scores['merah']['is_disq'] ? 'disabled' : '' }}
                        class="w-full py-2 px-3 rounded-xl font-black text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 shadow-sm {{ $scores['merah']['is_disq'] ? 'bg-red-900 text-white opacity-80 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 active:scale-98 text-white' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span id="label-disq-merah">{{ $scores['merah']['is_disq'] ? 'PESERTA MERAH TELAH DIDISKUALIFIKASI' : 'DISKUALIFIKASI (DISQ)' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- 5. PANEL VERIFIKASI JURI (SISTEM VAR) --}}
    <div class="bg-white rounded-2xl p-4 mb-4 border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 leading-tight">Sistem Verifikasi Juri (VAR Tanding)</h3>
                    <p class="text-[11px] text-gray-400">Gunakan untuk memeriksa ulang serangan atau jatuhan bersama Juri 1, 2, dan 3.</p>
                </div>
            </div>

            @if ($verifikasiAktif)
                <span class="px-2.5 py-1 rounded-full text-xs font-black bg-purple-100 text-purple-700 animate-pulse flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-purple-600 animate-ping"></span>
                    VERIFIKASI AKTIF
                </span>
            @endif
        </div>

        @if ($verifikasiAktif)
            {{-- SESI VERIFIKASI AKTIF --}}
            <div class="p-4 rounded-xl bg-purple-50/60 border border-purple-200 mb-4 space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <div>
                        <span class="text-[10px] font-black text-purple-500 uppercase tracking-widest">Keterangan Verifikasi:</span>
                        <h4 class="text-base font-extrabold text-purple-900">{{ $verifikasiAktif->keterangan }}</h4>
                    </div>
                    <span class="px-2.5 py-1 rounded bg-purple-200/80 text-purple-800 text-xs font-bold">
                        Babak {{ $verifikasiAktif->babak }}
                    </span>
                </div>

                {{-- Matriks Respon 3 Juri --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @for ($j = 1; $j <= 3; $j++)
                        @php
                            $ans = $verifikasiAktif->jawabanJuri->firstWhere('no_juri', $j);
                            $pil = $ans ? $ans->pilihan_sudut : null;
                        @endphp
                        <div class="bg-white p-3 rounded-xl border border-purple-100 shadow-sm space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-gray-700">Juri {{ $j }}</span>
                                <span id="ans-badge-{{ $j }}" class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $pil === 'Biru' ? 'bg-blue-600 text-white' : ($pil === 'Merah' ? 'bg-red-600 text-white' : ($pil === 'Batal' ? 'bg-gray-400 text-white' : 'bg-gray-100 text-gray-500')) }}">
                                    {{ $pil ?: 'Menunggu' }}
                                </span>
                            </div>
                            
                            {{-- Tombol Pilih Sudut untuk Juri --}}
                            <div class="grid grid-cols-3 gap-1">
                                <button type="button" onclick="recordJawabanVerifikasi({{ $verifikasiAktif->id }}, {{ $j }}, 'Biru')"
                                    class="py-1 px-1 rounded-lg text-[10px] font-bold bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 border border-blue-200 transition-all">
                                    Biru
                                </button>
                                <button type="button" onclick="recordJawabanVerifikasi({{ $verifikasiAktif->id }}, {{ $j }}, 'Merah')"
                                    class="py-1 px-1 rounded-lg text-[10px] font-bold bg-red-50 hover:bg-red-600 hover:text-white text-red-700 border border-red-200 transition-all">
                                    Merah
                                </button>
                                <button type="button" onclick="recordJawabanVerifikasi({{ $verifikasiAktif->id }}, {{ $j }}, 'Batal')"
                                    class="py-1 px-1 rounded-lg text-[10px] font-bold bg-gray-100 hover:bg-gray-600 hover:text-white text-gray-600 border border-gray-200 transition-all">
                                    Batal
                                </button>
                            </div>
                        </div>
                    @endfor
                </div>

                {{-- Eksekusi Keputusan Dewan --}}
                <div class="pt-3 border-t border-purple-200 flex flex-wrap items-center justify-between gap-2">
                    <span class="text-xs font-bold text-purple-900">Eksekusi Keputusan Dewan:</span>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" onclick="selesaiVerifikasi({{ $verifikasiAktif->id }}, 'jatuhan_biru')"
                            class="px-2.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm">
                            Sahkan Jatuhan Biru (+3)
                        </button>
                        <button type="button" onclick="selesaiVerifikasi({{ $verifikasiAktif->id }}, 'jatuhan_merah')"
                            class="px-2.5 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm">
                            Sahkan Jatuhan Merah (+3)
                        </button>
                        <button type="button" onclick="selesaiVerifikasi({{ $verifikasiAktif->id }}, 'sah_biru_tendangan')"
                            class="px-2.5 py-1.5 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-bold">
                            Tendangan Biru (+2)
                        </button>
                        <button type="button" onclick="selesaiVerifikasi({{ $verifikasiAktif->id }}, 'sah_merah_tendangan')"
                            class="px-2.5 py-1.5 rounded-lg bg-red-100 hover:bg-red-200 text-red-800 text-xs font-bold">
                            Tendangan Merah (+2)
                        </button>
                        <button type="button" onclick="selesaiVerifikasi({{ $verifikasiAktif->id }}, 'batal')"
                            class="px-2.5 py-1.5 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold">
                            Batal / Tutup Sesi
                        </button>
                    </div>
                </div>
            </div>
        @else
            {{-- FORM MEMULAI VERIFIKASI BARU --}}
            <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" id="input-keterangan-verifikasi" placeholder="Ketik keterangan verifikasi (contoh: Verifikasi Jatuhan Sudut Biru)..."
                    class="flex-1 h-10 px-3.5 text-xs rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-200 outline-none">
                <div class="flex gap-1.5">
                    <button type="button" onclick="quickVerifikasi('Verifikasi Jatuhan Biru')" class="px-2 py-1 rounded-lg bg-gray-100 hover:bg-blue-50 text-[10px] font-bold text-gray-600 hover:text-blue-700">
                        + Jatuhan Biru
                    </button>
                    <button type="button" onclick="quickVerifikasi('Verifikasi Jatuhan Merah')" class="px-2 py-1 rounded-lg bg-gray-100 hover:bg-red-50 text-[10px] font-bold text-gray-600 hover:text-red-700">
                        + Jatuhan Merah
                    </button>
                    <button type="button" onclick="quickVerifikasi('Verifikasi Peringatan / Pelanggaran')" class="px-2 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-[10px] font-bold text-gray-600">
                        + Pelanggaran
                    </button>
                    <button type="button" onclick="startVerifikasi()"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm shrink-0">
                        Mulai Verifikasi
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- 6. TIMELINE LOG RIWAYAT POIN & PELANGGARAN --}}
    <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
        <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Riwayat Log Aktivitas Pertandingan (Terbaru)
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 uppercase tracking-widest font-semibold">
                        <th class="px-3 py-2 w-20 text-center">Waktu</th>
                        <th class="px-3 py-2 w-20 text-center">Babak</th>
                        <th class="px-3 py-2 w-28 text-center">Sudut</th>
                        <th class="px-3 py-2 w-40">Aksi / Event</th>
                        <th class="px-3 py-2">Keterangan</th>
                    </tr>
                </thead>
                <tbody id="timeline-body" class="divide-y divide-gray-50">
                    @forelse ($timeline as $ev)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-3 py-2 text-center text-gray-400 font-mono">{{ $ev['time'] }}</td>
                            <td class="px-3 py-2 text-center font-bold text-gray-700">Babak {{ $ev['babak'] }}</td>
                            <td class="px-3 py-2 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $ev['sudut'] === 'Biru' ? 'bg-blue-600 text-white' : 'bg-red-600 text-white' }}">
                                    {{ $ev['sudut'] }}
                                </span>
                            </td>
                            <td class="px-3 py-2 font-bold text-gray-900">{{ $ev['badge'] }}</td>
                            <td class="px-3 py-2 text-gray-500">{{ $ev['keterangan'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-400 italic">
                                Belum ada riwayat aktivitas serangan atau hukuman yang dicatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL SELESAIKAN PERTANDINGAN --}}
    <div id="modal-selesai" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900">Selesaikan Pertandingan</h3>
                <button type="button" onclick="closeSelesaiModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <p class="text-xs text-gray-500 mb-4">Pilih atlet yang menjadi pemenang untuk mengunci skor dan mengupdate bagan partai selanjutnya:</p>

            <form action="{{ route('admin.manajemen.tanding.set-pemenang', $partai->id) }}" method="POST">
                @csrf
                <div class="space-y-3 mb-5">
                    @if ($partai->pesertaBiru)
                        <label class="block cursor-pointer">
                            <input type="radio" name="id_pemenang" value="{{ $partai->pesertaBiru->id }}" checked class="peer sr-only">
                            <div class="p-3.5 rounded-xl border-2 border-blue-100 bg-blue-50/40 peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-all flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-bold uppercase bg-blue-600 text-white px-2 py-0.5 rounded">Sudut Biru</span>
                                    <p class="text-sm font-bold text-blue-900 mt-1">{{ $partai->pesertaBiru->nama }}</p>
                                    <p class="text-[10px] text-blue-600">{{ $partai->pesertaBiru->kontingen }}</p>
                                </div>
                                <span class="text-xs font-black text-blue-700 font-mono score-preview-biru">{{ $scores['biru']['total'] }} Poin</span>
                            </div>
                        </label>
                    @endif

                    @if ($partai->pesertaMerah)
                        <label class="block cursor-pointer">
                            <input type="radio" name="id_pemenang" value="{{ $partai->pesertaMerah->id }}" class="peer sr-only">
                            <div class="p-3.5 rounded-xl border-2 border-red-100 bg-red-50/40 peer-checked:border-red-600 peer-checked:bg-red-50 transition-all flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-bold uppercase bg-red-600 text-white px-2 py-0.5 rounded">Sudut Merah</span>
                                    <p class="text-sm font-bold text-red-900 mt-1">{{ $partai->pesertaMerah->nama }}</p>
                                    <p class="text-[10px] text-red-600">{{ $partai->pesertaMerah->kontingen }}</p>
                                </div>
                                <span class="text-xs font-black text-red-700 font-mono score-preview-merah">{{ $scores['merah']['total'] }} Poin</span>
                            </div>
                        </label>
                    @endif
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeSelesaiModal()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm">
                        Kunci & Tetapkan Pemenang
                    </button>
                </div>
            </form>
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
    async function toggleJuri(noJuri) {
        const url = `/admin/dewan/tanding/${partaiId}/toggle-juri/${noJuri}`;
        const res = await sendPost(url);
        if (res.success) {
            updateJuriUI(noJuri, res.is_active);
            showToast('success', res.message);
        }
    }

    async function toggleAllJuri(action) {
        const url = `/admin/dewan/tanding/${partaiId}/toggle-all-juri`;
        const res = await sendPost(url, { action: action });
        if (res.success) {
            for (let i = 1; i <= 3; i++) {
                updateJuriUI(i, res.is_active);
            }
            showToast('success', res.message);
        }
    }

    function updateJuriUI(noJuri, isActive) {
        const card = document.getElementById(`card-juri-${noJuri}`);
        const icon = document.getElementById(`icon-juri-${noJuri}`);
        const text = document.getElementById(`status-text-juri-${noJuri}`);
        const dot = document.getElementById(`status-dot-juri-${noJuri}`);
        const badge = document.getElementById(`status-badge-juri-${noJuri}`);
        const btn = document.getElementById(`btn-toggle-juri-${noJuri}`);

        if (isActive) {
            card.className = "p-3.5 rounded-xl border transition-all flex items-center justify-between bg-emerald-50/50 border-emerald-200";
            icon.className = "w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm bg-emerald-600 text-white shadow-sm";
            text.innerText = "ACTIVE";
            badge.className = "inline-flex items-center gap-1 text-[10px] font-extrabold uppercase mt-0.5 text-emerald-700";
            dot.className = "w-1.5 h-1.5 rounded-full bg-emerald-500";
            btn.className = "px-3 py-1.5 rounded-xl text-xs font-bold transition-all shadow-sm bg-amber-500 hover:bg-amber-600 text-white";
            btn.innerText = "Jeda Juri";
        } else {
            card.className = "p-3.5 rounded-xl border transition-all flex items-center justify-between bg-gray-50 border-gray-200 opacity-80";
            icon.className = "w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm bg-gray-300 text-gray-700";
            text.innerText = "PAUSED";
            badge.className = "inline-flex items-center gap-1 text-[10px] font-extrabold uppercase mt-0.5 text-gray-500";
            dot.className = "w-1.5 h-1.5 rounded-full bg-gray-400";
            btn.className = "px-3 py-1.5 rounded-xl text-xs font-bold transition-all shadow-sm bg-emerald-600 hover:bg-emerald-700 text-white";
            btn.innerText = "Aktifkan";
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

    // 4. KONTROL VERIFIKASI JURI (VAR)
    function quickVerifikasi(text) {
        document.getElementById('input-keterangan-verifikasi').value = text;
    }

    async function startVerifikasi() {
        const ket = document.getElementById('input-keterangan-verifikasi').value;
        if (!ket) {
            showToast('error', 'Keterangan verifikasi wajib diisi!');
            return;
        }

        const url = `/admin/dewan/tanding/${partaiId}/start-verifikasi`;
        const res = await sendPost(url, { keterangan: ket });
        if (res.success) {
            window.location.reload();
        }
    }

    async function recordJawabanVerifikasi(idVerifikasi, noJuri, pilihan) {
        const url = `/admin/dewan/tanding/verifikasi/${idVerifikasi}/jawaban`;
        const res = await sendPost(url, { no_juri: noJuri, pilihan_sudut: pilihan });
        if (res.success) {
            const badge = document.getElementById(`ans-badge-${noJuri}`);
            badge.innerText = pilihan;
            if (pilihan === 'Biru') badge.className = "px-2 py-0.5 rounded text-[10px] font-black uppercase bg-blue-600 text-white";
            else if (pilihan === 'Merah') badge.className = "px-2 py-0.5 rounded text-[10px] font-black uppercase bg-red-600 text-white";
            else badge.className = "px-2 py-0.5 rounded text-[10px] font-black uppercase bg-gray-400 text-white";
            showToast('success', res.message);
        }
    }

    async function selesaiVerifikasi(idVerifikasi, keputusan) {
        const url = `/admin/dewan/tanding/verifikasi/${idVerifikasi}/selesai`;
        const res = await sendPost(url, { keputusan: keputusan });
        if (res.success) {
            window.location.reload();
        }
    }

    // 5. KONTROL BABAK
    async function changeBabak(babak) {
        const url = `/admin/dewan/tanding/${partaiId}/update-babak`;
        const res = await sendPost(url, { babak_aktif: babak });
        if (res.success) {
            document.querySelectorAll('[id^="btn-babak-"]').forEach((btn, idx) => {
                if ((idx + 1) === babak) {
                    btn.className = "px-3 py-1 rounded-lg text-xs font-black transition-all bg-primary-600 text-white shadow-sm";
                } else {
                    btn.className = "px-3 py-1 rounded-lg text-xs font-black transition-all text-gray-600 hover:bg-gray-200";
                }
            });
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
            document.querySelectorAll('[id^="btn-babak-"]').forEach((btn, idx) => {
                if ((idx + 1) === babak) {
                    btn.className = "px-3 py-1 rounded-lg text-xs font-black transition-all bg-primary-600 text-white shadow-sm";
                } else {
                    btn.className = "px-3 py-1 rounded-lg text-xs font-black transition-all text-gray-600 hover:bg-gray-200";
                }
            });
            document.getElementById('badge-babak-display').innerText = `BABAK ${babak}`;
            timerSeconds = 120;
            pauseTimer();
            updateTimerDisplay();
            showToast('success', res.message);
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

    // 7. HELPER UPDATE SKOR & STATUS UI
    function applyScores(scores) {
        // --- 1. SKOR UTAMA SUDUT BIRU ---
        const scoreBiruEl = document.getElementById('display-score-biru');
        const bannerBiru = document.getElementById('disq-banner-biru');
        const labelBiru = document.getElementById('label-score-biru');

        if (scores.biru.is_disq) {
            scoreBiruEl.innerHTML = '<span class="text-5xl md:text-6xl text-red-600 font-black tracking-normal">DISQ</span>';
            scoreBiruEl.className = "text-7xl font-black text-red-600 font-mono tracking-tighter";
            if (bannerBiru) bannerBiru.classList.remove('hidden');
            if (labelBiru) labelBiru.classList.add('hidden');
        } else {
            scoreBiruEl.innerText = scores.biru.total;
            scoreBiruEl.className = "text-7xl font-black text-blue-600 font-mono tracking-tighter";
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
            scoreMerahEl.innerHTML = '<span class="text-5xl md:text-6xl text-red-600 font-black tracking-normal">DISQ</span>';
            if (bannerMerah) bannerMerah.classList.remove('hidden');
            if (labelMerah) labelMerah.classList.add('hidden');
        } else {
            scoreMerahEl.innerText = scores.merah.total;
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
            'px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all border bg-white hover:bg-gray-100 text-gray-700 border-gray-300 shadow-sm cursor-pointer',
            'px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all border bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-binaan-biru',
            scores.biru.can_undo_binaan,
            'text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 bg-amber-100 hover:bg-amber-200 text-amber-900 border-amber-300 shadow-sm cursor-pointer',
            'text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-teguran-biru',
            scores.biru.can_undo_teguran,
            'text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 bg-orange-100 hover:bg-orange-200 text-orange-900 border-orange-300 shadow-sm cursor-pointer',
            'text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-peringatan-biru',
            scores.biru.can_undo_peringatan,
            'text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 bg-red-100 hover:bg-red-200 text-red-900 border-red-300 shadow-sm cursor-pointer',
            'text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-disq-biru',
            scores.biru.can_undo_disq,
            'text-[10px] font-bold px-2.5 py-1 rounded-lg border transition-all flex items-center gap-1 bg-red-200 hover:bg-red-300 text-red-950 border-red-400 shadow-sm cursor-pointer',
            'text-[10px] font-bold px-2.5 py-1 rounded-lg border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        // --- 4. STATUS TOMBOL UNDO SUDUT MERAH (AKTIF / INAKTIF) ---
        toggleBtnState(
            'btn-undo-jatuhan-merah',
            scores.merah.can_undo_jatuhan,
            'px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all border bg-white hover:bg-gray-100 text-gray-700 border-gray-300 shadow-sm cursor-pointer',
            'px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all border bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-binaan-merah',
            scores.merah.can_undo_binaan,
            'text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 bg-amber-100 hover:bg-amber-200 text-amber-900 border-amber-300 shadow-sm cursor-pointer',
            'text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-teguran-merah',
            scores.merah.can_undo_teguran,
            'text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 bg-orange-100 hover:bg-orange-200 text-orange-900 border-orange-300 shadow-sm cursor-pointer',
            'text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-peringatan-merah',
            scores.merah.can_undo_peringatan,
            'text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 bg-red-100 hover:bg-red-200 text-red-900 border-red-300 shadow-sm cursor-pointer',
            'text-[10px] font-bold px-2 py-1 rounded-lg border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        toggleBtnState(
            'btn-undo-disq-merah',
            scores.merah.can_undo_disq,
            'text-[10px] font-bold px-2.5 py-1 rounded-lg border transition-all flex items-center gap-1 bg-red-200 hover:bg-red-300 text-red-950 border-red-400 shadow-sm cursor-pointer',
            'text-[10px] font-bold px-2.5 py-1 rounded-lg border transition-all flex items-center gap-1 bg-gray-100 text-gray-400 border-gray-200 opacity-40 cursor-not-allowed pointer-events-none'
        );

        // --- 5. CHECKMARKS & HIGHLIGHTS TOMBOL BIRU ---
        toggleCheckmark('check-binaan-1-biru', scores.biru.has_binaan_1);
        toggleCheckmark('check-binaan-2-biru', scores.biru.has_binaan_2);
        toggleCheckmark('check-teguran-1-biru', scores.biru.has_teguran_1);
        toggleCheckmark('check-teguran-2-biru', scores.biru.has_teguran_2);
        toggleCheckmark('check-peringatan-1-biru', scores.biru.has_peringatan_1);
        toggleCheckmark('check-peringatan-2-biru', scores.biru.has_peringatan_2);

        const btnBinaan1B = document.getElementById('btn-binaan-1-biru');
        if (btnBinaan1B) btnBinaan1B.className = `py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-center gap-1.5 ${scores.biru.has_binaan_1 ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm'}`;
        const btnBinaan2B = document.getElementById('btn-binaan-2-biru');
        if (btnBinaan2B) btnBinaan2B.className = `py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-center gap-1.5 ${scores.biru.has_binaan_2 ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm'}`;

        const btnTeguran1B = document.getElementById('btn-teguran-1-biru');
        if (btnTeguran1B) btnTeguran1B.className = `py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between ${scores.biru.has_teguran_1 ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm'}`;
        const btnTeguran2B = document.getElementById('btn-teguran-2-biru');
        if (btnTeguran2B) btnTeguran2B.className = `py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between ${scores.biru.has_teguran_2 ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm'}`;

        const btnPer1B = document.getElementById('btn-peringatan-1-biru');
        if (btnPer1B) btnPer1B.className = `py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between ${scores.biru.has_peringatan_1 ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm'}`;
        const btnPer2B = document.getElementById('btn-peringatan-2-biru');
        if (btnPer2B) btnPer2B.className = `py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between ${scores.biru.has_peringatan_2 ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm'}`;

        const btnDisqB = document.getElementById('btn-disq-biru');
        const lblDisqB = document.getElementById('label-disq-biru');
        if (btnDisqB) {
            btnDisqB.disabled = scores.biru.is_disq;
            btnDisqB.className = `w-full py-2 px-3 rounded-xl font-black text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 shadow-sm ${scores.biru.is_disq ? 'bg-red-900 text-white opacity-80 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 active:scale-98 text-white'}`;
        }
        if (lblDisqB) lblDisqB.innerText = scores.biru.is_disq ? 'PESERTA BIRU TELAH DIDISKUALIFIKASI' : 'DISKUALIFIKASI (DISQ)';

        // --- 6. CHECKMARKS & HIGHLIGHTS TOMBOL MERAH ---
        toggleCheckmark('check-binaan-1-merah', scores.merah.has_binaan_1);
        toggleCheckmark('check-binaan-2-merah', scores.merah.has_binaan_2);
        toggleCheckmark('check-teguran-1-merah', scores.merah.has_teguran_1);
        toggleCheckmark('check-teguran-2-merah', scores.merah.has_teguran_2);
        toggleCheckmark('check-peringatan-1-merah', scores.merah.has_peringatan_1);
        toggleCheckmark('check-peringatan-2-merah', scores.merah.has_peringatan_2);

        const btnBinaan1M = document.getElementById('btn-binaan-1-merah');
        if (btnBinaan1M) btnBinaan1M.className = `py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-center gap-1.5 ${scores.merah.has_binaan_1 ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm'}`;
        const btnBinaan2M = document.getElementById('btn-binaan-2-merah');
        if (btnBinaan2M) btnBinaan2M.className = `py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-center gap-1.5 ${scores.merah.has_binaan_2 ? 'bg-amber-200 text-amber-900 border-amber-400 shadow-inner font-black' : 'bg-white hover:bg-amber-50 text-gray-700 hover:text-amber-800 border-gray-300 shadow-sm'}`;

        const btnTeguran1M = document.getElementById('btn-teguran-1-merah');
        if (btnTeguran1M) btnTeguran1M.className = `py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between ${scores.merah.has_teguran_1 ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm'}`;
        const btnTeguran2M = document.getElementById('btn-teguran-2-merah');
        if (btnTeguran2M) btnTeguran2M.className = `py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between ${scores.merah.has_teguran_2 ? 'bg-orange-200 text-orange-950 border-orange-400 shadow-inner font-black' : 'bg-white hover:bg-orange-100 text-orange-800 border-orange-200 shadow-sm'}`;

        const btnPer1M = document.getElementById('btn-peringatan-1-merah');
        if (btnPer1M) btnPer1M.className = `py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between ${scores.merah.has_peringatan_1 ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm'}`;
        const btnPer2M = document.getElementById('btn-peringatan-2-merah');
        if (btnPer2M) btnPer2M.className = `py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between ${scores.merah.has_peringatan_2 ? 'bg-red-200 text-red-950 border-red-400 shadow-inner font-black' : 'bg-white hover:bg-red-100 text-red-800 border-red-200 shadow-sm'}`;

        const btnDisqM = document.getElementById('btn-disq-merah');
        const lblDisqM = document.getElementById('label-disq-merah');
        if (btnDisqM) {
            btnDisqM.disabled = scores.merah.is_disq;
            btnDisqM.className = `w-full py-2 px-3 rounded-xl font-black text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 shadow-sm ${scores.merah.is_disq ? 'bg-red-900 text-white opacity-80 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 active:scale-98 text-white'}`;
        }
        if (lblDisqM) lblDisqM.innerText = scores.merah.is_disq ? 'PESERTA MERAH TELAH DIDISKUALIFIKASI' : 'DISKUALIFIKASI (DISQ)';

        // --- 7. MODAL SELESAI SKOR PREVIEW ---
        document.querySelectorAll('.score-preview-biru').forEach(el => el.innerText = `${scores.biru.is_disq ? 'DISQ' : scores.biru.total + ' Poin'}`);
        document.querySelectorAll('.score-preview-merah').forEach(el => el.innerText = `${scores.merah.is_disq ? 'DISQ' : scores.merah.total + ' Poin'}`);
    }

    // Refresh Timeline & Scores secara berkala (polling ringan tiap 5 detik)
    async function refreshStatus() {
        try {
            const res = await fetch(`/admin/dewan/tanding/${partaiId}/status-data`);
            const data = await res.json();
            if (data.success) {
                applyScores(data.scores);

                // Render Timeline
                const tbody = document.getElementById('timeline-body');
                if (data.timeline && data.timeline.length > 0) {
                    tbody.innerHTML = data.timeline.map(ev => `
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-3 py-2 text-center text-gray-400 font-mono">${ev.time}</td>
                            <td class="px-3 py-2 text-center font-bold text-gray-700">Babak ${ev.babak}</td>
                            <td class="px-3 py-2 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase ${ev.sudut === 'Biru' ? 'bg-blue-600 text-white' : 'bg-red-600 text-white'}">
                                    ${ev.sudut}
                                </span>
                            </td>
                            <td class="px-3 py-2 font-bold text-gray-900">${ev.badge}</td>
                            <td class="px-3 py-2 text-gray-500">${ev.keterangan}</td>
                        </tr>
                    `).join('');
                }
            }
        } catch (e) {
            console.error('Polling error:', e);
        }
    }

    setInterval(refreshStatus, 5000);

    // Modal Selesai
    function openSelesaiModal() {
        document.getElementById('modal-selesai').classList.remove('hidden');
    }

    function closeSelesaiModal() {
        document.getElementById('modal-selesai').classList.add('hidden');
    }
</script>
@endpush
