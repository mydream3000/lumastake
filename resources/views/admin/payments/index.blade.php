@extends('layouts.admin')

@section('title', 'Payments Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Payments Management</h1>
            <p class="text-gray-600 mt-1">Управление депозитами и выводами</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-6 lg:grid-cols-6 gap-4">
        <!-- Total Real Deposits -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Real Deposits</h3>
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">${{ number_format($totalRealDeposits, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">On-chain confirmed (USDT + USDC)</p>
        </div>

        <!-- Real Deposits Today -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Real Deposits Today</h3>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">${{ number_format($realDepositsToday, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">Today's on-chain deposits</p>
        </div>

        <!-- Total Deposits -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Deposits</h3>
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">${{ number_format($totalDeposits, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">All confirmed deposits</p>
        </div>

        <!-- Total Withdrawals -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Withdrawals</h3>
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">${{ number_format($totalWithdrawals, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">All confirmed withdrawals</p>
        </div>

        <!-- Pending Deposits -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Pending Deposits</h3>
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">${{ number_format($pendingDeposits, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">Awaiting confirmation</p>
        </div>

        <!-- Pending Withdrawals -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Pending Withdrawals</h3>
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900">${{ number_format($pendingWithdrawals, 2) }}</div>
            <p class="text-xs text-gray-500 mt-1">Awaiting approval</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search-input" placeholder="ID, User, TX Hash..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="type-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
                    <option value="">All</option>
                    <option value="deposit">Deposits</option>
                    <option value="withdraw">Withdrawals</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="failed">Failed</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" id="date-from-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" id="date-to-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
            </div>

            <!-- Filter Button -->
            <div class="flex items-end">
                <button id="apply-filters-btn" class="w-full px-4 py-2 bg-cabinet-orange text-white rounded-lg font-medium hover:bg-cabinet-orange/90 transition-colors">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </div>
        <div class="mt-4 flex justify-end gap-2">
            <button id="export-xlsx-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                <i class="fas fa-file-excel mr-2"></i>Export to XLSX
            </button>
            <button id="export-xls-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                <i class="fas fa-file-excel mr-2"></i>Export to XLS
            </button>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="js-payments-table" data-url="{{ route('admin.payments.index') }}"></div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approve-modal"
     x-data="{
         open: false,
         paymentId: null,
         comment: '',
         loading: false,
         async submit() {
             if (this.loading) return;
             this.loading = true;
             try {
                 const response = await fetch(`/admin/payments/${this.paymentId}/approve`, {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                     },
                     body: JSON.stringify({ comment: this.comment.trim() || null }),
                 });
                 const data = await response.json();
                 if (data.success) {
                     window.showToast(data.message, 'success');
                     this.open = false;
                     this.comment = '';
                     window.dispatchEvent(new Event('datatable-refresh'));
                     loadStats();
                 } else {
                     window.showToast(data.message || 'Error occurred', 'error');
                 }
             } catch (error) {
                 window.showToast('Error occurred', 'error');
                 console.error(error);
             } finally {
                 this.loading = false;
             }
         }
     }"
     x-cloak
     x-show="open"
     @open-approve-modal.window="open = true; paymentId = $event.detail.paymentId; comment = ''"
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6" @click.outside="open = false">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Approve Withdrawal</h3>
        <p class="text-sm text-gray-600 mb-4">Balance will be deducted from user account. You can add a comment (e.g., transaction hash).</p>

        <form @submit.prevent="submit()">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comment (Optional)</label>
                    <textarea x-model="comment" rows="4" placeholder="Transaction hash or any notes..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"></textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" :disabled="loading" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50">
                    <span x-show="!loading">Approve Withdrawal</span>
                    <span x-show="loading">Processing...</span>
                </button>
                <button type="button" @click="open = false" :disabled="loading" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors disabled:opacity-50">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal"
     x-data="{
         open: false,
         paymentId: null,
         reason: '',
         loading: false,
         async submit() {
             if (this.loading) return;
             if (!this.reason.trim()) {
                 window.showToast('Please provide a reason for rejection', 'error');
                 return;
             }
             this.loading = true;
             try {
                 const response = await fetch(`/admin/payments/${this.paymentId}/reject`, {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                     },
                     body: JSON.stringify({ reason: this.reason.trim() }),
                 });
                 const data = await response.json();
                 if (data.success) {
                     window.showToast(data.message, 'success');
                     this.open = false;
                     this.reason = '';
                     window.dispatchEvent(new Event('datatable-refresh'));
                     loadStats();
                 } else {
                     window.showToast(data.message || 'Error occurred', 'error');
                 }
             } catch (error) {
                 window.showToast('Error occurred', 'error');
                 console.error(error);
             } finally {
                 this.loading = false;
             }
         }
     }"
     x-cloak
     x-show="open"
     @open-reject-modal.window="open = true; paymentId = $event.detail.paymentId; reason = ''"
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6" @click.outside="open = false">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Withdrawal</h3>
        <p class="text-sm text-gray-600 mb-4">The withdrawal request will be cancelled. Balance was not deducted.</p>

        <form @submit.prevent="submit()">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                    <textarea x-model="reason" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"></textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" :disabled="loading" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50">
                    <span x-show="!loading">Reject Withdrawal</span>
                    <span x-show="loading">Processing...</span>
                </button>
                <button type="button" @click="open = false" :disabled="loading" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors disabled:opacity-50">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Complete Modal -->
<div id="complete-modal"
     x-data="{
         open: false,
         paymentId: null,
         txHash: '',
         loading: false,
         async submit() {
             if (this.loading) return;
             if (!this.txHash.trim()) {
                 window.showToast('Please enter transaction hash', 'error');
                 return;
             }
             this.loading = true;
             try {
                 const response = await fetch(`/admin/payments/${this.paymentId}/complete`, {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                     },
                     body: JSON.stringify({ tx_hash: this.txHash.trim() }),
                 });
                 const data = await response.json();
                 if (data.success) {
                     window.showToast(data.message, 'success');
                     this.open = false;
                     this.txHash = '';
                     window.dispatchEvent(new Event('datatable-refresh'));
                     loadStats();
                 } else {
                     window.showToast(data.message || 'Error occurred', 'error');
                 }
             } catch (error) {
                 window.showToast('Error occurred', 'error');
                 console.error(error);
             } finally {
                 this.loading = false;
             }
         }
     }"
     x-cloak
     x-show="open"
     @open-complete-modal.window="open = true; paymentId = $event.detail.paymentId; txHash = ''"
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6" @click.outside="open = false">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Complete Withdrawal</h3>
        <p class="text-sm text-gray-600 mb-4">Enter transaction hash from blockchain</p>

        <form @submit.prevent="submit()">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Hash</label>
                    <input type="text" x-model="txHash" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange">
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" :disabled="loading" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50">
                    <span x-show="!loading">Mark as Completed</span>
                    <span x-show="loading">Processing...</span>
                </button>
                <button type="button" @click="open = false" :disabled="loading" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors disabled:opacity-50">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Export buttons
    const exportXlsxBtn = document.getElementById('export-xlsx-btn');
    const exportXlsBtn = document.getElementById('export-xls-btn');

    function getExportUrl(format) {
        const baseUrl = "{{ route('admin.payments.export') }}";
        const params = new URLSearchParams(filters);
        params.append('format', format);
        return `${baseUrl}?${params.toString()}`;
    }

    if (exportXlsxBtn) {
        exportXlsxBtn.addEventListener('click', () => {
            window.location.href = getExportUrl('xlsx');
        });
    }

    if (exportXlsBtn) {
        exportXlsBtn.addEventListener('click', () => {
            window.location.href = getExportUrl('xls');
        });
    }
    // Load statistics
    loadStats();

    // Initialize DataTable
    const tableContainer = document.querySelector('.js-payments-table');
    if (!tableContainer) return;

    const columns = [
        {
            key: 'id',
            label: 'ID',
            sortable: true,
            width: '60px'
        },
        {
            key: 'type',
            label: 'Type',
            sortable: true,
            render: (value) => value === 'deposit'
                ? '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800"><i class="fas fa-arrow-down mr-1"></i>Deposit</span>'
                : '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800"><i class="fas fa-arrow-up mr-1"></i>Withdrawal</span>'
        },
        {
            key: 'user_name',
            label: 'User',
            sortable: true,
            render: (value, row) => `
                <div>
                    <p class="text-sm font-medium text-gray-900">${value}</p>
                    <p class="text-xs text-gray-500">${row.user_email}</p>
                </div>
            `
        },
        {
            key: 'real_total_deposits',
            label: 'Real Total Deposits',
            sortable: true,
            render: (value) => `<span class="text-sm font-semibold text-green-600">$${parseFloat(value || 0).toFixed(2)}</span>`
        },
        {
            key: 'today_withdrawals',
            label: 'Today Withdrawals',
            sortable: true,
            render: (value) => `<span class="text-sm font-semibold text-orange-600">$${parseFloat(value || 0).toFixed(2)}</span>`
        },
        {
            key: 'amount',
            label: 'Amount',
            sortable: true,
            render: (value) => `<span class="text-sm font-semibold text-gray-900">$${parseFloat(value).toFixed(2)}</span>`
        },
        {
            key: 'status',
            label: 'Status',
            sortable: true,
            render: (value) => {
                const statusMap = {
                    pending: { bg: 'yellow', text: 'Pending' },
                    confirmed: { bg: 'blue', text: 'Confirmed' },
                    completed: { bg: 'green', text: 'Completed' },
                    cancelled: { bg: 'red', text: 'Cancelled' },
                    failed: { bg: 'red', text: 'Failed' },
                };
                const status = statusMap[value] || { bg: 'gray', text: value };
                return `<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-${status.bg}-100 text-${status.bg}-800">${status.text}</span>`;
            }
        },
        {
            key: 'tx_hash',
            label: 'TX Hash',
            sortable: false,
            render: (value) => value ? `<span class="text-xs text-gray-600 font-mono">${value.substring(0, 10)}...</span>` : '<span class="text-xs text-gray-400">-</span>'
        },
        {
            key: 'created_at',
            label: 'Date',
            sortable: true,
            render: (value) => `<span class="text-sm text-gray-600">${value}</span>`
        },
        {
            key: 'actions',
            label: 'Actions',
            sortable: false,
            render: (value, row) => {
                const actions = [];

                actions.push(`<a href="/admin/payments/${row.id}" class="text-blue-600 hover:text-blue-800" title="View"><i class="fas fa-eye"></i></a>`);

                if (row.type === 'withdraw' && row.status === 'pending') {
                    actions.push(`<button onclick="openApproveModal(${row.id})" class="text-green-600 hover:text-green-800" title="Approve"><i class="fas fa-check"></i></button>`);
                    actions.push(`<button onclick="openRejectModal(${row.id})" class="text-red-600 hover:text-red-800" title="Reject"><i class="fas fa-times"></i></button>`);
                }

                if (row.type === 'withdraw' && (row.status === 'pending' || row.status === 'confirmed')) {
                    actions.push(`<button onclick="openCompleteModal(${row.id})" class="text-purple-600 hover:text-purple-800" title="Complete"><i class="fas fa-check-circle"></i></button>`);
                }

                return `<div class="flex gap-3">${actions.join('')}</div>`;
            }
        },
    ];

    // Filters
    let filters = {};

    const searchInput = document.getElementById('search-input');
    const typeFilter = document.getElementById('type-filter');
    const statusFilter = document.getElementById('status-filter');
    const dateFromFilter = document.getElementById('date-from-filter');
    const dateToFilter = document.getElementById('date-to-filter');

    function applyFilters() {
        filters = {
            search: searchInput.value,
            type: typeFilter.value,
            status: statusFilter.value,
            date_from: dateFromFilter.value,
            date_to: dateToFilter.value,
        };
        window.dispatchEvent(new CustomEvent('datatable-filter-change', { detail: filters }));
    }

    // Apply filters on button click
    const applyFiltersBtn = document.getElementById('apply-filters-btn');
    applyFiltersBtn.addEventListener('click', applyFilters);

    // Also apply on Enter key in search input
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });

    // Mount DataTable
    window.mountComponent('.js-payments-table', 'AjaxDataTable', {
        columns,
        dataUrl: tableContainer.dataset.url,
        filters,
    });
});

// Load statistics
async function loadStats() {
    try {
        const response = await fetch('/admin/payments/stats');
        const stats = await response.json();

        const statsContainer = document.getElementById('stats-cards');
        statsContainer.innerHTML = `
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Total Deposits</p>
                        <p class="text-xl font-bold text-green-600 mt-1">$${parseFloat(stats.deposits.total).toFixed(2)}</p>
                        <p class="text-xs text-gray-500 mt-1">${stats.deposits.count} transactions</p>
                    </div>
                    <i class="fas fa-arrow-down text-green-400 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Total Withdrawals</p>
                        <p class="text-xl font-bold text-orange-600 mt-1">$${parseFloat(stats.withdrawals.total).toFixed(2)}</p>
                        <p class="text-xs text-gray-500 mt-1">${stats.withdrawals.count} transactions</p>
                    </div>
                    <i class="fas fa-arrow-up text-orange-400 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Pending Withdrawals</p>
                        <p class="text-xl font-bold text-yellow-600 mt-1">${stats.withdrawals.pending}</p>
                        <p class="text-xs text-gray-500 mt-1">Awaiting approval</p>
                    </div>
                    <i class="fas fa-clock text-yellow-400 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Confirmed</p>
                        <p class="text-xl font-bold text-blue-600 mt-1">${stats.withdrawals.confirmed}</p>
                        <p class="text-xs text-gray-500 mt-1">Approved by admin</p>
                    </div>
                    <i class="fas fa-check text-blue-400 text-2xl"></i>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Failed to load stats:', error);
    }
}

// Open modals (triggered from DataTable actions)
window.openApproveModal = function(paymentId) {
    window.dispatchEvent(new CustomEvent('open-approve-modal', {
        detail: { paymentId }
    }));
};

window.openRejectModal = function(paymentId) {
    window.dispatchEvent(new CustomEvent('open-reject-modal', {
        detail: { paymentId }
    }));
};

window.openCompleteModal = function(paymentId) {
    window.dispatchEvent(new CustomEvent('open-complete-modal', {
        detail: { paymentId }
    }));
};
</script>
@endpush
