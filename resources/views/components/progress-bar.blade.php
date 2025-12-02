@props(['value'])

@php
    $color = match (true) {
        $value < 40 => 'bg-red-500',
        $value < 70 => 'bg-yellow-400',
        default    => 'bg-green-500',
    };
@endphp

<div class="w-full bg-gray-200 rounded-full h-3">
    <div class="{{ $color }} h-3 rounded-full transition-all duration-500"
         style="width: {{ $value }}%">
    </div>
</div>

<p class="text-xs mt-1 text-gray-600">{{ $value }}%</p>
