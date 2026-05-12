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
                    <div
                        class="w-24 h-24 rounded-2xl mx-auto bg-[#0D8ABC] text-white flex items-center justify-center shadow-sm select-none">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128"
                            class="w-24 h-24 rounded-2xl mx-auto shadow-sm select-none">
                            <rect width="128" height="128" fill="#0D8ABC" />
                            <text x="50%" y="50%" text-anchor="middle" dy=".35em" fill="#ffffff"
                                font-family="system-ui, -apple-system, sans-serif" font-size="50" font-weight="bold">
                                RA
                            </text>
                        </svg>
                    </div>
                    <button
                        class="absolute -bottom-2 -right-2 p-2 bg-white border border-gray-100 rounded-lg shadow-sm hover:bg-gray-50 text-primary-600 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
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
                                <path
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-gray-600">rian.hidayat@gmail.com</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <div class="p-2 bg-gray-50 rounded-lg text-gray-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="text-gray-600">Kota Serang, Banten</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
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
        </div>
    </div>
@endsection
