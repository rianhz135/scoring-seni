@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Pertandingan Pool</h1>
        <p class="text-sm text-gray-500 mt-1">Kontrol jalannya pertandingan kategori Seni.</p>
    </div>
    <div class="flex gap-3">
        <span class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-50 text-blue-700 text-xs font-bold border border-blue-100">
            Total: {{ $data_pool->total() }} Partai
        </span>
    </div>
</div>

<div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase w-16 text-center">Partai</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Kategori & Kelas</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Status</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Progres Peserta</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase text-center">Kontrol</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($data_pool as $pool)
            <tr class="hover:bg-gray-50/30 transition-all">
                <td class="px-6 py-4 text-center font-bold text-gray-900">
                    <div class="w-10 h-10 flex items-center justify-center bg-gray-900 text-white rounded-lg mx-auto text-sm">
                        {{ $pool->no_partai }}
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-800">{{ $pool->kategori->nama_kategori }}</div>
                    <div class="text-[11px] text-gray-400 font-medium uppercase">{{ $pool->kelas->label_kelas }}</div>
                </td>
                <td class="px-6 py-4">
                    @php
                        $statusColor = [
                            'Waiting' => 'bg-amber-50 text-amber-600 border-amber-100',
                            'Match' => 'bg-emerald-50 text-emerald-600 border-emerald-100 animate-pulse',
                            'Selesai' => 'bg-gray-50 text-gray-500 border-gray-100',
                        ][$pool->status_pertandingan] ?? 'bg-gray-50 text-gray-500';
                    @endphp
                    <span class="px-3 py-1.5 rounded-lg text-[10px] font-bold border {{ $statusColor }}">
                        {{ $pool->status_pertandingan }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <div class="flex -space-x-2">
                            @foreach($pool->detail_peserta->take(3) as $dp)
                                <div class="w-7 h-7 rounded-full border-2 border-white bg-primary-100 flex items-center justify-center text-[10px] font-bold text-primary-700" title="{{ $dp->peserta->nama_peserta }}">
                                    {{ substr($dp->peserta->nama_peserta, 0, 1) }}
                                </div>
                            @endforeach
                        </div>
                        <span class="text-xs text-gray-400 font-medium">+{{ $pool->detail_peserta->count() }} Orang</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        @if($pool->status_pertandingan == 'Waiting')
                            <button class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /></svg>
                                Mulai Partai
                            </button>
                        @elseif($pool->status_pertandingan == 'Match')
                            <a href="#" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition-all">
                                Input Nilai
                            </a>
                        @else
                            <button class="bg-gray-100 text-gray-400 px-4 py-2 rounded-lg text-xs font-bold cursor-not-allowed">
                                Selesai
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        <p class="text-gray-400 text-sm italic">Belum ada partai pool yang siap dijalankan.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $data_pool->links() }}
</div>
@endsection