@props(['name', 'show' => false, 'modalClass' => 'bg-white', 'cabinetCenter' => false])

<div
    x-data="{ show: {{ $show ? 'true' : 'false' }} }"
    x-show="show"
    x-on:open-modal.window="show = ($event.detail.name === '{{ $name }}')"
    x-on:close-modal.window="show = false"
    x-on:keydown.escape.window="show = false"
    style="display: {{ $show ? 'block' : 'none' }};"
    class="fixed z-[1000] inset-0 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <div class="flex items-start justify-center min-h-screen pt-[10vh] px-4 pb-20 text-center sm:items-center sm:pt-0 sm:p-0 {{ $cabinetCenter ? 'lg:pl-[346px]' : '' }}">
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-[1000] bg-gray-500/75 transition-opacity" x-on:click="show = false" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative z-[1001] inline-block align-top {{ $modalClass }} rounded-lg text-left overflow-hidden shadow-xl transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            {{ $slot }}
        </div>
    </div>
</div>
