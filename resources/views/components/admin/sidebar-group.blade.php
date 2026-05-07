@props(['title', 'id', 'icon', 'active' => false])

<div class="space-y-1">
    <button onclick="toggleSection('{{ $id }}')"
        class="sidebar-group-btn w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all group {{ $active ? 'text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">

        <div class="flex items-center gap-3">
            {{-- Icon --}}
            <span class="sidebar-icon shrink-0 flex items-center justify-center w-4 h-4">
                @if($icon == 'users')
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                @elseif($icon == 'management')
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                @elseif($icon == 'trophy')
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                @else
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                @endif
            </span>

            {{-- Teks --}}
            <span class="sidebar-group-text whitespace-nowrap overflow-hidden transition-all duration-200">{{ $title }}</span>
        </div>

        <svg id="chevron-{{ $id }}"
            class="sidebar-chevron chevron w-3.5 h-3.5 text-gray-400 group-hover:text-primary-500 transition-transform {{ $active ? 'open' : '' }}"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
        </svg>
    </button>

    <div id="subs-{{ $id }}" class="nav-sub-list space-y-1 ml-4 border-l border-gray-100 pl-2 {{ $active ? 'open' : '' }}">
        {{ $slot }}
    </div>
</div>