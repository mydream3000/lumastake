@extends('layouts.admin')

@section('title', 'Edit Pool Percentages')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Edit Pool Percentages</h1>
            <p class="text-gray-600 mt-1">Manage APR percentages for all tiers</p>
        </div>
        <a href="{{ route('admin.pools.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Pools
        </a>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('admin.pools.percentages', ['days' => $days, 'type' => 'normal']) }}"
               class="{{ $accountType === 'normal' ? 'border-cabinet-orange text-cabinet-orange' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Normal Account
            </a>
            <a href="{{ route('admin.pools.percentages', ['days' => $days, 'type' => 'islamic']) }}"
               class="{{ $accountType === 'islamic' ? 'border-cabinet-orange text-cabinet-orange' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Islamic Account
            </a>
        </nav>
    </div>

    <!-- Duration Selector -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Select Duration</label>
        <select id="duration-select" onchange="location.href='{{ route('admin.pools.percentages') }}?days=' + this.value + '&type={{ $accountType }}'"
                class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-cabinet-orange focus:ring focus:ring-cabinet-orange focus:ring-opacity-50">
            @foreach($durations as $duration)
                <option value="{{ $duration }}" {{ $days == $duration ? 'selected' : '' }}>
                    {{ $duration }} Days
                </option>
            @endforeach
        </select>
    </div>

    <!-- Percentages Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <form id="percentages-form">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance Range</th>
                                @if($accountType === 'islamic')
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current APR Range</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">New APR Range</th>
                                @else
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current APR (%)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">New APR (%)</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($pools as $pool)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold mr-3"
                                                 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                {{ $pool->tier->level }}
                                            </div>
                                            <span class="font-semibold text-gray-900">{{ $pool->tier->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        ${{ number_format($pool->tier->min_balance, 0) }}
                                        @if($pool->tier->max_balance)
                                            - ${{ number_format($pool->tier->max_balance, 0) }}
                                        @else
                                            +
                                        @endif
                                    </td>
                                    @if($accountType === 'islamic')
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ $pool->min_percentage }} - {{ $pool->max_percentage }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="hidden" name="pools[{{ $loop->index }}][id]" value="{{ $pool->id }}">
                                            <div class="flex items-center gap-2">
                                                <input type="text" name="pools[{{ $loop->index }}][min_percentage]" value="{{ $pool->min_percentage }}" class="w-24 rounded-md border-gray-300 shadow-sm focus:border-cabinet-orange focus:ring focus:ring-cabinet-orange focus:ring-opacity-50" required>
                                                <span>-</span>
                                                <input type="text" name="pools[{{ $loop->index }}][max_percentage]" value="{{ $pool->max_percentage }}" class="w-24 rounded-md border-gray-300 shadow-sm focus:border-cabinet-orange focus:ring focus:ring-cabinet-orange focus:ring-opacity-50" required>
                                            </div>
                                        </td>
                                    @else
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ $pool->percentage }}%
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="hidden" name="pools[{{ $loop->index }}][id]" value="{{ $pool->id }}">
                                            <input type="number"
                                                   name="pools[{{ $loop->index }}][percentage]"
                                                   value="{{ $pool->percentage }}"
                                                   min="0"
                                                   max="200"
                                                   step="0.01"
                                                   class="w-32 rounded-md border-gray-300 shadow-sm focus:border-cabinet-orange focus:ring focus:ring-cabinet-orange focus:ring-opacity-50"
                                                   required>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        No pools found for this duration
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($pools->isNotEmpty())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                        Changes will only apply to new staking deposits
                    </div>
                    <button type="submit"
                            class="inline-flex items-center px-6 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Save Changes
                    </button>
                </div>
            @endif
        </form>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-lightbulb text-blue-600 text-xl mr-3 mt-1"></i>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-1">Tip: Gradual Increases</h4>
                    <p class="text-sm text-blue-800">
                        Higher tiers should have progressively higher APR to incentivize users to increase their balance.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-amber-50 border-2 border-amber-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-amber-600 text-xl mr-3 mt-1"></i>
                <div>
                    <h4 class="font-semibold text-amber-900 mb-1">Important</h4>
                    <p class="text-sm text-amber-800">
                        Changing percentages will NOT affect existing active staking deposits. Only new deposits will use the updated rates.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('percentages-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const accountType = '{{ $accountType }}';
    const pools = [];

    const poolsData = {};
    for (const [key, value] of formData.entries()) {
        const match = key.match(/pools\[(\d+)\]\[(\w+)\]/);
        if (match) {
            const index = match[1];
            const field = match[2];
            if (!poolsData[index]) {
                poolsData[index] = {};
            }
            poolsData[index][field] = value;
        }
    }

    Object.values(poolsData).forEach(pool => {
        if (accountType === 'islamic') {
            pools.push({
                id: parseInt(pool.id),
                min_percentage: pool.min_percentage,
                max_percentage: pool.max_percentage,
            });
        } else {
            pools.push({
                id: parseInt(pool.id),
                percentage: parseFloat(pool.percentage),
            });
        }
    });

    try {
        const response = await fetch('{{ route('admin.pools.update-percentages') }}?type=' + accountType, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ pools }),
        });

        const data = await response.json();

        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'Failed to update percentages', 'error');
        }
    } catch (error) {
        showToast('Error updating percentages', 'error');
        console.error(error);
    }
});
</script>
@endpush
@endsection
