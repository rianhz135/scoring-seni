@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Peserta Regu</h1>
        <p class="text-sm text-gray-500 mt-1">Input data partai dan atlet yang akan bertanding.</p>
    </div>
    <a href="{{ route('admin.peserta.regu.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2 text-sm font-semibold transition-all">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali ke Daftar
    </a>
</div>

<form action="{{ route('admin.peserta.regu.store') }}" method="POST" class="space-y-6">
    @csrf
    
    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <div class="p-2 bg-primary-50 rounded-lg text-primary-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Informasi Pertandingan</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Nomor Partai --}}
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700 uppercase">Nomor Partai</label>
                <input type="text" name="no_partai" placeholder="Contoh: 001" required
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all text-sm">
            </div>

            {{-- Hidden ID Kategori --}}
            <input type="hidden" name="id_kategori" value="{{ $kategori->first()->id_kategori ?? '' }}">

            {{-- Dropdown Kelas --}}
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700 uppercase">Kelas</label>
                <select name="id_kelas" required
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 outline-none text-sm bg-white">
                    <option value="" disabled selected>Pilih Kelas</option>
                    @foreach ($kelas as $kls)
                        <option value="{{ $kls->id_kelas }}">{{ $kls->label_kelas }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 -mr-8 -mt-8 rounded-full opacity-50"></div>
            
            <div class="flex items-center gap-3 mb-6 relative">
                <div class="p-2 bg-blue-600 rounded-lg text-white shadow-md shadow-blue-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-blue-700 uppercase tracking-tight">SUDUT BIRU</h2>
            </div>

            <div class="space-y-4 relative">
                @for ($i = 1; $i <= 3; $i++)
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-blue-600 uppercase">Nama Atlet {{ $i }}</label>
                        <input type="text" name="biru_nama[]" placeholder="Nama atlet {{ $i }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm">
                    </div>
                @endfor
                <div class="space-y-2">
                    <label class="text-xs font-bold text-blue-600 uppercase">Kontingen</label>
                    <input type="text" name="biru_kontingen" placeholder="Provinsi / Kabupaten / Kota / Universitas / Sekolah..." required
                        class="w-full px-4 py-2.5 rounded-xl border border-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white border border-red-100 rounded-2xl p-6 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 -mr-8 -mt-8 rounded-full opacity-50"></div>

            <div class="flex items-center gap-3 mb-6 relative">
                <div class="p-2 bg-red-600 rounded-lg text-white shadow-md shadow-red-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-red-700 uppercase tracking-tight">SUDUT MERAH</h2>
            </div>

            <div class="space-y-4 relative">
                @for ($i = 1; $i <= 3; $i++)
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-red-600 uppercase">Nama Atlet {{ $i }}</label>
                        <input type="text" name="merah_nama[]" placeholder="Nama atlet {{ $i }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-red-100 focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition-all text-sm">
                    </div>
                @endfor
                <div class="space-y-2">
                    <label class="text-xs font-bold text-red-600 uppercase">Kontingen</label>
                    <input type="text" name="merah_kontingen" placeholder="Provinsi / Kabupaten / Kota / Universitas / Sekolah..." required
                        class="w-full px-4 py-2.5 rounded-xl border border-red-100 focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition-all text-sm">
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-4 pt-4">
        <button type="reset" class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-gray-700 transition-all">
            Reset Form
        </button>
        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-primary-100 transition-all">
            Simpan Data Peserta
        </button>
    </div>
</form>
@endsection