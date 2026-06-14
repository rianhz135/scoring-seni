@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rekapitulasi Hasil Pool</h1>
            <p class="text-sm text-gray-500 mt-1">Peringkat perolehan skor akhir sistem pool yang telah disahkan.</p>
        </div>



        <div class="flex items-center gap-2">
            <a href="/admin/hasil/pool/export-pdf" target="_blank"
                class="bg-red-600 border border-transparent text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-700 transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Export PDF
            </a>
            <button onclick="window.location.reload()"
                class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh Data
            </button>
        </div>
    </div>

    {{-- TAB NAVIGASI --}}
    <div class="mb-6">
        <ul class="flex flex-wrap gap-2 text-sm font-medium text-center" role="tablist">
            <li role="presentation">
                <button
                    class="tab-btn inline-block px-5 py-3 text-white bg-blue-600 rounded-lg shadow-md focus:outline-none transition-all duration-200"
                    id="btn-solo" onclick="changeTab('solo')" type="button" role="tab">
                    Kategori Solo
                </button>
            </li>
            <li role="presentation">
                <button
                    class="tab-btn inline-block px-5 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-900 rounded-lg focus:outline-none transition-all duration-200"
                    id="btn-ganda" onclick="changeTab('ganda')" type="button" role="tab">
                    Kategori Ganda
                </button>
            </li>
            <li role="presentation">
                <button
                    class="tab-btn inline-block px-5 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-900 rounded-lg focus:outline-none transition-all duration-200"
                    id="btn-tunggal" onclick="changeTab('tunggal')" type="button" role="tab">
                    Kategori Tunggal
                </button>
            </li>
            <li role="presentation">
                <button
                    class="tab-btn inline-block px-5 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-900 rounded-lg focus:outline-none transition-all duration-200"
                    id="btn-regu" onclick="changeTab('regu')" type="button" role="tab">
                    Kategori Regu
                </button>
            </li>
        </ul>
    </div>

    {{-- MEMANGGIL PARTIAL VIEW --}}
    <div id="content-solo" class="tab-content hidden">
        @include('pages.hasil.pool.partials.table', ['data' => $hasil_solo])
    </div>

    <div id="content-ganda" class="tab-content hidden">
        @include('pages.hasil.pool.partials.table', ['data' => $hasil_ganda])
    </div>

    <div id="content-tunggal" class="tab-content hidden">
        @include('pages.hasil.pool.partials.table', ['data' => $hasil_tunggal])
    </div>

    <div id="content-regu" class="tab-content hidden">
        @include('pages.hasil.pool.partials.table', ['data' => $hasil_regu])
    </div>

    {{-- SCRIPT PINDAH KE SINI AGAR PASTI TERBACA BROWSER --}}
    <script>
        function changeTab(tabName) {
            // Sembunyikan semua Tab konten
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('block');
            });

            // Reset warna semua tombol Tab
            const btns = document.querySelectorAll('.tab-btn');
            btns.forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
                btn.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200', 'hover:text-gray-900');
            });

            // Tampilkan Tab konten yang diklik
            const targetContent = document.getElementById('content-' + tabName);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('block');
            }

            // Ubah warna tombol Tab yang dipilih jadi Biru (Aktif)
            const activeBtn = document.getElementById('btn-' + tabName);
            if (activeBtn) {
                activeBtn.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200', 'hover:text-gray-900');
                activeBtn.classList.add('bg-blue-600', 'text-white', 'shadow-md');
            }

            // Simpan ke storage agar tidak kembali ke default saat refresh
            localStorage.setItem('lastActiveHasilPoolTab', tabName);
        }

        // Jalankan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const savedTab = localStorage.getItem('lastActiveHasilPoolTab') || 'solo';
            changeTab(savedTab);
        });
    </script>
@endsection
@push('scripts')
    {{-- MODAL PEMENANG POOL --}}
    <div id="modal-pemenang-pool" style="display:none;"
        class="fixed inset-0 z-[99] bg-slate-900/80 backdrop-blur-sm items-center justify-center p-4">
        <div
            class="bg-white rounded-2xl w-full max-w-5xl overflow-hidden shadow-2xl flex flex-col transform transition-all">

            {{-- Header Modal --}}
            <div class="p-6 text-center relative" style="background: linear-gradient(to right, #3730a3, #1d4ed8);">
                <button onclick="tutupModalPemenangPool()"
                    class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors cursor-pointer z-50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
                <h2 class="text-3xl font-black text-white uppercase tracking-widest drop-shadow-md">HASIL KEPUTUSAN PEMENANG
                </h2>
                <p id="modal-pool-subtitle" class="text-blue-100 mt-2 font-bold uppercase tracking-wider text-sm">PARTAI X -
                    KATEGORI Y</p>
            </div>

            {{-- Body Tabel --}}
            <div class="p-0 overflow-x-auto bg-gray-50">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr class="text-xs font-black text-gray-500 uppercase tracking-widest">
                            <th class="px-6 py-4 text-center w-24">Rank</th>
                            <th class="px-6 py-4">Peserta / Kontingen</th>
                            <th class="px-6 py-4 text-center">Waktu</th>
                            <th class="px-6 py-4 text-center">Median</th>
                            <th class="px-6 py-4 text-center">Deviasi</th>
                            <th class="px-6 py-4 text-center text-red-500">Hukuman</th>
                            <th class="px-6 py-4 text-right pr-8">Total Skor</th>
                        </tr>
                    </thead>
                    <tbody id="pool-winner-tbody" class="divide-y divide-gray-200 text-sm bg-white">
                        {{-- DIISI OLEH JAVASCRIPT --}}
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 p-4 border-t border-gray-200 flex justify-end">
                <button onclick="tutupModalPemenangPool()"
                    class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-xl font-bold uppercase tracking-wider shadow-lg transition-all active:scale-95">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    <script>
        let currentActivePartaiPoolId = null;

        function tampilkanPemenangPool(idPartai, noPartai, kategori) {
            currentActivePartaiPoolId = idPartai;

            Swal.fire({
                title: 'Menyusun Klasemen...',
                text: 'Menyiapkan data untuk Layar Display.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Update Subtitle Modal
            document.getElementById('modal-pool-subtitle').innerText = `PARTAI ${noPartai} — ${kategori}`;

            fetch(`/admin/hasil/pool/${idPartai}/tetapkan-pemenang`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.status === 'success') {
                        const tbody = document.getElementById('pool-winner-tbody');
                        tbody.innerHTML = ''; // Kosongkan tabel lama

                        // Looping data peserta berdasar ranking
                        data.data.forEach(p => {
                            let rankBadge = `<span class="text-base font-bold text-gray-400">#${p.rank}</span>`;
                            let rowClass = "hover:bg-blue-50/50 transition-colors";

                            if (p.rank === 1) {
                                rankBadge =
                                    `<span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-yellow-100 text-yellow-700 font-black text-lg border-2 border-yellow-300 shadow-md">1</span>`;
                                rowClass = "bg-yellow-50/30 hover:bg-yellow-50/80 transition-colors";
                            } else if (p.rank === 2) {
                                rankBadge =
                                    `<span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 text-slate-700 font-black text-lg border-2 border-slate-300 shadow-md">2</span>`;
                            } else if (p.rank === 3) {
                                rankBadge =
                                    `<span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-orange-100 text-orange-800 font-black text-lg border-2 border-orange-300 shadow-md">3</span>`;
                            }

                            const tr = `
                        <tr class="${rowClass}">
                            <td class="px-6 py-4 text-center">${rankBadge}</td>
                            <td class="px-6 py-4">
                                <div class="font-black text-gray-900 uppercase text-base tracking-wide">${p.nama}</div>
                                <div class="text-xs text-gray-500 uppercase mt-1 font-bold tracking-wider">${p.kontingen}</div>
                            </td>
                            <td class="px-6 py-4 text-center"><span class="bg-gray-100 px-3 py-1 rounded text-xs font-mono font-bold">${p.waktu}</span></td>
                            <td class="px-6 py-4 text-center font-bold text-gray-700 text-base">${p.median}</td>
                            <td class="px-6 py-4 text-center text-xs text-gray-500 font-mono">${p.deviasi}</td>
                            <td class="px-6 py-4 text-center text-red-600 font-bold">-${p.hukuman}</td>
                            <td class="px-6 py-4 text-right pr-8"><span class="text-2xl font-black text-blue-600">${p.total}</span></td>
                        </tr>
                    `;
                            tbody.innerHTML += tr;
                        });

                        // Buka Modal
                        const modal = document.getElementById('modal-pemenang-pool');
                        modal.style.display = 'flex';
                    }
                })
                .catch(err => {
                    Swal.close();
                    console.error(err);
                    Swal.fire('Error', 'Gagal memproses data.', 'error');
                });
        }

        function tutupModalPemenangPool() {
            const modal = document.getElementById('modal-pemenang-pool');
            if (modal) {
                // Ganti toggle class dengan style langsung
                modal.style.display = 'none';
            }

            if (!currentActivePartaiPoolId) return;

            fetch(`/admin/hasil/pool/${currentActivePartaiPoolId}/tutup-display`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }).then(() => console.log("Perintah tutup dikirim ke Layar Display."));

            currentActivePartaiPoolId = null;
        }
    </script>
@endpush
