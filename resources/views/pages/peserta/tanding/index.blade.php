@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Peserta Tanding</h1>
            <p class="text-sm text-gray-400 mt-0.5">Kelola data jadwal partai dan peserta kategori Tanding di sini.</p>
        </div>
        <a href="{{ route('admin.peserta.tanding.create') }}"
            class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Partai
        </a>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest w-16 text-center">No</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest w-24 text-center">Partai</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest w-40">Fase & Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center">Sudut Biru</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center">Sudut Merah</th>
                        <th class="px-3 py-4 text-xs font-semibold text-gray-400 uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($partais as $partai)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-600 text-center">
                                {{ $loop->iteration + ($partais->firstItem() - 1) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-black text-gray-900 text-lg">#{{ $partai->no_partai }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="whitespace-nowrap px-2.5 py-1 rounded-md bg-primary-50 text-primary-700 text-xs font-bold uppercase border border-primary-100 w-max">
                                        {{ $partai->kelas }}
                                    </span>
                                    <span class="text-xs text-gray-500 font-medium tracking-wide">
                                        {{ $partai->fase_babak }}
                                    </span>
                                </div>
                            </td>

                            {{-- Sudut Biru (Tanding) --}}
                            <td class="px-6 py-4">
                                <div class="p-3 rounded-xl bg-blue-50 border border-blue-100 text-center min-w-[200px]">
                                    @if($partai->pesertaBiru)
                                        <div class="text-sm font-bold text-blue-700">
                                            {{ $partai->pesertaBiru->nama }}
                                        </div>
                                        <p class="text-[10px] text-blue-500 uppercase font-medium tracking-tight">
                                            {{ $partai->pesertaBiru->kontingen }}
                                        </p>
                                    @elseif($partai->menunggu_pemenang_biru_dari)
                                        <div class="text-xs font-semibold text-blue-600/70 italic flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Menunggu Pemenang #{{ $partai->menunggu_pemenang_biru_dari }}
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 font-medium">Belum Ditentukan</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Sudut Merah (Tanding) --}}
                            <td class="px-6 py-4">
                                <div class="p-3 rounded-xl bg-red-50 border border-red-100 text-center min-w-[200px]">
                                    @if($partai->pesertaMerah)
                                        <div class="text-sm font-bold text-red-700">
                                            {{ $partai->pesertaMerah->nama }}
                                        </div>
                                        <p class="text-[10px] text-red-500 uppercase font-medium tracking-tight">
                                            {{ $partai->pesertaMerah->kontingen }}
                                        </p>
                                    @elseif($partai->menunggu_pemenang_merah_dari)
                                        <div class="text-xs font-semibold text-red-600/70 italic flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Menunggu Pemenang #{{ $partai->menunggu_pemenang_merah_dari }}
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 font-medium">Belum Ditentukan</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-3 py-4 text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <a href="{{ route('admin.peserta.tanding.edit', $partai->id) }}"
                                        class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </a>
                                    <button type="button" onclick="confirmDelete('{{ $partai->id }}', '{{ $partai->no_partai }}')" 
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>

                                    <form id="delete-form-{{ $partai->id }}"
                                        action="{{ route('admin.peserta.tanding.destroy', $partai->id) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-sm">
                                Belum ada data partai / peserta Tanding.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Render Pagination --}}
            <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50">
                {{ $partais->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id, noPartai) {
        Swal.fire({
            title: 'Hapus Partai #' + noPartai + '?',
            text: "Data partai tanding (termasuk riwayat nilainya) akan ikut terhapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', 
            cancelButtonColor: '#6b7280', 
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            border: 'none',
            borderRadius: '1.5rem',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

    @if(session('success'))
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
        Toast.fire({
            icon: 'success',
            title: "{{ session('success') }}"
        });
    @endif
</script>
@endpush