<x-cabinet-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-cabinet.card>
                <h1 class="text-2xl font-bold text-cabinet-text-main mb-6">
                    Transaction | <span class="text-cabinet-blue">Withdraw</span>
                </h1>

                <div class="js-withdraw-table min-h-[400px]" data-url="{{ route('cabinet.transactions.withdraw.data') }}"></div>
            </x-cabinet.card>
        </div>
    </div>
</x-cabinet-layout>
