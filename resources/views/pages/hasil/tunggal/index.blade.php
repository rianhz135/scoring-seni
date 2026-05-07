@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hasil Pertandingan: Tunggal</h1>
            <p class="text-sm text-gray-500 mt-1">Rekapitulasi nilai pertandingan kategori tunggal.</p>
        </div>
        <button
            class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export PDF
        </button>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1100px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 uppercase text-center">
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest w-12">No</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest w-16">Partai</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest w-24 text-left">Kelas</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest">Komparasi Nilai Sudut (Biru vs Merah)</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest w-32">Pemenang</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @for ($i = 1; $i <= 5; $i++)
                        <tr class="hover:bg-gray-50/50 transition-colors uppercase">
                            <td class="px-4 py-4 text-center text-xs text-gray-500 font-medium">{{ $i }}</td>
                            <td class="px-4 py-4 text-center font-extrabold text-gray-900">04{{ $i }}A</td>
                            <td class="px-4 py-4">
                                <span
                                    class="inline-block px-2 py-1 rounded bg-primary-50 text-primary-700 text-[10px] font-bold border border-primary-100 whitespace-nowrap">
                                    KELAS A
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-3 p-2 rounded-xl bg-blue-50/50 border border-blue-100">
                                        <div class="flex-1">
                                            <p class="text-[11px] font-black text-blue-700 leading-tight">RIAN HIDAYAT, RIZALUDIN, FIRMANSYAH</p>
                                            <p class="text-[9px] text-blue-500 font-bold uppercase">BANTEN</p>
                                        </div>
                                        <div class="flex items-center gap-4 text-[10px] text-blue-600 font-medium px-4 border-x border-blue-100">
                                            <span>MED: <b>9.85</b></span>
                                            <span>DEV: <b>0.02</b></span>
                                            <span>WKT: <b>03:00</b></span>
                                            <span class="text-red-500">HUK: <b>-0.05</b></span>
                                        </div>
                                        <div class="text-right min-w-[70px]">
                                            <p class="text-[8px] text-blue-400 font-bold uppercase leading-none">TOTAL</p>
                                            <p class="text-sm font-black text-blue-900 leading-tight">9.80</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 p-2 rounded-xl bg-red-50/50 border border-red-100">
                                        <div class="flex-1">
                                            <p class="text-[11px] font-black text-red-700 leading-tight">FAJAR ALFIAN</p>
                                            <p class="text-[9px] text-red-500 font-bold uppercase">JAWA BARAT</p>
                                        </div>
                                        <div class="flex items-center gap-4 text-[10px] text-red-600 font-medium px-4 border-x border-red-100">
                                            <span>MED: <b>9.80</b></span>
                                            <span>DEV: <b>0.05</b></span>
                                            <span>WKT: <b>02:58</b></span>
                                            <span class="text-red-500">HUK: <b>0.00</b></span>
                                        </div>
                                        <div class="text-right min-w-[70px]">
                                            <p class="text-[8px] text-red-400 font-bold uppercase leading-none">TOTAL</p>
                                            <p class="text-sm font-black text-red-900 leading-tight">9.80</p>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4 text-center">
                                <div
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-600 text-white text-[9px] font-black shadow-sm">
                                    <svg class="w-3 h-3 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    BIRU
                                </div>
                            </td>

                            <td class="px-4 py-4 text-center">
                                <button
                                    class="bg-slate-900 hover:bg-black text-white p-2.5 rounded-xl transition-all shadow-md flex items-center justify-center mx-auto group">
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-white" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path
                                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50 flex items-center justify-between">
            <div class="text-xs text-gray-500">
                Menampilkan <span class="font-semibold text-gray-900">1</span> sampai <span
                    class="font-semibold text-gray-900">6</span> dari <span class="font-semibold text-gray-900">45</span>
                data
            </div>

            <div class="flex items-center gap-1">
                <a href="#"
                    class="p-2 rounded-lg border border-gray-200 text-gray-400 hover:bg-white hover:text-primary-600 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <a href="#" class="px-3.5 py-1.5 rounded-lg bg-primary-600 text-white text-xs font-semibold shadow-sm">1</a>
                <a href="#" class="px-3.5 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 text-xs font-medium">2</a>
                <span class="px-2 text-gray-400">...</span>
                <a href="#"
                    class="p-2 rounded-lg border border-gray-200 text-gray-400 hover:bg-white hover:text-primary-600 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
@endsection