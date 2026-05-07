@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hasil Pertandingan: Solo</h1>
            <p class="text-sm text-gray-500 mt-1">Rekapitulasi nilai pertandingan kategori solo.</p>
        </div>
        <a href="{{ route('admin.hasil.solo.export-pdf') }}" target="_blank"
            class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export PDF
        </a>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1100px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 uppercase text-center">
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest w-12">No</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest w-16">Partai</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest w-24 text-left">Kelas</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest">Komparasi Nilai Sudut
                            (Biru vs Merah)</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest w-32">Pemenang</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 tracking-widest w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($partais as $index => $partai)
                        @php
                            // Ambil data sudut biru dan merah
                            $biru =
                                $partai->detail_peserta->firstWhere('sudut', 'Biru') ??
                                $partai->detail_peserta->firstWhere('sudut', 'biru');
                            $merah =
                                $partai->detail_peserta->firstWhere('sudut', 'Merah') ??
                                $partai->detail_peserta->firstWhere('sudut', 'merah');

                            // Cek status diskualifikasi (fallback antisipasi typo di database)
                            $disqBiru = $biru ? $biru->is_disqualifikasi ?? ($biru->is_disqulifikasi ?? 0) : 0;
                            $disqMerah = $merah ? $merah->is_disqualifikasi ?? ($merah->is_disqulifikasi ?? 0) : 0;
                        @endphp

                        <tr class="hover:bg-gray-50/50 transition-colors uppercase">
                            {{-- NO --}}
                            <td class="px-4 py-4 text-center text-xs text-gray-500 font-medium">
                                {{ $partais->firstItem() + $index }}
                            </td>

                            {{-- PARTAI --}}
                            <td class="px-4 py-4 text-center font-extrabold text-gray-900">
                                {{ $partai->no_partai }}
                            </td>

                            {{-- KELAS --}}
                            <td class="px-4 py-4">
                                <span
                                    class="inline-block px-2 py-1 rounded bg-primary-50 text-primary-700 text-[10px] font-bold border border-primary-100 whitespace-nowrap">
                                    {{-- Mengambil langsung dari kolom label_kelas sesuai Model RefKelas --}}
                                    {{ $partai->kelas->label_kelas ?? '-' }}
                                </span>
                            </td>

                            {{-- KOMPARASI NILAI SUDUT --}}
                            <td class="px-4 py-4">
                                <div class="flex flex-col gap-2">

                                    {{-- BOX SUDUT BIRU --}}
                                    @if ($biru)
                                        <div
                                            class="flex items-center gap-3 p-2 rounded-xl border {{ $disqBiru == 1 ? 'bg-red-50/30 border-red-200 opacity-80' : 'bg-blue-50/50 border-blue-100' }}">
                                            <div class="flex-1">
                                                <p
                                                    class="text-[11px] font-black {{ $disqBiru == 1 ? 'text-red-700 line-through' : 'text-blue-700' }} leading-tight">
                                                    {{-- Coba cek nama, jika kosong cek nama_peserta, jika kosong cek nama_lengkap --}}
                                                    {{ $biru->peserta->nama ?? ($biru->peserta->nama_peserta ?? ($biru->peserta->nama_lengkap ?? 'Nama Tidak Ditemukan')) }}
                                                </p>
                                                <p
                                                    class="text-[9px] {{ $disqBiru == 1 ? 'text-red-500' : 'text-blue-500' }} font-bold uppercase">
                                                    {{ $biru->peserta->kontingen ?? '-' }}
                                                </p>
                                            </div>

                                            @if ($disqBiru == 1)
                                                <div class="flex items-center justify-center px-4">
                                                    <span
                                                        class="text-xs font-black text-red-600 tracking-widest px-3 py-1 bg-red-100 rounded-md border border-red-200">DISKUALIFIKASI</span>
                                                </div>
                                            @else
                                                <div
                                                    class="flex items-center gap-4 text-[10px] text-blue-600 font-medium px-4 border-x border-blue-100">
                                                    <span>MED:
                                                        <b>{{ number_format($biru->nilai_median ?? 0, 3) }}</b></span>
                                                    <span>DEV: <b>{{ $biru->nilai_deviasi ?? 0 }}</b></span>
                                                    <span class="text-red-500">HUK:
                                                        <b>{{ number_format($biru->hukuman_ketua ?? 0, 2) }}</b></span>
                                                </div>
                                                <div class="text-right min-w-[70px]">
                                                    <p class="text-[8px] text-blue-400 font-bold uppercase leading-none">
                                                        TOTAL</p>
                                                    <p class="text-sm font-black text-blue-900 leading-tight">
                                                        {{ number_format($biru->total_nilai ?? 0, 3) }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- BOX SUDUT MERAH --}}
                                    @if ($merah)
                                        <div
                                            class="flex items-center gap-3 p-2 rounded-xl border {{ $disqMerah == 1 ? 'bg-red-50/30 border-red-200 opacity-80' : 'bg-red-50/50 border-red-100' }}">
                                            <div class="flex-1">
                                                <p
                                                    class="text-[11px] font-black {{ $disqMerah == 1 ? 'text-red-700 line-through' : 'text-red-700' }} leading-tight">
                                                    {{ $merah->peserta->nama ?? ($merah->peserta->nama_peserta ?? ($merah->peserta->nama_lengkap ?? 'Nama Tidak Ditemukan')) }}
                                                </p>
                                                <p class="text-[9px] text-red-500 font-bold uppercase">
                                                    {{ $merah->peserta->kontingen ?? '-' }}
                                                </p>
                                            </div>

                                            @if ($disqMerah == 1)
                                                <div class="flex items-center justify-center px-4">
                                                    <span
                                                        class="text-xs font-black text-red-600 tracking-widest px-3 py-1 bg-red-100 rounded-md border border-red-200">DISKUALIFIKASI</span>
                                                </div>
                                            @else
                                                <div
                                                    class="flex items-center gap-4 text-[10px] text-red-600 font-medium px-4 border-x border-red-100">
                                                    <span>MED:
                                                        <b>{{ number_format($merah->nilai_median ?? 0, 3) }}</b></span>
                                                    <span>DEV: <b>{{ $merah->nilai_deviasi ?? 0 }}</b></span>
                                                    <span class="text-red-500">HUK:
                                                        <b>{{ number_format($merah->hukuman_ketua ?? 0, 2) }}</b></span>
                                                </div>
                                                <div class="text-right min-w-[70px]">
                                                    <p class="text-[8px] text-red-400 font-bold uppercase leading-none">
                                                        TOTAL</p>
                                                    <p class="text-sm font-black text-red-900 leading-tight">
                                                        {{ number_format($merah->total_nilai ?? 0, 3) }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                </div>
                            </td>

                            {{-- PEMENANG --}}
                            <td class="px-4 py-4 text-center" id="col-pemenang-{{ $partai->id_partai }}">
                                @php
                                    $pemenang = $partai->detail_peserta->firstWhere('is_pemenang', 1);
                                @endphp

                                @if ($pemenang)
                                    <div
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg {{ strtolower($pemenang->sudut) == 'biru' ? 'bg-blue-600' : 'bg-red-600' }} text-white text-[10px] font-black shadow-sm">
                                        <svg class="w-3 h-3 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        {{ strtoupper($pemenang->sudut) }}
                                    </div>
                                @else
                                    <span
                                        class="inline-block px-3 py-1 rounded bg-gray-100 text-gray-500 border border-gray-200 text-[10px] font-bold">
                                        BELUM DIUMUMKAN
                                    </span>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 py-4 text-center">
                                <button type="button" onclick="tetapkanPemenang({{ $partai->id_partai }})"
                                    class="bg-gray-900 hover:bg-black text-white text-[10px] font-bold px-3 py-2 rounded-xl transition-all shadow-md w-full">
                                    TAMPILKAN PEMENANG
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-400 font-medium">
                                Belum ada pertandingan Solo yang selesai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50 flex items-center justify-between">
            <div class="text-xs text-gray-500">
                Menampilkan <span class="font-semibold text-gray-900">{{ $partais->firstItem() ?? 0 }}</span> sampai <span
                    class="font-semibold text-gray-900">{{ $partais->lastItem() ?? 0 }}</span> dari <span
                    class="font-semibold text-gray-900">{{ $partais->total() }}</span> data
            </div>

            <div class="flex items-center gap-1">
                {{ $partais->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    <div id="modal-pemenang"
        class="fixed inset-0 z-50 hidden bg-gray-900/90 backdrop-blur-md flex items-center justify-center p-4">
        <div
            class="bg-white rounded-[2.5rem] shadow-2xl max-w-4xl w-full overflow-hidden transform scale-95 transition-all">

            {{-- Header --}}
            <div class="bg-gray-900 py-6 text-center relative">
                <h2 class="text-2xl font-black text-white tracking-[0.2em]">HASIL KEPUTUSAN PEMENANG</h2>
                <button onclick="tutupModalPemenang({{ $partai->id_partai }})"
                    class="mt-6 w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition-all">
                    TUTUP
                </button>
            </div>

            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- KOLOM PEMENANG --}}
                <div class="border-2 border-yellow-400 rounded-[2rem] p-6 relative bg-yellow-50/30">
                    <div
                        class="absolute -top-4 left-1/2 -translate-x-1/2 bg-yellow-400 text-gray-900 px-6 py-1 rounded-full text-xs font-black tracking-widest">
                        PEMENANG</div>

                    <div class="text-center mb-6 pt-2">
                        <div id="win-sudut-label"
                            class="inline-block px-4 py-1 rounded-lg text-white font-black text-sm mb-3">SUDUT -</div>
                        <h3 id="win-nama" class="text-xl font-black text-gray-900 leading-tight uppercase">Nama Pemenang
                        </h3>
                        <p id="win-kontingen" class="text-xs font-bold text-gray-500 uppercase">Kontingen</p>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center bg-white p-3 rounded-xl border border-yellow-200">
                            <span class="text-xs font-bold text-gray-400 uppercase">Total Nilai</span>
                            <span id="win-total" class="text-2xl font-black text-green-600">0.000</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="bg-white p-2 rounded-lg border border-gray-100">
                                <p class="text-[9px] font-bold text-gray-400 uppercase">Median</p>
                                <p id="win-median" class="text-xs font-black">0.000</p>
                            </div>
                            <div class="bg-white p-2 rounded-lg border border-gray-100">
                                <p class="text-[9px] font-bold text-gray-400 uppercase">Hukuman</p>
                                <p id="win-hukuman" class="text-xs font-black text-red-500">0.00</p>
                            </div>
                            <div class="bg-white p-2 rounded-lg border border-gray-100 overflow-hidden">
                                <p class="text-[9px] font-bold text-gray-400 uppercase">Deviasi</p>
                                <p id="win-deviasi" class="text-[9px] font-black truncate">0.000</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KALAH / PESERTA LAIN --}}
                <div class="border-2 border-gray-200 rounded-[2rem] p-6 relative">
                    <div
                        class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gray-200 text-gray-600 px-6 py-1 rounded-full text-xs font-black tracking-widest">
                        PESERTA LAIN</div>

                    <div class="text-center mb-6 pt-2">
                        <div id="lose-sudut-label"
                            class="inline-block px-4 py-1 rounded-lg text-white font-black text-sm mb-3">SUDUT -</div>
                        <h3 id="lose-nama" class="text-xl font-black text-gray-500 leading-tight uppercase">Nama Peserta
                        </h3>
                        <p id="lose-kontingen" class="text-xs font-bold text-gray-400 uppercase">Kontingen</p>
                    </div>

                    <div class="space-y-3 opacity-60">
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <span class="text-xs font-bold text-gray-400 uppercase">Total Nilai</span>
                            <span id="lose-total" class="text-2xl font-black text-gray-600">0.000</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="bg-gray-50 p-2 rounded-lg border border-gray-100">
                                <p class="text-[9px] font-bold text-gray-400 uppercase">Median</p>
                                <p id="lose-median" class="text-xs font-black">0.000</p>
                            </div>
                            <div class="bg-gray-50 p-2 rounded-lg border border-gray-100">
                                <p class="text-[9px] font-bold text-gray-400 uppercase">Hukuman</p>
                                <p id="lose-hukuman" class="text-xs font-black text-red-400">0.00</p>
                            </div>
                            <div class="bg-gray-50 p-2 rounded-lg border border-gray-100 overflow-hidden">
                                <p class="text-[9px] font-bold text-gray-400 uppercase">Deviasi</p>
                                <p id="lose-deviasi" class="text-[9px] font-black truncate">0.000</p>
                            </div>
                        </div>
                        <div id="lose-disq-badge"
                            class="hidden text-center text-red-600 font-black text-xs tracking-tighter bg-red-100 py-1 rounded-md">
                            TERDISKUALIFIKASI</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function tetapkanPemenang(idPartai) {
            Swal.fire({
                title: 'Kalkulasi Pemenang...',
                text: 'Sistem sedang memproses nilai tertinggi dan deviasi.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/admin/hasil/solo/${idPartai}/tetapkan-pemenang`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();

                    if (data.status === 'success') {
                        if (data.winner) {
                            const win = data.winner;
                            const lose = data.loser;

                            // --- UPDATE BAGIAN PEMENANG ---
                            document.getElementById('win-nama').innerText = win.nama;
                            document.getElementById('win-kontingen').innerText = win.kontingen;
                            document.getElementById('win-sudut-label').innerText = "SUDUT " + win.sudut;
                            // Update Warna Label Pemenang
                            document.getElementById('win-sudut-label').className =
                                `inline-block px-4 py-1 rounded-lg text-white font-black text-sm mb-3 ${win.sudut === 'BIRU' ? 'bg-blue-600' : 'bg-red-600'}`;

                            document.getElementById('win-total').innerText = win.total;
                            document.getElementById('win-median').innerText = win.median;
                            document.getElementById('win-hukuman').innerText = win.hukuman; // Fix Undefined
                            document.getElementById('win-deviasi').innerText = win.deviasi;

                            // --- UPDATE BAGIAN PESERTA LAIN (KALAH) ---
                            if (lose) {
                                document.getElementById('lose-nama').innerText = lose.nama;
                                document.getElementById('lose-kontingen').innerText = lose.kontingen;
                                document.getElementById('lose-sudut-label').innerText = "SUDUT " + lose.sudut;
                                // Update Warna Label Kalah (Harus beda dengan pemenang)
                                document.getElementById('lose-sudut-label').className =
                                    `inline-block px-4 py-1 rounded-lg text-white font-black text-sm mb-3 ${lose.sudut === 'BIRU' ? 'bg-blue-600' : 'bg-red-600'}`;

                                document.getElementById('lose-total').innerText = lose.total;
                                document.getElementById('lose-median').innerText = lose.median;
                                document.getElementById('lose-hukuman').innerText = lose.hukuman; // Fix Undefined
                                document.getElementById('lose-deviasi').innerText = lose.deviasi;

                                // Handle Badge Disq
                                const disqBadge = document.getElementById('lose-disq-badge');
                                if (lose.is_disq) disqBadge.classList.remove('hidden');
                                else disqBadge.classList.add('hidden');
                            }

                            // Tampilkan Modal
                            const modal = document.getElementById('modal-pemenang');
                            modal.classList.remove('hidden');

                            // Update Lencana di Kolom Tabel (Tanpa Refresh)
                            const colPemenang = document.getElementById(`col-pemenang-${idPartai}`);
                            const colorClass = win.sudut === 'BIRU' ? 'bg-blue-600' : 'bg-red-600';
                            colPemenang.innerHTML = `
                    <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg ${colorClass} text-white text-[10px] font-black shadow-sm">
                        <svg class="w-3 h-3 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        ${win.sudut}
                    </div>
                `;
                        }
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.close();
                    Swal.fire('Error', 'Gagal memproses data. Cek koneksi atau database.', 'error');
                    console.error(error);
                });
        }

        function tutupModalPemenang(idPartai) {
            // console.log("Tombol Tutup diklik, ID Partai:", idPartai);
            // 1. Tutup modal di Admin
            const modal = document.getElementById('modal-pemenang');
            modal.classList.add('hidden');

            // 2. Beritahu server untuk menyuruh Display tutup (Reverb)
            fetch(`/admin/hasil/solo/${idPartai}/tutup-display`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
        }
    </script>
@endsection
