@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hasil Pertandingan: Solo</h1>
            <p class="text-sm text-gray-500 mt-1">Rekapitulasi nilai pertandingan kategori solo.</p>
        </div>
        <a href="{{ route('admin.hasil.solo.export-pdf') }}" target="_blank"
            class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export PDF
        </a>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1100px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 uppercase text-center">
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest w-12">No</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest w-16">Partai</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest w-24 text-left">Kelas</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest">Komparasi Nilai Sudut
                            (Biru vs Merah)</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest w-32">Pemenang</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($partais as $index => $partai)
                        @php
                            // Ambil data sudut biru dan merah
                            $biru =
                                $partai->detail_peserta->firstWhere('sudut', 'Biru') ??
                                $partai->detail_peserta->firstWhere('sudut', 'biru');
                            $merah =
                                $partai->detail_peserta->firstWhere('sudut', 'Merah') ??
                                $partai->detail_peserta->firstWhere('sudut', 'merah');

                            // Cek status diskualifikasi (fallback antisipasi typo di database)
                            $disqBiru = $biru ? $biru->is_disqualifikasi ?? ($biru->is_disqulifikasi ?? 0) : 0;
                            $disqMerah = $merah ? $merah->is_disqualifikasi ?? ($merah->is_disqulifikasi ?? 0) : 0;
                        @endphp

                        <tr class="hover:bg-gray-50/50 transition-colors uppercase">
                            {{-- NO --}}
                            <td class="px-4 py-4 text-center text-xs text-gray-500 font-medium">
                                {{ $partais->firstItem() + $index }}
                            </td>

                            {{-- PARTAI --}}
                            <td class="px-4 py-4 text-center font-extrabold text-gray-900">
                                {{ $partai->no_partai }}
                            </td>

                            {{-- KELAS --}}
                            <td class="px-4 py-4">
                                <span
                                    class="inline-block px-2 py-1 rounded bg-primary-50 text-primary-700 text-[10px] font-bold border border-primary-100 whitespace-nowrap">
                                    {{-- Mengambil langsung dari kolom label_kelas sesuai Model RefKelas --}}
                                    {{ $partai->kelas->label_kelas ?? '-' }}
                                </span>
                            </td>

                            {{-- KOMPARASI NILAI SUDUT --}}
                            <td class="px-4 py-4">
                                <div class="flex flex-col gap-2">

                                    {{-- BOX SUDUT BIRU --}}
                                    @if ($biru)
                                        <div
                                            class="flex items-center gap-3 p-2 rounded-xl border {{ $disqBiru == 1 ? 'bg-red-50/30 border-red-200 opacity-80' : 'bg-blue-50/50 border-blue-100' }}">
                                            <div class="flex-1">
                                                <p
                                                    class="text-[11px] font-black {{ $disqBiru == 1 ? 'text-red-700 line-through' : 'text-blue-700' }} leading-tight">
                                                    {{-- Coba cek nama, jika kosong cek nama_peserta, jika kosong cek nama_lengkap --}}
                                                    {{ $biru->peserta->nama ?? ($biru->peserta->nama_peserta ?? ($biru->peserta->nama_lengkap ?? 'Nama Tidak Ditemukan')) }}
                                                </p>
                                                <p
                                                    class="text-[9px] {{ $disqBiru == 1 ? 'text-red-500' : 'text-blue-500' }} font-bold uppercase">
                                                    {{ $biru->peserta->kontingen ?? '-' }}
                                                </p>
                                            </div>

                                            @if ($disqBiru == 1)
                                                <div class="flex items-center justify-center px-4">
                                                    <span
                                                        class="text-xs font-black text-red-600 tracking-widest px-3 py-1 bg-red-100 rounded-md border border-red-200">DISKUALIFIKASI</span>
                                                </div>
                                            @else
                                                <div
                                                    class="flex items-center gap-4 text-[10px] text-blue-600 font-medium px-4 border-x border-blue-100">
                                                    <span>MED:
                                                        <b>{{ number_format($biru->nilai_median ?? 0, 3) }}</b></span>
                                                    <span>DEV: <b>{{ $biru->nilai_deviasi ?? 0 }}</b></span>
                                                    <span class="text-red-500">HUK:
                                                        <b>{{ number_format($biru->hukuman_ketua ?? 0, 2) }}</b></span>
                                                </div>
                                                <div class="text-right min-w-[70px]">
                                                    <p class="text-[8px] text-blue-400 font-bold uppercase leading-none">
                                                        TOTAL</p>
                                                    <p class="text-sm font-black text-blue-900 leading-tight">
                                                        {{ number_format($biru->total_nilai ?? 0, 3) }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- BOX SUDUT MERAH --}}
                                    @if ($merah)
                                        <div
                                            class="flex items-center gap-3 p-2 rounded-xl border {{ $disqMerah == 1 ? 'bg-red-50/30 border-red-200 opacity-80' : 'bg-red-50/50 border-red-100' }}">
                                            <div class="flex-1">
                                                <p
                                                    class="text-[11px] font-black {{ $disqMerah == 1 ? 'text-red-700 line-through' : 'text-red-700' }} leading-tight">
                                                    {{ $merah->peserta->nama ?? ($merah->peserta->nama_peserta ?? ($merah->peserta->nama_lengkap ?? 'Nama Tidak Ditemukan')) }}
                                                </p>
                                                <p class="text-[9px] text-red-500 font-bold uppercase">
                                                    {{ $merah->peserta->kontingen ?? '-' }}
                                                </p>
                                            </div>

                                            @if ($disqMerah == 1)
                                                <div class="flex items-center justify-center px-4">
                                                    <span
                                                        class="text-xs font-black text-red-600 tracking-widest px-3 py-1 bg-red-100 rounded-md border border-red-200">DISKUALIFIKASI</span>
                                                </div>
                                            @else
                                                <div
                                                    class="flex items-center gap-4 text-[10px] text-red-600 font-medium px-4 border-x border-red-100">
                                                    <span>MED:
                                                        <b>{{ number_format($merah->nilai_median ?? 0, 3) }}</b></span>
                                                    <span>DEV: <b>{{ $merah->nilai_deviasi ?? 0 }}</b></span>
                                                    <span class="text-red-500">HUK:
                                                        <b>{{ number_format($merah->hukuman_ketua ?? 0, 2) }}</b></span>
                                                </div>
                                                <div class="text-right min-w-[70px]">
                                                    <p class="text-[8px] text-red-400 font-bold uppercase leading-none">
                                                        TOTAL</p>
                                                    <p class="text-sm font-black text-red-900 leading-tight">
                                                        {{ number_format($merah->total_nilai ?? 0, 3) }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                </div>
                            </td>

                            {{-- PEMENANG (Sesuai Syarat: Ditutup Sebelum Diklik Tampilkan) --}}
                            <td class="px-4 py-4 text-center">
                                <span
                                    class="inline-block px-3 py-1 rounded bg-gray-100 text-gray-500 border border-gray-200 text-[10px] font-bold">
                                    <svg class="w-3 h-3 inline-block mr-1 mb-0.5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    BELUM DIUMUMKAN
                                </span>
                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 py-4 text-center">
                                <button type="button"
                                    class="bg-gray-900 hover:bg-black text-white text-[10px] font-bold px-3 py-2 rounded-xl transition-all shadow-md w-full">
                                    TAMPILKAN PEMENANG
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-400 font-medium">
                                Belum ada pertandingan Solo yang selesai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50 flex items-center justify-between">
            <div class="text-xs text-gray-500">
                Menampilkan <span class="font-semibold text-gray-900">{{ $partais->firstItem() ?? 0 }}</span> sampai <span
                    class="font-semibold text-gray-900">{{ $partais->lastItem() ?? 0 }}</span> dari <span
                    class="font-semibold text-gray-900">{{ $partais->total() }}</span> data
            </div>

            <div class="flex items-center gap-1">
                {{ $partais->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
@endsection
