@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Partai Tanding</h1>
            <p class="text-sm text-gray-500 mt-1">Input data jadwal partai, atlet, atau status bagan (menunggu pemenang).</p>
        </div>
        <a href="{{ route('admin.peserta.tanding.index') }}"
            class="text-gray-600 hover:text-gray-900 flex items-center gap-2 text-sm font-semibold transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    <form action="{{ route('admin.peserta.tanding.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- INFORMASI PERTANDINGAN --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-primary-50 rounded-lg text-primary-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Informasi Pertandingan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 uppercase">Nomor Partai</label>
                    <input type="text" name="no_partai" placeholder="Contoh: A-01" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all text-sm font-bold uppercase">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 uppercase">Fase Babak</label>
                    <select name="fase_babak" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 outline-none text-sm bg-white">
                        <option value="" disabled selected>Pilih Babak</option>
                        <option value="Penyisihan">Penyisihan</option>
                        <option value="Semi">Semi Final</option>
                        <option value="Final">Final</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700 uppercase">Kelas & Kategori</label>
                    <input type="text" name="kelas" placeholder="Contoh: Kelas A Putra" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all text-sm">
                </div>
            </div>
        </div>

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
                        <input type="radio" name="status_biru" value="atlet" checked class="peer sr-only"
                            onchange="toggleSudut('biru', 'atlet')">
                        <div
                            class="px-4 py-2 rounded-md text-sm font-semibold text-gray-500 peer-checked:bg-white peer-checked:text-blue-600 peer-checked:shadow-sm transition-all">
                            Input Atlet
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status_biru" value="menunggu" class="peer sr-only"
                            onchange="toggleSudut('biru', 'menunggu')">
                        <div
                            class="px-4 py-2 rounded-md text-sm font-semibold text-gray-500 peer-checked:bg-white peer-checked:text-blue-600 peer-checked:shadow-sm transition-all">
                            Menunggu Pemenang
                        </div>
                    </label>
                </div>

                {{-- Form Biru: Atlet Baru --}}
                <div id="form_biru_atlet" class="space-y-4 relative block">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-blue-600 uppercase">Nama Atlet</label>
                        <input type="text" name="biru_nama" placeholder="Masukkan nama lengkap atlet" required
                            class="w-full px-4 py-2.5 rounded-xl border border-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-blue-600 uppercase">Kontingen</label>
                        <input type="text" name="biru_kontingen" placeholder="Asal kontingen" required
                            class="w-full px-4 py-2.5 rounded-xl border border-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm">
                    </div>
                </div>

                {{-- Form Biru: Menunggu Pemenang --}}
                <div id="form_biru_menunggu" class="space-y-4 relative hidden">
                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <label class="text-xs font-bold text-blue-600 uppercase block mb-2">Menunggu Pemenang Dari Partai
                            Ke-</label>
                        <input type="text" name="menunggu_biru" placeholder="Contoh: A-12"
                            class="w-full px-4 py-2.5 rounded-xl border border-blue-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm font-bold uppercase">
                        <p class="text-[10px] text-blue-400 mt-2">*Atlet sudut biru akan otomatis terisi oleh pemenang dari
                            nomor partai yang Anda masukkan di atas.</p>
                    </div>
                </div>
            </div>

            {{-- SUDUT MERAH --}}
            <div class="bg-white border border-red-100 rounded-2xl p-6 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 -mr-8 -mt-8 rounded-full opacity-50"></div>

                <div class="flex items-center gap-3 mb-6 relative">
                    <div class="p-2 bg-red-600 rounded-lg text-white shadow-md shadow-red-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-red-700 uppercase tracking-tight">SUDUT MERAH</h2>
                </div>

                <div class="mb-5 flex gap-4 p-1 bg-gray-50 rounded-lg border border-gray-100 w-max">
                    <label class="cursor-pointer">
                        <input type="radio" name="status_merah" value="atlet" checked class="peer sr-only"
                            onchange="toggleSudut('merah', 'atlet')">
                        <div
                            class="px-4 py-2 rounded-md text-sm font-semibold text-gray-500 peer-checked:bg-white peer-checked:text-red-600 peer-checked:shadow-sm transition-all">
                            Input Atlet
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status_merah" value="menunggu" class="peer sr-only"
                            onchange="toggleSudut('merah', 'menunggu')">
                        <div
                            class="px-4 py-2 rounded-md text-sm font-semibold text-gray-500 peer-checked:bg-white peer-checked:text-red-600 peer-checked:shadow-sm transition-all">
                            Menunggu Pemenang
                        </div>
                    </label>
                </div>

                {{-- Form Merah: Atlet Baru --}}
                <div id="form_merah_atlet" class="space-y-4 relative block">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-red-600 uppercase">Nama Atlet</label>
                        <input type="text" name="merah_nama" placeholder="Masukkan nama lengkap atlet" required
                            class="w-full px-4 py-2.5 rounded-xl border border-red-100 focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-red-600 uppercase">Kontingen</label>
                        <input type="text" name="merah_kontingen" placeholder="Asal kontingen" required
                            class="w-full px-4 py-2.5 rounded-xl border border-red-100 focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition-all text-sm">
                    </div>
                </div>

                {{-- Form Merah: Menunggu Pemenang --}}
                <div id="form_merah_menunggu" class="space-y-4 relative hidden">
                    <div class="p-4 bg-red-50 rounded-xl border border-red-100">
                        <label class="text-xs font-bold text-red-600 uppercase block mb-2">Menunggu Pemenang Dari Partai
                            Ke-</label>
                        <input type="text" name="menunggu_merah" placeholder="Contoh: A-11"
                            class="w-full px-4 py-2.5 rounded-xl border border-red-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all text-sm font-bold uppercase">
                        <p class="text-[10px] text-red-400 mt-2">*Atlet sudut merah akan otomatis terisi oleh pemenang dari
                            nomor partai yang Anda masukkan di atas.</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="flex items-center justify-end gap-4 pt-4">
            <button type="reset" class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-gray-700 transition-all">
                Reset Form
            </button>
            <button type="submit"
                class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-primary-100 transition-all">
                Simpan Jadwal Partai
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        // Script untuk memunculkan/menyembunyikan form dan mengatur atribut 'required'
        // agar form HTML5 tidak error saat disubmit jika salah satu disembunyikan.
        function toggleSudut(sudut, type) {
            const formAtlet = document.getElementById(`form_${sudut}_atlet`);
            const formMenunggu = document.getElementById(`form_${sudut}_menunggu`);

            const inputsAtlet = formAtlet.querySelectorAll('input, select');
            const inputsMenunggu = formMenunggu.querySelectorAll('input');

            if (type === 'atlet') {
                formAtlet.classList.remove('hidden');
                formMenunggu.classList.add('hidden');

                // Wajibkan input atlet, hilangkan wajib input tunggu
                inputsAtlet.forEach(el => el.setAttribute('required', 'required'));
                inputsMenunggu.forEach(el => {
                    el.removeAttribute('required');
                    el.value = ''; // Kosongkan nilai
                });
            } else {
                formAtlet.classList.add('hidden');
                formMenunggu.classList.remove('hidden');

                // Wajibkan input tunggu, hilangkan wajib input atlet
                inputsMenunggu.forEach(el => el.setAttribute('required', 'required'));
                inputsAtlet.forEach(el => {
                    el.removeAttribute('required');
                    el.value = ''; // Kosongkan nilai
                });
            }
        }
    </script>
@endpush
