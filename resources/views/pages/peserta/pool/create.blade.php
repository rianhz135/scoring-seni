@extends('layouts.admin')

@section('content')
    {{-- Header --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Pool Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Input data partai dan list urutan peserta.</p>
        </div>
        <a href="{{ route('admin.peserta.pool.index') }}"
            class="text-gray-600 hover:text-gray-900 flex items-center gap-2 text-sm font-semibold transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Batal
        </a>
    </div>

    <form action="{{ route('admin.peserta.pool.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- SISI KIRI: DATA PARTAI --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider">Informasi Partai</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Nomor Partai / Pool</label>
                            <input type="text" name="no_partai" value="{{ old('no_partai') }}" placeholder="Contoh: POOL A" 
    class="w-full mt-1 px-4 py-2.5 rounded-xl border {{ $errors->has('no_partai') ? 'border-red-500' : 'border-gray-200' }} text-sm outline-none">
                        </div>
                        {{-- Alert Validasi Error (Misal: Nama Pool Duplikat) --}}
                        @if ($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-red-500 mr-3" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="text-sm font-bold text-red-800">Ups! Ada Kesalahan</h3>
                                </div>
                                <ul class="mt-2 ml-8 list-disc list-inside text-xs text-red-700">
                                    @foreach ($errors->all() as $error)
                                        {{-- Kita custom pesan jika errornya adalah 'unique' --}}
                                        <li>{{ $error == 'The no partai has already been taken.' ? 'Nama POOL sudah terdaftar, gunakan nama lain!' : $error }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Kategori Seni</label>
                            <select name="id_kategori"
                                class="w-full mt-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm bg-white">
                                @foreach ($data_kategori as $kat)
                                    <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Kelas</label>
                            <select name="id_kelas"
                                class="w-full mt-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm bg-white">
                                @foreach ($data_kelas as $kelas)
                                    <option value="{{ $kelas->id_kelas }}">{{ $kelas->label_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-primary-500 hover:bg-primary-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-primary-100 transition-all">
                    Simpan Semua Data Pool
                </button>
            </div>

            {{-- SISI KANAN: LIST PESERTA --}}
            <div class="lg:col-span-2 space-y-4" id="peserta-container">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Daftar Urutan Peserta</h2>
                    <button type="button" id="add-row-btn"
                        class="text-xs font-bold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path d="M12 4v16m8-8H4" />
                        </svg> Tambah Baris
                    </button>
                </div>

                {{-- Baris Default #1 --}}
                <div class="grid grid-cols-12 gap-4 items-center bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="col-span-1 flex flex-col items-center">
                        <span class="text-[10px] font-bold text-gray-400 uppercase mb-1">Urut</span>
                        <div
                            class="w-8 h-8 flex items-center justify-center bg-gray-900 text-white rounded-lg font-bold text-sm">
                            1</div>
                        <input type="hidden" name="peserta[0][no_urut]" value="1">
                    </div>
                    <div class="col-span-6">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Nama Peserta</label>
                        <input type="text" name="peserta[0][nama]" required
                            class="w-full mt-1 px-4 py-2 rounded-lg border border-gray-100 text-sm focus:border-primary-500 outline-none">
                    </div>
                    <div class="col-span-5">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Kontingen</label>
                        <input type="text" name="peserta[0][kontingen]" required
                            class="w-full mt-1 px-4 py-2 rounded-lg border border-gray-100 text-sm focus:border-primary-500 outline-none uppercase">
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        let rowCount = 1;
        document.getElementById('add-row-btn').addEventListener('click', function() {
            const container = document.getElementById('peserta-container');
            const nextUrut = rowCount + 1;
            const html = `
            <div class="grid grid-cols-12 gap-4 items-center bg-white p-4 rounded-2xl border border-gray-100 shadow-sm animate-in slide-in-from-top-2 duration-300">
                <div class="col-span-1 flex flex-col items-center">
                    <div class="w-8 h-8 flex items-center justify-center bg-gray-200 text-gray-600 rounded-lg font-bold text-sm">${nextUrut}</div>
                    <input type="hidden" name="peserta[${rowCount}][no_urut]" value="${nextUrut}">
                </div>
                <div class="col-span-6">
                    <input type="text" name="peserta[${rowCount}][nama]" required placeholder="Nama Peserta ${nextUrut}" class="w-full px-4 py-2 rounded-lg border border-gray-100 text-sm focus:border-primary-500 outline-none">
                </div>
                <div class="col-span-4">
                    <input type="text" name="peserta[${rowCount}][kontingen]" required placeholder="Kontingen" class="w-full px-4 py-2 rounded-lg border border-gray-100 text-sm focus:border-primary-500 outline-none uppercase">
                </div>
                <div class="col-span-1 text-right">
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                </div>
            </div>
        `;
            container.insertAdjacentHTML('beforeend', html);
            rowCount++;
        });
    </script>
@endsection
