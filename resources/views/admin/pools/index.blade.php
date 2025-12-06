@extends('layouts.admin')

@section('title', 'Investment Pools Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Investment Pools</h1>
            <p class="text-gray-600 mt-1">Manage staking pools and percentages for all tiers</p>
        </div>
        <a href="{{ route('admin.pools.percentages') }}"
           class="inline-flex items-center px-4 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90 transition-colors">
            <i class="fas fa-percent mr-2"></i>
            Edit Percentages
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Duration Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                <select id="duration-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-cabinet-orange focus:ring focus:ring-cabinet-orange focus:ring-opacity-50">
                    <option value="">All Durations</option>
                    @foreach($durations as $duration)
                        <option value="{{ $duration }}">{{ $duration }} Days</option>
                    @endforeach
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="search-input" placeholder="Search pools..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-cabinet-orange focus:ring focus:ring-cabinet-orange focus:ring-opacity-50">
            </div>
        </div>
    </div>

    <!-- Pools Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="js-pools-table" data-url="{{ route('admin.pools.index') }}"></div>
    </div>
</div>

<!-- Edit Pool Modal -->
<div id="edit-pool-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Pool</h3>
        <form id="edit-pool-form">
            <input type="hidden" name="pool_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration (days)</label>
                    <input type="number" name="days" required min="1"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-cabinet-orange focus:ring focus:ring-cabinet-orange focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Min Stake ($)</label>
                    <input type="number" name="min_stake" required min="0" step="0.01"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-cabinet-orange focus:ring focus:ring-cabinet-orange focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Percentage (%)</label>
                    <input type="number" name="percentage" required min="0" max="200" step="0.01"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-cabinet-orange focus:ring focus:ring-cabinet-orange focus:ring-opacity-50">
                </div>
            </div>
            <div class="flex items-center gap-3 mt-6">
                <button type="submit"
                        class="flex-1 bg-cabinet-orange text-white px-4 py-2 rounded-md hover:bg-cabinet-orange/90 transition-colors">
                    Save Changes
                </button>
                <button type="button" onclick="closeEditPoolModal()"
                        class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableContainer = document.querySelector('.js-pools-table');
    if (!tableContainer) {
        console.error('Pools table container not found');
        return;
    }

    console.log('Pools table container found, data URL:', tableContainer.dataset.url);

    // Define columns
    const columns = [
            { key: 'id', label: 'ID', sortable: true },
            {
                key: 'tier',
                label: 'Tier',
                sortable: true,
                render: (value, row) => {
                    const name = row && row.tier && row.tier.name ? row.tier.name : '-';
                    return `<span class="font-semibold text-sm">${name}</span>`
                }
            },
            {
                key: 'days',
                label: 'Duration',
                sortable: true,
                render: (value, row) => `<span class="text-sm">${row.days} days</span>`
            },
            {
                key: 'percentage',
                label: 'APR',
                sortable: true,
                render: (value, row) => `<span class="font-semibold text-cabinet-green">${row.percentage}%</span>`
            },
            {
                key: 'min_stake',
                label: 'Min Stake',
                sortable: true,
                render: (value, row) => `<span class="text-sm">$${parseFloat(row.min_stake).toFixed(2)}</span>`
            },
            {
                key: 'actions',
                label: 'Actions',
                render: (value, row) => `
                    <div class="flex items-center gap-2">
                        <button type="button" data-action="edit-pool" data-id="${row.id}" class="text-blue-600 hover:text-blue-800" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                `
            },
    ];

    // Define filters
    const filters = {
        duration: '',
        search: '',
    };

    // Mount DataTable
    if (typeof window.mountComponent === 'function') {
        console.log('mountComponent is available for pools, calling it...');
        window.poolsTable = window.mountComponent('.js-pools-table', 'AjaxDataTable', {
            columns,
            dataUrl: tableContainer.dataset.url,
            filters,
        });
        console.log('Pools table mounted:', window.poolsTable);
    } else {
        console.error('mountComponent is not available for pools!');
    }

    // Делегирование кликов по кнопке Edit
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action="edit-pool"]');
        if (btn) {
            const id = parseInt(btn.getAttribute('data-id'));
            if (!isNaN(id)) {
                window.editPool(id);
            }
        }
    });

    // Filter listeners
    const durationFilter = document.getElementById('duration-filter');
    const searchInput = document.getElementById('search-input');

    function updateFilters() {
        const filters = {
            duration: durationFilter.value,
            search: searchInput.value,
        };
        window.dispatchEvent(new CustomEvent('datatable-filter-change', { detail: filters }));
    }

    durationFilter.addEventListener('change', updateFilters);

    let searchTimeout;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(updateFilters, 500);
    });

    // Edit Pool Form
    document.getElementById('edit-pool-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const poolId = formData.get('pool_id');

        try {
            const response = await fetch(`/admin/pools/${poolId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData)),
            });

            if (!response.ok) {
                // Handle validation errors (422)
                if (response.status === 422) {
                    const err = await response.json();
                    const messages = err?.errors ? Object.values(err.errors).flat().join(' ') : (err.message || 'Validation error');
                    showToast(messages, 'error');
                    return;
                }
                // Other errors
                showToast('Error occurred', 'error');
                return;
            }

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                window.closeEditPoolModal();
                window.dispatchEvent(new Event('datatable-refresh'));
            } else {
                showToast(data.message || 'Failed to update pool', 'error');
            }
        } catch (error) {
            showToast('Error updating pool', 'error');
            console.error(error);
        }
    });
});

window.closeEditPoolModal = function() {
    document.getElementById('edit-pool-modal').classList.add('hidden');
}

window.editPool = async function(poolId) {
    try {
        const response = await fetch(`{{ route('admin.pools.index') }}?per_page=9999`, {
            headers: {
                'Accept': 'application/json',
            }
        });
        const result = await response.json();
        const pool = result.data.find(p => p.id === poolId);

        if (pool) {
            const form = document.getElementById('edit-pool-form');
            form.querySelector('[name="pool_id"]').value = pool.id;
            form.querySelector('[name="days"]').value = pool.days;
            form.querySelector('[name="min_stake"]').value = pool.min_stake;
            form.querySelector('[name="percentage"]').value = pool.percentage;

            document.getElementById('edit-pool-modal').classList.remove('hidden');
        }
    } catch (error) {
        showToast('Error loading pool data', 'error');
        console.error(error);
    }
}

async function togglePoolStatus(poolId) {
    if (!confirm('Are you sure you want to change the status of this pool?')) {
        return;
    }

    try {
        const response = await fetch(`/admin/pools/${poolId}/toggle-active`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (data.success) {
            showToast(data.message, 'success');
            window.dispatchEvent(new Event('datatable-refresh'));
        } else {
            showToast(data.message || 'Failed to update status', 'error');
        }
    } catch (error) {
        showToast('Error updating status', 'error');
        console.error(error);
    }
}

async function deletePool(poolId) {
    if (!confirm('Are you sure you want to delete this pool? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`/admin/pools/${poolId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (data.success) {
            showToast(data.message, 'success');
            window.dispatchEvent(new Event('datatable-refresh'));
        } else {
            showToast(data.message || 'Failed to delete pool', 'error');
        }
    } catch (error) {
        showToast('Error deleting pool', 'error');
        console.error(error);
    }
}
</script>
@endpush
@endsection
