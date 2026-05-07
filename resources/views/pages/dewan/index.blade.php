@extends('layouts.dewan')

@section('content')
    {{-- Header Mini (Ganti Navbar) --}}
    {{-- Header Informasi Pertandingan & Atlet --}}
    <div
        class="bg-white rounded-3xl p-6 mb-6 border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-6">
        {{-- Sisi Kiri: Profil Atlet & Kontingen --}}
        <div class="flex items-center gap-5 w-full md:w-auto">
            <div class="bg-primary-600 text-white p-4 rounded-2xl shadow-lg shadow-primary-200 flex-shrink-0">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-primary-500 uppercase tracking-[0.2em] mb-1">
                    KONTINGEN: <span class="text-gray-900">SMKN 2 PANDEGLANG</span>
                </p>
                <div class="flex flex-col">
                    <h1 class="text-xl font-black text-gray-900 leading-tight uppercase italic">
                        1. RIAN HIDAYAT <br>
                        2. M. RIZALUDIN <br>
                        3. SAMHANI
                    </h1>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Status Partai & Navigasi --}}
        <div
            class="flex flex-col items-center md:items-end border-l-0 md:border-l border-gray-100 pl-0 md:pl-8 w-full md:w-auto">
            <div class="flex items-center gap-2 mb-3">
                <span
                    class="px-3 py-1 bg-primary-100 text-primary-700 text-[10px] font-black uppercase rounded-lg tracking-widest">
                    REGU
                </span>
                <span class="px-3 py-1 bg-red-100 text-red-700 text-[10px] font-black uppercase rounded-lg tracking-widest">
                    PARTAI #01A
                </span>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2">
                <button
                    class="bg-gray-100 text-gray-500 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-200 transition-all">
                    Keluar
                </button>

                {{-- Tombol Selesai / Simpan Hasil --}}
                <button
                    class="bg-green-500 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-green-600 transition-all shadow-lg shadow-green-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Sahkan & Simpan Hasil
                </button>
            </div>
        </div>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Sisi Kiri: Kontrol Dewan --}}
        {{-- Sisi Kiri: Kontrol Dewan (Compact Version) --}}
        <div class="lg:col-span-4 space-y-4">
            {{-- 1. Stopwatch (Lebih Pendek) --}}
            <div class="bg-white rounded-3xl p-5 border border-gray-200 shadow-sm text-center relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-primary-600"></div>
                <p class="text-[12px] font-black text-gray-400 uppercase tracking-widest mb-1">Waktu Pertandingan</p>
                <div class="text-6xl font-black text-gray-900 tracking-tighter mb-4 font-mono">03:00</div>
                
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <button class="bg-primary-600 hover:bg-primary-700 text-white py-3 rounded-xl text-[12px] font-black uppercase tracking-widest shadow-md shadow-primary-100 transition-all">START</button>
                    <button class="bg-amber-500 hover:bg-amber-600 text-white py-3 rounded-xl text-[12px] font-black uppercase tracking-widest shadow-md shadow-amber-50 transition-all">STOP</button>
                </div>
                <button class="w-full bg-gray-50 hover:bg-gray-100 text-gray-400 py-2 rounded-xl text-[12px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2 border border-gray-100">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    RESET
                </button>
            </div>

            {{-- 2. Nilai Hukuman (Lebih Rapat) --}}
            <div class="bg-white rounded-3xl p-5 border border-gray-200 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-amber-500"></div>
                <h3 class="text-[12px] font-black text-gray-400 uppercase tracking-widest mb-3">Hukuman Dewan</h3>
                
                <div class="flex gap-2 mb-3">
                    <button class="flex-1 bg-amber-500 hover:bg-amber-600 text-white py-3 rounded-xl font-black text-lg shadow-md shadow-amber-50 transition-all">-0.50</button>
                    <button class="w-14 bg-gray-50 hover:bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center transition-all border border-gray-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                    </button>
                </div>

                <div class="bg-amber-50/40 p-2.5 rounded-xl border border-amber-100/50">
                    <ul class="text-[11px] font-bold text-amber-800/80 space-y-1 list-disc pl-3 leading-tight">
                        <li>Keluar arena 10m x 10m.</li>
                        <li>Senjata jatuh/sentuh lantai.</li>
                        <li>Pakaian/Tanjak/Samping terlepas.</li>
                        <li>Diam lebih dari 5 detik.</li>
                    </ul>
                </div>
            </div>

            {{-- 3. Diskualifikasi (Paling Pendek) --}}
            <div class="bg-red-600 rounded-3xl p-5 shadow-lg shadow-red-100 relative overflow-hidden">
                <h3 class="text-[12px] font-black text-red-200 uppercase tracking-widest mb-3 text-center">Tindakan Fatal</h3>
                <div class="flex gap-2">
                    <button class="flex-1 bg-white text-red-600 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-50 transition-all">Diskualifikasi</button>
                    <button class="w-14 bg-red-700 text-white rounded-xl flex items-center justify-center hover:bg-red-800 transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Monitoring Juri --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden h-full">
                <div class="bg-primary-600 p-6 text-center shadow-inner">
                    <h2 class="text-white font-black uppercase tracking-[0.3em] text-xl">Hasil Penilaian Juri</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th
                                    class="py-5 px-4 text-[10px] font-black text-gray-400 uppercase border-r border-gray-100 w-32">
                                    Uraian</th>
                                @for ($i = 1; $i <= 6; $i++)
                                    <th
                                        class="py-5 px-2 text-xs font-black text-primary-700 uppercase border-r border-gray-100">
                                        Juri {{ $i }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            {{-- 1. Baris Pengurangan --}}
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td
                                    class="py-5 px-4 text-[10px] font-black text-gray-500 uppercase text-left border-r bg-gray-50/50">
                                    Pengurangan</td>
                                @for ($i = 1; $i <= 6; $i++)
                                    <td class="py-5 px-2 text-sm font-bold text-red-500 border-r border-gray-100">-0.00</td>
                                @endfor
                            </tr>

                            {{-- 2. Baris Penambahan --}}
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td
                                    class="py-5 px-4 text-[10px] font-black text-gray-500 uppercase text-left border-r bg-gray-50/50">
                                    Penambahan</td>
                                @for ($i = 1; $i <= 6; $i++)
                                    <td class="py-5 px-2 text-sm font-bold text-green-500 border-r border-gray-100">+0.00
                                    </td>
                                @endfor
                            </tr>

                            {{-- 3. Baris Total Nilai Per Juri --}}
                            <tr class="bg-primary-50/30">
                                <td
                                    class="py-6 px-4 text-[10px] font-black text-primary-800 uppercase text-left border-r border-primary-100">
                                    Nilai Juri</td>
                                @for ($i = 1; $i <= 6; $i++)
                                    <td class="py-6 px-2 text-2xl font-black text-primary-700 border-r border-primary-100">
                                        9.90</td>
                                @endfor
                            </tr>

                            {{-- 4. Baris Status Juri --}}
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td
                                    class="py-5 px-4 text-[10px] font-black text-gray-500 uppercase text-left border-r bg-gray-50/50">
                                    Status Juri</td>
                                @for ($i = 1; $i <= 6; $i++)
                                    <td class="py-5 px-2 border-r border-gray-100">
                                        {{-- Ukuran Status Disamakan (max-w-[85px] py-2.5) --}}
                                        <div
                                            class="w-full max-w-[85px] py-2.5 mx-auto bg-amber-500 text-white text-[10px] font-black rounded-xl uppercase flex items-center justify-center tracking-widest shadow-sm">
                                            PROSES
                                        </div>
                                    </td>
                                @endfor
                            </tr>

                            {{-- 5. Baris Aksi Dewan --}}
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td
                                    class="py-5 px-4 text-[10px] font-black text-gray-500 uppercase text-left border-r bg-gray-50/50">
                                    Aksi Dewan</td>
                                @for ($i = 1; $i <= 6; $i++)
                                    <td class="py-5 px-2 border-r border-gray-100">
                                        {{-- Ukuran Tombol Disamakan (max-w-[85px] py-2.5) --}}
                                        <button
                                            class="w-full max-w-[85px] py-2.5 mx-auto bg-red-600 hover:bg-red-700 text-white text-[10px] font-black rounded-xl uppercase block tracking-widest transition-all shadow-sm">
                                            JURI OFF
                                        </button>
                                    </td>
                                @endfor
                            </tr>

                            {{-- ========================================================== --}}
                            {{-- SATU BARIS AKUMULASI: MEDIAN, DEVIASI, HUKUMAN, TOTAL NILAI --}}
                            {{-- ========================================================== --}}
                            <tr class="border-t-4 border-gray-900 bg-gray-900 text-white">
                                <td
                                    class="py-6 px-4 text-[10px] font-black uppercase text-left border-r border-gray-800 bg-gray-800/50">
                                    Hasil Akhir
                                </td>

                                {{-- Median (Colspan 1) --}}
                                <td colspan="1" class="py-6 px-2 border-r border-gray-800">
                                    <span
                                        class="block text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Median</span>
                                    <span class="text-xl font-black tracking-tighter">0.00</span>
                                </td>

                                {{-- Deviasi (Colspan 2 agar muat angka panjang) --}}
                                <td colspan="2" class="py-6 px-2 border-r border-gray-800">
                                    <span
                                        class="block text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Deviasi</span>
                                    <span class="text-lg font-black tracking-tighter font-mono">0.000000000000000</span>
                                </td>

                                {{-- Hukuman Dewan (Colspan 1) --}}
                                <td colspan="1" class="py-6 px-2 border-r border-gray-800 bg-red-900/20">
                                    <span
                                        class="block text-[9px] font-bold text-red-400 uppercase tracking-widest mb-1">Hukuman</span>
                                    <span class="text-xl font-black tracking-tighter text-red-500">-0.00</span>
                                </td>

                                {{-- Total Nilai (Colspan 2 - Paling Dominan) --}}
                                <td colspan="2" class="py-6 px-2 bg-primary-600 shadow-inner">
                                    <span
                                        class="block text-[9px] font-bold text-primary-200 uppercase tracking-widest mb-1">Total
                                        Nilai</span>
                                    <span class="text-3xl font-black tracking-tighter italic">0.000</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- Sisi Kanan: Monitoring Juri (Kode yang sebelumnya sudah dibuat) --}}
        {{-- ... --}}
    </div> {{-- Penutup grid utama --}}

    {{-- Footer Layar Dewan --}}
    <footer class="mt-8 pt-6 border-t border-gray-200/60 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                Sistem Terhubung &bull; Latensi: 12ms
            </p>
        </div>

        <div class="text-center md:text-right">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                &copy; {{ date('Y') }} DIGITAL SCORING — SATRIA MUDA INDONESIA
            </p>
            <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest mt-1">
                Version 1.0.0 &bull; NOC Team
            </p>
        </div>
    </footer>
@endsection