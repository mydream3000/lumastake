@props([
    'label' => '',
    'name' => '',
    'value' => '',
    'options' => [],
    'placeholder' => 'Select an option',
])

<div {{ $attributes->only('class')->merge(['class' => 'relative']) }}>
    @if($label)
        <label for="{{ $name }}" class="block font-poppins font-normal text-base md:text-lg xl:text-[28px] text-[#CCCCCC] mb-1 md:mb-2">
            {{ $label }}
        </label>
    @endif
    <div class="relative">
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            {{ $attributes->except(['class', 'label', 'name', 'value', 'options', 'placeholder']) }}
            class="w-full bg-[#F8F8F8] border border-[rgba(68,68,68,0.6)] rounded-md px-3 py-2 md:px-4 md:py-3 xl:py-4 font-poppins text-sm md:text-base xl:text-lg appearance-none focus:outline-none focus:border-cabinet-green transition pr-10"
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @if(!empty($options))
                @foreach($options as $optValue => $optLabel)
                    <option value="{{ $optValue }}" {{ old($name, $value) == $optValue ? 'selected' : '' }}>
                        {{ $optLabel }}
                    </option>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </select>
        <svg class="absolute right-3 md:right-4 top-1/2 -translate-y-1/2 w-4 h-2 md:w-5 md:h-3 xl:w-[25px] xl:h-[15px] pointer-events-none text-cabinet-orange" fill="currentColor" viewBox="0 0 25 15" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 1L12.5 13L24 1"/>
        </svg>
    </div>
</div>
