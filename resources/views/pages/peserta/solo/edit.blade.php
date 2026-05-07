@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Peserta Solo</h1>
            <p class="text-sm text-gray-500 mt-1">Perbarui data partai atau atlet yang bertanding.</p>
        </div>
        <a href="{{ route('admin.peserta.solo.index') }}"
            class="text-gray-600 hover:text-gray-900 flex items-center gap-2 text-sm font-semibold transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Batal
        </a>
    </div>

    {{-- Untuk dummy testing, kita asumsikan variable $peserta dikirim dari controller --}}
    <form action="{{ route('admin.peserta.solo.update', $partai->id_partai) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        {{-- TAMBAHKAN BLOK ERROR INI --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-6 py-4 rounded-xl shadow-sm">
                <div class="font-bold mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Terdapat kesalahan pada input Anda:
                </div>
                <ul class="list-disc pl-8 space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- AKHIR BLOK ERROR --}}

        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Perbarui Informasi Pertandingan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Nomor Partai</label>
                    <input type="text" name="no_partai" value="{{ old('no_partai', $partai->no_partai) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 outline-none text-sm">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Kelas</label>
                    <select name="id_kelas"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 outline-none text-sm bg-white">
                        @foreach ($data_kelas as $kelas)
                            <option value="{{ $kelas->id_kelas }}"
                                {{ $partai->id_kelas == $kelas->id_kelas ? 'selected' : '' }}>
                                {{ $kelas->label_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="bg-white border border-blue-100 rounded-2xl p-6 shadow-sm relative overflow-hidden">
                <div class="flex items-center gap-3 mb-6 relative">
                    <div class="p-2 bg-blue-600 rounded-lg text-white shadow-md shadow-blue-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-blue-700 uppercase tracking-tight">SUDUT BIRU</h2>
                </div>

                <div class="space-y-4 relative">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-blue-600 uppercase">Nama Peserta</label>
                        <input type="text" name="biru_nama"
                            value="{{ old('biru_nama', $biru->peserta->nama_peserta ?? '') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-blue-100 focus:border-blue-500 outline-none text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-blue-600 uppercase">Kontingen</label>
                        <input type="text" name="biru_kontingen"
                            value="{{ old('biru_kontingen', $biru->peserta->kontingen ?? '') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-blue-100 focus:border-blue-500 outline-none text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white border border-red-100 rounded-2xl p-6 shadow-sm relative overflow-hidden">
                <div class="flex items-center gap-3 mb-6 relative">
                    <div class="p-2 bg-red-600 rounded-lg text-white shadow-md shadow-red-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-red-700 uppercase tracking-tight">SUDUT MERAH</h2>
                </div>

                <div class="space-y-4 relative">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-red-600 uppercase">Nama Peserta</label>
                        <input type="text" name="merah_nama"
                            value="{{ old('merah_nama', $merah->peserta->nama_peserta ?? '') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-red-100 focus:border-red-500 outline-none text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-red-600 uppercase">Kontingen</label>
                        <input type="text" name="merah_kontingen"
                            value="{{ old('merah_kontingen', $merah->peserta->kontingen ?? '') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-red-100 focus:border-red-500 outline-none text-sm">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-4">
            <button type="submit"
                class="bg-amber-500 hover:bg-amber-600 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-amber-100 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Update Data Pertandingan
            </button>
        </div>
    </form>
@endsection
