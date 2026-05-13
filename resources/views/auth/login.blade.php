<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <div class="min-h-screen bg-slate-900 flex items-center justify-center p-4 relative overflow-hidden">
        <div class="absolute top-0 -left-20 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -right-20 w-96 h-96 bg-emerald-600/20 rounded-full blur-3xl"></div>

        <div class="max-w-md w-full relative z-10">
            <div class="bg-white rounded-[2.5rem] shadow-2xl p-10">
                <div class="text-center mb-10">
                    <h2 class="text-4xl font-black text-slate-800 tracking-tighter">SILAT DIGITAL</h2>
                    <p class="text-slate-400 font-bold uppercase text-xs tracking-widest mt-2">Sistem Penilaian Seni</p>
                </div>

                <form method="POST" action="{{ route('login.process') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-black text-slate-700 uppercase mb-2">
                            Username
                        </label>

                        <input type="text" name="username" required
                            class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white outline-none transition-all font-bold">
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 uppercase mb-2">
                            Password
                        </label>

                        <input type="password" name="password" required
                            class="w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white outline-none transition-all font-bold">
                    </div>

                    @if (session('error'))
                        <div
                            class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-xl text-sm font-bold">
                            {{ session('error') }}
                        </div>
                    @endif

                    <button type="submit"
                        class="w-full py-4 bg-slate-800 hover:bg-indigo-600 text-white rounded-2xl font-black text-lg shadow-xl shadow-indigo-200 transition-all active:scale-95">
                        MASUK SISTEM
                    </button>
                </form>

                <div class="mt-10 pt-8 border-t border-slate-100">
                    <p class="text-center text-slate-400 text-sm font-bold uppercase mb-4">Akses Tanpa Login</p>
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('dewan.display') }}"
                            class="w-full py-3 bg-emerald-50 text-emerald-700 border-2 border-emerald-100 rounded-xl font-black text-center hover:bg-emerald-500 hover:text-white transition-all">
                            BUKA DISPLAY DEWAN
                        </a>
                    </div>
                </div>
            </div>
            <p class="text-center text-slate-500 text-xs mt-8 font-medium">Digital Scoring v2.0 &bull; 2026</p>
        </div>
    </div>


</body>

</html>
