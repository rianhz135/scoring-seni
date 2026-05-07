@props(['href', 'active' => false])

<a href="{{ $href }}" 
   class="sidebar-link flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm transition-all {{ $active ? 'active' : 'text-gray-500 border-l-2 border-transparent hover:bg-gray-50 hover:text-primary-600' }}">
    <span class="w-1 h-1 rounded-full {{ $active ? 'bg-primary-600' : 'bg-gray-300' }} shrink-0"></span> 
    {{ $slot }}
</a>