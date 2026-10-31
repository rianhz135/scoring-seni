@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hasil Pertandingan: Tanding</h1>
            <p class="text-sm text-gray-500 mt-1">Rekapitulasi nilai pertandingan dan audit input juri kategori tanding.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.hasil.tanding.export-pdf', request()->query()) }}" target="_blank"
                class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export PDF
            </a>
        </div>
    </div>

    {{-- FILTER & PENCARIAN --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.hasil.tanding.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Pertandingan</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari No. Partai, Kelas, Nama Atlet, Kontingen..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all pl-10">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status Pertandingan</label>
                <select name="status" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
                    <option value="">-- Semua Status --</option>
                    <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Berlangsung" {{ request('status') === 'Berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                    <option value="Belum" {{ request('status') === 'Belum' ? 'selected' : '' }}>Belum</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Per Halaman</label>
                <select name="per_page" onchange="this.form.submit()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all font-semibold">
                    <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5 data</option>
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 data</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 data</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 data</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 px-4 rounded-xl text-sm shadow-sm transition-all flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'per_page']))
                    <a href="{{ route('admin.hasil.tanding.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-2 px-3 rounded-xl text-sm transition-all flex items-center justify-center" title="Reset Filter">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABEL HASIL TANDING --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 uppercase text-center text-[10px] font-bold text-gray-400 tracking-wider">
                        <th class="px-4 py-3.5 w-12">No</th>
                        <th class="px-4 py-3.5 w-20">Partai</th>
                        <th class="px-4 py-3.5 w-32 text-left">Kelas / Babak</th>
                        <th class="px-4 py-3.5 text-left">Sudut Biru</th>
                        <th class="px-4 py-3.5 text-center w-14">Skor</th>
                        <th class="px-4 py-3.5 text-left">Sudut Merah</th>
                        <th class="px-4 py-3.5 text-left w-48">Pemenang</th>
                        <th class="px-4 py-3.5 text-center w-28">Status</th>
                        <th class="px-4 py-3.5 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse ($partais as $index => $partai)
                        @php
                            $scores = $partaiScores[$partai->id] ?? null;
                            $scoreBiru = $scores['biru']['total'] ?? 0;
                            $scoreMerah = $scores['merah']['total'] ?? 0;
                            $pemenangSudut = $partai->sudut_pemenang;
                            $isSelesai = $partai->status_pertandingan === 'Selesai';
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            {{-- NO --}}
                            <td class="px-4 py-4 text-center text-xs text-gray-500 font-medium">
                                {{ $partais->firstItem() + $index }}
                            </td>

                            {{-- PARTAI --}}
                            <td class="px-4 py-4 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-lg bg-gray-100 text-gray-800 font-extrabold text-xs">
                                    {{ $partai->no_partai }}
                                </span>
                            </td>

                            {{-- KELAS / BABAK --}}
                            <td class="px-4 py-4">
                                <div class="font-bold text-gray-900 text-xs">{{ $partai->kelas ?? '-' }}</div>
                                <div class="text-[11px] text-gray-500">{{ $partai->fase_babak ?? 'Penyisihan' }}</div>
                            </td>

                            {{-- SUDUT BIRU --}}
                            <td class="px-4 py-4">
                                <div class="p-2.5 rounded-xl border {{ $pemenangSudut === 'Biru' ? 'bg-blue-50/70 border-blue-200' : 'bg-gray-50/50 border-gray-100' }}">
                                    <div class="flex items-center gap-1.5 mb-0.5">
                                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                        <span class="text-[10px] font-bold text-blue-700 uppercase">Sudut Biru</span>
                                        @if($pemenangSudut === 'Biru')
                                            <span class="ml-auto text-[9px] bg-yellow-400 text-yellow-950 font-black px-1.5 py-0.5 rounded-md uppercase">Menang</span>
                                        @endif
                                    </div>
                                    <div class="font-bold text-xs text-gray-900 truncate">
                                        {{ $partai->pesertaBiru->nama ?? 'Belum Ditentukan' }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 truncate">
                                        {{ $partai->pesertaBiru->kontingen ?? '-' }}
                                    </div>
                                </div>
                            </td>

                            {{-- SKOR --}}
                            <td class="px-4 py-4 text-center font-black">
                                <div class="flex items-center justify-center gap-1 text-sm bg-gray-100/80 px-2 py-1.5 rounded-xl border border-gray-200">
                                    <span class="{{ $scoreBiru > $scoreMerah ? 'text-blue-700 font-extrabold' : 'text-gray-600' }}">{{ $scoreBiru }}</span>
                                    <span class="text-gray-400 text-xs">-</span>
                                    <span class="{{ $scoreMerah > $scoreBiru ? 'text-red-700 font-extrabold' : 'text-gray-600' }}">{{ $scoreMerah }}</span>
                                </div>
                            </td>

                            {{-- SUDUT MERAH --}}
                            <td class="px-4 py-4">
                                <div class="p-2.5 rounded-xl border {{ $pemenangSudut === 'Merah' ? 'bg-red-50/70 border-red-200' : 'bg-gray-50/50 border-gray-100' }}">
                                    <div class="flex items-center gap-1.5 mb-0.5">
                                        <span class="w-2 h-2 rounded-full bg-red-600"></span>
                                        <span class="text-[10px] font-bold text-red-700 uppercase">Sudut Merah</span>
                                        @if($pemenangSudut === 'Merah')
                                            <span class="ml-auto text-[9px] bg-yellow-400 text-yellow-950 font-black px-1.5 py-0.5 rounded-md uppercase">Menang</span>
                                        @endif
                                    </div>
                                    <div class="font-bold text-xs text-gray-900 truncate">
                                        {{ $partai->pesertaMerah->nama ?? 'Belum Ditentukan' }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 truncate">
                                        {{ $partai->pesertaMerah->kontingen ?? '-' }}
                                    </div>
                                </div>
                            </td>

                            {{-- PEMENANG --}}
                            <td class="px-4 py-4">
                                @if($isSelesai && $pemenangSudut)
                                    <div class="flex items-start gap-1.5">
                                        <div class="mt-0.5">
                                            @if($pemenangSudut === 'Biru')
                                                <span class="inline-block px-2 py-0.5 rounded-md bg-blue-600 text-white text-[10px] font-black uppercase shadow-xs">BIRU</span>
                                            @else
                                                <span class="inline-block px-2 py-0.5 rounded-md bg-red-600 text-white text-[10px] font-black uppercase shadow-xs">MERAH</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-xs text-gray-900 truncate">
                                                {{ $partai->pemenang->nama ?? ($pemenangSudut === 'Biru' ? ($partai->pesertaBiru->nama ?? '-') : ($partai->pesertaMerah->nama ?? '-')) }}
                                            </div>
                                            <div class="text-[10px] text-gray-500 leading-tight">
                                                {{ $partai->alasan_pemenang ?? 'Keputusan Wasit / Dewan' }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Belum Ada</span>
                                @endif
                            </td>

                            {{-- STATUS --}}
                            <td class="px-4 py-4 text-center">
                                @if($partai->status_pertandingan === 'Selesai')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Selesai
                                    </span>
                                @elseif($partai->status_pertandingan === 'Berlangsung')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Berlangsung
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">
                                        Belum
                                    </span>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 py-4 text-center">
                                <button type="button" onclick="bukaModalDetail({{ $partai->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-50 text-primary-700 hover:bg-primary-100 font-bold text-xs rounded-xl border border-primary-200 transition-all shadow-xs hover:scale-105 active:scale-95">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Lihat
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-semibold text-gray-500">Tidak ada data pertandingan tanding yang ditemukan.</p>
                                <p class="text-xs text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter status.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINASI --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $partais->links() }}
        </div>
    </div>

    {{-- MODAL DETAIL PERTANDINGAN & INPUT JURI --}}
    <div id="modal-detail-tanding"
        class="fixed inset-0 z-50 hidden bg-gray-900/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6 overflow-y-auto">
        <div class="bg-white rounded-3xl shadow-2xl max-w-5xl w-full max-h-[92vh] flex flex-col overflow-hidden transform scale-95 transition-all duration-300 border border-gray-100">
            
            {{-- MODAL HEADER --}}
            <div class="bg-gray-900 px-6 py-4 flex items-center justify-between text-white shrink-0">
                <div class="flex items-center gap-3">
                    <span id="modal-partai-badge" class="px-3 py-1 rounded-lg bg-primary-600 text-white font-extrabold text-sm tracking-wide">
                        PARTAI -
                    </span>
                    <div>
                        <h2 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                            Detail Pertandingan & Input Juri
                        </h2>
                        <p id="modal-subheading" class="text-xs text-gray-400">Kelas - • Fase -</p>
                    </div>
                </div>
                <button type="button" onclick="tutupModalDetail()" class="text-gray-400 hover:text-white hover:bg-gray-800 p-2 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- MODAL CONTENT (SCROLLABLE) --}}
            <div class="p-6 overflow-y-auto flex-1 space-y-6 text-gray-800 bg-gray-50/50">
                
                {{-- LOADING SPINNER --}}
                <div id="modal-loading" class="py-16 text-center">
                    <div class="inline-block w-10 h-10 border-4 border-primary-500 border-t-transparent rounded-full animate-spin mb-3"></div>
                    <p class="text-sm font-semibold text-gray-500">Memuat rincian pertandingan & log input juri...</p>
                </div>

                {{-- MODAL DATA WRAPPER --}}
                <div id="modal-body-data" class="hidden space-y-6">

                    {{-- 1. WINNER BANNER --}}
                    <div id="winner-banner" class="winner-banner rounded-2xl p-4 text-white shadow-md flex flex-col sm:flex-row items-center justify-between gap-3 transition-all duration-300 relative overflow-hidden">
                        {{-- Dekorasi Garis Putih Transparan seperti di Display --}}
                        <div class="absolute top-0 right-0 w-48 h-full bg-white/10 skew-x-12 translate-x-12 pointer-events-none"></div>

                        <div class="flex items-center gap-3.5 relative z-10">
                            <div id="modal-pemenang-icon-box" class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center shrink-0">
                                <svg id="modal-pemenang-trophy-icon" class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <div>
                                <span id="modal-pemenang-badge" class="text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-full inline-block">PEMENANG PERTANDINGAN</span>
                                <h3 id="modal-pemenang-nama" class="text-lg font-black tracking-tight leading-tight mt-0.5 text-white">Nama Pemenang</h3>
                                <p id="modal-pemenang-kontingen" class="text-xs font-bold mt-0.5">Kontingen</p>
                            </div>
                        </div>
                        <div id="modal-pemenang-alasan-wrapper" class="text-right sm:border-l sm:pl-4 relative z-10">
                            <span id="modal-pemenang-alasan-label" class="text-[10px] font-bold uppercase block">Kriteria / Alasan:</span>
                            <span id="modal-pemenang-alasan" class="text-xs font-black px-3 py-1 rounded-lg inline-block mt-0.5">Menang berdasarkan total nilai</span>
                        </div>
                    </div>

                    {{-- 2. SCOREBOARD KOMPARASI NILAI (BIRU VS MERAH) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- CARD SUDUT BIRU --}}
                        <div class="bg-white rounded-2xl p-5 border-2 border-blue-500/80 shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 right-0 bg-blue-600 text-white text-[10px] font-black px-4 py-1 rounded-bl-xl tracking-wider">
                                SUDUT BIRU
                            </div>
                            <div class="mb-4">
                                <h4 id="detail-biru-nama" class="text-base font-black text-gray-900 leading-tight">NAMA ATLET</h4>
                                <p id="detail-biru-kontingen" class="text-xs font-semibold text-gray-500">KONTINGEN</p>
                            </div>
                            <div class="flex items-center justify-between bg-blue-50 p-4 rounded-xl border border-blue-100 mb-4">
                                <span class="text-xs font-bold text-blue-900 uppercase">Total Nilai Akhir</span>
                                <span id="detail-biru-total" class="text-3xl font-black text-blue-600">0</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                                <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                                    <span class="text-[10px] font-bold text-gray-400 block uppercase">Poin Masuk</span>
                                    <span id="detail-biru-poin-masuk" class="text-sm font-black text-gray-800">0</span>
                                </div>
                                <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                                    <span class="text-[10px] font-bold text-gray-400 block uppercase">Jatuhan</span>
                                    <span id="detail-biru-jatuhan" class="text-sm font-black text-gray-800">0</span>
                                </div>
                                <div class="bg-red-50 p-2.5 rounded-xl border border-red-100">
                                    <span class="text-[10px] font-bold text-red-400 block uppercase">Hukuman</span>
                                    <span id="detail-biru-hukuman" class="text-sm font-black text-red-600">0</span>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-600">
                                <span>Binaan: <strong id="detail-biru-binaan" class="text-gray-900">0</strong></span>
                                <span>Teguran: <strong id="detail-biru-teguran" class="text-gray-900">0</strong></span>
                                <span>Peringatan: <strong id="detail-biru-peringatan" class="text-gray-900">0</strong></span>
                            </div>
                        </div>

                        {{-- CARD SUDUT MERAH --}}
                        <div class="bg-white rounded-2xl p-5 border-2 border-red-500/80 shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 right-0 bg-red-600 text-white text-[10px] font-black px-4 py-1 rounded-bl-xl tracking-wider">
                                SUDUT MERAH
                            </div>
                            <div class="mb-4">
                                <h4 id="detail-merah-nama" class="text-base font-black text-gray-900 leading-tight">NAMA ATLET</h4>
                                <p id="detail-merah-kontingen" class="text-xs font-semibold text-gray-500">KONTINGEN</p>
                            </div>
                            <div class="flex items-center justify-between bg-red-50 p-4 rounded-xl border border-red-100 mb-4">
                                <span class="text-xs font-bold text-red-900 uppercase">Total Nilai Akhir</span>
                                <span id="detail-merah-total" class="text-3xl font-black text-red-600">0</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                                <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                                    <span class="text-[10px] font-bold text-gray-400 block uppercase">Poin Masuk</span>
                                    <span id="detail-merah-poin-masuk" class="text-sm font-black text-gray-800">0</span>
                                </div>
                                <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                                    <span class="text-[10px] font-bold text-gray-400 block uppercase">Jatuhan</span>
                                    <span id="detail-merah-jatuhan" class="text-sm font-black text-gray-800">0</span>
                                </div>
                                <div class="bg-red-50 p-2.5 rounded-xl border border-red-100">
                                    <span class="text-[10px] font-bold text-red-400 block uppercase">Hukuman</span>
                                    <span id="detail-merah-hukuman" class="text-sm font-black text-red-600">0</span>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-600">
                                <span>Binaan: <strong id="detail-merah-binaan" class="text-gray-900">0</strong></span>
                                <span>Teguran: <strong id="detail-merah-teguran" class="text-gray-900">0</strong></span>
                                <span>Peringatan: <strong id="detail-merah-peringatan" class="text-gray-900">0</strong></span>
                            </div>
                        </div>
                    </div>

                    {{-- 3. PER-BABAK BREAKDOWN TABLE --}}
                    <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-xs">
                        <h4 class="text-xs font-black text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Rincian Poin Tiap Babak
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-center text-xs border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-600 font-bold uppercase text-[10px]">
                                        <th class="py-2 px-3 text-left">Babak</th>
                                        <th class="py-2 px-3 text-blue-700 bg-blue-50/70 border-x border-gray-200" colspan="3">Sudut Biru</th>
                                        <th class="py-2 px-3 text-red-700 bg-red-50/70" colspan="3">Sudut Merah</th>
                                    </tr>
                                    <tr class="bg-gray-50 text-gray-500 text-[9px] font-semibold uppercase border-b border-gray-200">
                                        <th class="py-1 px-3 text-left"></th>
                                        <th class="py-1 px-2 text-blue-600">Poin Masuk</th>
                                        <th class="py-1 px-2 text-blue-600">Jatuhan</th>
                                        <th class="py-1 px-2 text-blue-600 border-r border-gray-200">Hukuman</th>
                                        <th class="py-1 px-2 text-red-600">Poin Masuk</th>
                                        <th class="py-1 px-2 text-red-600">Jatuhan</th>
                                        <th class="py-1 px-2 text-red-600">Hukuman</th>
                                    </tr>
                                </thead>
                                <tbody id="detail-babak-tbody" class="divide-y divide-gray-100">
                                    {{-- Generated via JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 4. STATISTIK PER JURI (JURI 1, 2, 3) --}}
                    <div>
                        <h4 class="text-xs font-black text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Ringkasan Input & Konsensus Juri
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3" id="juri-stats-cards">
                            {{-- Generated via JS --}}
                        </div>
                    </div>

                    {{-- 5. AUDIT LOG LENGKAP INPUT JURI --}}
                    <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-xs">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                            <h4 class="text-xs font-black text-gray-700 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Log Seluruh Input Juri (<span id="total-log-count">0</span> Kejadian)
                            </h4>
                            <div class="flex items-center gap-1.5" id="log-filter-babak">
                                <button type="button" onclick="filterLogs('all')" class="log-filter-btn px-2.5 py-1 rounded-lg text-xs font-bold bg-primary-600 text-white" data-babak="all">Semua</button>
                                <button type="button" onclick="filterLogs(1)" class="log-filter-btn px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200" data-babak="1">Babak 1</button>
                                <button type="button" onclick="filterLogs(2)" class="log-filter-btn px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200" data-babak="2">Babak 2</button>
                                <button type="button" onclick="filterLogs(3)" class="log-filter-btn px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200" data-babak="3">Babak 3</button>
                            </div>
                        </div>

                        <div class="overflow-x-auto max-h-72 border border-gray-100 rounded-xl">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="sticky top-0 bg-gray-100 z-10 text-[10px] font-bold text-gray-500 uppercase">
                                    <tr>
                                        <th class="py-2.5 px-3">Waktu</th>
                                        <th class="py-2.5 px-2 text-center">Babak</th>
                                        <th class="py-2.5 px-3 text-center">Juri</th>
                                        <th class="py-2.5 px-3 text-center">Sudut</th>
                                        <th class="py-2.5 px-3">Serangan / Poin</th>
                                        <th class="py-2.5 px-3 text-center">Status Masuk</th>
                                        <th class="py-2.5 px-3">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody id="detail-logs-tbody" class="divide-y divide-gray-100">
                                    {{-- Generated via JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            {{-- MODAL FOOTER --}}
            <div class="bg-gray-100 px-6 py-3 flex items-center justify-end shrink-0 border-t border-gray-200">
                <button type="button" onclick="tutupModalDetail()"
                    class="bg-gray-800 hover:bg-gray-900 text-white font-bold px-6 py-2 rounded-xl text-sm transition-all shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentDetailLogs = [];

        function bukaModalDetail(partaiId) {
            const modal = document.getElementById('modal-detail-tanding');
            const loading = document.getElementById('modal-loading');
            const bodyData = document.getElementById('modal-body-data');

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            bodyData.classList.add('hidden');

            fetch(`/admin/hasil/tanding/${partaiId}/detail`)
                .then(res => res.json())
                .then(res => {
                    if (!res.success) {
                        alert('Gagal mengambil data detail pertandingan.');
                        tutupModalDetail();
                        return;
                    }

                    renderModalData(res);
                    loading.classList.add('hidden');
                    bodyData.classList.remove('hidden');
                })
                .catch(err => {
                    console.error('Error fetching detail:', err);
                    alert('Terjadi kesalahan jaringan.');
                    tutupModalDetail();
                });
        }

        function tutupModalDetail() {
            document.getElementById('modal-detail-tanding').classList.add('hidden');
        }

        function renderModalData(data) {
            const p = data.partai;
            const scores = data.scores;
            const evaluasi = data.evaluasi;
            const juriStats = data.juri_stats;
            currentDetailLogs = data.logs || [];

            // Header info
            document.getElementById('modal-partai-badge').innerText = `PARTAI ${p.no_partai}`;
            document.getElementById('modal-subheading').innerText = `Kelas ${p.kelas || '-'} • Babak ${p.fase_babak || 'Penyisihan'}`;

            // Pemenang banner
            const winnerBanner = document.getElementById('winner-banner');
            const pemenangBadge = document.getElementById('modal-pemenang-badge');
            const pemenangNama = document.getElementById('modal-pemenang-nama');
            const pemenangKontingen = document.getElementById('modal-pemenang-kontingen');
            const pemenangAlasanWrapper = document.getElementById('modal-pemenang-alasan-wrapper');
            const pemenangAlasanLabel = document.getElementById('modal-pemenang-alasan-label');
            const pemenangAlasan = document.getElementById('modal-pemenang-alasan');
            const trophyIcon = document.getElementById('modal-pemenang-trophy-icon');
            const iconBox = document.getElementById('modal-pemenang-icon-box');

            if (p.sudut_pemenang) {
                winnerBanner.classList.remove('hidden');
                const isBiru = (p.sudut_pemenang.toLowerCase() === 'biru');
                const winData = p.pemenang || (isBiru ? p.peserta_biru : p.peserta_merah);
                const cornerColor = isBiru ? '#2563eb' : '#dc2626';

                pemenangNama.innerText = `SUDUT ${p.sudut_pemenang.toUpperCase()} - ${winData ? winData.nama : '-'}`;
                pemenangKontingen.innerText = winData ? winData.kontingen : '-';
                pemenangAlasan.innerText = p.alasan_pemenang || evaluasi.alasan || 'Menang Berdasarkan Aturan Pertandingan';

                // Terapkan warna sudut langsung ke SVG & Container agar tidak mewarisi text-white
                if (iconBox) iconBox.style.color = cornerColor;
                if (trophyIcon) {
                    trophyIcon.setAttribute('fill', cornerColor);
                    trophyIcon.style.fill = cornerColor;
                    trophyIcon.style.color = cornerColor;
                }

                if (isBiru) {
                    // Warna Sudut Biru (Solid #2563eb persis seperti di display)
                    winnerBanner.style.backgroundColor = '#2563eb';
                    winnerBanner.className = "winner-banner rounded-2xl p-4 text-white shadow-lg border border-blue-400/40 flex flex-col sm:flex-row items-center justify-between gap-3 transition-all duration-300 relative overflow-hidden";
                    if (pemenangBadge) pemenangBadge.className = "text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-full bg-blue-950/60 text-blue-100 border border-white/20 inline-block";
                    if (pemenangKontingen) pemenangKontingen.className = "text-xs font-bold text-blue-100 mt-0.5";
                    if (pemenangAlasanWrapper) pemenangAlasanWrapper.className = "text-right sm:border-l sm:border-white/20 sm:pl-4 relative z-10";
                    if (pemenangAlasanLabel) pemenangAlasanLabel.className = "text-[10px] font-bold uppercase text-blue-100/90 block";
                    if (pemenangAlasan) pemenangAlasan.className = "text-xs font-black bg-blue-950/60 text-white border border-white/20 px-3 py-1 rounded-lg inline-block mt-0.5";
                } else {
                    // Warna Sudut Merah (Solid #dc2626 persis seperti di display)
                    winnerBanner.style.backgroundColor = '#dc2626';
                    winnerBanner.className = "winner-banner rounded-2xl p-4 text-white shadow-lg border border-red-400/40 flex flex-col sm:flex-row items-center justify-between gap-3 transition-all duration-300 relative overflow-hidden";
                    if (pemenangBadge) pemenangBadge.className = "text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-full bg-red-950/60 text-red-100 border border-white/20 inline-block";
                    if (pemenangKontingen) pemenangKontingen.className = "text-xs font-bold text-red-100 mt-0.5";
                    if (pemenangAlasanWrapper) pemenangAlasanWrapper.className = "text-right sm:border-l sm:border-white/20 sm:pl-4 relative z-10";
                    if (pemenangAlasanLabel) pemenangAlasanLabel.className = "text-[10px] font-bold uppercase text-red-100/90 block";
                    if (pemenangAlasan) pemenangAlasan.className = "text-xs font-black bg-red-950/60 text-white border border-white/20 px-3 py-1 rounded-lg inline-block mt-0.5";
                }
            } else {
                winnerBanner.classList.add('hidden');
            }

            // Sudut Biru
            const biruNama = p.peserta_biru ? p.peserta_biru.nama : 'Belum Ditentukan';
            const biruKontingen = p.peserta_biru ? p.peserta_biru.kontingen : '-';
            document.getElementById('detail-biru-nama').innerText = biruNama;
            document.getElementById('detail-biru-kontingen').innerText = biruKontingen;
            document.getElementById('detail-biru-total').innerText = scores.biru.total;
            document.getElementById('detail-biru-poin-masuk').innerText = scores.biru.poin_masuk;
            document.getElementById('detail-biru-jatuhan').innerText = `${scores.biru.jatuhan_count} (${scores.biru.poin_jatuhan} pt)`;
            document.getElementById('detail-biru-hukuman').innerText = scores.biru.poin_minus;
            document.getElementById('detail-biru-binaan').innerText = scores.biru.binaan_count;
            document.getElementById('detail-biru-teguran').innerText = scores.biru.teguran_list.length;
            document.getElementById('detail-biru-peringatan').innerText = scores.biru.peringatan_list.length;

            // Sudut Merah
            const merahNama = p.peserta_merah ? p.peserta_merah.nama : 'Belum Ditentukan';
            const merahKontingen = p.peserta_merah ? p.peserta_merah.kontingen : '-';
            document.getElementById('detail-merah-nama').innerText = merahNama;
            document.getElementById('detail-merah-kontingen').innerText = merahKontingen;
            document.getElementById('detail-merah-total').innerText = scores.merah.total;
            document.getElementById('detail-merah-poin-masuk').innerText = scores.merah.poin_masuk;
            document.getElementById('detail-merah-jatuhan').innerText = `${scores.merah.jatuhan_count} (${scores.merah.poin_jatuhan} pt)`;
            document.getElementById('detail-merah-hukuman').innerText = scores.merah.poin_minus;
            document.getElementById('detail-merah-binaan').innerText = scores.merah.binaan_count;
            document.getElementById('detail-merah-teguran').innerText = scores.merah.teguran_list.length;
            document.getElementById('detail-merah-peringatan').innerText = scores.merah.peringatan_list.length;

            // Per-Babak Table
            const babakTbody = document.getElementById('detail-babak-tbody');
            babakTbody.innerHTML = '';
            for (let b = 1; b <= 3; b++) {
                const bData = scores.per_babak && scores.per_babak[b] ? scores.per_babak[b] : {
                    biru: { poin_masuk: 0, jatuhan: 0, hukuman: 0 },
                    merah: { poin_masuk: 0, jatuhan: 0, hukuman: 0 }
                };
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50';
                tr.innerHTML = `
                    <td class="py-2.5 px-3 text-left font-bold text-gray-700">Babak ${b}</td>
                    <td class="py-2.5 px-2 font-bold text-blue-700 bg-blue-50/30">${bData.biru.poin_masuk}</td>
                    <td class="py-2.5 px-2 text-gray-700 bg-blue-50/30">${bData.biru.jatuhan}</td>
                    <td class="py-2.5 px-2 text-red-600 bg-blue-50/30 border-r border-gray-200">-${bData.biru.hukuman}</td>
                    <td class="py-2.5 px-2 font-bold text-red-700 bg-red-50/30">${bData.merah.poin_masuk}</td>
                    <td class="py-2.5 px-2 text-gray-700 bg-red-50/30">${bData.merah.jatuhan}</td>
                    <td class="py-2.5 px-2 text-red-600 bg-red-50/30">-${bData.merah.hukuman}</td>
                `;
                babakTbody.appendChild(tr);
            }

            // Statistik 3 Juri
            const statsContainer = document.getElementById('juri-stats-cards');
            statsContainer.innerHTML = '';
            for (let j = 1; j <= 3; j++) {
                const s = juriStats[j] || { total_input: 0, biru_pukulan: 0, biru_tendangan: 0, merah_pukulan: 0, merah_tendangan: 0, input_sah: 0 };
                const col = document.createElement('div');
                col.className = 'bg-white rounded-xl p-3.5 border border-gray-200 shadow-xs';
                col.innerHTML = `
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-gray-100">
                        <span class="text-xs font-black text-gray-900 uppercase">Juri ${j}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-primary-100 text-primary-800">
                            ${s.total_input} Total Input
                        </span>
                    </div>
                    <div class="space-y-1.5 text-[11px]">
                        <div class="flex justify-between items-center bg-blue-50/80 px-2.5 py-1 rounded-lg">
                            <span class="text-blue-800 font-semibold">Biru: Pukulan / Tendangan</span>
                            <span class="font-bold text-blue-900">${s.biru_pukulan} / ${s.biru_tendangan}</span>
                        </div>
                        <div class="flex justify-between items-center bg-red-50/80 px-2.5 py-1 rounded-lg">
                            <span class="text-red-800 font-semibold">Merah: Pukulan / Tendangan</span>
                            <span class="font-bold text-red-900">${s.merah_pukulan} / ${s.merah_tendangan}</span>
                        </div>
                        <div class="flex justify-between items-center bg-green-50 px-2.5 py-1 rounded-lg">
                            <span class="text-green-800 font-semibold">Input Sah (Masuk Skor)</span>
                            <span class="font-black text-green-700">${s.input_sah}</span>
                        </div>
                    </div>
                `;
                statsContainer.appendChild(col);
            }

            // Logs input juri
            document.getElementById('total-log-count').innerText = currentDetailLogs.length;
            filterLogs('all');
        }

        function filterLogs(babak) {
            // Update filter button styling
            const buttons = document.querySelectorAll('.log-filter-btn');
            buttons.forEach(btn => {
                if (btn.getAttribute('data-babak') == babak) {
                    btn.className = 'log-filter-btn px-2.5 py-1 rounded-lg text-xs font-bold bg-primary-600 text-white';
                } else {
                    btn.className = 'log-filter-btn px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200';
                }
            });

            const logsTbody = document.getElementById('detail-logs-tbody');
            logsTbody.innerHTML = '';

            const filtered = babak === 'all' 
                ? currentDetailLogs 
                : currentDetailLogs.filter(l => l.babak == babak);

            if (filtered.length === 0) {
                logsTbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400 italic">
                            Tidak ada log input juri pada babak ini.
                        </td>
                    </tr>
                `;
                return;
            }

            filtered.forEach((l, idx) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50/80 transition-colors';

                const isBiru = l.sudut === 'Biru';
                const sudutBadge = isBiru
                    ? '<span class="inline-block px-2 py-0.5 rounded text-[10px] font-black bg-blue-100 text-blue-700">BIRU</span>'
                    : '<span class="inline-block px-2 py-0.5 rounded text-[10px] font-black bg-red-100 text-red-700">MERAH</span>';

                let statusBadge = '';
                if (l.status_input === 'Berhasil') {
                    statusBadge = '<span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-100 text-green-700">Berhasil (Sah)</span>';
                } else if (l.status_input === 'Belum Proses') {
                    statusBadge = '<span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-semibold bg-amber-100 text-amber-700">Menunggu</span>';
                } else {
                    statusBadge = `<span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-semibold bg-gray-100 text-gray-600">${l.status_input || '-'}</span>`;
                }

                tr.innerHTML = `
                    <td class="py-2 px-3 text-gray-500 font-mono text-[11px]">${l.time || '-'}</td>
                    <td class="py-2 px-2 text-center font-bold text-gray-700">B${l.babak}</td>
                    <td class="py-2 px-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded bg-gray-100 font-black text-gray-800 text-[10px]">Juri ${l.no_juri}</span>
                    </td>
                    <td class="py-2 px-3 text-center">${sudutBadge}</td>
                    <td class="py-2 px-3 font-semibold text-gray-800">
                        ${l.jenis_serangan} <span class="text-xs font-bold ${isBiru ? 'text-blue-600' : 'text-red-600'}">(+${l.poin})</span>
                    </td>
                    <td class="py-2 px-3 text-center">${statusBadge}</td>
                    <td class="py-2 px-3 text-[11px] text-gray-500">${l.keterangan || '-'}</td>
                `;
                logsTbody.appendChild(tr);
            });
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                tutupModalDetail();
            }
        });
    </script>
@endsection

