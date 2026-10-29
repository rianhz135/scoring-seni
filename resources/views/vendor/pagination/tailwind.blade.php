<nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3 w-full">
    {{-- Info Jumlah Data --}}
    <div class="text-xs text-gray-500 font-medium">
        @if ($paginator->firstItem())
            Menampilkan <span class="font-bold text-gray-900">{{ $paginator->firstItem() }}</span> sampai <span class="font-bold text-gray-900">{{ $paginator->lastItem() }}</span> dari <span class="font-bold text-gray-900">{{ $paginator->total() }}</span> data
        @else
            Menampilkan <span class="font-bold text-gray-900">{{ $paginator->count() }}</span> data
        @endif
    </div>

    {{-- Navigasi Tombol Halaman --}}
    <div class="flex items-center gap-1.5">
        @if ($paginator->hasPages())
            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="px-2.5 py-1.5 text-xs font-semibold text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                    <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-2.5 py-1.5 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-lg transition-all shadow-xs">
                    <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            @endif

            {{-- Nomor-Nomor Halaman --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 py-1 text-xs text-gray-400 font-semibold">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="px-3 py-1.5 text-xs font-bold text-white bg-primary-600 border border-primary-600 rounded-lg shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-100 border border-gray-200 rounded-lg transition-all">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-2.5 py-1.5 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-lg transition-all shadow-xs">
                    <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span aria-disabled="true" class="px-2.5 py-1.5 text-xs font-semibold text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                    <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        @elseif ($paginator->total() > 0)
            <span class="px-3 py-1.5 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-lg">
                Halaman 1 dari 1
            </span>
        @endif
    </div>
</nav>
