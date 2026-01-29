<x-cabinet-layout>
    <div class="bg-white rounded-xl border border-cabinet-border overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-cabinet-text-main">
                Transactions | <span class="text-cabinet-blue">Withdrawals</span>
            </h2>
        </div>
        <div class="p-6">
            <div class="js-withdraw-table" data-url="{{ route('cabinet.transactions.withdraw.data') }}"></div>
        </div>
    </div>
</x-cabinet-layout>
