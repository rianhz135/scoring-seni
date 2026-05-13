@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Profil Admin</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi akun dan pengaturan keamanan Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm text-center">
                <div class="relative inline-block">
                    <div class="w-24 h-24 rounded-2xl mx-auto bg-[#0D8ABC] text-white flex items-center justify-center shadow-sm select-none">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128" class="w-24 h-24 rounded-2xl mx-auto shadow-sm select-none">
                            <rect width="128" height="128" fill="#0D8ABC" />
                            <text x="50%" y="50%" text-anchor="middle" dy=".35em" fill="#ffffff"
                                font-family="system-ui, -apple-system, sans-serif" font-size="50" font-weight="bold">
                                RA
                            </text>
                        </svg>
                    </div>
                    <button class="absolute -bottom-2 -right-2 p-2 bg-white border border-gray-100 rounded-lg shadow-sm hover:bg-gray-50 text-primary-600 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                <h2 class="mt-4 text-lg font-bold text-gray-900">Rian Hidayat</h2>
                <p class="text-xs font-semibold text-primary-600 uppercase tracking-wider">Super Admin</p>

                <hr class="my-6 border-gray-50">

                <div class="text-left space-y-4">
                    <div class="flex items-center gap-3 text-sm">
                        <div class="p-2 bg-gray-50 rounded-lg text-gray-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-gray-600">rian.hidayat@gmail.com</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <div class="p-2 bg-gray-50 rounded-lg text-gray-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="text-gray-600">Kota Serang, Banten</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">

            {{-- Informasi Personal --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-8 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Informasi Personal</h3>
                <form action="#" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Nama Lengkap</label>
                        <input type="text" value="Rian Hidayat"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Alamat Email</label>
                        <input type="email" value="rian.hidayat@gmail.com"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all text-sm">
                    </div>
                    <div class="md:col-span-2 pt-2">
                        <button type="submit" disabled
                            class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-primary-100 transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Keamanan Akun --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-8 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-6 text-red-600">Keamanan Akun</h3>
                <form action="#" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Password Baru</label>
                            <input type="password" placeholder="••••••••"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Konfirmasi Password</label>
                            <input type="password" placeholder="••••••••"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all text-sm">
                        </div>
                    </div>
                    <button type="submit" disabled
                        class="bg-gray-900 hover:bg-black text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all shadow-lg shadow-gray-200">
                        Update Password
                    </button>
                </form>
            </div>

            {{-- Reset Data --}}
            <div class="bg-white border border-red-100 rounded-2xl p-8 shadow-sm">
                <div class="flex items-start gap-4 mb-6">
                    <div class="p-2.5 bg-red-50 rounded-xl text-red-500 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-red-600">Zona Berbahaya</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Tindakan berikut bersifat permanen dan tidak dapat dibatalkan.</p>
                    </div>
                </div>

                <div class="space-y-3">

                    {{-- Reset Semua --}}
                    <div class="flex items-center justify-between p-4 bg-red-600 border border-red-700 rounded-xl">
                        <div>
                            <p class="text-sm font-bold text-white">Reset Semua Data</p>
                            <p class="text-xs text-red-200 mt-0.5">Menghapus seluruh data sistem. Tidak dapat dikembalikan!</p>
                        </div>
                        <button onclick="konfirmasiReset('semua')"
                            class="flex-shrink-0 ml-4 px-4 py-2 bg-white border border-white text-red-600 hover:bg-red-800 hover:text-white hover:border-red-800 rounded-xl text-sm font-bold transition-all">
                            Reset Semua
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script>
        const resetRoutes = {
            semua:  '{{ route("admin.reset.semua") }}',
        };

        const resetLabels = {
            nilai:  'semua data nilai juri',
            partai: 'semua data partai',
            semua:  'SELURUH DATA SISTEM',
        };

        function konfirmasiReset(tipe) {
            Swal.fire({
                title: 'Anda Yakin?',
                html: `Tindakan ini akan menghapus <b>${resetLabels[tipe]}</b> secara permanen.<br><br>Ketik <b>RESET</b> untuk konfirmasi.`,
                input: 'text',
                inputPlaceholder: 'Ketik RESET',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                preConfirm: (val) => {
                    if (val !== 'RESET') {
                        Swal.showValidationMessage('Ketik RESET untuk melanjutkan');
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(resetRoutes[tipe], {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message ?? 'Data berhasil direset.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => window.location.reload());
                        } else {
                            Swal.fire('Gagal', data.message ?? 'Terjadi kesalahan.', 'error');
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Gagal terhubung ke server.', 'error'));
                }
            });
        }
    </script>
@endsection