@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Peserta Ganda</h1>
            <p class="text-sm text-gray-400 mt-0.5">Kelola data peserta kategori ganda di sini.</p>
        </div>
        <a href="{{ route('admin.peserta.ganda.create') }}"
            class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Peserta
        </a>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th
                            class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest w-16 text-center">
                            No</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest w-32">No Partai
                        </th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest w-40">Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center">
                            Sudut Biru (Pasangan & Kontingen)</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center">
                            Sudut Merah (Pasangan & Kontingen)</th>
                        <th class="px-3 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($data_ganda as $partai)
                        @php
                            // Filter detail berdasarkan sudut dan kelompokkan berdasarkan no_urut
                            // Ganda biasanya punya 2 orang per sudut
                            $biru = $partai->detail_peserta->where('sudut', 'Biru');
                            $merah = $partai->detail_peserta->where('sudut', 'Merah');
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-600 text-center">
                                {{ $loop->iteration + ($data_ganda->firstItem() - 1) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-gray-900">#{{ $partai->no_partai }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="whitespace-nowrap px-2.5 py-1 rounded-md bg-primary-50 text-primary-700 text-xs font-bold uppercase border border-primary-100">
                                    {{ $partai->kelas->label_kelas ?? '-' }}
                                </span>
                            </td>

                            {{-- Sudut Biru (Ganda) --}}
                            <td class="px-6 py-4">
                                <div class="p-3 rounded-xl bg-blue-50 border border-blue-100 text-center min-w-[220px]">
                                    <div class="text-sm font-bold text-blue-700">
                                        @forelse($biru as $b)
                                            {{ $b->peserta->nama_peserta }}{{ !$loop->last ? ' & ' : '' }}
                                        @empty
                                            TBD
                                        @endforelse
                                    </div>
                                    <p class="text-[10px] text-blue-500 uppercase font-medium tracking-tight">
                                        {{ $biru->first()->peserta->kontingen ?? '-' }}
                                    </p>
                                </div>
                            </td>

                            {{-- Sudut Merah (Ganda) --}}
                            <td class="px-6 py-4">
                                <div class="p-3 rounded-xl bg-red-50 border border-red-100 text-center min-w-[220px]">
                                    <div class="text-sm font-bold text-red-700">
                                        @forelse($merah as $m)
                                            {{ $m->peserta->nama_peserta }}{{ !$loop->last ? ' & ' : '' }}
                                        @empty
                                            TBD
                                        @endforelse
                                    </div>
                                    <p class="text-[10px] text-red-500 uppercase font-medium tracking-tight">
                                        {{ $merah->first()->peserta->kontingen ?? '-' }}
                                    </p>
                                </div>
                            </td>

                            <td class="px-3 py-4 text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <a href="{{ route('admin.peserta.ganda.edit', $partai->id_partai) }}"
                                        class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </a>
                                    <button type="button"
                                        onclick="confirmDelete('{{ $partai->id_partai }}', '{{ $partai->no_partai }}')"
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path
                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                    <form id="delete-form-{{ $partai->id_partai }}"
                                        action="{{ route('admin.peserta.ganda.destroy', $partai->id_partai) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">Data peserta ganda belum
                                tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination Footer --}}
        <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Menampilkan <span class="font-semibold text-gray-900">{{ $data_ganda->firstItem() }}</span> sampai <span
                    class="font-semibold text-gray-900">{{ $data_ganda->lastItem() }}</span> dari <span
                    class="font-semibold text-gray-900">{{ $data_ganda->total() }}</span> data
            </div>
            <div>{{ $data_ganda->links() }}</div>
        </div>
    </div>
@endsection
<script>
    function confirmDelete(id, noPartai) {
        Swal.fire({
            title: 'Hapus Partai #' + noPartai + '?',
            text: "Data atlet dan detail tanding akan ikut terhapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // Warna merah tailwind
            cancelButtonColor: '#6b7280', // Warna abu-abu tailwind
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            border: 'none',
            borderRadius: '1.5rem', // Sesuai dengan style rounded-2xl kamu
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form hidden yang sudah kita buat tadi
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
