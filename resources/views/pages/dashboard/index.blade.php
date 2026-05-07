@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Utama</h1>
    <p class="text-sm text-gray-500 mt-1">Selamat datang kembali, berikut adalah ringkasan data sistem saat ini.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <span class="text-[10px] font-bold text-green-500 bg-green-50 px-2 py-1 rounded-lg">+12%</span>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Total Peserta</p>
            <h3 class="text-2xl font-black text-gray-900">124</h3>
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-red-50 rounded-xl text-red-600 animate-pulse">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded-lg">LIVE</span>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Sedang Tanding</p>
            <h3 class="text-2xl font-black text-gray-900">2</h3>
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-green-50 rounded-xl text-green-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-[10px] font-bold text-gray-400 bg-gray-50 px-2 py-1 rounded-lg">FINAL</span>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Partai Selesai</p>
            <h3 class="text-2xl font-black text-gray-900">45</h3>
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-lg">MASTER</span>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Kategori Seni</p>
            <h3 class="text-2xl font-black text-gray-900">4</h3>
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
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('admin.peserta.solo.index') }}" class="p-4 border border-gray-50 bg-gray-50/50 rounded-xl hover:border-primary-200 hover:bg-primary-50 transition-all group">
                <p class="text-sm font-bold text-gray-700 group-hover:text-primary-700">Data Peserta</p>
                <p class="text-[10px] text-gray-400 uppercase font-bold">Manage Users</p>
            </a>
            <a href="{{ route('admin.manajemen.solo.index') }}" class="p-4 border border-gray-50 bg-gray-50/50 rounded-xl hover:border-primary-200 hover:bg-primary-50 transition-all group">
                <p class="text-sm font-bold text-gray-700 group-hover:text-primary-700">Mulai Tanding</p>
                <p class="text-[10px] text-gray-400 uppercase font-bold">Live Scoring</p>
            </a>
        </div>
    </div>
</div>
@endsection