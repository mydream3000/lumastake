<x-cabinet-layout>
    <div class="card">
        <x-slot name="header">
            <h2 class="text-xl font-bold text-cabinet-text-dark">
                Transactions | <span class="text-cabinet-blue">Withdraw</span>
            </h2>
        </x-slot>
        <div class="js-withdraw-table" data-url="{{ route('cabinet.transactions.withdraw.data') }}"></div>
    </div>
</x-cabinet-layout>
