<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Juri</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-900 p-6">

    <div class="max-w-7xl mx-auto">

        {{-- HEADER --}}
        <div class="mb-10 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-4xl font-black text-white tracking-tight">DASHBOARD JURI</h1>
                    <span class="px-3 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/40 rounded-xl text-xs font-black uppercase tracking-wider">
                        {{ $pengaturanDisplay->gelanggang ?? 'GELANGGANG 1' }}
                    </span>
                </div>
                <p class="text-slate-400 mt-2 font-medium">
                    {{ $pengaturanDisplay->nama_kejuaraan ?? 'Kejuaraan Pencak Silat' }} &bull; Pilih kategori penilaian pertandingan
                </p>
            </div>

            <div class="flex items-center gap-3">

                {{-- INFO GELANGGANG & JURI --}}
                <div class="bg-slate-800 border border-slate-700 rounded-2xl px-5 py-3 flex items-center gap-4">
                    <div>
                        <p class="text-slate-400 text-xs uppercase font-bold">Gelanggang</p>
                        <h2 class="text-base font-black text-amber-400 uppercase">{{ $pengaturanDisplay->gelanggang ?? 'GELANGGANG 1' }}</h2>
                    </div>
                    <div class="w-px h-8 bg-slate-700"></div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase font-bold">Login Sebagai</p>
                        <h2 class="text-base font-black text-white">{{ auth()->user()->name ?? 'Juri' }}</h2>
                    </div>
                </div>

                {{-- LOGOUT --}}
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-5 py-3 bg-red-500 hover:bg-red-600 text-white rounded-2xl font-bold transition-all cursor-pointer">
                        Logout
                    </button>
                </form>

            </div>
        </div>

        {{-- MENU KATEGORI UTAMA --}}
        <div id="menu-kategori" class="flex flex-col gap-10">

            {{-- ============================================== --}}
            {{-- BARIS PERTAMA : SISTEM POOL --}}
            {{-- ============================================== --}}
            <div>
                <div class="flex items-center gap-6 mb-5">
                    <h3 class="text-xl font-black text-slate-300 tracking-widest uppercase">Sistem Pool</h3>
                    <div class="h-px bg-slate-700 flex-1"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- 1. POOL SOLO GANDA --}}
                    <button onclick="bukaModal('solo-ganda', 'POOL SOLO GANDA', '🎭', 'cyan')"
                        class="group bg-white rounded-3xl p-8 hover:scale-[1.02] transition-all shadow-2xl text-left cursor-pointer">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 font-bold uppercase text-sm">Kategori</p>
                                <h2 class="text-3xl font-black text-slate-800 mt-2">SOLO GANDA</h2>
                            </div>
                            <div
                                class="w-16 h-16 rounded-2xl bg-cyan-100 flex items-center justify-center text-3xl group-hover:bg-cyan-500 group-hover:text-white transition-all shrink-0">
                                🎭
                            </div>
                        </div>
                    </button>

                    {{-- 2. POOL TUNGGAL REGU --}}
                    <button onclick="bukaModal('tunggal-regu', 'POOL TUNGGAL REGU', '🎪', 'fuchsia')"
                        class="group bg-white rounded-3xl p-8 hover:scale-[1.02] transition-all shadow-2xl text-left cursor-pointer">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 font-bold uppercase text-sm">Kategori</p>
                                <h2 class="text-3xl font-black text-slate-800 mt-2">TUNGGAL REGU</h2>
                            </div>
                            <div
                                class="w-16 h-16 rounded-2xl bg-fuchsia-100 flex items-center justify-center text-3xl group-hover:bg-fuchsia-500 group-hover:text-white transition-all shrink-0">
                                🎪
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            {{-- ============================================== --}}
            {{-- BARIS KEDUA : SISTEM BATTLE --}}
            {{-- ============================================== --}}
            <div>
                <div class="flex items-center gap-6 mb-5">
                    <h3 class="text-xl font-black text-slate-300 tracking-widest uppercase">Sistem Battle</h3>
                    <div class="h-px bg-slate-700 flex-1"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- 3. SOLO --}}
                    <button onclick="bukaModal('solo', 'SOLO (BATTLE)', '🥋', 'indigo')"
                        class="group bg-white rounded-3xl p-6 hover:scale-[1.02] transition-all shadow-2xl text-left cursor-pointer">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 font-bold uppercase text-sm">Kategori</p>
                                <h2 class="text-2xl font-black text-slate-800 mt-1">SOLO</h2>
                            </div>
                            <div
                                class="w-14 h-14 rounded-2xl bg-indigo-100 flex items-center justify-center text-2xl group-hover:bg-indigo-500 group-hover:text-white transition-all shrink-0">
                                🥋
                            </div>
                        </div>
                    </button>

                    {{-- 4. GANDA --}}
                    <button onclick="bukaModal('ganda', 'GANDA (BATTLE)', '👥', 'emerald')"
                        class="group bg-white rounded-3xl p-6 hover:scale-[1.02] transition-all shadow-2xl text-left cursor-pointer">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 font-bold uppercase text-sm">Kategori</p>
                                <h2 class="text-2xl font-black text-slate-800 mt-1">GANDA</h2>
                            </div>
                            <div
                                class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center text-2xl group-hover:bg-emerald-500 group-hover:text-white transition-all shrink-0">
                                👥
                            </div>
                        </div>
                    </button>

                    {{-- 5. TUNGGAL --}}
                    <button onclick="bukaModal('tunggal', 'TUNGGAL (BATTLE)', '⚔️', 'orange')"
                        class="group bg-white rounded-3xl p-6 hover:scale-[1.02] transition-all shadow-2xl text-left cursor-pointer">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 font-bold uppercase text-sm">Kategori</p>
                                <h2 class="text-2xl font-black text-slate-800 mt-1">TUNGGAL</h2>
                            </div>
                            <div
                                class="w-14 h-14 rounded-2xl bg-orange-100 flex items-center justify-center text-2xl group-hover:bg-orange-500 group-hover:text-white transition-all shrink-0">
                                ⚔️
                            </div>
                        </div>
                    </button>

                    {{-- 6. REGU --}}
                    <button onclick="bukaModal('regu', 'REGU (BATTLE)', '🛡️', 'red')"
                        class="group bg-white rounded-3xl p-6 hover:scale-[1.02] transition-all shadow-2xl text-left cursor-pointer">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 font-bold uppercase text-sm">Kategori</p>
                                <h2 class="text-2xl font-black text-slate-800 mt-1">REGU</h2>
                            </div>
                            <div
                                class="w-14 h-14 rounded-2xl bg-red-100 flex items-center justify-center text-2xl group-hover:bg-red-500 group-hover:text-white transition-all shrink-0">
                                🛡️
                            </div>
                        </div>
                    </button>
                </div>
            </div>


            {{-- ============================================== --}}
            {{-- BARIS KETIGA : KATEGORI TANDING               --}}
            {{-- ============================================== --}}
            <div>
                <div class="flex items-center gap-6 mb-5">
                    <h3 class="text-xl font-black text-slate-300 tracking-widest uppercase">Kategori Tanding</h3>
                    <div class="h-px bg-slate-700 flex-1"></div>
                </div>

                <div class="bg-gradient-to-r from-red-950/80 via-slate-800 to-blue-950/80 border border-slate-700 rounded-3xl p-6 shadow-2xl flex flex-col lg:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-rose-500/20 text-rose-300 flex items-center justify-center text-3xl border border-rose-500/30 shrink-0">
                            🥊
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                    Pertarungan Sudut Merah vs Biru
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    Pukulan (+1) & Tendangan (+2)
                                </span>
                            </div>
                            <h2 class="text-2xl md:text-3xl font-black text-white">PENILAIAN TANDING</h2>
                            <p class="text-slate-400 text-sm font-medium mt-0.5">Pilih nomor meja juri untuk masuk ke panel penilaian</p>
                        </div>
                    </div>

                    {{-- Tombol Langsung Login Juri 1, 2, 3 --}}
                    <div class="grid grid-cols-3 gap-3 w-full lg:w-auto">
                        <a href="{{ url('/juri/tanding/1') }}"
                            class="px-6 py-4 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white flex flex-col items-center justify-center shadow-lg shadow-blue-900/40 hover:scale-105 transition-all text-center">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest opacity-80">MASUK KE</span>
                            <span class="text-2xl font-black">JURI 1</span>
                        </a>
                        <a href="{{ url('/juri/tanding/2') }}"
                            class="px-6 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white flex flex-col items-center justify-center shadow-lg shadow-indigo-900/40 hover:scale-105 transition-all text-center">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest opacity-80">MASUK KE</span>
                            <span class="text-2xl font-black">JURI 2</span>
                        </a>
                        <a href="{{ url('/juri/tanding/3') }}"
                            class="px-6 py-4 rounded-2xl bg-red-600 hover:bg-red-500 text-white flex flex-col items-center justify-center shadow-lg shadow-red-900/40 hover:scale-105 transition-all text-center">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest opacity-80">MASUK KE</span>
                            <span class="text-2xl font-black">JURI 3</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL PILIH JURI --}}
    <div id="modal-juri"
        class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
        <div class="bg-slate-800 border border-slate-700 rounded-3xl p-8 w-full max-w-md shadow-2xl">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <span id="modal-icon" class="text-3xl"></span>
                    <div>
                        <p class="text-slate-400 text-xs uppercase font-bold">Kategori</p>
                        <h3 id="modal-title" class="text-2xl font-black text-white"></h3>
                    </div>
                </div>
                <button onclick="tutupModal()"
                    class="w-10 h-10 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-300 flex items-center justify-center transition-all cursor-pointer">
                    ✕
                </button>
            </div>

            <p class="text-slate-400 font-bold uppercase text-sm mb-4">Pilih No. Juri</p>

            {{-- Grid Juri --}}
            <div class="grid grid-cols-3 gap-3" id="grid-juri">
                {{-- Diisi oleh JS --}}
            </div>

        </div>
    </div>

    <script>
        let kategoriAktif = '';

        const warnaBg = {
            cyan: {
                normal: 'bg-cyan-500/20 border-cyan-500/30 text-cyan-300',
                hover: 'hover:bg-cyan-500 hover:text-white hover:border-cyan-500'
            },
            fuchsia: {
                normal: 'bg-fuchsia-500/20 border-fuchsia-500/30 text-fuchsia-300',
                hover: 'hover:bg-fuchsia-500 hover:text-white hover:border-fuchsia-500'
            },
            indigo: {
                normal: 'bg-indigo-500/20 border-indigo-500/30 text-indigo-300',
                hover: 'hover:bg-indigo-500 hover:text-white hover:border-indigo-500'
            },
            emerald: {
                normal: 'bg-emerald-500/20 border-emerald-500/30 text-emerald-300',
                hover: 'hover:bg-emerald-500 hover:text-white hover:border-emerald-500'
            },
            orange: {
                normal: 'bg-orange-500/20 border-orange-500/30 text-orange-300',
                hover: 'hover:bg-orange-500 hover:text-white hover:border-orange-500'
            },
            red: {
                normal: 'bg-red-500/20 border-red-500/30 text-red-300',
                hover: 'hover:bg-red-500 hover:text-white hover:border-red-500'
            },
        };

        function bukaModal(kategori, label, icon, warna) {
            kategoriAktif = kategori;
            document.getElementById('modal-title').innerText = label;
            document.getElementById('modal-icon').innerText = icon;

            const grid = document.getElementById('grid-juri');
            grid.innerHTML = '';

            const w = warnaBg[warna] || warnaBg['indigo'];

            for (let i = 1; i <= 6; i++) {
                let url = `/juri/${kategori}/${i}`;

                const btn = document.createElement('a');
                btn.href = url;
                btn.className = `border rounded-2xl py-5 flex flex-col items-center justify-center gap-1 font-black transition-all ${w.normal} ${w.hover} cursor-pointer`;
                btn.innerHTML = `
                    <span class="text-xs uppercase tracking-widest opacity-70">Juri</span>
                    <span class="text-4xl">${i}</span>
                `;
                grid.appendChild(btn);
            }

            document.getElementById('modal-juri').classList.remove('hidden');
            document.getElementById('modal-juri').classList.add('flex');
        }

        function tutupModal() {
            document.getElementById('modal-juri').classList.add('hidden');
            document.getElementById('modal-juri').classList.remove('flex');
        }

        document.getElementById('modal-juri').addEventListener('click', function(e) {
            if (e.target === this) tutupModal();
        });
    </script>

</body>

</html>