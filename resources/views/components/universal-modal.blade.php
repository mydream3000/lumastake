<div
    x-data="{ open: false, title: '', message: '', confirmText: 'OK' }"
    x-show="open"
    @open-modal.window="
        title = $event.detail.title || 'Information';
        message = $event.detail.message || '';
        confirmText = $event.detail.confirmText || 'OK';
        open = true;
    "
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
    x-cloak
>
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/70 transition-opacity" @click="open = false"></div>

    <!-- Modal Dialog -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div @click.stop
             class="relative bg-cabinet-dark rounded-lg shadow-xl max-w-lg w-full p-6 border border-cabinet-grey"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <!-- Close Button -->
            <button @click="open = false"
                    class="absolute top-4 right-4 text-cabinet-text-grey hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Title -->
            <h3 class="text-xl font-bold text-cabinet-text-dark mb-4" x-text="title"></h3>

            <!-- Content -->
            <div class="text-cabinet-text-grey mb-6" x-html="message"></div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
                <button @click="open = false"
                        class="px-6 py-2.5 bg-cabinet-blue text-white rounded-lg font-semibold hover:bg-cabinet-blue/90 transition-colors"
                        x-text="confirmText">
                </button>
            </div>
        </div>
    </div>
</div>
