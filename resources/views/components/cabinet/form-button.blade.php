@props([
    'color' => 'orange',
    'type' => 'submit',
])

@php
    $colorClasses = [
        'orange' => 'bg-cabinet-orange hover:bg-cabinet-orange/90',
        'green' => 'bg-cabinet-green hover:bg-cabinet-green/90',
        'gray' => 'bg-gray-500 hover:bg-gray-600',
    ];

    $bgClass = $colorClasses[$color] ?? $colorClasses['orange'];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "block w-full {$bgClass} rounded-md py-2 md:py-2.5 xl:py-3 text-center transition"]) }}
>
    <span class="font-poppins font-semibold text-base md:text-lg xl:text-[22px] text-white">
        {{ $slot }}
    </span>
</button>
