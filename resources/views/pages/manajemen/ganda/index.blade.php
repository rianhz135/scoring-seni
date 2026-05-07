@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Manajemen Pertandingan: Ganda</h1>
        <p class="text-sm text-gray-400 mt-0.5">Kontrol status kesiapan dan mulai pertandingan per sudut.</p>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest w-16">No</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest w-24">Partai</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest w-32">Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center">
                            Sudut Biru (Kontrol)</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center">
                            Sudut Merah (Kontrol)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-600">1</td>
                        <td class="px-6 py-4 font-bold text-gray-900">#021</td>
                        <td class="px-6 py-4">
                            <span
                                class="px-2.5 py-1 rounded-md bg-primary-50 text-primary-700 text-xs font-bold uppercase border border-primary-100">
                                Kelas A
                            </span>
                        </td>

                        <td class="px-4 py-4">
                            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-100 space-y-3">
                                <div class="text-center">
                                    <p class="text-sm font-bold text-blue-700">Rian Hidayat</p>
                                    <p class="text-[10px] text-blue-500 uppercase font-medium">UNIVERSITAS PAMULANG</p>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <span
                                        class="text-center py-1 px-2 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700 uppercase">Menunggu</span>
                                    <button
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-1.5 rounded-lg text-[10px] font-extrabold transition-all flex items-center justify-center gap-1 shadow-sm shadow-blue-200">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                        MULAI BIRU
                                    </button>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-4">
                            <div class="p-4 rounded-2xl bg-red-50 border border-red-100 space-y-3">
                                <div class="text-center">
                                    <p class="text-sm font-bold text-red-700">Reza Aldiansyah</p>
                                    <p class="text-[10px] text-red-500 uppercase font-medium">Universitas Pamulang</p>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <span
                                        class="text-center py-1 px-2 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700 uppercase">Menunggu</span>
                                    <button
                                        class="w-full bg-red-600 hover:bg-red-700 text-white py-1.5 rounded-lg text-[10px] font-extrabold transition-all flex items-center justify-center gap-1 shadow-sm shadow-red-200">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                        MULAI MERAH
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-600">2</td>
                        <td class="px-6 py-4 font-bold text-gray-900">#022</td>
                        <td class="px-6 py-4">
                            <span
                                class="px-2.5 py-1 rounded-md bg-primary-50 text-primary-700 text-xs font-bold uppercase border border-primary-100">
                                Kelas B
                            </span>
                        </td>

                        <td class="px-4 py-4">
                            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-100 space-y-3 opacity-80">
                                <div class="text-center">
                                    <p class="text-sm font-bold text-blue-700">Rizaludin</p>
                                    <p class="text-[10px] text-blue-500 uppercase font-medium">SMKN 2 PANDEGLANG</p>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <span
                                        class="text-center py-1 px-2 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase italic">Bertanding</span>
                                    <button
                                        class="w-full bg-gray-300 text-white py-1.5 rounded-lg text-[10px] font-extrabold cursor-not-allowed flex items-center justify-center gap-1"
                                        disabled>
                                        RUNNING...
                                    </button>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-4">
                            <div class="p-4 rounded-2xl bg-red-50 border border-red-100 space-y-3">
                                <div class="text-center">
                                    <p class="text-sm font-bold text-red-700">DZULHAIDAR</p>
                                    <p class="text-[10px] text-red-500 uppercase font-medium">SMKN 1 PANDEGLANG</p>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <span
                                        class="text-center py-1 px-2 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700 uppercase">Menunggu</span>
                                    <button
                                        class="w-full bg-red-600 hover:bg-red-700 text-white py-1.5 rounded-lg text-[10px] font-extrabold transition-all flex items-center justify-center gap-1 shadow-sm shadow-red-200">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                        MULAI MERAH
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Menampilkan <span class="font-semibold text-gray-900">1</span> sampai <span
                        class="font-semibold text-gray-900">10</span> dari <span
                        class="font-semibold text-gray-900">45</span> data
                </div>

                <div class="flex items-center gap-1">
                    <a href="#"
                        class="p-2 rounded-lg border border-gray-200 text-gray-400 hover:bg-white hover:text-primary-600 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </a>

                    <a href="#"
                        class="px-3.5 py-1.5 rounded-lg bg-primary-600 text-white text-sm font-semibold shadow-sm shadow-primary-200">1</a>
                    <a href="#"
                        class="px-3.5 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 text-sm font-medium hover:border-primary-400 hover:text-primary-600 transition-all">2</a>
                    <a href="#"
                        class="px-3.5 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 text-sm font-medium hover:border-primary-400 hover:text-primary-600 transition-all">3</a>

                    <span class="px-2 text-gray-400">...</span>

                    <a href="#"
                        class="px-3.5 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 text-sm font-medium hover:border-primary-400 hover:text-primary-600 transition-all">12</a>

                    <a href="#"
                        class="p-2 rounded-lg border border-gray-200 text-gray-400 hover:bg-white hover:text-primary-600 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
