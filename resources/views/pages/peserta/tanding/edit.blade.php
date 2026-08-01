@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Edit Partai Tanding #{{ $partai->no_partai }}</h1>
        <p class="text-sm text-gray-500 mt-1">Perbarui data jadwal partai, atlet, atau status bagan.</p>
    </div>
    <a href="{{ route('admin.peserta.tanding.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2 text-sm font-semibold transition-all">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali
    </a>
</div>

<form action="{{ route('admin.peserta.tanding.update', $partai->id) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')
    
    {{-- INFORMASI PERTANDINGAN --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <div class="p-2 bg-primary-50 rounded-lg text-primary-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Informasi Pertandingan</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Nomor Partai --}}
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700 uppercase">Nomor Partai</label>
                <input type="text" name="no_partai" value="{{ $partai->no_partai }}" placeholder="Contoh: A-01" required
                    class="w-full h-10 px-4 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all text-sm font-bold uppercase">
            </div>

            {{-- FASE BABAK --}}
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700 uppercase">Fase Babak</label>
                <div class="flex bg-gray-100 border border-gray-200 rounded-xl p-1 gap-1 w-full h-10 items-center">
                    
                    <label class="flex-1 h-full cursor-pointer relative">
                        <input type="radio" name="fase_babak" value="Penyisihan" class="sr-only babak-radio" {{ $partai->fase_babak == 'Penyisihan' ? 'checked' : '' }} onchange="updateBabakUI()">
                        <div class="babak-label h-full w-full flex items-center justify-center rounded-lg text-xs md:text-sm font-bold transition-all text-gray-500 hover:text-gray-700">
                            Penyisihan
                        </div>
                    </label>
                    
                    <label class="flex-1 h-full cursor-pointer relative">
                        <input type="radio" name="fase_babak" value="Semi" class="sr-only babak-radio" {{ $partai->fase_babak == 'Semi' ? 'checked' : '' }} onchange="updateBabakUI()">
                        <div class="babak-label h-full w-full flex items-center justify-center rounded-lg text-xs md:text-sm font-bold transition-all text-gray-500 hover:text-gray-700">
                            Semi
                        </div>
                    </label>

                    <label class="flex-1 h-full cursor-pointer relative">
                        <input type="radio" name="fase_babak" value="Final" class="sr-only babak-radio" {{ $partai->fase_babak == 'Final' ? 'checked' : '' }} onchange="updateBabakUI()">
                        <div class="babak-label h-full w-full flex items-center justify-center rounded-lg text-xs md:text-sm font-bold transition-all text-gray-500 hover:text-gray-700">
                            Final
                        </div>
                    </label>

                </div>
            </div>

            {{-- Kelas & Kategori --}}
            <div class="space-y-2">
                <label class="text-sm font-bold text-gray-700 uppercase">Kelas & Kategori</label>
                <input type="text" name="kelas" value="{{ $partai->kelas }}" placeholder="Contoh: Kelas A Putra" required
                    class="w-full h-10 px-4 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all text-sm">
            </div>
        </div>
    </div>

    @php
        $status_biru = $partai->id_biru ? 'atlet' : 'menunggu';
        $status_merah = $partai->id_merah ? 'atlet' : 'menunggu';
    @endphp

    {{-- SUDUT ATLET --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- SUDUT BIRU --}}
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

            <div class="mb-5 flex gap-4 p-1 bg-gray-50 rounded-lg border border-gray-100 w-max">
                <label class="cursor-pointer">
                    <input type="radio" name="status_biru" value="atlet" class="peer sr-only" onchange="toggleSudut('biru', 'atlet')" {{ $status_biru == 'atlet' ? 'checked' : '' }}>
                    <div class="px-4 py-2 rounded-md text-sm font-semibold text-gray-500 peer-checked:bg-white peer-checked:text-blue-600 peer-checked:shadow-sm transition-all">
                        Input Atlet
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="status_biru" value="menunggu" class="peer sr-only" onchange="toggleSudut('biru', 'menunggu')" {{ $status_biru == 'menunggu' ? 'checked' : '' }}>
                    <div class="px-4 py-2 rounded-md text-sm font-semibold text-gray-500 peer-checked:bg-white peer-checked:text-blue-600 peer-checked:shadow-sm transition-all">
                        Menunggu Pemenang
                    </div>
                </label>
            </div>

            <div id="form_biru_atlet" class="space-y-4 relative {{ $status_biru == 'atlet' ? 'block' : 'hidden' }}">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-blue-600 uppercase">Nama Atlet</label>
                    <input type="text" name="biru_nama" value="{{ $partai->pesertaBiru->nama ?? '' }}" placeholder="Masukkan nama lengkap atlet" 
                        class="w-full px-4 py-2.5 rounded-xl border border-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-blue-600 uppercase">Kontingen</label>
                    <input type="text" name="biru_kontingen" value="{{ $partai->pesertaBiru->kontingen ?? '' }}" placeholder="Asal kontingen" 
                        class="w-full px-4 py-2.5 rounded-xl border border-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm">
                </div>
            </div>

            <div id="form_biru_menunggu" class="space-y-4 relative {{ $status_biru == 'menunggu' ? 'block' : 'hidden' }}">
                <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <label class="text-xs font-bold text-blue-600 uppercase block mb-2">Menunggu Pemenang Dari Partai Ke-</label>
                    <input type="text" name="menunggu_biru" value="{{ $partai->menunggu_pemenang_biru_dari ?? '' }}" placeholder="Contoh: A-12" 
                        class="w-full px-4 py-2.5 rounded-xl border border-blue-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm font-bold uppercase">
                </div>
            </div>
        </div>

        {{-- SUDUT MERAH --}}
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

            <div class="mb-5 flex gap-4 p-1 bg-gray-50 rounded-lg border border-gray-100 w-max">
                <label class="cursor-pointer">
                    <input type="radio" name="status_merah" value="atlet" class="peer sr-only" onchange="toggleSudut('merah', 'atlet')" {{ $status_merah == 'atlet' ? 'checked' : '' }}>
                    <div class="px-4 py-2 rounded-md text-sm font-semibold text-gray-500 peer-checked:bg-white peer-checked:text-red-600 peer-checked:shadow-sm transition-all">
                        Input Atlet
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="status_merah" value="menunggu" class="peer sr-only" onchange="toggleSudut('merah', 'menunggu')" {{ $status_merah == 'menunggu' ? 'checked' : '' }}>
                    <div class="px-4 py-2 rounded-md text-sm font-semibold text-gray-500 peer-checked:bg-white peer-checked:text-red-600 peer-checked:shadow-sm transition-all">
                        Menunggu Pemenang
                    </div>
                </label>
            </div>

            <div id="form_merah_atlet" class="space-y-4 relative {{ $status_merah == 'atlet' ? 'block' : 'hidden' }}">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-red-600 uppercase">Nama Atlet</label>
                    <input type="text" name="merah_nama" value="{{ $partai->pesertaMerah->nama ?? '' }}" placeholder="Masukkan nama lengkap atlet" 
                        class="w-full px-4 py-2.5 rounded-xl border border-red-100 focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition-all text-sm">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-red-600 uppercase">Kontingen</label>
                    <input type="text" name="merah_kontingen" value="{{ $partai->pesertaMerah->kontingen ?? '' }}" placeholder="Asal kontingen" 
                        class="w-full px-4 py-2.5 rounded-xl border border-red-100 focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition-all text-sm">
                </div>
            </div>

            <div id="form_merah_menunggu" class="space-y-4 relative {{ $status_merah == 'menunggu' ? 'block' : 'hidden' }}">
                <div class="p-4 bg-red-50 rounded-xl border border-red-100">
                    <label class="text-xs font-bold text-red-600 uppercase block mb-2">Menunggu Pemenang Dari Partai Ke-</label>
                    <input type="text" name="menunggu_merah" value="{{ $partai->menunggu_pemenang_merah_dari ?? '' }}" placeholder="Contoh: A-11" 
                        class="w-full px-4 py-2.5 rounded-xl border border-red-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all text-sm font-bold uppercase">
                </div>
            </div>
        </div>

    </div>

    <div class="flex items-center justify-end gap-4 pt-4">
        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-primary-100 transition-all">
            Simpan Perubahan
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // 1. FUNGSI UNTUK MENGATUR UI TOMBOL FASE BABAK
    function updateBabakUI() {
        const radios = document.querySelectorAll('.babak-radio');
        radios.forEach(radio => {
            const labelDiv = radio.nextElementSibling;
            if (radio.checked) {
                labelDiv.className = "babak-label h-full w-full flex items-center justify-center rounded-lg text-xs md:text-sm font-bold transition-all bg-primary-600 text-white shadow-md";
            } else {
                labelDiv.className = "babak-label h-full w-full flex items-center justify-center rounded-lg text-xs md:text-sm font-bold transition-all text-gray-500 hover:text-gray-700 bg-transparent shadow-none";
            }
        });
    }

    // 2. FUNGSI UNTUK TOGGLE FORM SUDUT MERAH/BIRU
    function toggleSudut(sudut, type) {
        const formAtlet = document.getElementById(`form_${sudut}_atlet`);
        const formMenunggu = document.getElementById(`form_${sudut}_menunggu`);
        const inputsAtlet = formAtlet.querySelectorAll('input');
        const inputsMenunggu = formMenunggu.querySelectorAll('input');

        if (type === 'atlet') {
            formAtlet.classList.remove('hidden');
            formMenunggu.classList.add('hidden');
            inputsAtlet.forEach(el => el.setAttribute('required', 'required'));
            inputsMenunggu.forEach(el => el.removeAttribute('required'));
        } else {
            formAtlet.classList.add('hidden');
            formMenunggu.classList.remove('hidden');
            inputsMenunggu.forEach(el => el.setAttribute('required', 'required'));
            inputsAtlet.forEach(el => el.removeAttribute('required'));
        }
    }

    // 3. JALANKAN SAAT HALAMAN DIMUAT
    document.addEventListener('DOMContentLoaded', () => {
        updateBabakUI();
        toggleSudut('biru', '{{ $status_biru }}');
        toggleSudut('merah', '{{ $status_merah }}');
    });
</script>
@endpush