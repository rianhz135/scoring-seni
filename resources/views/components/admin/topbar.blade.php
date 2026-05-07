<header id="topbar"
    class="fixed top-0 right-0 z-30 bg-white border-b border-gray-100 h-16 flex items-center justify-between px-5 shadow-sm">

    <div class="flex items-center gap-3">
        <button onclick="toggleSidebar()"
            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 transition-all flex-shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <h2 class="text-sm font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h2>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.profile') }}"
            class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 rounded-lg p-2 -m-2 transition-colors">
            <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center text-xs font-semibold text-primary-700">AD</div>
            <span class="text-sm font-medium text-gray-700 hidden sm:block">Administrator</span>
            <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
        </a>
    </div>
</header>