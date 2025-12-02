@props(['label', 'route'])

@php
    $active = request()->routeIs($route);
@endphp

<li>
    <a href="{{ route($route) }}"
       class="block px-6 py-3 rounded-lg 
        {{ $active ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
        {{ $label }}
    </a>
</li>
