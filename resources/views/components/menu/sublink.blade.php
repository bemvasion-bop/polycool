@php
    $active = request()->routeIs($route);
@endphp

<li>
    <a href="{{ Route::has($route) ? route($route) : '#' }}"
       class="block px-4 py-2 rounded
              {{ $active ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
        {{ $label }}
    </a>
</li>