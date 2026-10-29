@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Profil & Pengaturan Display</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola branding layar display proyektor, informasi akun, dan pengaturan keamanan Anda.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm shadow-xs">
            <p class="font-bold mb-1">Terjadi kesalahan validasi:</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KIRI: KARTU AKUN ADMIN --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm text-center">
                <div class="relative inline-block">
                    <div class="w-24 h-24 rounded-2xl mx-auto bg-gradient-to-br from-[#0D8ABC] to-indigo-700 text-white flex items-center justify-center shadow-md select-none text-2xl font-black">
                        {{ strtoupper(substr($user->name ?? 'AD', 0, 2)) }}
                    </div>
                </div>
                <h2 class="mt-4 text-lg font-bold text-gray-900">{{ $user->name ?? 'Administrator' }}</h2>
                <p class="text-xs font-bold text-primary-600 uppercase tracking-wider">{{ $user->role ?? 'Admin' }}</p>

                <hr class="my-5 border-gray-100">

                <div class="text-left space-y-3">
                    <div class="flex items-center gap-3 text-sm">
                        <div class="p-2 bg-gray-50 rounded-lg text-gray-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-gray-600 font-medium text-xs break-all">{{ $user->email ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <div class="p-2 bg-gray-50 rounded-lg text-gray-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="text-gray-600 font-medium text-xs">Username: {{ $user->username ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- LIVE PREVIEW CARD DISPLAY --}}
            <div class="bg-gradient-to-br from-slate-900 to-indigo-950 rounded-2xl p-5 text-white shadow-md border border-slate-800">
                <div class="flex items-center justify-between mb-3 border-b border-slate-700/60 pb-2">
                    <span class="text-[11px] font-black uppercase tracking-wider text-amber-300 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Preview Header Display
                    </span>
                    <a href="{{ route('dewan.display') }}" target="_blank" class="text-[10px] bg-white/10 hover:bg-white/20 text-white px-2 py-0.5 rounded transition-all">
                        Buka Layar ↗
                    </a>
                </div>
                
                <div class="text-center py-2">
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Judul Kejuaraan:</p>
                    <p class="text-sm font-black text-amber-300 drop-shadow uppercase mt-0.5 line-clamp-2">
                        {{ $pengaturan->nama_kejuaraan ?? 'PERTANDINGAN SMI CUP 2026' }}
                    </p>
                </div>

                <div class="mt-3 pt-2.5 border-t border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-400 text-[11px]">Gelanggang:</span>
                    <span class="font-black px-2 py-0.5 bg-primary-600 rounded text-white text-[11px] uppercase tracking-wider">
                        {{ $pengaturan->gelanggang ?? 'GELANGGANG 1' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- KANAN: FORM PENGATURAN DISPLAY & AKUN --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. PENGATURAN DISPLAY (GELANGGANG, INFORMASI KEJUARAAN & LOGO) --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-start justify-between gap-4 mb-6">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-lg">📺</span>
                            <h3 class="text-lg font-bold text-gray-900">Pengaturan Display (Tanding, Seni, Standby)</h3>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Ubah teks Gelanggang, judul informasi kejuaraan, dan logo yang tampil di layar display proyektor secara real-time.</p>
                    </div>
                </div>

                <form action="{{ route('admin.profile.update-display') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama Gelanggang --}}
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1">
                                Text Gelanggang <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="gelanggang" value="{{ old('gelanggang', $pengaturan->gelanggang) }}" required
                                placeholder="Contoh: GELANGGANG 1 atau GELANGGANG A"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all text-sm font-semibold">
                            <p class="text-[11px] text-gray-400">Teks ini tampil di badge informasi pada Display Tanding, Seni, dan Standby.</p>
                        </div>

                        {{-- Nama Kejuaraan --}}
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1">
                                Informasi Kejuaraan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_kejuaraan" value="{{ old('nama_kejuaraan', $pengaturan->nama_kejuaraan) }}" required
                                placeholder="Contoh: PERTANDINGAN SMI CUP 2026"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all text-sm font-semibold">
                            <p class="text-[11px] text-gray-400">Judul utama event kejuaraan di bagian atas layar display.</p>
                        </div>
                    </div>

                    {{-- GRID UPLOAD LOGO 1 & LOGO 2 --}}
                    <div class="pt-2 border-t border-gray-100">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">
                            Logo Layar Display (Kiri & Kanan)
                        </label>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- LOGO 1 (KIRI / UTAMA) --}}
                            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/60 flex flex-col justify-between gap-3">
                                <div>
                                    <span class="text-xs font-bold text-gray-800 flex items-center gap-1">
                                        🛡️ Logo Kiri (Logo Utama / Penyelenggara)
                                    </span>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Tampil di sisi kiri atas Tanding/Seni dan di tengah mode Standby.</p>
                                </div>

                                <div class="flex items-center gap-4 my-1">
                                    <div class="w-20 h-20 rounded-xl bg-slate-900 border border-slate-700 flex items-center justify-center p-2 shrink-0 shadow-inner">
                                        @if (!empty($pengaturan->logo_kiri) && file_exists(public_path($pengaturan->logo_kiri)))
                                            <img src="{{ asset($pengaturan->logo_kiri) }}" alt="Logo Kiri" class="max-h-full max-w-full object-contain">
                                        @elseif (file_exists(public_path('smi.png')))
                                            <img src="{{ asset('smi.png') }}" alt="Logo Kiri" class="max-h-full max-w-full object-contain">
                                        @else
                                            <span class="text-xs text-gray-400">Default</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 space-y-1">
                                        <input type="file" name="logo_kiri" accept="image/*"
                                            class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                                        <p class="text-[10px] text-gray-400">PNG transparan disarankan. Maks: 3MB.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- LOGO 2 (KANAN / SPONSOR) --}}
                            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/60 flex flex-col justify-between gap-3">
                                <div>
                                    <span class="text-xs font-bold text-gray-800 flex items-center gap-1">
                                        🏆 Logo Kanan (Logo Tambahan / Sponsor)
                                    </span>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Tampil di sisi kanan atas layar display (opsional).</p>
                                </div>

                                <div class="flex items-center gap-4 my-1">
                                    <div class="w-20 h-20 rounded-xl bg-slate-900 border border-slate-700 flex items-center justify-center p-2 shrink-0 shadow-inner">
                                        @if (!empty($pengaturan->logo_kanan) && file_exists(public_path($pengaturan->logo_kanan)))
                                            <img src="{{ asset($pengaturan->logo_kanan) }}" alt="Logo Kanan" class="max-h-full max-w-full object-contain">
                                        @elseif (file_exists(public_path('logo2.png')))
                                            <img src="{{ asset('logo2.png') }}" alt="Logo Kanan" class="max-h-full max-w-full object-contain">
                                        @else
                                            <div class="text-amber-400 text-2xl">🏆</div>
                                        @endif
                                    </div>
                                    <div class="flex-1 space-y-1.5">
                                        <input type="file" name="logo_kanan" accept="image/*"
                                            class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                                        
                                        @if(!empty($pengaturan->logo_kanan))
                                            <label class="flex items-center gap-1.5 text-[11px] text-red-600 font-semibold cursor-pointer">
                                                <input type="checkbox" name="hapus_logo_kanan" value="1" class="rounded text-red-600 focus:ring-0">
                                                Hapus logo kanan (Gunakan icon default)
                                            </label>
                                        @else
                                            <p class="text-[10px] text-gray-400">Kosongkan jika ingin memakai icon default.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                            class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-md shadow-primary-200 transition-all flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan & Sinkronkan ke Display
                        </button>
                    </div>
                </form>
            </div>

            {{-- 2. INFORMASI PERSONAL ADMIN --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 sm:p-8 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Informasi Personal Admin</h3>
                <form action="{{ route('admin.profile.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all text-sm font-medium">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition-all text-sm font-medium">
                    </div>
                    <div class="md:col-span-2 pt-2 flex justify-end">
                        <button type="submit"
                            class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer">
                            Simpan Profil
                        </button>
                    </div>
                </form>
            </div>

            {{-- 3. KEAMANAN AKUN --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 sm:p-8 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Keamanan Akun</h3>
                <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Password Baru</label>
                            <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all text-sm font-medium">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-700">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required minlength="6" placeholder="Ketik ulang password baru"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all text-sm font-medium">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm cursor-pointer">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

            {{-- 4. RESET DATA (ZONA BERBAHAYA) --}}
            <div class="bg-white border border-red-100 rounded-2xl p-6 sm:p-8 shadow-sm">
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
                    <div class="flex items-center justify-between p-4 bg-red-600 border border-red-700 rounded-xl">
                        <div>
                            <p class="text-sm font-bold text-white">Reset Semua Data</p>
                            <p class="text-xs text-red-200 mt-0.5">Menghapus seluruh data sistem. Tidak dapat dikembalikan!</p>
                        </div>
                        <button type="button" onclick="konfirmasiReset('semua')"
                            class="flex-shrink-0 ml-4 px-4 py-2 bg-white border border-white text-red-600 hover:bg-red-800 hover:text-white hover:border-red-800 rounded-xl text-sm font-bold transition-all cursor-pointer">
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
