@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Manajemen Pertandingan: Regu
            <p class="text-sm text-gray-400 mt-0.5">Kontrol status kesiapan dan mulai pertandingan per sudut.</p>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest w-10">No</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest w-24">Partai</th>

                        {{-- PERBAIKAN DI SINI: Hapus w-32, tambahkan whitespace-nowrap --}}
                        <th
                            class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">
                            Kelas</th>

                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center">
                            Sudut Biru (Kontrol)</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center">
                            Sudut Merah (Kontrol)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($partais as $index => $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $partais->firstItem() + $index }}
                            </td>

                            <td class="px-6 py-4 font-bold text-gray-900">
                                #{{ $item->no_partai }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2.5 py-1 rounded-md bg-primary-50 text-primary-700 text-xs font-bold uppercase border border-primary-100">
                                    {{-- Gunakan label_kelas atau nama_kelas sesuai struktur table ref_kelas kamu --}}
                                    {{ $item->kelas->nama_kelas ?? ($item->kelas->label_kelas ?? '-') }}
                                </span>
                            </td>

                            {{-- Rendering Sudut Biru & Merah --}}
                            @foreach (['biru', 'merah'] as $sudut)
                                <td class="px-4 py-4">
                                    @php
                                        $sudutDb = ucfirst($sudut);
                                        // 1. Ambil SEMUA peserta di sudut ini (kumpulan 3 orang)
                                        $semuaPeserta = $item->detail_peserta->where('sudut', $sudutDb);

                                        // 2. Ambil 1 orang sebagai "Jangkar" untuk status dan kontingen
                                        $anchor = $semuaPeserta->first();
                                    @endphp

                                    @if ($anchor && $anchor->peserta)
                                        <div
                                            class="p-4 rounded-2xl bg-{{ $sudut == 'biru' ? 'blue' : 'red' }}-50 border border-{{ $sudut == 'biru' ? 'blue' : 'red' }}-100 space-y-3 {{ $anchor->status_mulai != 0 ? 'opacity-70' : '' }}">
                                            <div class="text-center">

                                                {{-- PERBAIKAN: Tampilkan 3 Nama bersusun ke bawah --}}
                                                <p
                                                    class="text-sm font-bold text-{{ $sudut == 'biru' ? 'blue' : 'red' }}-700 leading-tight">
                                                    {{ $semuaPeserta->pluck('peserta.nama_peserta')->implode(' & ') }}
                                                </p>

                                                <p
                                                    class="text-[10px] text-{{ $sudut == 'biru' ? 'blue' : 'red' }}-500 uppercase font-medium mt-2 border-t border-{{ $sudut == 'biru' ? 'blue' : 'red' }}-200 pt-1 mx-2">
                                                    {{ $anchor->peserta->kontingen }}
                                                </p>
                                            </div>

                                            <div class="flex flex-col gap-2">
                                                @if ($anchor->status_mulai == 0)
                                                    <a href="{{ route('admin.manajemen.regu.mulai', ['id' => $item->id_partai, 'sudut' => $sudut]) }}"
                                                        class="w-full bg-{{ $sudut == 'biru' ? 'blue' : 'red' }}-600 hover:bg-{{ $sudut == 'biru' ? 'blue' : 'red' }}-700 text-white py-1.5 rounded-lg text-[10px] font-extrabold transition-all flex items-center justify-center gap-1">
                                                        MULAI {{ strtoupper($sudut) }}
                                                    </a>
                                                @elseif($anchor->status_mulai == 1)
                                                    <button disabled
                                                        class="w-full bg-gray-300 text-white py-1.5 rounded-lg text-[10px] font-extrabold flex items-center justify-center gap-1">
                                                        RUNNING...
                                                    </button>
                                                @else
                                                    <button disabled
                                                        class="w-full bg-gray-200 text-gray-500 py-1.5 rounded-lg text-[10px] font-extrabold">
                                                        SELESAI
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center text-xs text-gray-300 italic py-4">BYE / KOSONG</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Data tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($partais->hasPages())
            <div class="p-4 border-t border-gray-50">
                {{ $partais->links() }}
            </div>
        @endif
    </div>
@endsection
