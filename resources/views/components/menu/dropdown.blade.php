{{-- resources/views/components/menu/dropdown.blade.php --}}
@props(['label'])

<li x-data="{ open: false }" class="select-none">
    <button 
        @click="open = !open"
        class="w-full flex justify-between items-center px-6 py-3 text-gray-800 hover:bg-gray-100"
    >
        <span class="font-semibold">{{ $label }}</span>
        <span x-text="open ? '−' : '+'"></span>
    </button>

    <ul x-show="open" class="ml-6 space-y-1 py-1">
        {{ $slot }}
    </ul>
</li>
