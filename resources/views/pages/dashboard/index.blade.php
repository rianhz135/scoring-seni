@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Utama</h1>
        <p class="text-sm text-gray-500 mt-1">Selamat datang kembali, berikut adalah ringkasan data sistem saat ini.</p>
    </div>

    {{-- Grid 5 Kartu Kategori dalam Satu Baris --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">

        {{-- 1. SOLO --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">SOLO</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500">Total Partai Solo</p>
                <h3 class="text-2xl font-black text-gray-900 mt-0.5">{{ $totalSolo }}</h3>
            </div>
        </div>

        {{-- 2. GANDA --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-red-50 rounded-xl text-red-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded-lg">GANDA</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500">Total Partai Ganda</p>
                <h3 class="text-2xl font-black text-gray-900 mt-0.5">{{ $totalGanda }}</h3>
            </div>
        </div>

        {{-- 3. TUNGGAL --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-50 rounded-xl text-green-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path
                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                </div>
                <span class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-1 rounded-lg">TUNGGAL</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500">Total Partai Tunggal</p>
                <h3 class="text-2xl font-black text-gray-900 mt-0.5">{{ $totalTunggal }}</h3>
            </div>
        </div>

        {{-- 4. REGU --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-lg">REGU</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500">Total Partai Regu</p>
                <h3 class="text-2xl font-black text-gray-900 mt-0.5">{{ $totalRegu }}</h3>
            </div>
        </div>

        {{-- 5. TANDING --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                </div>
                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">TANDING</span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500">Total Partai Tanding</p>
                <h3 class="text-2xl font-black text-gray-900 mt-0.5">{{ $totalTanding }}</h3>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Aktivitas Terbaru</h3>
            <div class="space-y-4">
                <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-xl transition-all">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-gray-800">Partai #045 Selesai</p>
                        <p class="text-xs text-gray-400">Pemenang: Sudut Biru (Banten)</p>
                    </div>
                    <span class="text-[10px] font-medium text-gray-400">2 Menit Lalu</span>
                </div>
                <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-xl transition-all">
                    <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-gray-800">Partai #046 Dimulai</p>
                        <p class="text-xs text-gray-400">Kategori: Solo - Kelas B</p>
                    </div>
                    <span class="text-[10px] font-medium text-gray-400">10 Menit Lalu</span>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Navigasi Cepat</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.peserta.solo.index') }}"
                    class="p-3.5 border border-gray-100 bg-gray-50/50 rounded-xl hover:border-primary-200 hover:bg-primary-50 transition-all group">
                    <p class="text-xs font-bold text-gray-800 group-hover:text-primary-700">Data Peserta</p>
                    <p class="text-[10px] text-gray-400 font-semibold uppercase mt-0.5">Kelola Peserta</p>
                </a>
                <a href="{{ route('admin.manajemen.solo.index') }}"
                    class="p-3.5 border border-gray-100 bg-gray-50/50 rounded-xl hover:border-primary-200 hover:bg-primary-50 transition-all group">
                    <p class="text-xs font-bold text-gray-800 group-hover:text-primary-700">Manajemen Seni</p>
                    <p class="text-[10px] text-gray-400 font-semibold uppercase mt-0.5">Solo / Ganda / Regu</p>
                </a>
                <a href="{{ route('admin.manajemen.tanding.index') }}"
                    class="p-3.5 border border-amber-100 bg-amber-50/30 rounded-xl hover:border-amber-300 hover:bg-amber-50 transition-all group">
                    <p class="text-xs font-bold text-amber-900 group-hover:text-amber-700">Manajemen Tanding</p>
                    <p class="text-[10px] text-amber-600/70 font-semibold uppercase mt-0.5">Live Scoring & Dewan</p>
                </a>
                <a href="{{ route('admin.hasil.tanding.index') }}"
                    class="p-3.5 border border-blue-100 bg-blue-50/30 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition-all group">
                    <p class="text-xs font-bold text-blue-900 group-hover:text-blue-700">Hasil Tanding</p>
                    <p class="text-[10px] text-blue-600/70 font-semibold uppercase mt-0.5">Rekapitulasi Nilai</p>
                </a>
            </div>
        </div>
    </div>
@endsection
