@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 mt-1">Общая статистика и аналитика платформы</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-500">Последнее обновление</p>
            <p class="text-sm font-medium text-gray-900">{{ now()->format('d, M, Y H:i') }}</p>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($totalUsers) }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        +{{ $newUsersToday }} today
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Users</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($activeUsers) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ round(($activeUsers / max($totalUsers, 1)) * 100, 1) }}% of total
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Balance -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Balance</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">${{ number_format($totalBalance, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        All users combined
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-wallet text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Stakes -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Stakes</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($activeStakes) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        ${{ number_format($totalStaked, 2) }} staked
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-coins text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Real Deposits -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Total Real Deposits</p>
                    <p class="text-xl font-bold text-orange-600 mt-1">${{ number_format($totalRealDeposits, 2) }}</p>
                </div>
                <i class="fas fa-clock text-orange-400"></i>
            </div>
        </div>

        <!-- Pending Withdrawals -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Pending Withdrawals</p>
                    <p class="text-xl font-bold text-red-600 mt-1">{{ $pendingWithdrawals }}</p>
                    <p class="text-xs text-gray-500">${{ number_format($pendingWithdrawalsAmount, 2) }}</p>
                </div>
                <i class="fas fa-exclamation-triangle text-red-400"></i>
            </div>
        </div>

        <!-- Real Deposits Today -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Real Deposits Today</p>
                    <p class="text-xl font-bold text-green-600 mt-1">${{ number_format($realDepositsToday, 2) }}</p>
                </div>
                <i class="fas fa-arrow-down text-green-400"></i>
            </div>
        </div>

        <!-- Today Withdrawals -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Withdrawals Today</p>
                    <p class="text-xl font-bold text-red-600 mt-1">${{ number_format($completedWithdrawalsToday, 2) }}</p>
                </div>
                <i class="fas fa-arrow-up text-red-400"></i>
            </div>
        </div>
    </div>

    <!-- New Statistics: Registrations & Depositors -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Registrations Statistics -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-user-plus text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">New Registrations</h3>
                    <p class="text-sm text-gray-500">User registration statistics</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <p class="text-sm font-medium text-gray-600 mb-1">Today</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $newUsersToday }}</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <p class="text-sm font-medium text-gray-600 mb-1">This Week</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $newUsersThisWeek }}</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <p class="text-sm font-medium text-gray-600 mb-1">This Month</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $newUsersThisMonth }}</p>
                </div>
            </div>
        </div>

        <!-- New Depositors Statistics -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">New Depositors</h3>
                    <p class="text-sm text-gray-500">Clients who made deposits</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-sm font-medium text-gray-600 mb-1">Today</p>
                    <p class="text-3xl font-bold text-green-600">{{ $newDepositorsToday }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-sm font-medium text-gray-600 mb-1">This Week</p>
                    <p class="text-3xl font-bold text-green-600">{{ $newDepositorsThisWeek }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-sm font-medium text-gray-600 mb-1">This Month</p>
                    <p class="text-3xl font-bold text-green-600">{{ $newDepositorsThisMonth }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Registrations Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-user-plus text-blue-600 mr-2"></i>
                Registrations (7 days)
            </h3>
            <div class="space-y-2">
                @php
                    $maxReg = max(array_values($registrationsChart) ?: [1]);
                @endphp
                @forelse(array_reverse($registrationsChart, true) as $date => $count)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ \Carbon\Carbon::parse($date)->format('d M') }}</span>
                            <span class="font-semibold text-gray-900">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($count / $maxReg) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No data available</p>
                @endforelse
            </div>
        </div>

        <!-- Deposits Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-line text-green-600 mr-2"></i>
                Deposits (7 days)
            </h3>
            <div class="space-y-2">
                @php
                    $maxDep = max(array_values($depositsChart) ?: [1]);
                @endphp
                @forelse(array_reverse($depositsChart, true) as $date => $amount)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ \Carbon\Carbon::parse($date)->format('d M') }}</span>
                            <span class="font-semibold text-gray-900">${{ number_format($amount, 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($amount / $maxDep) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No data available</p>
                @endforelse
            </div>
        </div>

        <!-- Staking Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-coins text-purple-600 mr-2"></i>
                Staking (7 days)
            </h3>
            <div class="space-y-2">
                @php
                    $maxStake = max(array_values($stakingChart) ?: [1]);
                @endphp
                @forelse(array_reverse($stakingChart, true) as $date => $amount)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ \Carbon\Carbon::parse($date)->format('d M') }}</span>
                            <span class="font-semibold text-gray-900">${{ number_format($amount, 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ ($amount / $maxStake) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Activity Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user-plus text-blue-600 mr-2"></i>
                    Recent Registrations
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentUsers as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">${{ number_format($user->balance, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('d, M, Y') }}</td>
                                <td class="px-6 py-4">
                                    @if($user->active)
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">No recent registrations</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-200">
                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-cabinet-orange hover:text-cabinet-orange/80">
                    View all users <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Recent Deposits -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-arrow-down text-green-600 mr-2"></i>
                    Recent Deposits
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentDeposits as $deposit)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $deposit->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $deposit->user->email }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-green-600">${{ number_format($deposit->amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $deposit->created_at->format('d, M, Y') }}</td>
                                <td class="px-6 py-4">
                                    @if($deposit->status === 'completed')
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Completed</span>
                                    @elseif($deposit->status === 'pending')
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ ucfirst($deposit->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">No recent deposits</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-200">
                <a href="{{ route('admin.payments.index') }}" class="text-sm font-medium text-cabinet-orange hover:text-cabinet-orange/80">
                    View all payments <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Recent Withdrawals -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-arrow-up text-red-600 mr-2"></i>
                    Recent Withdrawals
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentWithdrawals as $withdrawal)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $withdrawal->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $withdrawal->user->email }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-red-600">${{ number_format($withdrawal->amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $withdrawal->created_at->format('d, M, Y') }}</td>
                                <td class="px-6 py-4">
                                    @if($withdrawal->status === 'completed')
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Completed</span>
                                    @elseif($withdrawal->status === 'pending')
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @elseif($withdrawal->status === 'cancelled')
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Cancelled</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ ucfirst($withdrawal->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">No recent withdrawals</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-200">
                <a href="{{ route('admin.payments.index') }}" class="text-sm font-medium text-cabinet-orange hover:text-cabinet-orange/80">
                    View all payments <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Recent Stakes -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-coins text-purple-600 mr-2"></i>
                    Recent Staking
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentStakes as $stake)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $stake->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $stake->user->email }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-purple-600">${{ number_format($stake->amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $stake->days }} days</td>
                                <td class="px-6 py-4">
                                    @if($stake->status === 'active')
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                    @elseif($stake->status === 'pending')
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @elseif($stake->status === 'completed')
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Completed</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ ucfirst($stake->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">No recent staking</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-200">
                <a href="{{ route('admin.pools.index') }}" class="text-sm font-medium text-cabinet-orange hover:text-cabinet-orange/80">
                    View all pools <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
