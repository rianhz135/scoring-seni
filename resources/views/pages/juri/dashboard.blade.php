<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Juri</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-900 p-6">

    <div class="max-w-5xl mx-auto">

        {{-- HEADER --}}
        <div class="mb-8 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-4xl font-black text-white tracking-tight">DASHBOARD JURI</h1>
                <p class="text-slate-400 mt-2 font-medium">Pilih kategori penilaian pertandingan</p>
            </div>

            <div class="flex items-center gap-3">

                {{-- INFO JURI --}}
                <div class="bg-slate-800 border border-slate-700 rounded-2xl px-5 py-3 flex items-center gap-4">
                    <div>
                        <p class="text-slate-400 text-xs uppercase font-bold">Login Sebagai</p>
                        <h2 class="text-base font-black text-white">{{ auth()->user()->name }}</h2>
                    </div>
                </div>

                {{-- LOGOUT --}}
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-5 py-3 bg-red-500 hover:bg-red-600 text-white rounded-2xl font-bold transition-all">
                        Logout
                    </button>
                </form>

            </div>
        </div>

        {{-- MENU KATEGORI --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="menu-kategori">

            {{-- SOLO --}}
            <button onclick="bukaModal('solo', 'SOLO', '🥋', 'indigo')"
                class="group bg-white rounded-3xl p-8 hover:scale-[1.02] transition-all shadow-2xl text-left">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 font-bold uppercase text-sm">Kategori</p>
                        <h2 class="text-4xl font-black text-slate-800 mt-2">SOLO</h2>
                    </div>
                    <div
                        class="w-16 h-16 rounded-2xl bg-indigo-100 flex items-center justify-center text-3xl group-hover:bg-indigo-500 group-hover:text-white transition-all">
                        🥋
                    </div>
                </div>
            </button>

            {{-- GANDA --}}
            <button onclick="bukaModal('ganda', 'GANDA', '👥', 'emerald')"
                class="group bg-white rounded-3xl p-8 hover:scale-[1.02] transition-all shadow-2xl text-left">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 font-bold uppercase text-sm">Kategori</p>
                        <h2 class="text-4xl font-black text-slate-800 mt-2">GANDA</h2>
                    </div>
                    <div
                        class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center text-3xl group-hover:bg-emerald-500 group-hover:text-white transition-all">
                        👥
                    </div>
                </div>
            </button>

            {{-- TUNGGAL --}}
            <button onclick="bukaModal('tunggal', 'TUNGGAL', '⚔️', 'orange')"
                class="group bg-white rounded-3xl p-8 hover:scale-[1.02] transition-all shadow-2xl text-left">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 font-bold uppercase text-sm">Kategori</p>
                        <h2 class="text-4xl font-black text-slate-800 mt-2">TUNGGAL</h2>
                    </div>
                    <div
                        class="w-16 h-16 rounded-2xl bg-orange-100 flex items-center justify-center text-3xl group-hover:bg-orange-500 group-hover:text-white transition-all">
                        ⚔️
                    </div>
                </div>
            </button>

            {{-- REGU --}}
            <button onclick="bukaModal('regu', 'REGU', '🛡️', 'red')"
                class="group bg-white rounded-3xl p-8 hover:scale-[1.02] transition-all shadow-2xl text-left">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 font-bold uppercase text-sm">Kategori</p>
                        <h2 class="text-4xl font-black text-slate-800 mt-2">REGU</h2>
                    </div>
                    <div
                        class="w-16 h-16 rounded-2xl bg-red-100 flex items-center justify-center text-3xl group-hover:bg-red-500 group-hover:text-white transition-all">
                        🛡️
                    </div>
                </div>
            </button>

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
                    class="w-10 h-10 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-300 flex items-center justify-center transition-all">
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
                const url = `/juri/${kategori}/${i}`;
                const btn = document.createElement('a');
                btn.href = url;
                btn.className =
                    `border rounded-2xl py-5 flex flex-col items-center justify-center gap-1 font-black transition-all ${w.normal} ${w.hover}`;
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

        // Tutup modal jika klik di luar
        document.getElementById('modal-juri').addEventListener('click', function(e) {
            if (e.target === this) tutupModal();
        });
    </script>

</body>

</html>
