<x-cabinet-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Deposit Funds</h1>

            <x-cabinet.card>
                <div class="js-deposit-form min-h-[400px]"></div>
            </x-cabinet.card>

            @if(isset($pendingDeposits) && $pendingDeposits->count() > 0)
                <div class="mt-6">
                    <h2 class="text-xl font-semibold mb-4">Pending Deposits</h2>
                    @foreach($pendingDeposits as $deposit)
                        <x-cabinet.card class="mb-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-semibold">${{ number_format($deposit->amount, 2) }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $deposit->created_at->diffForHumans() }}</p>
                                </div>
                                <x-cabinet.badge>Pending</x-cabinet.badge>
                            </div>
                        </x-cabinet.card>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-cabinet-layout>
