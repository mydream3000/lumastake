@props(['name', 'show' => false, 'title' => '', 'titleColor' => null])

<div
    x-show="rightbarOpen === '{{ $name }}'"
    x-on:keydown.escape.window="closeRightbar()"
    x-cloak
    class="fixed z-50 inset-0 overflow-hidden"
    aria-labelledby="slide-over-title"
    role="dialog"
    aria-modal="true"
>
    <div class="absolute inset-0 overflow-hidden">
        <div x-show="rightbarOpen === '{{ $name }}'" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-gray-500/75 transition-opacity" x-on:click="closeRightbar()" aria-hidden="true"></div>

        <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
            <div x-show="rightbarOpen === '{{ $name }}'" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="relative w-screen max-w-md">
                <div class="h-full flex flex-col py-6 bg-white shadow-xl overflow-y-scroll">
                    <div class="px-4 sm:px-6">
                        <div class="flex items-start justify-between">
                            <h2 class="text-2xl font-black text-cabinet-blue uppercase tracking-tight" id="slide-over-title" @if($titleColor) style="color: {{ $titleColor }}" @endif>
                                {{ $title }}
                            </h2>
                            <div class="ml-3 h-7 flex items-center">
                                <button x-on:click="closeRightbar()" type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <span class="sr-only">Close panel</span>
                                    <!-- Heroicon name: outline/x -->
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <hr class="mt-4 border-gray-300">
                    </div>
                    <div class="mt-6 relative flex-1 px-4 sm:px-6">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
