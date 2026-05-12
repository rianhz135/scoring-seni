@extends('layouts.dewan')

@section('content')
    @php
        $sudutDb = ucfirst($sudut);
        $aktif = $partai->detail_peserta->where('sudut', $sudutDb)->first();
    @endphp

    {{-- 1. Header Informasi Atlet --}}
    <div
        class="bg-white rounded-3xl p-4 md:p-2 lg:p-4 mb-6 md:mb-2 lg:mb-6 border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4 md:gap-2 lg:gap-6">
        <div
            class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-4 md:gap-5 w-full md:w-auto">
            <div
                class="{{ strtolower($sudut) == 'biru' ? 'bg-blue-600' : 'bg-red-600' }} text-white p-4 rounded-2xl shadow-lg flex-shrink-0">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="flex flex-col items-center sm:items-start">
                <p
                    class="text-[10px] font-black {{ strtolower($sudut) == 'biru' ? 'text-blue-500' : 'text-red-500' }} uppercase tracking-[0.2em] mb-1 md:mb-2">
                    KONTINGEN: <span class="text-gray-900">{{ $aktif->peserta->kontingen ?? '-' }}</span>
                </p>

                {{-- Pembungkus Nama & Badge --}}
                <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-3">
                    <h1 class="text-xl md:text-2xl font-black text-gray-900 leading-none uppercase tracking-tight">
                        @php
                            // Ambil semua peserta di sudut yang sedang aktif
                            $pesertaRegu = $partai->detail_peserta->where('sudut', ucfirst($sudut));
                        @endphp

                        @foreach ($pesertaRegu as $det)
                            {{ $det->peserta->nama_peserta ?? ($det->peserta->nama ?? 'NAMA TIDAK DITEMUKAN') }}@if (!$loop->last)
                                &
                            @endif
                        @endforeach
                    </h1>

                    <div class="flex flex-wrap justify-center sm:justify-start items-center gap-2 mt-1 md:mt-0">
                        <span
                            class="px-2 py-0.5 text-[10px] font-bold rounded-md border uppercase {{ strtolower($sudut) == 'biru' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-red-50 text-red-600 border-red-200' }}">
                            Sudut {{ $sudutDb }}
                        </span>
                        <span
                            class="px-2 py-0.5 bg-primary-50 text-primary-600 text-[10px] font-bold rounded-md border border-primary-100 uppercase">
                            {{ $partai->kelas->nama_kelas ?? ($partai->kelas->label_kelas ?? 'REGU') }}
                        </span>
                        <span
                            class="px-2 py-0.5 bg-purple-50 text-purple-600 text-[10px] font-bold rounded-md border border-purple-100 uppercase">
                            KATEGORI: {{ $partai->kategori->nama_kategori ?? 'REGURA/PUTRI' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="flex items-center justify-center w-full md:w-auto border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-8">
            <div class="text-center md:text-right">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pertandingan</p>
                <div class="flex items-center justify-center md:justify-end gap-2">
                    <span class="text-3xl font-black text-gray-900 tracking-tighter">#{{ $partai->no_partai }}</span>
                    <div class="h-8 w-[2px] bg-gray-100 mx-1"></div>
                    <span class="text-xl font-bold text-primary-600 uppercase">GEL {{ $partai->gelanggang ?? 'A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-2 lg:gap-6">
        {{-- 2. Timer & Stopwatch --}}
        <div class="md:col-span-3 space-y-6 md:space-y-2 lg:space-y-6">
            {{-- 2. Timer --}}
            <div
                class="bg-gray-900 rounded-3xl p-8 md:p-5 flex flex-col items-center justify-center shadow-xl border-4 border-gray-800 relative overflow-hidden">
                <div class="absolute top-0 w-full h-1 bg-primary-500"></div>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.4em] mb-4 md:mb-1 lg:mb-4">Timer
                    Penampilan</p>

                {{-- Ambil waktu awal dari database (misal format di DB: "01:25") --}}
                <div class="text-7xl md:text-4xl font-black text-white font-mono tracking-tighter mb-8 md:mb-4 md:tabular-nums"
                    id="timer-display">
                    {{-- Format detik ke MM:SS di server-side --}}
                    @php
                        $detik = $aktif->waktu_tampil ?? 0;
                        $menit = floor($detik / 60);
                        $sisaDetik = $detik % 60;
                        echo sprintf('%02d:%02d', $menit, $sisaDetik);
                    @endphp
                </div>

                {{-- Grid Tombol Timer --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 w-full">
                    <button id="btn-start" onclick="startTimer()"
                        class="bg-primary-600 hover:bg-primary-700 text-white py-4  rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all active:scale-95 shadow-lg">
                        Start
                    </button>
                    <button id="btn-pause" onclick="pauseTimer()" disabled
                        class="bg-gray-800 hover:bg-gray-700 text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all active:scale-95 opacity-50 cursor-not-allowed">
                        Pause
                    </button>
                    <button id="btn-reset" onclick="resetTimer()"
                        class="bg-red-900/30 hover:bg-red-900/50 text-red-500 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all active:scale-95">
                        Reset
                    </button>
                </div>
            </div>
            {{-- 3. Kontrol Ketua Pertandingan (Hukuman & Diskualifikasi) --}}
            <div class="bg-white rounded-3xl p-6 md:p-4 border border-gray-200 shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b border-gray-50 pb-3 md:pb-0">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Hukuman & Status</p>
                    {{-- Indikator Status Diskualifikasi (Kecil di pojok) --}}
                    <div id="badge-disq" class="{{ ($aktif->is_disqualifikasi ?? 0) == 1 ? '' : 'hidden' }}">
                        <span
                            class="bg-red-600 text-white text-[8px] font-black py-1 px-2 rounded-lg animate-pulse uppercase">Terdiskualifikasi</span>
                    </div>
                </div>

                {{-- Display Nilai Hukuman --}}
                <div class="bg-red-50 rounded-2xl p-4 md:p-2 text-center border border-red-100">
                    <p class="text-[10px] font-bold text-red-400 uppercase mb-1 md:mb-0">Total Hukuman</p>
                    <span id="display-hukuman-ketua" class="text-4xl md:text-2xl font-black text-red-600 font-mono">
                        {{ number_format($aktif->hukuman_ketua ?? 0, 2) }}
                    </span>
                </div>

                {{-- Grid Tombol Hukuman --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                    <button onclick="updateHukuman('add')"
                        class="bg-gray-900 hover:bg-black text-white py-4 md:p-2 rounded-xl font-black text-[10px] uppercase transition-all active:scale-95">
                        - 0.50
                    </button>
                    <button onclick="updateHukuman('undo')"
                        class="bg-white hover:bg-gray-50 text-gray-600 border border-gray-200 py-4 md:p-2 rounded-xl font-black text-[10px] uppercase transition-all active:scale-95">
                        Undo
                    </button>
                    <button type="button" id="btn-diskualifikasi" onclick="toggleDisq()"
                        data-url="{{ route('admin.manajemen.regu.diskualifikasi', ['id' => $partai->id_partai, 'sudut' => $sudut]) }}"
                        data-status="{{ $aktif->is_disqualifikasi ?? 0 }}"
                        class="{{ ($aktif->is_disqualifikasi ?? 0) == 1 ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 border border-red-100' }} py-4 md:p-1 rounded-xl font-black text-[9px] uppercase transition-all active:scale-95 flex items-center justify-center text-center leading-tight">
                        <span id="teks-disq">
                            {{ ($aktif->is_disqualifikasi ?? 0) == 1 ? 'BATAL DISQ' : 'DISQ' }}
                        </span>
                    </button>
                </div>

                <div class="hidden lg:block mt-3 pt-3 border-t border-gray-100">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Kriteria Hukuman</p>
                    </div>

                    <ul class="grid grid-cols-1 xl:grid-cols-2 gap-1.5">
                        <li
                            class="flex justify-between items-center bg-gray-50/80 hover:bg-gray-100 transition-colors px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                            <span class="text-[8px] font-bold text-gray-600 uppercase tracking-wide">Keluar Garis</span>
                            <span
                                class="text-[9px] font-black text-red-600 bg-red-100/50 px-1.5 py-0.5 rounded-md border border-red-100">-0.50</span>
                        </li>
                        <li
                            class="flex justify-between items-center bg-gray-50/80 hover:bg-gray-100 transition-colors px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                            <span class="text-[8px] font-bold text-gray-600 uppercase tracking-wide">Alat Jatuh</span>
                            <span
                                class="text-[9px] font-black text-red-600 bg-red-100/50 px-1.5 py-0.5 rounded-md border border-red-100">-0.50</span>
                        </li>
                        <li
                            class="flex justify-between items-center bg-gray-50/80 hover:bg-gray-100 transition-colors px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                            <span class="text-[8px] font-bold text-gray-600 uppercase tracking-wide">Pakaian Salah</span>
                            <span
                                class="text-[9px] font-black text-red-600 bg-red-100/50 px-1.5 py-0.5 rounded-md border border-red-100">-0.50</span>
                        </li>
                        <li
                            class="flex justify-between items-center bg-gray-50/80 hover:bg-gray-100 transition-colors px-2 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                            <span class="text-[8px] font-bold text-gray-600 uppercase tracking-wide">Waktu (-/+)</span>
                            <span
                                class="text-[9px] font-black text-red-600 bg-red-100/50 px-1.5 py-0.5 rounded-md border border-red-100">-/+
                                5 <span class="text-[7px]">/dtk</span></span>
                        </li>
                    </ul>
                </div>


                <div class="mt-6 lg:hidden flex gap-3 h-12">
                    {{-- TOMBOL SIMPAN HASIL --}}
                    <form
                        action="{{ route('admin.dewan.regu.selesai', ['id' => $partai->id_partai, 'sudut' => strtolower($aktif->sudut)]) }}"
                        method="POST" onsubmit="return konfirmasiSelesai(event, this)"class="flex-1 h-full">
                        @csrf
                        <button type="submit"
                            class="w-full h-full bg-green-600 text-white text-sm font-black tracking-widest hover:bg-green-700 transition-all shadow-md active:scale-95 rounded-xl flex items-center justify-center">
                            SIMPAN HASIL
                        </button>
                    </form>

                    {{-- TOMBOL REFRESH --}}
                    <button type="button" onclick="refreshLayarDisplay()"
                        class="shrink-0 aspect-square h-full bg-blue-600 text-white rounded-xl shadow-md hover:bg-blue-700 transition-all active:scale-95 flex items-center justify-center"
                        title="Refresh Layar Display Penonton">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>


        {{-- 4. Tabel Nilai Juri --}}
        <div class="md:col-span-9">
            {{-- PANEL REKAPITULASI NILAI AKHIR --}}
            <div class="md:col-span-8 mb-4 md:mb-2 lg:mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Block Median --}}
                    <div class="bg-indigo-50 border border-indigo-100 rounded-3xl p-5 md:p-3 lg:p-5 text-center shadow-sm">
                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Median Juri Aktif
                        </p>
                        <p id="display-median"
                            class="text-3xl md:text-2xl lg:text-4xl font-black text-indigo-700 font-mono">
                            {{ number_format($aktif->nilai_median ?? 0, 3) }}
                        </p>
                    </div>

                    {{-- Block Standar Deviasi --}}
                    <div
                        class="bg-amber-50 border border-amber-100 rounded-3xl p-5 md:p-3 lg:p-5 text-center shadow-sm overflow-hidden">
                        <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-1">Standar Deviasi</p>
                        <p id="display-deviasi"
                            class="text-base md:text-xl lg:text-4xl font-black text-amber-600 font-mono tracking-tighter break-words">
                            {{ number_format($aktif->nilai_deviasi ?? 0, 15) }}
                        </p>
                    </div>

                    {{-- Block Total Nilai Akhir --}}
                    <div
                        class="bg-emerald-50 border border-emerald-100 rounded-3xl p-5 md:p-3 lg:p-5 text-center shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-16 h-full bg-emerald-200/50 skew-x-12 translate-x-4"></div>
                        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1 relative z-10">
                            Total Nilai Bersih</p>
                        <p id="display-total-nilai"
                            class="text-4xl md:text-2xl lg:text-4xl font-black text-emerald-700 font-mono relative z-10 leading-none">
                            {{ number_format($aktif->total_nilai ?? 0, 3) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 md:p-3 lg:p-5 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Live Score Monitoring</p>
                    <span class="flex items-center gap-2 text-[10px] font-bold text-green-600 uppercase tracking-widest">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Streaming
                    </span>
                </div>

                {{-- MENGAMBIL DATA JURI DARI DATABASE --}}
                @php
                    $juriDb = \Illuminate\Support\Facades\DB::table('input_nilai_juri')
                        ->where('id_detail', $aktif->id_detail ?? 0)
                        ->orderBy('no_juri', 'asc')
                        ->get()
                        ->keyBy('no_juri');
                @endphp

                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="bg-indigo-400 text-white text-[11px] font-black uppercase tracking-widest">
                                <th class="px-6 py-4 md:py-1 lg:py-4 text-left border-r border-indigo-300 w-1/4">
                                    Jenis Nilai
                                </th>
                                @for ($i = 1; $i <= 6; $i++)
                                    <th
                                        class="px-4 md:px-2 py-4 md:py-2 lg:py-4 border-r border-indigo-300 last:border-r-0">
                                        Juri {{ $i }}
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">

                            {{-- Baris 1: Pengurangan/Kesalahan (nilai_a) --}}
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td
                                    class="px-6 md:px-2 lg:px-6 py-4 md:py-1 lg:py-4 text-left font-bold text-gray-700 border-r border-gray-100 text-sm">
                                    Pengurangan / Kesalahan
                                </td>
                                @for ($i = 1; $i <= 6; $i++)
                                    <td id="juri-{{ $i }}-nilai-a"
                                        class="px-4 md:px-2 lg:px-6 py-4 md:py-2 lg:py-4 font-mono text-red-500 font-semibold border-r border-gray-100 last:border-r-0">
                                        -{{ number_format($juriDb[$i]->nilai_a ?? 0, 2) }}
                                    </td>
                                @endfor
                            </tr>

                            {{-- Baris 2: Kemantapan (nilai_b) --}}
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td
                                    class="px-6 md:px-2 lg:px-6 py-4 md:py-1 lg:py-4 text-left font-bold text-gray-700 border-r border-gray-100 text-sm">
                                    Kemantapan
                                </td>
                                @for ($i = 1; $i <= 6; $i++)
                                    <td id="juri-{{ $i }}-nilai-b"
                                        class="px-4 md:px-2 lg:px-6 py-4 md:py-2 lg:py-4 font-mono text-blue-600 font-semibold border-r border-gray-100 last:border-r-0">
                                        +{{ number_format($juriDb[$i]->nilai_b ?? 0, 2) }}
                                    </td>
                                @endfor
                            </tr>

                            {{-- Baris 3: Total Nilai (skor_akhir_juri) --}}
                            <tr class="hover:bg-gray-50/50 transition-colors bg-gray-50/50 border-t border-indigo-100">
                                <td
                                    class="px-6 md:px-2 lg:px-6 py-5 md:py-1 lg:py-5 text-left font-black text-gray-900 border-r border-gray-100 text-sm">
                                    Total Nilai
                                </td>
                                @for ($i = 1; $i <= 6; $i++)
                                    @php
                                        // Kalkulasi PHP Regu: Default 9.90 - Kesalahan + Kemantapan
                                        $nilaiA = $juriDb[$i]->nilai_a ?? 0;
                                        $nilaiB = $juriDb[$i]->nilai_b ?? 0;
                                        $totalNilai = 9.9 - $nilaiA + $nilaiB;
                                    @endphp
                                    <td id="juri-{{ $i }}-skor-akhir"
                                        class="px-4 md:px-2 lg:px-6 py-5 md:py-2 lg:py-5 font-mono text-lg font-black text-gray-900 border-r border-gray-100 last:border-r-0">
                                        {{ number_format($totalNilai, 2) }}
                                    </td>
                                @endfor
                            </tr>

                            {{-- MENGAMBIL DATA KONTROL DARI DATABASE SAAT PERTAMA DIMUAT --}}
                            @php
                                $kontrolDb = \Illuminate\Support\Facades\DB::table('status_kontrol_juri')
                                    ->where('id_partai', $partai->id_partai)
                                    ->orderBy('no_juri', 'asc')
                                    ->get()
                                    ->keyBy('no_juri');
                            @endphp

                            {{-- Baris 4: Toggle Status Juri (Tombol Interaktif) --}}
                            <tr class="hover:bg-gray-50/50 transition-colors border-t border-gray-200">
                                <td
                                    class="px-6 md:px-2 lg:px-6 py-5 md:py-1 lg:py-5 text-left font-bold text-gray-700 border-r border-gray-100 text-sm">
                                    Toggle Status Juri
                                </td>
                                @for ($i = 1; $i <= 6; $i++)
                                    @php $isOn = $kontrolDb[$i]->is_on ?? 0; @endphp
                                    <td
                                        class="px-4 md:px-2 lg:px-6 py-4 md:py-2 lg:py-4 border-r border-gray-100 last:border-r-0 text-center">
                                        <button id="btn-toggle-juri-{{ $i }}"
                                            onclick="toggleJuri({{ $i }})"
                                            class="px-5 py-2 rounded-lg font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-sm {{ $isOn == 1 ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white' }}">
                                            {{ $isOn == 1 ? 'ON' : 'OFF' }}
                                        </button>
                                    </td>
                                @endfor
                            </tr>

                            {{-- Baris 5: Status Penilaian (Badge Info) --}}
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td
                                    class="px-6 md:px-2 lg:px-6 py-5 md:py-1 lg:py-5 text-left font-bold text-gray-700 border-r border-gray-100 text-sm">
                                    Status Penilaian
                                </td>
                                @for ($i = 1; $i <= 6; $i++)
                                    @php $statusInput = $kontrolDb[$i]->status_input ?? 'Proses'; @endphp
                                    <td
                                        class="px-4 md:px-2 lg:px-6 py-4 md:py-2 lg:py-4 border-r border-gray-100 last:border-r-0 text-center">
                                        <button id="badge-status-juri-{{ $i }}"
                                            onclick="toggleStatusInput({{ $i }})"
                                            class="px-4 py-1.5 rounded-lg font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-sm {{ strtolower($statusInput) == 'selesai' ? 'bg-blue-600 text-white' : 'bg-orange-500 text-white' }}">
                                            {{ $statusInput }}
                                        </button>
                                    </td>
                                @endfor
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="hidden lg:flex mt-6 justify-end gap-3 h-14">
                {{-- Form untuk mengunci nilai dan menyelesaikan pertandingan --}}
                <form
                    action="{{ route('admin.dewan.regu.selesai', ['id' => $partai->id_partai, 'sudut' => strtolower($aktif->sudut)]) }}"
                    method="POST" class="h-full" onsubmit="return konfirmasiSelesai(event, this)">
                    @csrf
                    <button type="submit"
                        class="h-full bg-gray-900 text-white px-10 rounded-2xl font-black uppercase tracking-widest hover:bg-black transition-all shadow-xl active:scale-95 flex items-center justify-center">
                        Selesai & Kunci Nilai
                    </button>
                </form>

                {{-- TOMBOL REFRESH --}}
                <button type="button" onclick="refreshLayarDisplay()"
                    class="shrink-0 aspect-square h-full bg-blue-600 text-white rounded-2xl shadow-xl hover:bg-blue-700 transition-all active:scale-95 flex items-center justify-center"
                    title="Refresh Layar Display Penonton">
                    {{-- Ukuran icon saya besarkan sedikit (w-6 h-6) agar pas di Desktop --}}
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        function konfirmasiSelesai(event, form) {
            // 1. Hentikan form agar tidak langsung ter-submit
            event.preventDefault();

            // 2. Tampilkan SweetAlert2
            Swal.fire({
                title: 'Selesaikan Partai?',
                text: "Apakah Anda yakin ingin menyelesaikan partai ini? Nilai juri akan dikunci secara permanen dan layar akan kembali Standby.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#22c55e', // Warna hijau sukses
                cancelButtonColor: '#ef4444', // Warna merah batal
                confirmButtonText: 'Ya, Selesai & Kunci!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                backdrop: `rgba(0,0,0,0.6)`
            }).then((result) => {
                if (result.isConfirmed) {
                    // 3. Tampilkan efek loading agar antarmuka tidak terasa macet saat submit
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Merekap nilai dan mengunci layar...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // 4. Lanjutkan submit form ke Controller
                    form.submit();
                }
            });
        }

        function refreshLayarDisplay() {
            // Tampilkan loading di cursor agar UI terasa responsif
            document.body.style.cursor = 'wait';

            fetch("{{ route('admin.dewan.regu.refresh') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    document.body.style.cursor = 'default';
                    if (data.status === 'success') {
                        // Tampilan Sukses dengan SweetAlert2 (Tertutup otomatis dalam 2 detik)
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Sinyal refresh telah dikirim ke Layar Display.',
                            timer: 2000,
                            showConfirmButton: false,
                            backdrop: `rgba(0,0,0,0.6)`
                        });
                    }
                })
                .catch(err => {
                    document.body.style.cursor = 'default';
                    console.error('Error:', err);

                    // Tampilan Error dengan SweetAlert2
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengirim Sinyal',
                        text: 'Pastikan koneksi jaringan/WiFi Anda stabil dan coba lagi.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Tutup'
                    });
                });
        }
    </script>
@endsection
<script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
<script>
    // --- VARIABEL UTAMA TIMER ---
    let seconds = {{ $aktif->waktu_tampil ?? 0 }};
    let isRunning = {{ ($aktif->is_timer_running ?? 0) == 1 ? 'true' : 'false' }};
    let timerInterval;

    function formatTime(totalSeconds) {
        if (isNaN(totalSeconds) || totalSeconds < 0) totalSeconds = 0;
        const mins = Math.floor(totalSeconds / 60);
        const secs = totalSeconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    function updateDisplay() {
        const display = document.getElementById('timer-display');
        if (display) {
            display.innerText = formatTime(seconds);
        }
    }

    // ==========================================
    // AUTO-RESUME SAAT HALAMAN DI-REFRESH
    // ==========================================
    if (isRunning) {
        toggleButtonState(true);
        timerInterval = setInterval(() => {
            seconds++;
            updateDisplay();
        }, 1000);
    } else {
        toggleButtonState(false);
    }

    // ==========================================
    // 1. KONTROL LOKAL DEWAN (INSTAN & RESPONSIVE)
    // ==========================================
    function startTimer() {
        if (isRunning) return;

        isRunning = true;
        toggleButtonState(true);

        if (typeof timerInterval !== 'undefined') clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            seconds++;
            updateDisplay();
        }, 1000);

        updateTimerStatus('start');
    }

    function pauseTimer() {
        if (!isRunning) return;

        isRunning = false;
        toggleButtonState(false);
        if (typeof timerInterval !== 'undefined') clearInterval(timerInterval);

        updateTimerStatus('pause');
    }

    function updateTimerStatus(action) {
        const url =
            "{{ route('admin.manajemen.regu.update-timer', ['id' => $partai->id_partai, 'sudut' => $sudut]) }}";
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                waktu: seconds,
                status_running: (action === 'start' ? 1 : 0)
            })
        }).catch(err => console.error("Gagal konek ke server:", err));
    }

    function resetTimer() {
        Swal.fire({
            title: 'Reset Timer?',
            text: "Waktu akan dikembalikan ke 00:00",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Reset!'
        }).then((result) => {
            if (result.isConfirmed) {
                if (typeof timerInterval !== 'undefined') clearInterval(timerInterval);
                isRunning = false;
                seconds = 0;
                updateDisplay();
                toggleButtonState(false);

                const url =
                    "{{ route('admin.manajemen.regu.update-timer', ['id' => $partai->id_partai, 'sudut' => $sudut]) }}";

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        waktu: 0,
                        status_running: 0
                    })
                }).then(() => {
                    Swal.fire('Direset!', 'Waktu telah kembali ke 00:00.', 'success');
                });
            }
        });
    }

    function toggleButtonState(running) {
        const btnStart = document.getElementById('btn-start');
        const btnPause = document.getElementById('btn-pause');

        if (running && btnStart && btnPause) {
            btnStart.disabled = true;
            btnStart.classList.add('opacity-50', 'cursor-not-allowed');
            btnPause.disabled = false;
            btnPause.classList.remove('opacity-50', 'cursor-not-allowed');
        } else if (!running && btnStart && btnPause) {
            btnStart.disabled = false;
            btnStart.classList.remove('opacity-50', 'cursor-not-allowed');
            btnPause.disabled = true;
            btnPause.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    function updateHukuman(action) {
        // Mengambil data dari URL atau variabel yang dikirim controller
        const partyId = "{{ $partai->id_partai }}";
        const sudut = "{{ strtolower($sudut) }}";
        const url = `/admin/manajemen/regu/hukuman/${partyId}/${sudut}`;

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // 1. Update angka Hukuman di layar secara real-time
                    const newHukuman = parseFloat(data.new_value || 0);
                    document.getElementById('display-hukuman-ketua').innerText = newHukuman.toFixed(2);

                    // 2. Kalkulasi Instan Total Nilai (Median - Hukuman)
                    const elMedian = document.getElementById('display-median');
                    const elTotal = document.getElementById('display-total-nilai');

                    if (elMedian && elTotal) {
                        const currentMedian = parseFloat(elMedian.innerText) || 0;
                        const newTotal = currentMedian - newHukuman;
                        elTotal.innerText = newTotal.toFixed(3);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function toggleDisq() {
        const btn = document.getElementById('btn-diskualifikasi');
        const badge = document.getElementById('badge-disq');
        const label = document.getElementById('teks-disq');

        // CEK STATUS DARI LAYAR (Bukan dari variabel PHP yang statis)
        const sedangDisq = !badge.classList.contains('hidden');

        Swal.fire({
            title: sedangDisq ? 'Batalkan Diskualifikasi?' : 'Diskualifikasi Peserta?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Eksekusi!',
            cancelButtonText: 'Batal',
            borderRadius: '1.5rem',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(btn.getAttribute('data-url'), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // MANIPULASI UI LANGSUNG (IDENTIK DENGAN FITUR HUKUMAN)
                            if (data.is_disqualifikasi == 1) {
                                badge.classList.remove('hidden');
                                btn.classList.remove('bg-red-50', 'text-red-700', 'border-red-200');
                                btn.classList.add('bg-gray-800', 'text-white', 'border-gray-900');
                                label.innerText = 'UNDO DISQ';
                            } else {
                                badge.classList.add('hidden');
                                btn.classList.remove('bg-gray-800', 'text-white', 'border-gray-900');
                                btn.classList.add('bg-red-50', 'text-red-700', 'border-red-200');
                                label.innerText = 'DISQ';
                            }
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                timer: 800,
                                showConfirmButton: false
                            });
                        }
                    })
                    .catch(err => Swal.fire('Error', 'Gagal update database', 'error'));
            }
        });
    }

    // ==========================================
    // 2. REVERB LISTENER (SINKRONISASI AKTIF)
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Echo) {
            const channel = window.Echo.channel(`partai.regu.{{ $partai->id_partai }}`);

            // Listener 1: Timer (Koreksi jika ada selisih lag jaringan)
            // PENTING: Gunakan awalan titik (.)
            channel.listen('.TimerReguUpdated', (e) => {
                if (Math.abs(seconds - e.waktu) > 2) {
                    seconds = parseInt(e.waktu);
                    updateDisplay();
                }
            });

            // Listener 2: Nilai Juri Masuk
            channel.listen('.UpdateNilaiJuriRegu', (e) => {
                // Update Tabel Nilai A (Pengurangan)
                const elA = document.getElementById(`juri-${e.noJuri}-nilai-a`);
                if (elA && e.nilaiA !== undefined) {
                    elA.innerText = "-" + parseFloat(e.nilaiA).toFixed(2);
                    // Efek kedip merah saat pengurangan bertambah
                    // elA.classList.add('scale-110', 'text-red-700');
                    setTimeout(() => elA.classList.remove('scale-110', 'text-red-700'), 300);
                }

                // Update Tabel Nilai B (Kemantapan)
                const elB = document.getElementById(`juri-${e.noJuri}-nilai-b`);
                if (elB && e.nilaiB !== undefined) {
                    elB.innerText = "+" + parseFloat(e.nilaiB).toFixed(2);
                    // Efek kedip biru saat kemantapan bertambah
                    // elB.classList.add('scale-110', 'text-blue-800');
                    setTimeout(() => elB.classList.remove('scale-110', 'text-blue-800'), 300);
                }

                // Update Skor Akhir
                const elAkhir = document.getElementById(`juri-${e.noJuri}-skor-akhir`);
                if (elAkhir && e.skorAkhir !== undefined) {
                    elAkhir.innerText = parseFloat(e.skorAkhir).toFixed(2);
                } // <--- UBAH JADI 2

                // Update Panel Rekapitulasi Atas (Cek null agar tidak error jika controller tidak mengirimnya)
                if (e.median !== undefined && e.median !== null) {
                    const elMed = document.getElementById('display-median');
                    if (elMed) elMed.innerText = parseFloat(e.median).toFixed(3);

                    const elDev = document.getElementById('display-deviasi');
                    if (elDev) elDev.innerText = parseFloat(e.deviasi).toFixed(15);

                    const elTot = document.getElementById('display-total-nilai');
                    if (elTot) elTot.innerText = parseFloat(e.totalNilai).toFixed(3);
                }
            });

            // Listener 3: Kontrol ON/OFF dan Status Penilaian
            channel.listen('.ToggleKontrolJuriRegu', (e) => {
                // Update Tombol ON/OFF
                const btnToggle = document.getElementById(`btn-toggle-juri-${e.noJuri}`);
                if (btnToggle && e.isOn !== null && e.isOn !== undefined) {
                    if (e.isOn == 1) {
                        btnToggle.innerText = 'ON';
                        btnToggle.className =
                            'px-5 py-2 rounded-lg font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-sm bg-emerald-500 text-white';
                    } else {
                        btnToggle.innerText = 'OFF';
                        btnToggle.className =
                            'px-5 py-2 rounded-lg font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-sm bg-red-500 text-white';
                    }
                }

                // Update Badge Status (Proses/Selesai)
                const badgeStatus = document.getElementById(`badge-status-juri-${e.noJuri}`);
                if (badgeStatus && e.statusInput) {
                    badgeStatus.innerText = e.statusInput.toUpperCase();
                    if (e.statusInput.toLowerCase() === 'selesai') {
                        badgeStatus.className =
                            'px-4 py-1.5 rounded-lg font-black text-[10px] uppercase tracking-widest bg-blue-600 text-white shadow-sm transition-all active:scale-95';
                    } else {
                        badgeStatus.className =
                            'px-4 py-1.5 rounded-lg font-black text-[10px] uppercase tracking-widest bg-orange-500 text-white shadow-sm transition-all active:scale-95';
                    }
                }

                // Update Rekapitulasi (Dari sinyal Kontrol / Diskualifikasi)
                if (e.median !== null && e.median !== undefined) {
                    const elMed = document.getElementById('display-median');
                    if (elMed) elMed.innerText = parseFloat(e.median).toFixed(3);

                    const elDev = document.getElementById('display-deviasi');
                    if (elDev) elDev.innerText = parseFloat(e.deviasi).toFixed(15);

                    const elTot = document.getElementById('display-total-nilai');
                    if (elTot) {
                        if (e.isDisq == 1) {
                            elTot.innerText = "DISKUALIFIKASI";
                            elTot.classList.add('text-red-500'); // Merahkan teks jika diskualifikasi
                        } else {
                            elTot.innerText = parseFloat(e.totalNilai).toFixed(3);
                            elTot.classList.remove('text-red-500');
                        }
                    }
                }
            });

            // Listener 4: Hukuman dari Dewan
            channel.listen('.UpdateHukumanRegu', (e) => {
                const elHukuman = document.getElementById('display-hukuman-ketua');
                if (elHukuman && e.hukuman !== undefined) {
                    elHukuman.innerText = "-" + parseFloat(e.hukuman).toFixed(2);
                }

                const elTotal = document.getElementById('display-total-nilai');
                if (elTotal && e.totalNilai !== undefined) {
                    elTotal.innerText = parseFloat(e.totalNilai).toFixed(3);
                }
            });

        } else {
            console.warn('Laravel Echo tidak ditemukan. Pastikan Echo sudah di-setup dengan benar.');
        }
    });

    function toggleJuri(noJuri) {
        const partyId = "{{ $partai->id_partai }}";
        const sudut = "{{ strtolower($sudut) }}"; // Tambahan variabel sudut
        const btn = document.getElementById(`btn-toggle-juri-${noJuri}`);

        // Manipulasi instan
        const isCurrentlyOn = btn.innerText.trim() === 'ON';
        if (!isCurrentlyOn) {
            btn.innerText = 'ON';
            btn.className =
                'px-5 py-2 rounded-lg font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-sm bg-emerald-500 text-white';
        } else {
            btn.innerText = 'OFF';
            btn.className =
                'px-5 py-2 rounded-lg font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-sm bg-red-500 text-white';
        }

        // Tembak perintah ke Server dengan body "sudut"
        fetch(`/admin/manajemen/regu/toggle-juri/${partyId}/${noJuri}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                sudut: sudut
            }) // Ditangkap Request $request di PHP
        }).catch(err => console.error("Gagal toggle juri:", err));
    }
    // ==========================================
    // 3. FUNGSI KONTROL DEWAN (OPTIMISTIC UI)
    // ==========================================

    function toggleStatusInput(noJuri) {
        const partyId = "{{ $partai->id_partai }}";
        const sudut = "{{ strtolower($sudut) }}";
        const btn = document.getElementById(`badge-status-juri-${noJuri}`);
        const currentStatus = btn.innerText.trim();

        // Manipulasi instan
        const nextStatus = (currentStatus.toLowerCase() === 'selesai') ? 'Proses' : 'Selesai';
        btn.innerText = nextStatus.toUpperCase();

        if (nextStatus.toLowerCase() === 'selesai') {
            btn.className =
                'px-4 py-1.5 rounded-lg font-black text-[10px] uppercase tracking-widest bg-blue-600 text-white shadow-sm transition-all active:scale-95';
        } else {
            btn.className =
                'px-4 py-1.5 rounded-lg font-black text-[10px] uppercase tracking-widest bg-orange-500 text-white shadow-sm transition-all active:scale-95';
        }

        // Tembak perintah ke Server
        fetch(`/admin/manajemen/regu/toggle-status-input/${partyId}/${noJuri}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                status: nextStatus,
                sudut: sudut
            })
        }).catch(err => console.error("Gagal toggle status:", err));
    }
</script>
