@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-cabinet-orange hover:text-cabinet-orange/80 mb-2 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Users
            </a>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
            <p class="text-gray-600 mt-1">{{ $user->email }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if($user->is_admin)
                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-purple-100 text-purple-800">
                    <i class="fas fa-crown mr-1"></i> Admin
                </span>
            @endif
            @if(!$user->blocked)
                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">
                    <i class="fas fa-check-circle mr-1"></i> Active
                </span>
            @else
                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-red-100 text-red-800">
                    <i class="fas fa-ban mr-1"></i> Blocked
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Balance Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Balance Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                        <label class="block text-sm font-medium text-green-800 mb-1">Balance</label>
                        <p class="text-2xl font-bold text-green-900">${{ number_format($user->balance, 2) }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Total Deposited</label>
                        <p class="text-xl font-semibold text-gray-900">${{ number_format($user->deposited, 2) }}</p>
                    </div>

                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <label class="block text-sm font-medium text-green-800 mb-1">Real Deposit</label>
                        <p class="text-xl font-bold text-green-900">${{ number_format($realDeposits, 2) }}</p>
                        <p class="text-[10px] text-green-600 mt-1 uppercase font-semibold">On-chain confirmed</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Current Tier</label>
                        @if($user->current_tier)
                            <p class="text-xl font-semibold text-purple-700">Tier {{ $user->current_tier }}</p>
                        @else
                            <p class="text-xl font-semibold text-gray-400">No Tier</p>
                        @endif
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Verification</label>
                        @if($user->verification_status === 'verified')
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i> Verified
                            </span>
                        @elseif($user->verification_status === 'pending')
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                <i class="fas fa-times mr-1"></i> Unverified
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Transactions ({{ $user->transactions->count() }})</h2>

                @if($user->transactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($user->transactions->take(10) as $transaction)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        @if($transaction->type === 'deposit')
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-arrow-down mr-1"></i> Deposit
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                                <i class="fas fa-arrow-up mr-1"></i> Withdrawal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-semibold">${{ number_format($transaction->amount, 2) }}</td>
                                    <td class="px-4 py-3">
                                        @if($transaction->status === 'completed')
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Completed</span>
                                        @elseif($transaction->status === 'pending')
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">{{ ucfirst($transaction->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $transaction->created_at->format('d, M, Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.payments.show', $transaction) }}" class="text-cabinet-orange hover:text-cabinet-orange/80" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($user->transactions->count() > 10)
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.payments.index') }}?user_id={{ $user->id }}" class="text-sm text-cabinet-orange hover:text-cabinet-orange/80">
                                View all transactions <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @endif
                @else
                    <p class="text-center text-gray-500 py-8">No transactions yet</p>
                @endif
            </div>

            <!-- Active Staking -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Active Staking ({{ $user->stakingDeposits->where('status', 'active')->count() }})</h2>

                @if($user->stakingDeposits->where('status', 'active')->count() > 0)
                    <div class="space-y-3">
                        @foreach($user->stakingDeposits->where('status', 'active')->take(5) as $stake)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-cabinet-orange transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-gray-900">${{ number_format($stake->amount, 2) }}</span>
                                <span class="text-sm text-gray-600">{{ $stake->days }} days @ {{ $stake->percentage }}%</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Started: {{ $stake->start_date->format('d, M, Y') }}</span>
                                <span class="text-gray-600">Ends: {{ $stake->end_date->format('d, M, Y') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-8">No active staking</p>
                @endif
            </div>

            <!-- Earnings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Earnings Summary</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <label class="block text-sm font-medium text-green-800 mb-1">Profit Earnings</label>
                        <p class="text-xl font-bold text-green-900">
                            ${{ number_format($user->earnings->where('type', 'profit')->sum('amount'), 2) }}
                        </p>
                        <p class="text-xs text-green-700 mt-1">{{ $user->earnings->where('type', 'profit')->count() }} transactions</p>
                    </div>

                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <label class="block text-sm font-medium text-blue-800 mb-1">Referral Earnings</label>
                        <p class="text-xl font-bold text-blue-900">
                            ${{ number_format($user->earnings->where('type', 'referral')->sum('amount'), 2) }}
                        </p>
                        <p class="text-xs text-blue-700 mt-1">{{ $user->earnings->where('type', 'referral')->count() }} transactions</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Account Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h2>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">User ID</label>
                        <p class="text-sm text-gray-900 font-mono">{{ $user->id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Account Type</label>
                        <p class="text-sm text-gray-900">{{ ucfirst($user->account_type) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">UUID</label>
                        <p class="text-sm text-gray-900 font-mono break-all">{{ $user->uuid }}</p>
                    </div>

                    @if($user->phone)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                        <p class="text-sm text-gray-900">
                            @php
                                $phone = trim((string)$user->phone);
                            @endphp
                            @if(str_starts_with($phone, '+'))
                                {{ $phone }}
                            @else
                                {{ trim(($user->country_code ? $user->country_code . ' ' : '') . $phone) }}
                            @endif
                        </p>
                    </div>
                    @endif

                    @if($user->country)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Country</label>
                        <p class="text-sm text-gray-900">{{ $user->country }}</p>
                    </div>
                    @endif

                    @if($user->referral_code)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Referral Code</label>
                        <p class="text-sm text-gray-900 font-mono">{{ $user->referral_code }}</p>
                    </div>
                    @endif

                    @if($user->referred_by)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Referred By</label>
                        <p class="text-sm text-gray-900 font-mono">{{ $user->referred_by }}</p>
                    </div>
                    @endif

                    <div class="pt-3 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Registered</label>
                        <p class="text-sm text-gray-900">{{ $user->created_at->format('d, M, Y H:i') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                        <p class="text-sm text-gray-900">{{ $user->updated_at->format('d, M, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>

                <div class="space-y-3">
                    <div>
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center font-medium">
                            <i class="fas fa-edit mr-2"></i>
                            Edit User Profile
                        </a>
                        <p class="text-xs text-gray-500 mt-1 px-2">Modify user details, password, and settings</p>
                    </div>

                    @if(!$user->is_admin)
                        <div>
                            <form action="{{ route('admin.users.login-as', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to login as this user?')">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    Login As User
                                </button>
                            </form>
                            <p class="text-xs text-gray-500 mt-1 px-2">Access the platform as this user (impersonation)</p>
                        </div>
                    @endif

                    <div>
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-balance-modal', { detail: { userId: {{ $user->id }}, userName: '{{ addslashes($user->name) }}' } }))"
                                class="w-full px-4 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90 transition-colors font-medium">
                            <i class="fas fa-wallet mr-2"></i>
                            Adjust Balance
                        </button>
                        <p class="text-xs text-gray-500 mt-1 px-2">Add or subtract funds from user's balance</p>
                    </div>

                    <div>
                        <a href="{{ route('admin.payments.index') }}?search={{ $user->email }}"
                           class="block w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors text-center">
                            <i class="fas fa-filter mr-2"></i>
                            View All Transactions
                        </a>
                        <p class="text-xs text-gray-500 mt-1 px-2">Filter payments by this user</p>
                    </div>

                    @if(Auth::user()->is_super_admin && !$user->is_admin)
                        <div>
                            <button type="button" onclick="toggleAdminStatus({{ $user->id }})"
                                    class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium">
                                <i class="fas fa-crown mr-2"></i>
                                Grant Admin Access
                            </button>
                            <p class="text-xs text-gray-500 mt-1 px-2">Give administrator privileges to user</p>
                        </div>
                    @elseif(Auth::user()->is_super_admin && $user->is_admin && $user->id !== Auth::id())
                        <div>
                            <button type="button" onclick="toggleAdminStatus({{ $user->id }})"
                                    class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-medium">
                                <i class="fas fa-user-minus mr-2"></i>
                                Revoke Admin Access
                            </button>
                            <p class="text-xs text-gray-500 mt-1 px-2">Remove administrator privileges from user</p>
                        </div>
                    @endif

                    <div>
                        @if($user->blocked)
                            <button type="button" onclick="toggleBlockedStatus({{ $user->id }})"
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                                <i class="fas fa-check-circle mr-2"></i>
                                Activate Account
                            </button>
                            <p class="text-xs text-red-600 mt-1 px-2 font-medium">⚠️ User is currently BLOCKED and cannot login</p>
                        @else
                            <button type="button" onclick="toggleBlockedStatus({{ $user->id }})"
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                                <i class="fas fa-ban mr-2"></i>
                                Block Account
                            </button>
                            <p class="text-xs text-green-600 mt-1 px-2 font-medium">✓ User can login and use the platform</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function toggleAdminStatus(userId) {
    if (!confirm('Are you sure you want to make this user an admin?')) {
        return;
    }

    try {
        const response = await fetch(`/admin/users/${userId}/toggle-admin`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (data.success) {
            window.showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            window.showToast(data.message || 'Failed to update admin status', 'error');
        }
    } catch (error) {
        window.showToast('Error occurred', 'error');
        console.error(error);
    }
}

async function toggleBlockedStatus(userId) {
    if (!confirm('Are you sure you want to change the blocked status of this user?')) {
        return;
    }

    try {
        const response = await fetch(`/admin/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (data.success) {
            window.showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            window.showToast(data.message || 'Failed to update active status', 'error');
        }
    } catch (error) {
        window.showToast('Error occurred', 'error');
        console.error(error);
    }
}
</script>
@endpush
