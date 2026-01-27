<x-cabinet-layout>
    <div class="card p-4 lg:p-6">
        <h2 class="text-xl font-bold text-cabinet-text-main mb-6">
            Earnings | <span class="text-cabinet-blue">Profit Earnings</span>
        </h2>
        <div class="js-profit-earnings-table" data-url="{{ route('cabinet.earnings.profit.data') }}"></div>
    </div>
</x-cabinet-layout>
