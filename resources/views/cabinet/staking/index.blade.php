<x-cabinet-layout>
    <div class="card">
        <x-slot name="header">
            <h2 class="text-xl font-bold text-cabinet-text-dark">Manage Staking</h2>
        </x-slot>

        <div class="js-staking-table" data-url="{{ route('cabinet.staking.data') }}"></div>
    </div>
</x-cabinet-layout>
