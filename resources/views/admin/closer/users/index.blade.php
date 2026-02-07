@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Users</h1>
        <p class="text-gray-600 mt-1">View and manage user notes</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search-input" placeholder="Name, email, ID..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
            </div>

            <!-- Closer Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="closer-status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange focus:border-transparent">
                    <option value="">All</option>
                    <option value="int">Interested</option>
                    <option value="no-int">Not Interested</option>
                    <option value="re-call">Re-call</option>
                    <option value="fake">Fake</option>
                </select>
            </div>

            <!-- Filter Button -->
            <div class="flex items-end">
                <button id="apply-filters-btn" class="w-full px-4 py-2 bg-cabinet-orange text-white rounded-lg font-medium hover:bg-cabinet-orange/90 transition-colors">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="js-closer-users-table" data-url="{{ route('admin.closer.users.index') }}"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableContainer = document.querySelector('.js-closer-users-table');
    if (!tableContainer) return;

    let filters = {};

    const columns = [
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
                <a href="/admin/closer/users/${row.id}" class="group block">
                    <p class="text-xs font-medium text-gray-900 group-hover:text-cabinet-orange transition-colors">${value}</p>
                    <p class="text-[10px] text-gray-500">${row.email}</p>
                </a>
            `
        },
        {
            key: 'phone',
            label: 'Phone',
            sortable: false,
            render: (value) => value ? `<span class="text-xs text-gray-700">${value}</span>` : '<span class="text-xs text-gray-400">-</span>'
        },
        {
            key: 'account_type',
            label: 'Type',
            sortable: true,
            width: '80px',
            render: (value) => value ? `<span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded-full ${value === 'islamic' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">${value}</span>` : '<span class="text-xs text-gray-400">-</span>'
        },
        {
            key: 'country',
            label: 'Country',
            sortable: false,
            width: '70px',
            render: (value) => value ? `<span class="text-xs text-gray-600 uppercase">${value}</span>` : '<span class="text-xs text-gray-400">-</span>'
        },
        {
            key: 'closer_comment',
            label: 'Comment',
            sortable: false,
            render: (value, row) => {
                const escaped = (value || '').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                return `<textarea class="closer-comment w-full text-xs border border-gray-200 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-cabinet-orange resize-none"
                            rows="2" data-user-id="${row.id}" placeholder="Add comment...">${escaped}</textarea>`;
            }
        },
        {
            key: 'closer_status',
            label: 'Status',
            sortable: false,
            width: '120px',
            render: (value, row) => {
                const options = [
                    { value: '', label: '-- Select --' },
                    { value: 'int', label: 'Interested' },
                    { value: 'no-int', label: 'Not Interested' },
                    { value: 're-call', label: 'Re-call' },
                    { value: 'fake', label: 'Fake' },
                ];
                const optionsHtml = options.map(opt =>
                    `<option value="${opt.value}" ${opt.value === (value || '') ? 'selected' : ''}>${opt.label}</option>`
                ).join('');
                return `<select class="closer-status text-xs border border-gray-200 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-cabinet-orange w-full"
                            data-user-id="${row.id}">${optionsHtml}</select>`;
            }
        },
        {
            key: 'closer_name',
            label: 'Last By',
            sortable: false,
            width: '100px',
            render: (value, row) => {
                if (!value) return '<span class="text-xs text-gray-400">-</span>';
                return `<div>
                    <p class="text-[10px] font-medium text-gray-700">${value}</p>
                    <p class="text-[9px] text-gray-400">${row.closer_updated_at || ''}</p>
                </div>`;
            }
        },
        {
            key: 'created_at',
            label: 'Reg.',
            sortable: true,
            render: (value) => `<span class="text-xs text-gray-600">${value}</span>`
        },
    ];

    // Search
    const searchInput = document.getElementById('search-input');
    const closerStatusFilter = document.getElementById('closer-status-filter');

    function applyFilters() {
        filters = {
            search: searchInput.value,
            closer_status: closerStatusFilter.value,
        };
        window.dispatchEvent(new CustomEvent('datatable-filter-change', { detail: filters }));
    }

    document.getElementById('apply-filters-btn').addEventListener('click', applyFilters);
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') applyFilters();
    });

    // Mount DataTable
    window.mountComponent('.js-closer-users-table', 'AjaxDataTable', {
        columns,
        dataUrl: tableContainer.dataset.url,
        filters,
    });

    // Auto-save comment on blur
    document.addEventListener('focusout', async (e) => {
        if (!e.target.classList.contains('closer-comment')) return;
        const userId = e.target.dataset.userId;
        const comment = e.target.value;
        // Get current status from the same row
        const row = e.target.closest('tr');
        const statusSelect = row ? row.querySelector('.closer-status') : null;
        const status = statusSelect ? statusSelect.value : '';

        await saveNote(userId, comment, status);
    });

    // Auto-save status on change
    document.addEventListener('change', async (e) => {
        if (!e.target.classList.contains('closer-status')) return;
        const userId = e.target.dataset.userId;
        const status = e.target.value;
        // Get current comment from the same row
        const row = e.target.closest('tr');
        const commentTextarea = row ? row.querySelector('.closer-comment') : null;
        const comment = commentTextarea ? commentTextarea.value : '';

        await saveNote(userId, comment, status);
    });

    async function saveNote(userId, comment, status) {
        try {
            const response = await fetch(`/admin/closer/users/${userId}/note`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ comment, status: status || null }),
            });

            const data = await response.json();
            if (data.success) {
                window.showToast('Note saved', 'success');
            } else {
                window.showToast(data.message || 'Failed to save note', 'error');
            }
        } catch (error) {
            window.showToast('Error saving note', 'error');
        }
    }
});
</script>
@endpush
