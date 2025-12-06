@extends('layouts.admin')

@section('title', 'Tiers Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Tiers Management</h1>
            <p class="text-gray-600 mt-1">Управление уровнями и процентами вознаграждений</p>
        </div>
        <button type="button" onclick="openBulkEditModal()"
           class="inline-flex items-center px-4 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90 transition-colors">
            <i class="fas fa-edit mr-2"></i>
            Edit All Tiers
        </button>
    </div>

    <!-- Tiers Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="js-tiers-table" data-url="{{ route('admin.tiers.index') }}"></div>
    </div>
</div>

<!-- Edit Tier Modal -->
<div id="edit-tier-modal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     x-data="{ open: false, tier: null }"
     x-show="open"
     x-cloak
     @open-edit-tier-modal.window="open = true; tier = $event.detail.tier"
     @close-edit-tier-modal.window="open = false">
    <div class="bg-white rounded-lg max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto" @click.outside="open = false">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Tier</h3>

        <form @submit.prevent="updateTier()">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                    <input type="text" x-model="tier.level" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" x-model="tier.name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Balance ($)</label>
                    <input type="number" x-model="tier.min_balance" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Balance ($)</label>
                    <input type="number" x-model="tier.max_balance" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange">
                </div>

            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-cabinet-orange text-white px-4 py-2 rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                    Update Tier
                </button>
                <button type="button" @click="open = false" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Edit All Tiers Modal -->
<div id="bulk-edit-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-4xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit All Tiers</h3>

        <form id="bulk-edit-form">
            <div class="space-y-4">
                <div id="tiers-bulk-container"></div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-cabinet-orange text-white px-4 py-2 rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                    Save All Changes
                </button>
                <button type="button" onclick="closeBulkEditModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
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
    // Initialize DataTable
    const tableContainer = document.querySelector('.js-tiers-table');
    if (!tableContainer) {
        console.error('Tiers table container not found');
        return;
    }

    console.log('Tiers table container found, mounting component...');

    const columns = [
        {
            key: 'level',
            label: 'Level',
            sortable: true,
            width: '100px',
            render: (value, row) => `<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">Level ${value}</span>`
        },
        {
            key: 'name',
            label: 'Name',
            sortable: true,
            render: (value, row) => `<span class="text-sm font-medium text-gray-900">${value}</span>`
        },
        {
            key: 'min_balance',
            label: 'Min Balance',
            sortable: true,
            render: (value, row) => `<span class="text-sm text-gray-600">$${parseFloat(value).toFixed(2)}</span>`
        },
        {
            key: 'max_balance',
            label: 'Max Balance',
            sortable: true,
            render: (value, row) => value ? `<span class="text-sm text-gray-600">$${parseFloat(value).toFixed(2)}</span>` : '<span class="text-sm text-gray-400">No limit</span>'
        },
        {
            key: 'actions',
            label: 'Actions',
            sortable: false,
            render: (value, row) => `
                <div class="flex gap-3">
                    <button type="button" data-action="edit-tier" data-id="${row.id}" class="text-blue-600 hover:text-blue-800" title="Edit">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </div>
            `
        }
    ];

    // Mount DataTable
    if (typeof window.mountComponent === 'function') {
        console.log('mountComponent is available, calling it...');
        window.mountComponent('.js-tiers-table', 'AjaxDataTable', {
            columns,
            dataUrl: tableContainer.dataset.url
        });
    } else {
        console.error('mountComponent is not available!');
    }

    // Делегирование кликов по кнопке Edit (на случай, если inline-обработчики блокируются CSP)
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action="edit-tier"]');
        if (btn) {
            const id = parseInt(btn.getAttribute('data-id'));
            if (!isNaN(id)) {
                window.openEditModal(id);
            }
        }
    });
});

window.openEditModal = async function(tierId) {
    try {
        const response = await fetch(`{{ route('admin.tiers.index') }}?per_page=9999`, {
            headers: {
                'Accept': 'application/json',
            }
        });
        const result = await response.json();
        const tier = result.data.find(t => t.id === tierId);

        if (tier) {
            window.dispatchEvent(new CustomEvent('open-edit-tier-modal', {
                detail: { tier }
            }));
        } else {
            window.showToast('Tier not found', 'error');
        }
    } catch (error) {
        console.error('Error loading tier:', error);
        window.showToast('Error loading tier', 'error');
    }
}

window.updateTier = async function() {
    const modal = document.querySelector('#edit-tier-modal');
    const tier = modal.__x.$data.tier;

    try {
        const response = await fetch(`/admin/tiers/${tier.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                name: tier.name,
                min_balance: tier.min_balance,
                max_balance: tier.max_balance,
            }),
        });

        if (!response.ok) {
            // Handle validation errors (422)
            if (response.status === 422) {
                const err = await response.json();
                const messages = err?.errors ? Object.values(err.errors).flat().join(' ') : (err.message || 'Validation error');
                window.showToast(messages, 'error');
                return;
            }
            // Other errors
            window.showToast('Error occurred', 'error');
            return;
        }

        const data = await response.json();

        if (data.success) {
            window.showToast(data.message, 'success');
            modal.__x.$data.open = false;
            window.dispatchEvent(new Event('datatable-refresh'));
        } else {
            window.showToast(data.message || 'Error occurred', 'error');
        }
    } catch (error) {
        window.showToast('Error occurred', 'error');
    }
};

// Bulk Edit Functions
window.openBulkEditModal = async function() {
    try {
        const response = await fetch(`{{ route('admin.tiers.index') }}?per_page=9999`, {
            headers: {
                'Accept': 'application/json',
            }
        });
        const result = await response.json();
        const tiers = result.data;

        const container = document.getElementById('tiers-bulk-container');
        container.innerHTML = tiers.map(tier => `
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-3">Level ${tier.level} - ${tier.name}</h4>
                <input type="hidden" name="tiers[${tier.id}][id]" value="${tier.id}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="tiers[${tier.id}][name]" value="${tier.name}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min Balance ($)</label>
                        <input type="number" name="tiers[${tier.id}][min_balance]" value="${tier.min_balance}" required step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Balance ($)</label>
                        <input type="number" name="tiers[${tier.id}][max_balance]" value="${tier.max_balance || ''}" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                               placeholder="No limit">
                    </div>
                </div>
            </div>
        `).join('');

        document.getElementById('bulk-edit-modal').classList.remove('hidden');
    } catch (error) {
        window.showToast('Error loading tiers', 'error');
        console.error(error);
    }
};

window.closeBulkEditModal = function() {
    document.getElementById('bulk-edit-modal').classList.add('hidden');
};

document.getElementById('bulk-edit-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const tiersData = {};

    for (let [key, value] of formData.entries()) {
        const match = key.match(/tiers\[(\d+)\]\[(\w+)\]/);
        if (match) {
            const [, tierId, field] = match;
            if (!tiersData[tierId]) tiersData[tierId] = {};
            tiersData[tierId][field] = value;
        }
    }

    try {
        const updates = Object.values(tiersData).map(async (tier) => {
            const response = await fetch(`/admin/tiers/${tier.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    name: tier.name,
                    min_balance: tier.min_balance,
                    max_balance: tier.max_balance || null,
                }),
            });
            return response.json();
        });

        await Promise.all(updates);

        window.showToast('All tiers updated successfully', 'success');
        window.closeBulkEditModal();
        window.dispatchEvent(new Event('datatable-refresh'));
    } catch (error) {
        window.showToast('Error updating tiers', 'error');
        console.error(error);
    }
});
</script>
@endpush
