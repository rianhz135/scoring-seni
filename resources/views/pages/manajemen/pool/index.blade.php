@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Pertandingan Pool</h1>
            <p class="text-sm text-gray-500 mt-1">Kontrol jalannya pertandingan kategori Seni.</p>
        </div>
    </div>

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

    <div id="tabContents">

        <div class="tab-content block p-6 bg-white rounded-xl shadow-sm border border-gray-100" id="content-solo"
            role="tabpanel">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Daftar Antrean Pool - Solo</h2>
            @if ($pool_solo->isEmpty())
                <p class="text-gray-500 italic">Belum ada data pool untuk kategori Solo.</p>
            @else
                @include('pages.manajemen.pool.partials.table', ['data' => $pool_solo])
            @endif
        </div>

        <div class="tab-content hidden p-6 bg-white rounded-xl shadow-sm border border-gray-100" id="content-ganda"
            role="tabpanel">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Daftar Antrean Pool - Ganda</h2>
            @if ($pool_ganda->isEmpty())
                <p class="text-gray-500 italic">Belum ada data pool untuk kategori Ganda.</p>
            @else
                @include('pages.manajemen.pool.partials.table', ['data' => $pool_ganda])
            @endif
        </div>

        <div class="tab-content hidden p-6 bg-white rounded-xl shadow-sm border border-gray-100" id="content-tunggal"
            role="tabpanel">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Daftar Antrean Pool - Tunggal</h2>
            @if ($pool_tunggal->isEmpty())
                <p class="text-gray-500 italic">Belum ada data pool untuk kategori Tunggal.</p>
            @else
                @include('pages.manajemen.pool.partials.table', ['data' => $pool_tunggal])
            @endif
        </div>

        <div class="tab-content hidden p-6 bg-white rounded-xl shadow-sm border border-gray-100" id="content-regu"
            role="tabpanel">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Daftar Antrean Pool - Regu</h2>
            @if ($pool_regu->isEmpty())
                <p class="text-gray-500 italic">Belum ada data pool untuk kategori Regu.</p>
            @else
                @include('pages.manajemen.pool.partials.table', ['data' => $pool_regu])
            @endif
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // 1. Fungsi Utama untuk merubah Tab
        function changeTab(tabName) {
            // Sembunyikan semua konten Tab
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.remove('block');
                el.classList.add('hidden');
            });

            // Reset status aktif semua tombol menu Tab menjadi abu-abu (Tidak Aktif)
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
                el.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200', 'hover:text-gray-900');
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

            // --- TAMBAHAN: Simpan pilihan tab ke storage browser ---
            localStorage.setItem('lastActivePoolTab', tabName);
        }

        // 2. Fungsi untuk menjalankan tab yang tersimpan saat halaman dimuat (Refresh/Redirect)
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil data tab terakhir dari localStorage
            const savedTab = localStorage.getItem('lastActivePoolTab');

            if (savedTab) {
                // Jika ada yang tersimpan, jalankan tab tersebut
                changeTab(savedTab);
            } else {
                // Jika tidak ada (pertama kali buka), jalankan default 'solo'
                changeTab('solo');
            }
        });
    </script>
@endpush
