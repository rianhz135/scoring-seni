<aside id="sidebar" class="fixed top-0 left-0 h-screen bg-white border-r border-gray-100 flex flex-col z-20 shadow-sm"
    style="width: 240px; transition: width 0.3s ease; overflow: hidden;">

    {{-- Brand --}}
    <div class="px-4 py-5 border-b border-gray-100 flex items-center gap-2.5" style="min-height: 64px;">
        <div class="w-8 h-8 rounded-lg bg-primary-500 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path
                    d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
            </svg>
        </div>
        <div id="sidebar-brand-text" class="overflow-hidden whitespace-nowrap">
            <p class="text-sm font-semibold text-gray-900 leading-tight">Admin Panel</p>
            <p class="text-[10px] text-gray-400 leading-tight">Sistem Pertandingan</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">

        <x-admin.sidebar-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" icon="dashboard">
            Dashboard
        </x-admin.sidebar-link>

        <div class="pt-3 pb-1 px-3 sidebar-section-label">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">Data Master
            </p>
        </div>
        <x-admin.sidebar-group title="Peserta" id="peserta" icon="users" :active="request()->is('admin/peserta*')">
            <x-admin.sidebar-sublink href="{{ route('admin.peserta.solo.index') }}"
                :active="request()->routeIs('admin.peserta.solo.*')">Solo</x-admin.sidebar-sublink>
            <x-admin.sidebar-sublink href="{{ route('admin.peserta.ganda.index') }}"
                :active="request()->routeIs('admin.peserta.ganda.*')">Ganda</x-admin.sidebar-sublink>
            <x-admin.sidebar-sublink href="{{ route('admin.peserta.tunggal.index') }}"
                :active="request()->routeIs('admin.peserta.tunggal.*')">Tunggal</x-admin.sidebar-sublink>
            <x-admin.sidebar-sublink href="{{ route('admin.peserta.regu.index') }}"
                :active="request()->routeIs('admin.peserta.regu.*')">Regu</x-admin.sidebar-sublink>
            <x-admin.sidebar-sublink href="{{ route('admin.peserta.pool.index') }}" :active="request()->routeIs('admin.peserta.pool.*')">Pool
                (Seni)</x-admin.sidebar-sublink>
            <x-admin.sidebar-sublink href="{{ route('admin.peserta.tanding.index') }}" :active="request()->routeIs('admin.peserta.tanding.*')">Tanding</x-admin.sidebar-sublink>
        </x-admin.sidebar-group>

        <div class="pt-3 pb-1 px-3 sidebar-section-label">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">Operasional
            </p>
        </div>
        <x-admin.sidebar-group title="Manajemen" id="manajemen" icon="management" :active="request()->is('admin/manajemen*')">
            <x-admin.sidebar-sublink href="{{ route('admin.manajemen.solo.index') }}"
                :active="request()->routeIs('admin.manajemen.solo.*')">Solo</x-admin.sidebar-sublink>
            <x-admin.sidebar-sublink href="{{ route('admin.manajemen.ganda.index') }}"
                :active="request()->routeIs('admin.manajemen.ganda.*')">Ganda</x-admin.sidebar-sublink>
            <x-admin.sidebar-sublink href="{{ route('admin.manajemen.tunggal.index') }}"
                :active="request()->routeIs('admin.manajemen.tunggal.*')">Tunggal</x-admin.sidebar-sublink>
            <x-admin.sidebar-sublink href="{{ route('admin.manajemen.regu.index') }}"
                :active="request()->routeIs('admin.manajemen.regu.*')">Regu</x-admin.sidebar-sublink>
            <x-admin.sidebar-sublink href="{{ route('admin.manajemen.pool.index') }}" :active="request()->routeIs('admin.manajemen.pool.*')">Pool
                (Seni)</x-admin.sidebar-sublink>
            <x-admin.sidebar-sublink href="{{ route('admin.manajemen.tanding.index') }}" :active="request()->routeIs('admin.manajemen.tanding.*')">Tanding</x-admin.sidebar-sublink>
        </x-admin.sidebar-group>

        <x-admin.sidebar-link href="{{ route('admin.manajemen.kelas.index') }}" :active="request()->routeIs('admin.manajemen.kelas.*')" icon="management">
            Kelas Seni
        </x-admin.sidebar-link>

        <div class="pt-3 pb-1 px-3 sidebar-section-label">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">Laporan</p>
        </div>
        <x-admin.sidebar-group title="Hasil" id="hasil" icon="trophy" :active="request()->is('admin/hasil*')">
            <x-admin.sidebar-sublink href="{{ route('admin.hasil.solo.index') }}"
                :active="request()->routeIs('admin.hasil.solo.*')">Solo</x-admin.sidebar-sublink>
            <x-admin.sidebar-sublink href="{{ route('admin.hasil.ganda.index') }}"
                :active="request()->routeIs('admin.hasil.ganda.*')">Ganda</x-admin.sidebar-sublink>
            <x-admin.sidebar-sublink href="{{ route('admin.hasil.tunggal.index') }}"
                :active="request()->routeIs('admin.hasil.tunggal.*')">Tunggal</x-admin.sidebar-sublink>
            <x-admin.sidebar-sublink href="{{ route('admin.hasil.regu.index') }}"
                :active="request()->routeIs('admin.hasil.regu.*')">Regu</x-admin.sidebar-sublink>
            <x-admin.sidebar-sublink href="{{ route('admin.hasil.pool.index') }}"
                :active="request()->routeIs('admin.hasil.pool.*')">Pool</x-admin.sidebar-sublink>
            <x-admin.sidebar-sublink href="{{ route('admin.hasil.tanding.index') }}"
                :active="request()->routeIs('admin.hasil.tanding.*')">Tanding</x-admin.sidebar-sublink>
        </x-admin.sidebar-group>


    </nav>

    <div class="border-t border-gray-100 px-2 py-3">
        <x-admin.sidebar-link href="{{ route('admin.profile') }}" :active="request()->routeIs('admin.profile')" icon="user">
            Admin Profile
        </x-admin.sidebar-link>
    </div>
    <form action="{{ route('logout') }}" method="POST" class="px-2 mb-3">
        @csrf
        <x-admin.sidebar-link href="#" class="text-red-500 hover:bg-red-50 hover:text-red-600" icon="logout"
            onclick="event.preventDefault(); this.closest('form').submit()">
            Logout
        </x-admin.sidebar-link>
    </form>
</aside>
