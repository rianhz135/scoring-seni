@extends('layouts.admin')

@section('content')
    {{-- Header Halaman --}}
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary-50 text-primary-600 border border-primary-200">
                    Operasional
                </span>
                <span class="text-xs text-gray-400 font-medium">• Kategori Tanding</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Manajemen Pertandingan: Tanding</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kontrol status jalannya partai tanding dan pantau daftar peserta di setiap sudut arena.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.peserta.tanding.index') }}"
                class="px-3.5 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold transition-all flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                Data Master Peserta
            </a>
            <a href="{{ route('admin.peserta.tanding.create') }}"
                class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 shadow-sm shadow-primary-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Partai
            </a>
        </div>
    </div>

    {{-- Kartu Ringkasan Statistik (1 Baris Ringkas & Vertikal) --}}
    <div class="grid grid-cols-5 gap-3 mb-6" style="display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 0.75rem;">
        {{-- Card 1: Total Partai --}}
        <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between hover:border-gray-200 transition-all min-w-0">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <span class="text-[9px] font-bold text-gray-500 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-100">Semua</span>
            </div>
            <div>
                <h3 class="text-lg font-black text-gray-900 leading-tight">{{ $stats['total'] }}</h3>
                <p class="text-[11px] text-gray-400 font-medium truncate mt-0.5">Total Partai</p>
            </div>
        </div>

        {{-- Card 2: Belum Bertanding --}}
        <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between hover:border-amber-200 transition-all min-w-0">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-[9px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100">Antrean</span>
            </div>
            <div>
                <h3 class="text-lg font-black text-amber-700 leading-tight">{{ $stats['belum'] }}</h3>
                <p class="text-[11px] text-amber-600/80 font-medium truncate mt-0.5">Belum Tanding</p>
            </div>
        </div>

        {{-- Card 3: Sedang Berlangsung --}}
        <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between hover:border-emerald-200 transition-all min-w-0">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 relative">
                    <span class="absolute top-0.5 right-0.5 flex h-1.5 w-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                    </span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100 flex items-center gap-1">
                    <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live
                </span>
            </div>
            <div>
                <h3 class="text-lg font-black text-emerald-700 leading-tight">{{ $stats['berlangsung'] }}</h3>
                <p class="text-[11px] text-emerald-600/80 font-medium truncate mt-0.5">Berlangsung</p>
            </div>
        </div>

        {{-- Card 4: Partai Selesai --}}
        <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between hover:border-blue-200 transition-all min-w-0">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-[9px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">Final</span>
            </div>
            <div>
                <h3 class="text-lg font-black text-blue-700 leading-tight">{{ $stats['selesai'] }}</h3>
                <p class="text-[11px] text-blue-600/80 font-medium truncate mt-0.5">Selesai</p>
            </div>
        </div>

        {{-- Card 5: Total Atlet Tanding --}}
        <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between hover:border-purple-200 transition-all min-w-0">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />
                    </svg>
                </div>
                <span class="text-[9px] font-bold text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100">Peserta</span>
            </div>
            <div>
                <h3 class="text-lg font-black text-purple-700 leading-tight">{{ $stats['total_atlet'] }}</h3>
                <p class="text-[11px] text-purple-600/80 font-medium truncate mt-0.5">Total Atlet</p>
            </div>
        </div>
    </div>

    {{-- Tab Navigasi: Jadwal & Kontrol vs Daftar Atlet --}}
    <div class="mb-5 flex border-b border-gray-200">
        <button id="tab-btn-partai" onclick="switchTab('partai')"
            class="tab-button inline-flex items-center gap-2 py-3 px-5 border-b-2 font-bold text-sm transition-all text-primary-600 border-primary-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
            </svg>
            Daftar Jadwal & Kontrol Partai
            <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] bg-primary-100 text-primary-700 font-extrabold">
                {{ $partais->total() }}
            </span>
        </button>
        <button id="tab-btn-atlet" onclick="switchTab('atlet')"
            class="tab-button inline-flex items-center gap-2 py-3 px-5 border-b-2 font-bold text-sm transition-all text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
            </svg>
            Daftar Seluruh Peserta / Atlet
            <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] bg-gray-100 text-gray-600 font-extrabold">
                {{ $atlets->total() }}
            </span>
        </button>
    </div>

    {{-- KONTEN TAB 1: DAFTAR PARTAI & KONTROL --}}
    <div id="tab-content-partai" class="space-y-4">
        {{-- Filter & Pencarian --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
            <form action="{{ route('admin.manajemen.tanding.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <input type="hidden" name="tab" value="partai">
                
                {{-- Input Cari --}}
                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-gray-500 uppercase">Cari Partai / Atlet</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="No partai, atlet, kontingen, kelas..."
                            class="w-full h-9 pl-8 pr-3 text-xs rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-200 outline-none">
                        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                </div>

                {{-- Filter Status --}}
                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-gray-500 uppercase">Status Pertandingan</label>
                    <select name="status" class="w-full h-9 px-3 text-xs rounded-xl border border-gray-200 focus:border-primary-500 outline-none bg-white">
                        <option value="all">Semua Status</option>
                        <option value="Belum" {{ request('status') === 'Belum' ? 'selected' : '' }}>Belum Dimulai</option>
                        <option value="Berlangsung" {{ request('status') === 'Berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                        <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                {{-- Filter Fase Babak --}}
                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-gray-500 uppercase">Fase Babak</label>
                    <select name="fase_babak" class="w-full h-9 px-3 text-xs rounded-xl border border-gray-200 focus:border-primary-500 outline-none bg-white">
                        <option value="all">Semua Fase</option>
                        <option value="Penyisihan" {{ request('fase_babak') === 'Penyisihan' ? 'selected' : '' }}>Penyisihan</option>
                        <option value="Semi" {{ request('fase_babak') === 'Semi' ? 'selected' : '' }}>Semi Final</option>
                        <option value="Final" {{ request('fase_babak') === 'Final' ? 'selected' : '' }}>Final</option>
                    </select>
                </div>

                {{-- Tombol Aksi Filter --}}
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="flex-1 h-9 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                        </svg>
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'fase_babak']))
                        <a href="{{ route('admin.manajemen.tanding.index') }}"
                            class="h-9 px-3 border border-gray-200 hover:bg-gray-100 text-gray-600 rounded-xl text-xs font-semibold flex items-center justify-center transition-all">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Manajemen Pertandingan --}}
        <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-4 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-widest w-12 text-center">No</th>
                            <th class="px-4 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-widest w-20 text-center">Partai</th>
                            <th class="px-4 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-widest w-36">Kelas & Babak</th>
                            <th class="px-4 py-3.5 text-xs font-semibold text-blue-700 uppercase tracking-widest text-center min-w-[200px]">Sudut Biru</th>
                            <th class="px-4 py-3.5 text-xs font-semibold text-red-700 uppercase tracking-widest text-center min-w-[200px]">Sudut Merah</th>
                            <th class="px-4 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center w-28">Babak Aktif</th>
                            <th class="px-4 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center w-32">Status</th>
                            <th class="px-4 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center w-48">Kontrol Operasional</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($partais as $index => $partai)
                            <tr class="hover:bg-gray-50/60 transition-colors {{ $partai->status_pertandingan === 'Berlangsung' ? 'bg-emerald-50/30' : '' }}">
                                {{-- Nomor Urut --}}
                                <td class="px-4 py-4 text-xs text-gray-500 text-center font-medium">
                                    {{ $partais->firstItem() + $index }}
                                </td>

                                {{-- Nomor Partai --}}
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-block px-2.5 py-1 rounded-lg bg-gray-900 text-white font-black text-sm tracking-wide shadow-sm">
                                        #{{ $partai->no_partai }}
                                    </span>
                                </td>

                                {{-- Kelas & Fase Babak --}}
                                <td class="px-4 py-4">
                                    <div class="space-y-1">
                                        <span class="inline-block px-2 py-0.5 rounded-md bg-primary-50 text-primary-700 text-[11px] font-bold uppercase border border-primary-100">
                                            {{ $partai->kelas }}
                                        </span>
                                        <div>
                                            <span class="text-xs font-semibold text-gray-700">
                                                Babak: {{ $partai->fase_babak }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Sudut Biru --}}
                                <td class="px-4 py-4">
                                    <div class="p-3 rounded-xl bg-blue-50/80 border border-blue-100 text-center relative {{ $partai->id_pemenang && $partai->id_pemenang === $partai->id_biru ? 'ring-2 ring-blue-500 shadow-sm' : '' }}">
                                        @if ($partai->id_pemenang && $partai->id_pemenang === $partai->id_biru)
                                            <span class="absolute -top-2.5 left-1/2 transform -translate-x-1/2 px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-blue-600 text-white shadow-sm flex items-center gap-1">
                                                <svg class="w-3 h-3 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                Pemenang
                                            </span>
                                        @endif

                                        @if($partai->pesertaBiru)
                                            <div class="text-sm font-bold text-blue-700">
                                                {{ $partai->pesertaBiru->nama }}
                                            </div>
                                            <p class="text-[10px] text-blue-500 uppercase font-semibold mt-0.5 tracking-tight">
                                                {{ $partai->pesertaBiru->kontingen }}
                                            </p>
                                        @elseif($partai->menunggu_pemenang_biru_dari)
                                            <div class="text-xs font-semibold text-blue-600/70 italic flex items-center justify-center gap-1 py-1">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Menunggu #{{ $partai->menunggu_pemenang_biru_dari }}
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic py-1 block">Belum ditentukan</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Sudut Merah --}}
                                <td class="px-4 py-4">
                                    <div class="p-3 rounded-xl bg-red-50/80 border border-red-100 text-center relative {{ $partai->id_pemenang && $partai->id_pemenang === $partai->id_merah ? 'ring-2 ring-red-500 shadow-sm' : '' }}">
                                        @if ($partai->id_pemenang && $partai->id_pemenang === $partai->id_merah)
                                            <span class="absolute -top-2.5 left-1/2 transform -translate-x-1/2 px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-red-600 text-white shadow-sm flex items-center gap-1">
                                                <svg class="w-3 h-3 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                Pemenang
                                            </span>
                                        @endif

                                        @if($partai->pesertaMerah)
                                            <div class="text-sm font-bold text-red-700">
                                                {{ $partai->pesertaMerah->nama }}
                                            </div>
                                            <p class="text-[10px] text-red-500 uppercase font-semibold mt-0.5 tracking-tight">
                                                {{ $partai->pesertaMerah->kontingen }}
                                            </p>
                                        @elseif($partai->menunggu_pemenang_merah_dari)
                                            <div class="text-xs font-semibold text-red-600/70 italic flex items-center justify-center gap-1 py-1">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Menunggu #{{ $partai->menunggu_pemenang_merah_dari }}
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic py-1 block">Belum ditentukan</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Babak Aktif --}}
                                <td class="px-4 py-4 text-center">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100 text-gray-800 text-xs font-bold">
                                        <span>Babak {{ $partai->babak_aktif ?: 1 }}</span>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-1 font-medium">Timer: {{ $partai->waktu_timer ?? 120 }}s</p>
                                </td>

                                {{-- Status Pertandingan --}}
                                <td class="px-4 py-4 text-center">
                                    @if ($partai->status_pertandingan === 'Berlangsung')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                            Berlangsung
                                        </span>
                                    @elseif ($partai->status_pertandingan === 'Selesai')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            Belum Dimulai
                                        </span>
                                    @endif
                                </td>

                                {{-- Kontrol Operasional --}}
                                <td class="px-4 py-4">
                                    <div class="flex flex-col gap-1.5 min-w-[170px]">
                                        @if ($partai->status_pertandingan === 'Belum')
                                            {{-- Tombol Mulai Partai --}}
                                            <form action="{{ route('admin.manajemen.tanding.mulai', $partai->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-1.5 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                                                    </svg>
                                                    Mulai Partai
                                                </button>
                                            </form>
                                        @elseif ($partai->status_pertandingan === 'Berlangsung')
                                            {{-- Tombol Buka Panel Dewan --}}
                                            <a href="{{ route('admin.dewan.tanding.index', $partai->id) }}"
                                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-1.5 px-2.5 rounded-lg text-xs font-black transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                                <svg class="w-3.5 h-3.5 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                                </svg>
                                                Masuk Panel Dewan
                                            </a>

                                            {{-- Ganti Babak & Tentukan Pemenang --}}
                                            <div class="grid grid-cols-2 gap-1.5">
                                                <button type="button"
                                                    onclick="openBabakModal('{{ $partai->id }}', '{{ $partai->no_partai }}', '{{ $partai->babak_aktif ?: 1 }}')"
                                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 py-1.5 px-2 rounded-lg text-[11px] font-bold transition-all flex items-center justify-center gap-1">
                                                    <svg class="w-3 h-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                    </svg>
                                                    Babak
                                                </button>
                                                <button type="button"
                                                    onclick="openPemenangModal('{{ $partai->id }}', '{{ $partai->no_partai }}', '{{ $partai->pesertaBiru ? $partai->pesertaBiru->id : '' }}', '{{ $partai->pesertaBiru ? addslashes($partai->pesertaBiru->nama) : '' }}', '{{ $partai->pesertaMerah ? $partai->pesertaMerah->id : '' }}', '{{ $partai->pesertaMerah ? addslashes($partai->pesertaMerah->nama) : '' }}')"
                                                    class="bg-primary-600 hover:bg-primary-700 text-white py-1.5 px-2 rounded-lg text-[11px] font-bold transition-all flex items-center justify-center gap-1 shadow-sm">
                                                    <svg class="w-3 h-3 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                    Pemenang
                                                </button>
                                            </div>

                                            <form action="{{ route('admin.manajemen.tanding.selesai', $partai->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 py-1 px-2 rounded-lg text-[10px] font-bold transition-all flex items-center justify-center gap-1 border border-blue-200">
                                                    Tandai Selesai
                                                </button>
                                            </form>
                                        @else
                                            {{-- Selesai: Tampilkan Tombol Reset --}}
                                            <div class="flex items-center gap-1">
                                                <button type="button"
                                                    onclick="openPemenangModal('{{ $partai->id }}', '{{ $partai->no_partai }}', '{{ $partai->pesertaBiru ? $partai->pesertaBiru->id : '' }}', '{{ $partai->pesertaBiru ? addslashes($partai->pesertaBiru->nama) : '' }}', '{{ $partai->pesertaMerah ? $partai->pesertaMerah->id : '' }}', '{{ $partai->pesertaMerah ? addslashes($partai->pesertaMerah->nama) : '' }}', '{{ $partai->id_pemenang }}')"
                                                    class="flex-1 bg-gray-50 hover:bg-gray-100 text-gray-700 py-1.5 px-2 rounded-lg text-[11px] font-semibold border border-gray-200 transition-all flex items-center justify-center gap-1">
                                                    Ubah Juara
                                                </button>
                                                <button type="button"
                                                    onclick="confirmReset('{{ $partai->id }}', '{{ $partai->no_partai }}')"
                                                    class="p-1.5 rounded-lg border border-gray-200 text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all"
                                                    title="Reset Partai">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <form id="reset-form-{{ $partai->id }}"
                                                action="{{ route('admin.manajemen.tanding.reset', $partai->id) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        @endif

                                        {{-- Link Edit Partai --}}
                                        <a href="{{ route('admin.peserta.tanding.edit', $partai->id) }}"
                                            class="text-gray-400 hover:text-primary-600 text-[10px] text-center font-medium transition-colors flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                            </svg>
                                            Edit Data Partai
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                    <div class="max-w-xs mx-auto space-y-2">
                                        <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <p class="text-sm font-semibold text-gray-600">Belum ada data partai tanding</p>
                                        <p class="text-xs text-gray-400">Silakan tambahkan data partai dan atlet tanding melalui tombol Tambah Partai.</p>
                                        <div class="pt-2">
                                            <a href="{{ route('admin.peserta.tanding.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-600 text-white rounded-lg text-xs font-semibold hover:bg-primary-700">
                                                Tambah Partai Sekarang
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Partai --}}
            @if ($partais->hasPages())
                <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50">
                    {{ $partais->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- KONTEN TAB 2: DAFTAR SELURUH ATLET TANDING --}}
    <div id="tab-content-atlet" class="space-y-4 hidden">
        {{-- Search Atlet --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
            <form action="{{ route('admin.manajemen.tanding.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="hidden" name="tab" value="atlet">
                <div class="relative flex-1">
                    <input type="text" name="search_atlet" value="{{ request('search_atlet') }}"
                        placeholder="Cari berdasarkan nama atlet, kontingen, atau kategori umur..."
                        class="w-full h-10 pl-9 pr-4 text-xs rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-200 outline-none">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <button type="submit"
                    class="h-10 px-5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm">
                    Cari Atlet
                </button>
                @if(request('search_atlet'))
                    <a href="{{ route('admin.manajemen.tanding.index', ['tab' => 'atlet']) }}"
                        class="h-10 px-4 border border-gray-200 hover:bg-gray-100 text-gray-600 rounded-xl text-xs font-semibold flex items-center justify-center transition-all">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabel Daftar Atlet --}}
        <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-widest w-16 text-center">No</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-widest">Nama Atlet</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-widest">Asal Kontingen</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-widest">Kategori Umur</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-widest">Partai yang Diikuti</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center">Status Prestasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($atlets as $index => $atlet)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-4 text-xs text-gray-500 text-center font-medium">
                                    {{ $atlets->firstItem() + $index }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center font-bold text-xs uppercase border border-primary-100">
                                            {{ substr($atlet->nama, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 leading-tight">{{ $atlet->nama }}</p>
                                            <p class="text-[11px] text-gray-400">ID: #{{ $atlet->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-block px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold uppercase">
                                        {{ $atlet->kontingen }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-600">
                                    {{ $atlet->kategori_umur ?: '-' }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($atlet->partaiBiru as $pBiru)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                                Partai #{{ $pBiru->no_partai }} (Biru)
                                            </span>
                                        @endforeach
                                        @foreach ($atlet->partaiMerah as $pMerah)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-50 text-red-700 border border-red-200 text-[10px] font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                                Partai #{{ $pMerah->no_partai }} (Merah)
                                            </span>
                                        @endforeach
                                        @if ($atlet->partaiBiru->isEmpty() && $atlet->partaiMerah->isEmpty())
                                            <span class="text-xs text-gray-400 italic">Belum ada jadwal partai</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if ($atlet->partaiMenang->isNotEmpty())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            Menang ({{ $atlet->partaiMenang->count() }}x)
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-sm">
                                    Belum ada data atlet tanding terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($atlets->hasPages())
                <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50">
                    {{ $atlets->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL UBAH BABAK AKTIF --}}
    <div id="modal-babak" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
        <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl border border-gray-100 transform transition-all">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900">Ubah Babak Pertandingan</h3>
                <button type="button" onclick="closeBabakModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <p id="babak-modal-desc" class="text-xs text-gray-500 mb-5">Pilih babak yang sedang berjalan untuk partai ini:</p>

            <form id="form-update-babak" method="POST" action="">
                @csrf
                <div class="grid grid-cols-3 gap-2.5 mb-5">
                    @foreach ([1, 2, 3] as $b)
                        <label class="cursor-pointer">
                            <input type="radio" name="babak_aktif" value="{{ $b }}" id="radio-babak-{{ $b }}" class="peer sr-only">
                            <div class="p-3 text-center rounded-xl border border-gray-200 peer-checked:border-primary-600 peer-checked:bg-primary-50 peer-checked:text-primary-700 font-extrabold text-sm transition-all hover:bg-gray-50">
                                Babak {{ $b }}
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeBabakModal()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                        Simpan Babak
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL TENTUKAN PEMENANG --}}
    <div id="modal-pemenang" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl border border-gray-100 transform transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Tentukan Pemenang Partai</h3>
                </div>
                <button type="button" onclick="closePemenangModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <p id="pemenang-modal-desc" class="text-xs text-gray-500 mb-5">Pilih atlet sudut yang dinyatakan memenangkan pertandingan ini:</p>

            <form id="form-set-pemenang" method="POST" action="">
                @csrf
                <div class="space-y-3 mb-6">
                    {{-- Opsi Sudut Biru --}}
                    <label id="label-opsi-biru" class="block cursor-pointer">
                        <input type="radio" name="id_pemenang" id="radio-pemenang-biru" value="" class="peer sr-only">
                        <div class="p-4 rounded-xl border-2 border-blue-100 bg-blue-50/50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:shadow-sm transition-all flex items-center justify-between">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-600 text-white">Sudut Biru</span>
                                <p id="nama-pemenang-biru" class="text-sm font-bold text-blue-900 mt-1">-</p>
                            </div>
                            <div class="w-5 h-5 rounded-full border-2 border-blue-300 peer-checked:border-blue-600 peer-checked:bg-blue-600 flex items-center justify-center">
                                <span class="w-2 h-2 rounded-full bg-white hidden peer-checked:block"></span>
                            </div>
                        </div>
                    </label>

                    {{-- Opsi Sudut Merah --}}
                    <label id="label-opsi-merah" class="block cursor-pointer">
                        <input type="radio" name="id_pemenang" id="radio-pemenang-merah" value="" class="peer sr-only">
                        <div class="p-4 rounded-xl border-2 border-red-100 bg-red-50/50 peer-checked:border-red-600 peer-checked:bg-red-50 peer-checked:shadow-sm transition-all flex items-center justify-between">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-red-600 text-white">Sudut Merah</span>
                                <p id="nama-pemenang-merah" class="text-sm font-bold text-red-900 mt-1">-</p>
                            </div>
                            <div class="w-5 h-5 rounded-full border-2 border-red-300 peer-checked:border-red-600 peer-checked:bg-red-600 flex items-center justify-center">
                                <span class="w-2 h-2 rounded-full bg-white hidden peer-checked:block"></span>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 mb-5 text-[11px] text-amber-800">
                    <p class="font-bold flex items-center gap-1">
                        <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pembaruan Otomatis Bagan
                    </p>
                    <p class="mt-0.5 text-amber-700">Jika partai berikutnya menunggu pemenang dari partai ini, atlet pemenang akan otomatis diisi ke jadwal babak selanjutnya.</p>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closePemenangModal()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                        Tetapkan Pemenang
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Tab switching
    function switchTab(tab) {
        const tabPartai = document.getElementById('tab-content-partai');
        const tabAtlet = document.getElementById('tab-content-atlet');
        const btnPartai = document.getElementById('tab-btn-partai');
        const btnAtlet = document.getElementById('tab-btn-atlet');

        if (tab === 'partai') {
            tabPartai.classList.remove('hidden');
            tabAtlet.classList.add('hidden');
            btnPartai.classList.add('text-primary-600', 'border-primary-600');
            btnPartai.classList.remove('text-gray-500', 'border-transparent');
            btnAtlet.classList.remove('text-primary-600', 'border-primary-600');
            btnAtlet.classList.add('text-gray-500', 'border-transparent');
        } else {
            tabPartai.classList.add('hidden');
            tabAtlet.classList.remove('hidden');
            btnAtlet.classList.add('text-primary-600', 'border-primary-600');
            btnAtlet.classList.remove('text-gray-500', 'border-transparent');
            btnPartai.classList.remove('text-primary-600', 'border-primary-600');
            btnPartai.classList.add('text-gray-500', 'border-transparent');
        }
    }

    // Auto switch tab jika URL query atau hash berisi tab=atlet
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tab') === 'atlet' || urlParams.has('atlet_page') || urlParams.has('search_atlet')) {
            switchTab('atlet');
        }
    });

    // Modal Ubah Babak
    function openBabakModal(id, noPartai, babakAktif) {
        const form = document.getElementById('form-update-babak');
        form.action = `/admin/manajemen/tanding/${id}/update-babak`;
        document.getElementById('babak-modal-desc').innerText = `Pilih babak yang sedang berjalan untuk Partai #${noPartai}:`;

        const radio = document.getElementById(`radio-babak-${babakAktif}`);
        if (radio) radio.checked = true;

        document.getElementById('modal-babak').classList.remove('hidden');
    }

    function closeBabakModal() {
        document.getElementById('modal-babak').classList.add('hidden');
    }

    // Modal Tentukan Pemenang
    function openPemenangModal(id, noPartai, idBiru, namaBiru, idMerah, namaMerah, idPemenang = null) {
        const form = document.getElementById('form-set-pemenang');
        form.action = `/admin/manajemen/tanding/${id}/set-pemenang`;
        document.getElementById('pemenang-modal-desc').innerText = `Pilih atlet sudut pemenang untuk Partai #${noPartai}:`;

        const radioBiru = document.getElementById('radio-pemenang-biru');
        const radioMerah = document.getElementById('radio-pemenang-merah');
        const labelBiru = document.getElementById('label-opsi-biru');
        const labelMerah = document.getElementById('label-opsi-merah');

        if (idBiru && namaBiru) {
            radioBiru.value = idBiru;
            radioBiru.disabled = false;
            document.getElementById('nama-pemenang-biru').innerText = namaBiru;
            labelBiru.classList.remove('opacity-40', 'pointer-events-none');
            if (idPemenang && idPemenang == idBiru) {
                radioBiru.checked = true;
            } else if (!idPemenang) {
                radioBiru.checked = true;
            }
        } else {
            radioBiru.disabled = true;
            document.getElementById('nama-pemenang-biru').innerText = 'Atlet Belum Ditentukan';
            labelBiru.classList.add('opacity-40', 'pointer-events-none');
        }

        if (idMerah && namaMerah) {
            radioMerah.value = idMerah;
            radioMerah.disabled = false;
            document.getElementById('nama-pemenang-merah').innerText = namaMerah;
            labelMerah.classList.remove('opacity-40', 'pointer-events-none');
            if (idPemenang && idPemenang == idMerah) {
                radioMerah.checked = true;
            } else if (!idBiru) {
                radioMerah.checked = true;
            }
        } else {
            radioMerah.disabled = true;
            document.getElementById('nama-pemenang-merah').innerText = 'Atlet Belum Ditentukan';
            labelMerah.classList.add('opacity-40', 'pointer-events-none');
        }

        document.getElementById('modal-pemenang').classList.remove('hidden');
    }

    function closePemenangModal() {
        document.getElementById('modal-pemenang').classList.add('hidden');
    }

    // SweetAlert Konfirmasi Reset
    function confirmReset(id, noPartai) {
        Swal.fire({
            title: `Reset Partai #${noPartai}?`,
            text: "Status pertandingan akan dikembalikan ke Belum Dimulai dan pemenang akan dikosongkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal',
            borderRadius: '1rem',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('reset-form-' + id).submit();
            }
        });
    }

    // Flash Toast Notifications
    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
        });
    @endif

    @if(session('error'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: "{{ session('error') }}",
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });
    @endif
</script>
@endpush

