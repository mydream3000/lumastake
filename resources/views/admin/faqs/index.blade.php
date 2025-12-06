@extends('layouts.admin')

@section('title', 'FAQ Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">FAQ Management</h1>
            <p class="text-gray-600 mt-1">Управление вопросами и ответами</p>
        </div>
        <a href="{{ route('admin.faqs.create') }}"
           class="inline-flex items-center px-4 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Add New FAQ
        </a>
    </div>

    <!-- FAQs Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="js-faqs-table" data-url="{{ route('admin.faqs.index') }}"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableContainer = document.querySelector('.js-faqs-table');
    if (!tableContainer) {
        console.error('FAQs table container not found');
        return;
    }

    const columns = [
        {
            key: 'id',
            label: 'ID',
            sortable: true,
            width: '80px',
            render: (value) => `<span class="text-sm text-gray-600">#${value}</span>`
        },
        {
            key: 'question',
            label: 'Question',
            sortable: true,
            render: (value) => `<span class="text-sm font-medium text-gray-900">${value}</span>`
        },
        {
            key: 'answer',
            label: 'Answer',
            sortable: false,
            render: (value) => `<span class="text-sm text-gray-600">${value}</span>`
        },
        {
            key: 'is_active',
            label: 'Status',
            sortable: true,
            width: '120px',
            render: (value) => value === 'Active'
                ? '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>'
                : '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Inactive</span>'
        },
        {
            key: 'order',
            label: 'Order',
            sortable: true,
            width: '100px',
            render: (value) => `<span class="text-sm text-gray-600">${value}</span>`
        },
        {
            key: 'created_at',
            label: 'Created',
            sortable: true,
            width: '140px',
            render: (value) => `<span class="text-sm text-gray-500">${value}</span>`
        },
        {
            key: 'actions',
            label: 'Actions',
            sortable: false,
            width: '150px',
            render: (value, row) => `
                <div class="flex gap-3">
                    <a href="/admin/faqs/${row.id}/edit" class="text-blue-600 hover:text-blue-800" title="Edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" onclick="deleteFaq(${row.id})" class="text-red-600 hover:text-red-800" title="Delete">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            `
        }
    ];

    if (typeof window.mountComponent === 'function') {
        window.mountComponent('.js-faqs-table', 'AjaxDataTable', {
            columns,
            dataUrl: tableContainer.dataset.url
        });
    } else {
        console.error('mountComponent is not available!');
    }
});

window.deleteFaq = async function(faqId) {
    if (!confirm('Are you sure you want to delete this FAQ?')) {
        return;
    }

    try {
        const response = await fetch(`/admin/faqs/${faqId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        if (response.ok) {
            window.showToast('FAQ deleted successfully', 'success');
            window.dispatchEvent(new Event('datatable-refresh'));
        } else {
            window.showToast('Error deleting FAQ', 'error');
        }
    } catch (error) {
        window.showToast('Error occurred', 'error');
        console.error(error);
    }
};
</script>
@endpush

