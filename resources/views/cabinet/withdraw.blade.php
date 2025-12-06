<x-cabinet-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Withdraw Funds</h1>

            <x-cabinet.card>
                <h3 class="text-lg font-semibold mb-4">Withdraw Crypto</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Available balance: $<span class="font-semibold text-gray-900 dark:text-white" x-text="$store.userBalance.displayAvailableBalance.toFixed(2)">{{ number_format($availableBalance, 2) }}</span>
                </p>
                <x-cabinet.button
                    x-on:click="$dispatch('open-rightbar', { name: 'withdraw-sidebar' })"
                >
                    Withdraw
                </x-cabinet.button>
            </x-cabinet.card>
        </div>
    </div>
</x-cabinet-layout>
