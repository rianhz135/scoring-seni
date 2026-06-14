@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Data Pool</h1>
            <p class="text-sm text-gray-500 mt-1">Sesuaikan partai atau daftar peserta.</p>
        </div>
    </div>

    <form action="{{ route('admin.peserta.pool.update', $partai->id_partai) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- SISI KIRI: DATA PARTAI --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Nomor Partai / Pool</label>
                            <input type="text" name="no_partai" value="{{ $partai->no_partai }}"
                                class="w-full mt-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-primary-500 outline-none">
                        </div>
                        {{-- Kategori & Kelas (Sama seperti Create tapi pakai selected) --}}
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Kategori</label>
                            <select name="id_kategori"
                                class="w-full mt-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm bg-white">
                                @foreach ($data_kategori as $kat)
                                    <option value="{{ $kat->id_kategori }}"
                                        {{ $partai->id_kategori == $kat->id_kategori ? 'selected' : '' }}>
                                        {{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Kelas</label>
                            {{-- Pastikan name="id_kelas" --}}
                            <select name="id_kelas"
                                class="w-full mt-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm bg-white">
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
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold shadow-lg transition-all">
                    Update Data Pool
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
                        </svg> Tambah Peserta
                    </button>
                </div>

                @foreach ($partai->detail_peserta->sortBy('no_urut') as $index => $dp)
                    <div
                        class="grid grid-cols-12 gap-4 items-center bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="col-span-1 flex flex-col items-center">
                            <input type="hidden" name="peserta[{{ $index }}][id_detail]"
                                value="{{ $dp->id_detail }}">
                            <div
                                class="w-8 h-8 flex items-center justify-center bg-gray-900 text-white rounded-lg font-bold text-sm">
                                {{ $loop->iteration }}</div>
                        </div>
                        <div class="col-span-6">
                            <input type="text" name="peserta[{{ $index }}][nama]"
                                value="{{ $dp->peserta->nama_peserta }}" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-100 text-sm outline-none">
                        </div>
                        <div class="col-span-4">
                            <input type="text" name="peserta[{{ $index }}][kontingen]"
                                value="{{ $dp->peserta->kontingen }}" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-100 text-sm outline-none uppercase">
                        </div>
                        <div class="col-span-1 text-right">
                            <button type="button" onclick="this.parentElement.parentElement.remove()"
                                class="text-red-400 hover:text-red-600"><svg class="w-5 h-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg></button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </form>

    <script>
        // Inisialisasi rowCount berdasarkan jumlah data yang sudah ada dari database
        let rowCount = {{ $partai->detail_peserta->count() }};

        document.getElementById('add-row-btn').addEventListener('click', function() {
            const container = document.getElementById('peserta-container');

            // Nomor urut visual (berdasarkan jumlah baris saat ini + 1)
            const nextUrut = container.querySelectorAll('.grid').length + 1;

            const html = `
            <div class="grid grid-cols-12 gap-4 items-center bg-white p-4 rounded-2xl border border-gray-100 shadow-sm animate-in slide-in-from-top-2 duration-300">
                <div class="col-span-1 flex flex-col items-center">
                    <div class="w-8 h-8 flex items-center justify-center bg-gray-200 text-gray-600 rounded-lg font-bold text-sm">${nextUrut}</div>
                    {{-- Gunakan index rowCount agar tidak bentrok --}}
                    <input type="hidden" name="peserta[${rowCount}][no_urut]" value="${nextUrut}">
                </div>
                <div class="col-span-6">
                    <input type="text" name="peserta[${rowCount}][nama]" required placeholder="Nama Peserta Baru" class="w-full px-4 py-2 rounded-lg border border-gray-100 text-sm focus:border-primary-500 outline-none">
                </div>
                <div class="col-span-4">
                    <input type="text" name="peserta[${rowCount}][kontingen]" required placeholder="Kontingen" class="w-full px-4 py-2 rounded-lg border border-gray-100 text-sm focus:border-primary-500 outline-none uppercase">
                </div>
                <div class="col-span-1 text-right">
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        `;

            container.insertAdjacentHTML('beforeend', html);
            rowCount++; // Naikkan index untuk row berikutnya
        });
    </script>
@endsection
