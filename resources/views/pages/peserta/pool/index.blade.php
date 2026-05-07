@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Data Pool (Seni)</h1>
            <p class="text-sm text-gray-500 mt-1">Manajemen urutan penampilan kategori Tunggal, Ganda, Regu.</p>
        </div>
        <a href="{{ route('admin.peserta.pool.create') }}"
            class="bg-primary-500 hover:bg-primary-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-primary-100 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path d="M12 4v16m8-8H4" />
            </svg>
            Tambah Pool Baru
        </a>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Partai</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Kategori</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Daftar Peserta & Kontingen</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($data_pool as $pool)
                    <tr class="hover:bg-gray-50/50 transition-all">
                        <td class="px-6 py-4 font-bold text-gray-900">{{ $pool->no_partai }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span class="block font-bold">{{ $pool->kategori->nama_kategori }}</span>
                            <span class="text-[10px] text-gray-400">{{ $pool->kelas->label_kelas }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-2">
                                @foreach ($pool->detail_peserta->sortBy('no_urut') as $dp)
                                    <div class="flex items-center gap-3 bg-gray-50 p-2 rounded-lg border border-gray-100">
                                        <span
                                            class="w-6 h-6 flex items-center justify-center bg-primary-500 text-white rounded-full text-[10px] font-bold">{{ $dp->no_urut }}</span>
                                        <div>
                                            <p class="text-xs font-bold text-gray-900">{{ $dp->peserta->nama_peserta }}</p>
                                            <p class="text-[9px] text-gray-400 font-bold uppercase">
                                                {{ $dp->peserta->kontingen }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            {{-- Kita gunakan flex-row dan w-fit untuk memastikan kontainer hanya selebar isinya --}}
                            <div class="flex items-center justify-center gap-1">

                                {{-- Tombol Edit --}}
                                <a href="{{ route('admin.peserta.pool.edit', $pool->id_partai) }}"
                                    class="inline-flex items-center justify-center p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>

                                {{-- Tombol Delete --}}
                                {{-- Paksa form menjadi murni inline dengan style="display: inline;" jika Tailwind belum cukup --}}
                                <form action="{{ route('admin.peserta.pool.destroy', $pool->id_partai) }}" method="POST"
                                    id="delete-form-{{ $pool->id_partai }}" style="display: inline; margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        onclick="confirmDelete('{{ $pool->id_partai }}', '{{ $pool->no_partai }}')"
                                        class="inline-flex items-center justify-center p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path
                                                d="M19 7l-.867 12.142A2 2.001 0 0116.138 21H7.862a2 2.001 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $data_pool->links() }}
        </div>
    </div>
@endsection

<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Pool?',
            text: "Anda akan menghapus " + name + ". Semua data peserta di dalamnya akan hilang permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // Warna merah tailwind
            cancelButtonColor: '#6b7280', // Warna abu-abu tailwind
            confirmButtonText: 'Ya, Hapus Sekarang!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            borderRadius: '15px',
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form secara manual jika user klik Ya
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
