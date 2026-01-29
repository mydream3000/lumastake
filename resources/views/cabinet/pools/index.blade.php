<x-cabinet-layout>
    <div class="bg-white rounded-xl border border-cabinet-border overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-cabinet-text-main">Investment Pools</h2>
        </div>
        <div class="p-6">
            <div class="js-pools-list" data-url="{{ route('cabinet.pools.data') }}" data-balance="{{ $user->available_balance }}"></div>
        </div>
    </div>
</x-cabinet-layout>
