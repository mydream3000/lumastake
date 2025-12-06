@props([
    'type' => 'text',
    'label' => '',
    'name' => '',
    'value' => '',
    'required' => false,
    'placeholder' => '',
])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $name }}" class="block font-poppins font-normal text-base md:text-lg xl:text-[28px] text-[#CCCCCC] mb-1 md:mb-2">
            {{ $label }}
        </label>
    @endif
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->except(['class', 'label', 'name', 'value', 'required', 'placeholder', 'type']) }}
        class="w-full bg-[#F8F8F8] border border-[rgba(68,68,68,0.6)] rounded-md px-3 py-2 md:px-4 md:py-3 xl:py-4 font-poppins text-sm md:text-base xl:text-lg focus:outline-none focus:border-cabinet-green transition"
    >
</div>
