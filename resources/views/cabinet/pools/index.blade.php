<x-cabinet-layout>
    <!-- Desktop Version (Таблица как раньше) -->
    <div class="hidden lg:block">
        <x-cabinet.card>
            <x-slot name="header">
                Investment Pools
            </x-slot>

            <div class="card-scroll">
                <div class="js-investment-pool-table" data-url="{{ route('cabinet.pools.data') }}" data-balance="{{ $user->available_balance }}"></div>
            </div>
        </x-cabinet.card>
    </div>

    <!-- Mobile Version (Новый дизайн) -->
    <div class="lg:hidden bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Investment Pools</h2>
        </div>

        <!-- Vue Pools List (mobile) -->
        <div class="p-0">
            <div class="js-pools-list" data-url="{{ route('cabinet.pools.data') }}" data-balance="{{ $user->available_balance }}"></div>
        </div>
    </div>
</x-cabinet-layout>
