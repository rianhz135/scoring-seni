@extends('layouts.admin')
@section('content')
    <div class="mb-6 flex justify-between items-center max-w-2xl">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Kelas</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola daftar kelas untuk kategori tanding.</p>
        </div>
        <button type="button" onclick="openModal('modal-tambah')"
            class="bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-primary-100 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path d="M12 4v16m8-8H4" />
            </svg>
            Tambah Kelas
        </button>
    </div>

    <div class="max-w-2xl bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50/50 border-b border-gray-100">
                <tr>
                    <th class="w-16 px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">No
                    </th>

                    <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-left">Label
                        Kelas</th>

                    <th class="w-32 px-12 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-left">
                        Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($data_kelas as $kelas)
                    <tr class="hover:bg-gray-50/50 transition-colors group text-sm">
                        <td class="px-6 py-3 text-gray-500 font-medium text-center">{{ $loop->iteration }}</td>
                        <td class="px-6 py-3">
                            <span
                                class="px-2.5 py-1 bg-primary-50 text-primary-700 rounded-lg text-[11px] font-bold uppercase tracking-wider">
                                {{ $kelas->label_kelas }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                <button type="button"
                                    onclick="openEditModal({{ $kelas->id_kelas }}, @json($kelas->label_kelas))"
                                    class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>

                                <form id="delete-form-{{ $kelas->id_kelas }}"
                                    action="{{ route('admin.manajemen.kelas.destroy', $kelas->id_kelas) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete({{ $kelas->id_kelas }})"
                                        class="p-1.5 text-red-500 hover:bg-red-50 rounded-md transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    {{-- empty state --}}
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="modal-tambah" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"
            onclick="closeModal('modal-tambah')"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <form id="form-kelas" action="{{ route('admin.manajemen.kelas.store') }}" method="POST"
                class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative p-6" onclick="event.stopPropagation()">
                @csrf
                <div id="method-field"></div>

                <div class="flex justify-between items-center mb-6">
                    <h3 id="modal-title" class="text-lg font-bold text-gray-900">Tambah Kelas</h3>
                    <button type="button" onclick="closeModal('modal-tambah')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama
                            Kelas</label>
                        <input type="text" name="label_kelas" id="input-label-kelas" placeholder="Contoh: Kelas A"
                            required
                            class="w-full px-4 py-3 rounded-xl border border-gray-100 focus:border-primary-500 focus:ring-4 focus:ring-primary-50 outline-none transition-all">
                        @error('label_kelas')
                            <p id="error-label"
                                class="error-message text-red-500 text-[10px] mt-1 font-bold italic tracking-wide">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <button type="submit" id="btn-submit"
                        class="w-full bg-primary-600 text-white py-3 rounded-xl font-bold shadow-lg shadow-primary-100 hover:bg-primary-700 transition-all">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Kelas?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4254e8', // Warna primary kamu
                cancelButtonColor: '#ef4444', // Warna merah
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                borderRadius: '15px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form berdasarkan ID unik tadi
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
        // Konfigurasi standar untuk Toast
        const toast = window.Toast || Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Jalankan Toast jika ada session success dari Laravel
        @if (session('success'))
            toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        // Jalankan Toast jika ada session error
        @if (session('error'))
            toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        @endif

        function openModal(id) {
            if (id === 'modal-tambah') {
                // RESET ke mode TAMBAH
                document.getElementById('modal-title').innerText = 'Tambah Kelas';
                document.getElementById('btn-submit').innerText = 'Simpan Data';
                document.getElementById('input-label-kelas').value = '';
                document.getElementById('method-field').innerHTML = ''; // Hapus @method('PUT')
                document.getElementById('form-kelas').action = "{{ route('admin.manajemen.kelas.store') }}";
            }
            document.getElementById(id).classList.remove('hidden');
        }

        function openEditModal(id, label) {
            // 1. Ubah Judul dan Teks Tombol
            document.getElementById('modal-title').innerText = 'Edit Kelas';
            document.getElementById('btn-submit').innerText = 'Update Data';

            // 2. Isi Input dengan nama kelas yang akan diedit
            document.getElementById('input-label-kelas').value = label;

            // 3. Tambahkan hidden input untuk Method PUT (Syarat Update Laravel)
            document.getElementById('method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';

            // 4. Ubah URL Action Form ke Update (Contoh: /admin/manajemen/kelas/5)
            document.getElementById('form-kelas').action = "{{ url('admin/manajemen/kelas') }}" + "/" + id;

            // 5. Tampilkan Modal
            document.getElementById('modal-tambah').classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        // Cari elemen alert
        const alertSuccess = document.getElementById('alert-success');

        if (alertSuccess) {
            // Tunggu 3 detik (3000ms)
            setTimeout(() => {
                // Beri efek fade out
                alertSuccess.style.opacity = '0';

                // Hapus elemen dari layout setelah transisi selesai
                setTimeout(() => {
                    alertSuccess.remove();
                }, 500);
            }, 3000);
        }
        // Jalankan Toast jika ada error validasi global
        @if ($errors->any())
            toast.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                text: '{{ $errors->first() }}' // Mengambil pesan error pertama
            });

            // Opsional: Jika ada error, otomatis buka kembali modalnya agar user bisa langsung perbaiki
            document.getElementById('modal-tambah').classList.remove('hidden');
        @endif
    </script>
@endsection
