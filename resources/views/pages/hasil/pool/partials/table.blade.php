{{-- resources/views/pages/hasil/pool/partials/table.blade.php --}}

<div class="space-y-6">
    {{-- Header Tabel Utama --}}
    <div class="rounded-xl border border-gray-200 overflow-hidden shadow-sm bg-gray-50">
        <table class="w-full table-fixed text-left text-xs font-bold uppercase tracking-wide text-gray-700">
            <colgroup>
                <col style="width: 60px;"> {{-- Rank (Dipersempit sedikit) --}}
                <col style="width: 230px;"> {{-- Peserta / Kontingen --}}
                <col style="width: 80px;"> {{-- Waktu Tampil (Dipersempit) --}}
                <col style="width: 80px;"> {{-- Median (Dipersempit) --}}
                <col style="width: 220px;"> {{-- Deviasi (Diperlebar Maksimal) --}}
                <col style="width: 80px;"> {{-- Hukuman (Dipersempit, tidak butuh space besar) --}}
                <col> {{-- Total Nilai --}}
            </colgroup>
            <thead>
                <tr>
                    <th class="px-4 py-3 text-center">Rank</th>
                    <th class="px-4 py-3">Peserta & Kontingen</th>
                    <th class="px-4 py-3 text-center">Waktu</th>
                    <th class="px-4 py-3 text-center">Median</th>
                    <th class="px-4 py-3 text-center">Deviasi</th>
                    <th class="px-4 py-3 text-center text-red-500">Hukuman</th>
                    <th class="px-4 py-3 text-right pr-6">Total Nilai</th>
                </tr>
            </thead>
        </table>
    </div>

    @forelse($data as $partai)
        {{-- Card Container Per Partai --}}
        <div
            class="rounded-xl border border-gray-200 overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow">
            {{-- Label Nama Partai --}}
            <div class="bg-gray-50/80 border-b border-gray-100 px-4 py-3 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span
                        class="bg-blue-100 text-blue-700 px-3 py-1 rounded-md font-black text-xs uppercase tracking-wider">
                        Partai {{ $partai->no_partai }}
                    </span>
                    <span class="font-bold text-xs text-gray-600 uppercase">
                        Gelanggang {{ $partai->gelanggang }}
                    </span>
                </div>
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                    Kategori: {{ $partai->kategori->nama_kategori ?? '-' }}
                </span>
                <button
                    onclick="tampilkanPemenangPool({{ $partai->id_partai }}, '{{ $partai->no_partai }}', '{{ $partai->kategori->nama_kategori ?? '-' }}')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 shadow-sm transition-all active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                    Tampilkan Pemenang
                </button>
            </div>

            <table class="w-full table-fixed text-left text-sm">
                <colgroup>
                    <col style="width: 60px;">
                    <col style="width: 230px;">
                    <col style="width: 80px;">
                    <col style="width: 80px;">
                    <col style="width: 220px;">
                    <col style="width: 80px;">
                    <col>
                </colgroup>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($partai->detail_peserta as $index => $row)
                        @php
                            $rank = $loop->iteration;
                            $isDisq = $row->is_disqualifikasi == 1;
                        @endphp

                        {{-- Warnai baris merah sedikit jika dia diskualifikasi --}}
                        <tr
                            class="{{ $isDisq ? 'bg-red-50/40 hover:bg-red-100/50' : 'hover:bg-blue-50/30' }} transition-colors">

                            {{-- Kolom Medali / Peringkat --}}
                            <td class="px-4 py-4 text-center">
                                @if ($isDisq)
                                    <span
                                        class="text-[10px] font-black text-red-600 bg-red-100 px-2 py-1 rounded border border-red-200">DISQ</span>
                                @elseif ($rank == 1)
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 font-black text-sm border border-yellow-200 shadow-sm">1</span>
                                @elseif($rank == 2)
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-black text-sm border border-slate-200 shadow-sm">2</span>
                                @elseif($rank == 3)
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-800 font-black text-sm border border-orange-200 shadow-sm">3</span>
                                @else
                                    <span class="text-sm font-bold text-gray-400">#{{ $rank }}</span>
                                @endif
                            </td>

                            {{-- Identitas Peserta --}}
                            <td class="px-4 py-4">
                                <div
                                    class="font-bold text-gray-900 uppercase tracking-wide text-xs truncate {{ $isDisq ? 'line-through opacity-70' : '' }}">
                                    {{ $row->peserta->nama_peserta ?? 'NAMA KOSONG' }}
                                </div>
                                <div
                                    class="text-[10px] text-gray-500 font-bold uppercase mt-1 tracking-wider flex items-center gap-1 truncate">
                                    <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $row->peserta->kontingen ?? '-' }}
                                </div>
                            </td>

                            {{-- Format Waktu --}}
                            <td class="px-4 py-4 text-center">
                                @php
                                    $rawWaktu = $row->waktu_tampil;
                                    if (is_numeric($rawWaktu)) {
                                        $menit = floor($rawWaktu / 60);
                                        $detik = $rawWaktu % 60;
                                        $waktuFormatted = sprintf('%02d:%02d', $menit, $detik);
                                    } elseif (empty($rawWaktu)) {
                                        $waktuFormatted = '00:00';
                                    } else {
                                        $waktuFormatted = $rawWaktu;
                                    }
                                @endphp
                                <span
                                    class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded font-mono text-xs font-bold border border-gray-200">
                                    {{ $waktuFormatted }}
                                </span>
                            </td>

                            {{-- Nilai Median --}}
                            <td class="px-4 py-4 text-center font-black text-gray-700">
                                {{ number_format($row->nilai_median, 3) }}
                            </td>

                            {{-- Nilai Deviasi (DIPERBAIKI: Hapus pemaksa baris baru, gunakan tracking-tighter agar huruf agak rapat) --}}
                            <td
                                class="px-4 py-4 text-center font-black text-gray-700 whitespace-nowrap tracking-tighter">
                                {{ $row->nilai_deviasi }}
                            </td>

                            {{-- Nilai Hukuman --}}
                            <td class="px-4 py-4 text-center text-red-600 font-black text-sm">
                                -{{ number_format($row->hukuman_ketua, 2) }}
                            </td>

                            {{-- Total Skor Akhir --}}
                            <td class="px-4 py-4 text-right pr-6">
                                @if ($isDisq)
                                    <span class="text-base font-black text-red-600 tracking-wider">
                                        DISQ
                                    </span>
                                @else
                                    <span class="text-base font-black text-blue-600 tracking-wider">
                                        {{ number_format($row->total_nilai, 3) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        {{-- Tampilan jika data kosong --}}
        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-16 text-center shadow-sm">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <h3 class="text-base font-bold text-gray-600">Belum Ada Hasil Resmi</h3>
            <p class="text-sm text-gray-400 mt-1">Sistem akan menampilkan rekapitulasi setelah Dewan menekan tombol
                'Selesai'.</p>
        </div>
    @endforelse
</div>
