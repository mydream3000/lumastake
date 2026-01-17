<x-cabinet-layout>
    <div class="card p-6">
        <h2 class="text-xl font-bold text-cabinet-text-main mb-6">
            Transactions | <span class="text-cabinet-blue">Deposits</span>
        </h2>
        <div class="js-deposits-table" data-url="{{ route('cabinet.transactions.deposits.data') }}"></div>
    </div>
</x-cabinet-layout>
