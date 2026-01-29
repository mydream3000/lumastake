<x-cabinet-layout>
    <div class="bg-white rounded-xl border border-cabinet-border overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-cabinet-text-main">
                Earnings | <span class="text-cabinet-blue">Profit Earnings</span>
            </h2>
        </div>
        <div class="p-4 lg:p-6">
            <div class="js-profit-earnings-table" data-url="{{ route('cabinet.earnings.profit.data') }}"></div>
        </div>
    </div>
</x-cabinet-layout>
