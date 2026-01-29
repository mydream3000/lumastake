<x-cabinet-layout>
    <div class="bg-white rounded-xl border border-cabinet-border overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-cabinet-text-main">
                Transactions | <span class="text-cabinet-blue">Deposits</span>
            </h2>
        </div>
        <div class="p-6">
            <div class="js-deposits-table" data-url="{{ route('cabinet.transactions.deposits.data') }}"></div>
        </div>
    </div>
</x-cabinet-layout>
