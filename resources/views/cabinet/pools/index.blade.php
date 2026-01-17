<x-cabinet-layout>
    <div class="card">
        <x-slot name="header">
            <h2 class="text-xl font-bold text-cabinet-text-dark">Investment Pools</h2>
        </x-slot>

        <div class="js-pools-list" data-url="{{ route('cabinet.pools.data') }}" data-balance="{{ $user->available_balance }}"></div>
    </div>
</x-cabinet-layout>
