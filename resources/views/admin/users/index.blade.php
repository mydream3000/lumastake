@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Users Management</h1>
            <p class="text-gray-600 mt-1">Управление пользователями платформы</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-9 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search-input" placeholder="Name, email, ID..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="blocked">Blocked</option>
                </select>
            </div>

            <!-- Role Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="role-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
                    <option value="">All</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>

            <!-- Account Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Account Type</label>
                <select id="account-type-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
                    <option value="">All</option>
                    <option value="normal">Normal</option>
                    <option value="islamic">Islamic</option>
                </select>
            </div>

            <!-- Tier Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tier</label>
                <select id="tier-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
                    <option value="">All</option>
                    @for($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}">Tier {{ $i }}</option>
                    @endfor
                </select>
            </div>

            <!-- Verification Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Verification</label>
                <select id="verification-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
                    <option value="">All</option>
                    <option value="verified">Verified</option>
                    <option value="pending">Pending</option>
                    <option value="unverified">Unverified</option>
                </select>
            </div>

            <!-- Referral Level Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Referral Level</label>
                <select id="ref-level-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
                    <option value="">All</option>
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <!-- Favorite Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Favorite</label>
                <select id="favorite-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
                    <option value="">All</option>
                    <option value="1">Favorites Only</option>
                </select>
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

    <!-- Bulk Actions Toolbar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" id="select-all" class="rounded border-gray-300">
                <span>Select all results (by current filters)</span>
            </label>
            <span class="text-gray-300">|</span>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" id="select-page" class="rounded border-gray-300">
                <span>Select current page</span>
            </label>
            <button id="clear-selection" class="text-sm text-gray-500 hover:text-gray-700 underline hidden">Clear selection</button>
            <span id="selected-count" class="text-sm text-gray-500">Selected: 0</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button id="btn-mass-credit" class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-sm hover:bg-emerald-700">
                <i class="fas fa-plus-circle mr-1"></i> Mass Credit
            </button>
            <button id="btn-mass-block" class="px-3 py-2 rounded-lg bg-red-600 text-white text-sm hover:bg-red-700">
                <i class="fas fa-ban mr-1"></i> Block Account
            </button>
            <button id="btn-mass-unblock" class="px-3 py-2 rounded-lg bg-green-600 text-white text-sm hover:bg-green-700">
                <i class="fas fa-check-circle mr-1"></i> Unblock Account
            </button>
            <button id="btn-mass-block-withdrawal" class="px-3 py-2 rounded-lg bg-orange-600 text-white text-sm hover:bg-orange-700">
                <i class="fas fa-ban mr-1"></i> Block Withdrawal
            </button>
            <button id="btn-mass-unblock-withdrawal" class="px-3 py-2 rounded-lg bg-teal-600 text-white text-sm hover:bg-teal-700">
                <i class="fas fa-check-circle mr-1"></i> Unblock Withdrawal
            </button>
            <button id="btn-mass-email" class="px-3 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700">
                <i class="fas fa-envelope mr-1"></i> Send Email
            </button>
            @if(Auth::user()->is_super_admin)
            <button id="btn-mass-delete" class="px-3 py-2 rounded-lg bg-red-800 text-white text-sm hover:bg-red-900">
                <i class="fas fa-trash mr-1"></i> Delete Users
            </button>
            @endif
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="js-users-table" data-url="{{ route('admin.users.index') }}"></div>
    </div>

    <!-- Mass Credit Modal -->
    <div id="mass-credit-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mass Credit</h3>
            <form id="mass-credit-form">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount ($)</label>
                        <input type="number" step="0.01" min="0.01" name="amount" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                        <textarea name="comment" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"></textarea>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="email" class="rounded border-gray-300" checked>
                        <span>Send email notification</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_real" class="rounded border-gray-300">
                        <span class="font-bold text-red-600 italic">Real money (visible in deposit history)</span>
                    </label>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="flex-1 bg-cabinet-orange text-white px-4 py-2 rounded-lg hover:bg-cabinet-orange/90 transition-colors">Apply</button>
                    <button type="button" id="mass-credit-cancel" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Mass Email Modal -->
    <div id="mass-email-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Send Email to Selected Users</h3>
            <form id="mass-email-form">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Template</label>
                        <select name="template_key" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange">
                            <option value="" disabled selected>Choose template</option>
                            @foreach(($templates ?? []) as $tpl)
                                <option value="{{ $tpl->key }}">{{ $tpl->name }} ({{ $tpl->key }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="flex-1 bg-cabinet-orange text-white px-4 py-2 rounded-lg hover:bg-cabinet-orange/90 transition-colors">Send</button>
                    <button type="button" id="mass-email-cancel" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Balance Adjustment Modal -->
<div id="balance-modal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     x-data="{
         open: false,
         userId: null,
         userName: '',
         type: 'add',
         amount: 0,
         reason: '',
         isReal: false,

         async adjustBalance() {
             try {
                 const response = await fetch(`/admin/users/${this.userId}/adjust-balance`, {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                     },
                     body: JSON.stringify({
                         type: this.type,
                         amount: parseFloat(this.amount),
                         reason: this.reason,
                         is_real: this.isReal
                     })
                 });

                 const data = await response.json();

                 if (data.success) {
                     window.showToast(data.message, 'success');
                     this.open = false;
                     this.amount = 0;
                     this.reason = '';
                     window.dispatchEvent(new Event('datatable-refresh'));
                 } else {
                     window.showToast(data.message || 'Error occurred', 'error');
                 }
             } catch (error) {
                 window.showToast('Error occurred', 'error');
             }
         }
     }"
     x-show="open"
     x-cloak
     @open-balance-modal.window="open = true; userId = $event.detail.userId; userName = $event.detail.userName; isReal = false"
     @close-balance-modal.window="open = false">
    <div class="bg-white rounded-lg max-w-md w-full p-6" @click.outside="open = false">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Adjust Balance</h3>
        <p class="text-sm text-gray-600 mb-4">User: <span class="font-medium" x-text="userName"></span></p>

        <form @submit.prevent="adjustBalance()">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select x-model="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange">
                        <option value="add">Add</option>
                        <option value="subtract">Subtract</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount ($)</label>
                    <input type="number" x-model="amount" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                    <textarea x-model="reason" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"></textarea>
                </div>

                <div x-show="type === 'add'">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" x-model="isReal" class="rounded border-gray-300">
                        <span class="font-bold text-red-600 italic">Real money (visible in deposit history)</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-cabinet-orange text-white px-4 py-2 rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                    Adjust Balance
                </button>
                <button type="button" @click="open = false" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Super admin flag from server
const isSuperAdmin = {{ Auth::user()->is_super_admin ? 'true' : 'false' }};

document.addEventListener('DOMContentLoaded', function() {
    // Export buttons
    const exportXlsxBtn = document.getElementById('export-xlsx-btn');
    const exportXlsBtn = document.getElementById('export-xls-btn');

    function getExportUrl(format) {
        const baseUrl = "{{ route('admin.users.export') }}";
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
    // Initialize DataTable
    const tableContainer = document.querySelector('.js-users-table');
    if (!tableContainer) return;

    // Selection state
    let selectedIds = new Set();
    let allSelected = false; // when true, apply to ALL results by current filters
    let currentPageIds = [];

    const selectAllCheckbox = document.getElementById('select-all');
    const selectPageCheckbox = document.getElementById('select-page');
    const clearSelectionBtn = document.getElementById('clear-selection');
    const selectedCountEl = document.getElementById('selected-count');

    function updateSelectedCount() {
        if (allSelected) {
            selectedCountEl.textContent = 'Selected: All filtered results';
            clearSelectionBtn.classList.remove('hidden');
        } else {
            selectedCountEl.textContent = `Selected: ${selectedIds.size}`;
            if (selectedIds.size > 0) {
                clearSelectionBtn.classList.remove('hidden');
            } else {
                clearSelectionBtn.classList.add('hidden');
            }
        }
        // Manage select-page checkbox state
        const selectPageCheckbox = document.getElementById('select-page');
        if (selectPageCheckbox) {
            selectPageCheckbox.disabled = allSelected;
            if (allSelected) {
                selectPageCheckbox.checked = false;
            }
        }
    }

    function clearSelection() {
        allSelected = false;
        selectedIds.clear();
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
        const selectPageCb = document.getElementById('select-page');
        if (selectPageCb) {
            selectPageCb.checked = false;
            selectPageCb.indeterminate = false;
        }
        document.querySelectorAll('.row-select').forEach(cb => cb.checked = false);
        updateSelectedCount();
        updateSelectPageState();
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', (e) => {
            allSelected = e.target.checked;
            if (allSelected) {
                selectedIds.clear(); // ignore per-row when selecting all
                document.querySelectorAll('.row-select').forEach(cb => cb.checked = false);
                if (selectPageCheckbox) {
                    selectPageCheckbox.checked = false;
                    selectPageCheckbox.indeterminate = false;
                }
            }
            updateSelectedCount();
            updateSelectPageState();
        });
    }

    // Select current page toggle
    if (selectPageCheckbox) {
        selectPageCheckbox.addEventListener('change', (e) => {
            if (allSelected) {
                // not applicable when all selected
                e.target.checked = false;
                e.target.indeterminate = false;
                return;
            }
            const check = e.target.checked;
            if (check) {
                currentPageIds.forEach(id => selectedIds.add(Number(id)));
            } else {
                currentPageIds.forEach(id => selectedIds.delete(Number(id)));
            }
            // Update row checkboxes for current page
            document.querySelectorAll('.row-select').forEach(cb => {
                const id = Number(cb.getAttribute('data-id'));
                if (currentPageIds.includes(id)) {
                    cb.checked = check;
                }
            });
            updateSelectedCount();
            updateSelectPageState();
        });
    }

    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', (e) => {
            e.preventDefault();
            clearSelection();
        });
    }

    // Filters state
    let filters = {};

    // Update tri-state of "Select current page" based on selectedIds and currentPageIds
    function updateSelectPageState() {
        const cb = document.getElementById('select-page');
        if (!cb) return;
        if (allSelected) {
            cb.checked = false;
            cb.indeterminate = false;
            cb.disabled = true;
            return;
        }
        cb.disabled = false;
        const totalOnPage = currentPageIds.length;
        if (totalOnPage === 0) {
            cb.checked = false;
            cb.indeterminate = false;
            return;
        }
        let count = 0;
        for (const id of currentPageIds) {
            if (selectedIds.has(Number(id))) count++;
        }
        if (count === 0) {
            cb.checked = false;
            cb.indeterminate = false;
        } else if (count === totalOnPage) {
            cb.checked = true;
            cb.indeterminate = false;
        } else {
            cb.checked = false;
            cb.indeterminate = true;
        }
    }

    // Sync row checkboxes on current page with selectedIds set
    function syncRowCheckboxes() {
        document.querySelectorAll('.row-select').forEach(cb => {
            const id = Number(cb.getAttribute('data-id'));
            cb.checked = selectedIds.has(id);
        });
    }

    const columns = [
        {
            key: 'select',
            label: 'select',
            sortable: false,
            width: '40px',
            render: (value, row) => `<input type="checkbox" class="row-select rounded border-gray-300" data-id="${row.id}">`
        },
        {
            key: 'favorite',
            label: '⭐',
            sortable: false,
            width: '40px',
            render: (value, row) => {
                const starColor = row.is_favorite ? 'text-yellow-500' : 'text-gray-300';
                return `<button type="button" data-action="toggle-favorite" data-id="${row.id}" class="${starColor} hover:text-yellow-400 text-xl transition-colors">
                    <i class="fas fa-star"></i>
                </button>`;
            }
        },
        {
            key: 'id',
            label: 'ID',
            sortable: true,
            width: '60px'
        },
        {
            key: 'name',
            label: 'Name',
            sortable: true,
            render: (value, row) => `
                <a href="/admin/users/${row.id}" class="group block">
                    <p class="text-xs font-medium text-gray-900 group-hover:text-cabinet-orange transition-colors">${value}</p>
                    <p class="text-[10px] text-gray-500">${row.email}</p>
                </a>
            `
        },
        {
            key: 'country',
            label: 'Cntry',
            sortable: true,
            width: '60px',
            render: (value) => value ? `<span class="text-xs text-gray-600 uppercase">${value}</span>` : '<span class="text-xs text-gray-400">-</span>'
        },
        {
            key: 'account_type',
            label: 'Type',
            sortable: true,
            width: '70px',
            render: (value) => value ? `<span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded-full ${value === 'islamic' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">${value}</span>` : '<span class="text-xs text-gray-400">-</span>'
        },
        {
            key: 'balance',
            label: 'Balance',
            sortable: true,
            render: (value, row) => `
                <p class="text-xs font-semibold text-gray-900">$${parseFloat(value).toFixed(2)}</p>
            `
        },
        {
            key: 'real_deposits',
            label: 'Real Dep.',
            sortable: true,
            render: (value) => `
                <p class="text-xs font-semibold text-gray-900">$${parseFloat(value || 0).toFixed(2)}</p>
            `
        },
        {
            key: 'current_tier',
            label: 'Tier',
            sortable: true,
            width: '70px',
            render: (value) => value ? `<span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-purple-100 text-purple-800">T${value}</span>` : '<span class="text-xs text-gray-400">-</span>'
        },
        {
            key: 'referral_level_id',
            label: 'Ref. Lvl',
            sortable: true,
            width: '60px',
            render: (value) => {
                if (!value) return '<span class="text-xs text-gray-400">-</span>';
                const levelColors = {
                    1: 'bg-cabinet-level-1/20 text-cabinet-level-1',
                    2: 'bg-cabinet-level-2/20 text-cabinet-level-2',
                    3: 'bg-cabinet-level-3/20 text-cabinet-level-3',
                    4: 'bg-cabinet-level-4/20 text-cabinet-level-4',
                    5: 'bg-cabinet-level-5/20 text-cabinet-level-5'
                };
                const colorClass = levelColors[value] || 'bg-gray-100 text-gray-800';
                return `<span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded-full ${colorClass}">${value}</span>`;
            }
        },
        {
            key: 'is_admin',
            label: 'Role',
            sortable: true,
            width: '60px',
            render: (value) => value
                ? '<span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-blue-100 text-blue-800">Adm</span>'
                : '<span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-gray-100 text-gray-800">Usr</span>'
        },
        {
            key: 'verification_status',
            label: 'Verif.',
            sortable: true,
            render: (value) => {
                const map = {
                    'verified': 'bg-green-100 text-green-800',
                    'pending': 'bg-yellow-100 text-yellow-800',
                    'unverified': 'bg-red-100 text-red-800'
                };
                const label = (value || 'unverified');
                const cls = map[label] || 'bg-gray-100 text-gray-800';
                return `<span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded-full ${cls}">${label.substring(0,5)}</span>`;
            }
        },
        {
            key: 'blocked',
            label: 'Status',
            sortable: true,
            width: '70px',
            render: (value, row) => !value
                ? '<span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-800">Active</span>'
                : '<span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-red-100 text-red-800">Block</span>'
        },
        {
            key: 'withdrawal_blocked',
            label: 'W.Draw',
            sortable: true,
            width: '70px',
            render: (value) => {
                const blocked = !!value;
                return blocked
                    ? '<span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-red-100 text-red-800">Block</span>'
                    : '<span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-800">OK</span>'
            }
        },
        {
            key: 'created_at',
            label: 'Reg.',
            sortable: true,
            render: (value) => `<span class="text-xs text-gray-600">${value}</span>`
        },
        {
            key: 'actions',
            label: 'Actions',
            sortable: false,
            render: (value, row) => {
                const escapedName = (row.name || '').replace(/'/g, "\\'");
                const deleteButton = isSuperAdmin
                    ? `<button type="button" data-action="delete-user" data-id="${row.id}" data-name="${escapedName}" data-active-stakes="${row.active_stakes_count || 0}"
                            class="text-red-600 hover:text-red-800" title="Delete User">
                        <i class="fas fa-trash"></i>
                    </button>`
                    : '';
                return `
                    <div class="flex gap-2">
                        <a href="/admin/users/${row.id}" class="text-blue-600 hover:text-blue-800" title="View Profile">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button type="button" data-action="edit-user" data-id="${row.id}"
                                class="text-purple-600 hover:text-purple-800" title="Edit User">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" data-action="adjust-balance" data-id="${row.id}" data-name="${escapedName}"
                                class="text-yellow-600 hover:text-yellow-800" title="Adjust Balance (Add/Subtract funds)">
                            <i class="fas fa-wallet"></i>
                        </button>
                        <button type="button" data-action="login-as" data-id="${row.id}"
                                class="text-indigo-600 hover:text-indigo-800" title="Login As User">
                            <i class="fas fa-sign-in-alt"></i>
                        </button>
                        <button type="button" data-action="toggle-status" data-id="${row.id}" data-blocked="${row.blocked ? '1' : '0'}"
                                class="text-${row.blocked ? 'green' : 'red'}-600 hover:text-${row.blocked ? 'green' : 'red'}-800"
                                title="${row.blocked ? 'Activate User' : 'Block User'}">
                            <i class="fas fa-${row.blocked ? 'check-circle' : 'ban'}"></i>
                        </button>
                        ${deleteButton}
                    </div>
                `;
            }
        },
    ];

    // Get filter elements
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('status-filter');
    const roleFilter = document.getElementById('role-filter');
    const accountTypeFilter = document.getElementById('account-type-filter');
    const tierFilter = document.getElementById('tier-filter');
    const verificationFilter = document.getElementById('verification-filter');
    const refLevelFilter = document.getElementById('ref-level-filter');
    const favoriteFilter = document.getElementById('favorite-filter');

    function applyFilters() {
        // Reset selection when filters change
        clearSelection();
        filters = {
            search: searchInput.value,
            status: statusFilter.value,
            role: roleFilter.value,
            account_type: accountTypeFilter.value,
            tier: tierFilter.value,
            verification: verificationFilter.value,
            ref_level: refLevelFilter.value,
            favorite: favoriteFilter.value,
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
    window.mountComponent('.js-users-table', 'AjaxDataTable', {
        columns,
        dataUrl: tableContainer.dataset.url,
        filters,
    });

    // Listen for current page data from DataTable (visible row IDs)
    window.addEventListener('datatable-page-data', (e) => {
        const detail = e.detail || {};
        currentPageIds = (detail.ids || []).map(Number);

        // When "select all" is active, we keep per-row checkboxes unchecked
        if (allSelected) {
            document.querySelectorAll('.row-select').forEach(cb => cb.checked = false);
        } else {
            syncRowCheckboxes();
        }
        updateSelectedCount();
        updateSelectPageState();
    });

    // Делегирование событий для кнопок действий
    document.addEventListener('click', (e) => {
        // Edit User
        const editBtn = e.target.closest('[data-action="edit-user"]');
        if (editBtn) {
            const userId = parseInt(editBtn.getAttribute('data-id'));
            window.location.href = `/admin/users/${userId}/edit`;
            return;
        }

        // Adjust Balance
        const adjustBtn = e.target.closest('[data-action="adjust-balance"]');
        if (adjustBtn) {
            const userId = parseInt(adjustBtn.getAttribute('data-id'));
            const userName = adjustBtn.getAttribute('data-name');
            window.dispatchEvent(new CustomEvent('open-balance-modal', {
                detail: { userId, userName }
            }));
            return;
        }

        // Login As User
        const loginAsBtn = e.target.closest('[data-action="login-as"]');
        if (loginAsBtn) {
            const userId = parseInt(loginAsBtn.getAttribute('data-id'));
            window.loginAsUser(userId);
            return;
        }

        // Toggle Status
        const toggleBtn = e.target.closest('[data-action="toggle-status"]');
        if (toggleBtn) {
            const userId = parseInt(toggleBtn.getAttribute('data-id'));
            const isBlocked = toggleBtn.getAttribute('data-blocked') === '1';
            window.toggleUserStatus(userId, isBlocked);
            return;
        }

        // Delete User
        const deleteBtn = e.target.closest('[data-action="delete-user"]');
        if (deleteBtn) {
            const userId = parseInt(deleteBtn.getAttribute('data-id'));
            const userName = deleteBtn.getAttribute('data-name');
            const activeStakes = parseInt(deleteBtn.getAttribute('data-active-stakes')) || 0;
            window.deleteUser(userId, userName, activeStakes);
            return;
        }

        // Mass Credit open modal
        const massCreditBtn = e.target.closest('#btn-mass-credit');
        if (massCreditBtn) {
            const modal = document.getElementById('mass-credit-modal');
            modal && modal.classList.remove('hidden');
            modal && modal.classList.add('flex');
            return;
        }

        // Mass Email open modal
        const massEmailBtn = e.target.closest('#btn-mass-email');
        if (massEmailBtn) {
            const modal = document.getElementById('mass-email-modal');
            modal && modal.classList.remove('hidden');
            modal && modal.classList.add('flex');
            return;
        }

        // Mass Block
        const massBlockBtn = e.target.closest('#btn-mass-block');
        if (massBlockBtn) {
            handleBulkAction('/admin/users/bulk/block');
            return;
        }

        // Mass Unblock
        const massUnblockBtn = e.target.closest('#btn-mass-unblock');
        if (massUnblockBtn) {
            handleBulkAction('/admin/users/bulk/unblock');
            return;
        }

        // Mass Block Withdrawal
        const massBlockWithdrawalBtn = e.target.closest('#btn-mass-block-withdrawal');
        if (massBlockWithdrawalBtn) {
            handleBulkAction('/admin/users/bulk/block-withdrawal');
            return;
        }

        // Mass Unblock Withdrawal
        const massUnblockWithdrawalBtn = e.target.closest('#btn-mass-unblock-withdrawal');
        if (massUnblockWithdrawalBtn) {
            handleBulkAction('/admin/users/bulk/unblock-withdrawal');
            return;
        }

        // Mass Delete (Super Admin Only)
        const massDeleteBtn = e.target.closest('#btn-mass-delete');
        if (massDeleteBtn) {
            handleBulkDelete();
            return;
        }

        // Toggle Favorite
        const favoriteBtn = e.target.closest('[data-action="toggle-favorite"]');
        if (favoriteBtn) {
            const userId = parseInt(favoriteBtn.getAttribute('data-id'));
            window.toggleFavorite(userId, favoriteBtn);
            return;
        }
    });

    // Row selection via event delegation
    document.addEventListener('change', (e) => {
        const cb = e.target.closest('.row-select');
        if (!cb) return;
        const id = parseInt(cb.getAttribute('data-id'));
        if (isNaN(id)) return;
        if (allSelected) {
            // switching from ALL to specific selection
            allSelected = false;
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        }
        if (cb.checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }
        updateSelectedCount();
        updateSelectPageState();
    });

    // Close modals
    const massCreditCancel = document.getElementById('mass-credit-cancel');
    if (massCreditCancel) {
        massCreditCancel.addEventListener('click', () => {
            const modal = document.getElementById('mass-credit-modal');
            modal && modal.classList.add('hidden');
            modal && modal.classList.remove('flex');
        });
    }

    const massEmailCancel = document.getElementById('mass-email-cancel');
    if (massEmailCancel) {
        massEmailCancel.addEventListener('click', () => {
            const modal = document.getElementById('mass-email-modal');
            modal && modal.classList.add('hidden');
            modal && modal.classList.remove('flex');
        });
    }

    function getPayload() {
        if (allSelected) {
            return { all: true, ...filters };
        }
        const ids = Array.from(selectedIds);
        if (!ids.length) {
            window.showToast('Select users first or use "Select all results"', 'error');
            return null;
        }
        return { user_ids: ids };
    }

    async function handleBulkAction(url, extra = {}) {
        const payload = getPayload();
        if (!payload) return;
        const body = { ...payload, ...extra, ...filters };
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(body)
            });
            const data = await res.json();
            if (res.ok && data.success) {
                window.showToast(data.message || 'Done', 'success');
                clearSelection();
                window.dispatchEvent(new Event('datatable-refresh'));
            } else {
                window.showToast(data.message || 'Action failed', 'error');
            }
        } catch (e) {
            window.showToast('Network error', 'error');
        }
    }

    async function handleBulkDelete() {
        const payload = getPayload();
        if (!payload) return;

        const confirmMessage = allSelected
            ? 'Are you sure you want to DELETE ALL filtered users?\n\nThis is a DESTRUCTIVE action and CANNOT be undone!\n\nType "DELETE ALL" to confirm:'
            : `Are you sure you want to DELETE ${selectedIds.size} selected user(s)?\n\nThis is a DESTRUCTIVE action and CANNOT be undone!\n\nType "DELETE" to confirm:`;

        const requiredText = allSelected ? 'DELETE ALL' : 'DELETE';
        const userInput = prompt(confirmMessage);

        if (userInput !== requiredText) {
            window.showToast('Deletion cancelled', 'info');
            return;
        }

        const body = { ...payload, ...filters };

        try {
            const res = await fetch('/admin/users/bulk/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(body)
            });
            const data = await res.json();
            if (res.ok && data.success) {
                let message = data.message;
                if (data.errors && data.errors.length > 0) {
                    console.log('Deletion errors:', data.errors);
                }
                window.showToast(message, 'success');
                clearSelection();
                window.dispatchEvent(new Event('datatable-refresh'));
            } else {
                window.showToast(data.message || 'Deletion failed', 'error');
            }
        } catch (e) {
            window.showToast('Network error', 'error');
        }
    }

    // Mass Credit form submit
    const massCreditForm = document.getElementById('mass-credit-form');
    if (massCreditForm) {
        massCreditForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(massCreditForm);
            const amount = parseFloat(formData.get('amount'));
            const comment = (formData.get('comment') || '').toString().trim();
            const email = !!formData.get('email');
            if (!amount || amount <= 0 || !comment) {
                window.showToast('Amount and comment are required', 'error');
                return;
            }
            await handleBulkAction('/admin/users/bulk/adjust-balance', { amount, comment, email });
            const modal = document.getElementById('mass-credit-modal');
            modal && modal.classList.add('hidden');
            modal && modal.classList.remove('flex');
            massCreditForm.reset();
        });
    }

    // Mass Email form submit
    const massEmailForm = document.getElementById('mass-email-form');
    if (massEmailForm) {
        massEmailForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(massEmailForm);
            const template_key = formData.get('template_key');
            if (!template_key) {
                window.showToast('Select email template', 'error');
                return;
            }
            await handleBulkAction('/admin/users/bulk/send-email', { template_key });
            const modal = document.getElementById('mass-email-modal');
            modal && modal.classList.add('hidden');
            modal && modal.classList.remove('flex');
            massEmailForm.reset();
        });
    }
});

// Toggle user status
window.toggleUserStatus = async function(userId, isBlocked) {
    if (!confirm(`Are you sure you want to ${isBlocked ? 'activate' : 'block'} this user?`)) {
        return;
    }

    try {
        const response = await fetch(`/admin/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            },
        });

        const data = await response.json();

        if (data.success) {
            window.showToast(data.message, 'success');
            window.dispatchEvent(new Event('datatable-refresh'));
        } else {
            window.showToast(data.message || 'Error occurred', 'error');
        }
    } catch (error) {
        window.showToast('Error occurred', 'error');
    }
};

// Login as user
window.loginAsUser = async function(userId) {
    if (!confirm('Are you sure you want to login as this user?')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/users/${userId}/login-as`;

    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
    form.appendChild(csrfToken);

    document.body.appendChild(form);
    form.submit();
};

// Delete user
window.deleteUser = async function(userId, userName, activeStakes) {
    const hasActiveStakes = activeStakes > 0;

    let confirmMessage = `Are you sure you want to delete user "${userName}" (ID: ${userId})?\n\n`;

    if (hasActiveStakes) {
        confirmMessage += `⚠️ WARNING: This user has ${activeStakes} active staking deposit(s)!\n\n`;
        confirmMessage += `This action will:\n`;
        confirmMessage += `- Delete ALL user data (transactions, stakes, earnings, etc.)\n`;
        confirmMessage += `- Cancel all active staking deposits\n`;
        confirmMessage += `- Nullify referral links (referrals will remain)\n\n`;
        confirmMessage += `This action CANNOT be undone!\n\n`;
        confirmMessage += `Type "DELETE" to confirm:`;

        const userInput = prompt(confirmMessage);
        if (userInput !== 'DELETE') {
            window.showToast('Deletion cancelled', 'info');
            return;
        }
    } else {
        confirmMessage += `This will permanently delete:\n`;
        confirmMessage += `- All transactions\n`;
        confirmMessage += `- All staking history\n`;
        confirmMessage += `- All earnings\n`;
        confirmMessage += `- All crypto data\n\n`;
        confirmMessage += `Referrals will remain but referral link will be nullified.\n\n`;
        confirmMessage += `This action CANNOT be undone!`;

        if (!confirm(confirmMessage)) {
            return;
        }
    }

    try {
        const url = `/admin/users/${userId}${hasActiveStakes ? '?force=true' : ''}`;
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            },
        });

        const data = await response.json();

        if (data.success) {
            window.showToast(data.message, 'success');

            // Show deletion statistics if available
            if (data.deleted_data) {
                const stats = data.deleted_data;
                const statsMessage = `Deleted:\n` +
                    `- ${stats.transactions} transaction(s)\n` +
                    `- ${stats.stakings} staking deposit(s)\n` +
                    `- ${stats.earnings} earning(s)\n` +
                    `- ${stats.crypto_data} crypto record(s)\n` +
                    `- ${stats.referrals_nullified} referral link(s) nullified`;
                console.log(statsMessage);
            }

            window.dispatchEvent(new Event('datatable-refresh'));
        } else {
            window.showToast(data.message || 'Error occurred', 'error');
        }
    } catch (error) {
        console.error('Delete user error:', error);
        window.showToast('Error occurred while deleting user', 'error');
    }
};

// Toggle favorite
window.toggleFavorite = async function(userId, buttonElement) {
    try {
        const response = await fetch(`/admin/users/${userId}/toggle-favorite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            },
        });

        // Если миграции не применены — сервер вернёт 503. Покажем инфо, без ошибки.
        if (response.status === 503) {
            try {
                const soft = await response.json();
                window.showToast(soft.message || 'Favorites will be available after migrations are applied', 'info');
            } catch (e) {
                window.showToast('Favorites will be available after migrations are applied', 'info');
            }
            return;
        }

        const data = await response.json();

        if (data.success) {
            // Update button appearance
            if (data.is_favorite) {
                buttonElement.classList.remove('text-gray-300');
                buttonElement.classList.add('text-yellow-500');
            } else {
                buttonElement.classList.remove('text-yellow-500');
                buttonElement.classList.add('text-gray-300');
            }
            window.showToast(data.message, 'success');
        } else {
            window.showToast(data.message || 'Error occurred', 'error');
        }
    } catch (error) {
        window.showToast('Error occurred', 'error');
    }
};

</script>
@endpush
