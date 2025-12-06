@extends('layouts.admin')

@section('title', 'Referral Levels Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Referral Levels</h1>
            <p class="text-gray-600 mt-1">Manage referral program levels and reward percentages</p>
        </div>
    </div>

    <!-- Referral Levels Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <form id="referral-levels-form">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Min Partners</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Reward (%)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">New Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">New Min Partners</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">New Reward (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($levels as $level)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                                 style="background: linear-gradient(135deg, {{ ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444'][$level->level - 1] ?? '#6b7280' }} 0%, {{ ['#059669', '#2563eb', '#7c3aed', '#d97706', '#dc2626'][$level->level - 1] ?? '#4b5563' }} 100%);">
                                                {{ $level->level }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        {{ $level->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $level->min_partners }}+
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $level->reward_percentage }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="hidden" name="levels[{{ $loop->index }}][id]" value="{{ $level->id }}">
                                        <input type="text"
                                               name="levels[{{ $loop->index }}][name]"
                                               value="{{ $level->name }}"
                                               class="w-40 rounded-md border-gray-300 shadow-sm focus:border-cabinet-orange focus:ring focus:ring-cabinet-orange focus:ring-opacity-50"
                                               required>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number"
                                               name="levels[{{ $loop->index }}][min_partners]"
                                               value="{{ $level->min_partners }}"
                                               min="0"
                                               class="w-24 rounded-md border-gray-300 shadow-sm focus:border-cabinet-orange focus:ring focus:ring-cabinet-orange focus:ring-opacity-50"
                                               required>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number"
                                               name="levels[{{ $loop->index }}][reward_percentage]"
                                               value="{{ $level->reward_percentage }}"
                                               min="0"
                                               max="100"
                                               step="0.01"
                                               class="w-24 rounded-md border-gray-300 shadow-sm focus:border-cabinet-orange focus:ring focus:ring-cabinet-orange focus:ring-opacity-50"
                                               required>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        No referral levels found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($levels->isNotEmpty())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                        Changes will recalculate referral levels for all users
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
                    <h4 class="font-semibold text-blue-900 mb-1">Tip: Progressive Rewards</h4>
                    <p class="text-sm text-blue-800">
                        Higher levels should have progressively higher reward percentages to incentivize users to refer more active partners.
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
                        Changing levels will immediately recalculate all users' referral levels. Only referrals who made at least one deposit count as active partners.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Referral System Info -->
    <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-lg p-6">
        <div class="flex items-start">
            <i class="fas fa-users text-purple-600 text-2xl mr-4 mt-1"></i>
            <div>
                <h4 class="font-bold text-purple-900 mb-2 text-lg">How the Referral System Works</h4>
                <ul class="space-y-2 text-sm text-purple-800">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-600 mr-2 mt-0.5"></i>
                        <span>One-level referral system: Users earn rewards only from their direct referrals</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-600 mr-2 mt-0.5"></i>
                        <span>Active partners: Only referrals who made at least one deposit count</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-600 mr-2 mt-0.5"></i>
                        <span>Rewards source: Referrers earn a percentage from their referrals' staking profits only (not from deposits)</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-purple-600 mr-2 mt-0.5"></i>
                        <span>Level progression: As users gain more active partners, they unlock higher reward percentages</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('referral-levels-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    if (!confirm('Are you sure you want to update referral levels? This will recalculate levels for all users.')) {
        return;
    }

    const formData = new FormData(e.target);
    const levelsData = {};

    // Convert FormData to object
    for (const [key, value] of formData.entries()) {
        const match = key.match(/levels\[(\d+)\]\[(\w+)\]/);
        if (match) {
            const index = match[1];
            const field = match[2];
            if (!levelsData[index]) {
                levelsData[index] = {};
            }
            levelsData[index][field] = value;
        }
    }

    // Update each level
    let allSuccess = true;
    let errorMessages = [];

    for (const levelData of Object.values(levelsData)) {
        try {
            const response = await fetch(`/admin/referral-levels/${levelData.id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    name: levelData.name,
                    min_partners: parseInt(levelData.min_partners),
                    reward_percentage: parseFloat(levelData.reward_percentage),
                }),
            });

            const data = await response.json();

            if (!data.success) {
                allSuccess = false;
                errorMessages.push(data.message || 'Failed to update level');
            }
        } catch (error) {
            allSuccess = false;
            errorMessages.push('Error updating level: ' + error.message);
            console.error(error);
        }
    }

    if (allSuccess) {
        showToast('All referral levels updated successfully. User levels have been recalculated.', 'success');
        setTimeout(() => {
            location.reload();
        }, 1500);
    } else {
        showToast('Some levels failed to update: ' + errorMessages.join(', '), 'error');
    }
});
</script>
@endpush
@endsection
