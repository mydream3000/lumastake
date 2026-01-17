<x-cabinet-layout>
    <div class="card">
        <x-slot name="header">
            <h2 class="text-xl font-bold text-cabinet-text-dark">
                Transactions | <span class="text-cabinet-blue">Deposits</span>
            </h2>
        </x-slot>
        <div class="js-deposits-table" data-url="{{ route('cabinet.transactions.deposits.data') }}"></div>
    </div>
</x-cabinet-layout>
