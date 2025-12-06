@extends('layouts.admin')

@section('title', 'Email Templates Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Email Templates</h1>
            <p class="text-gray-600 mt-1">Управление шаблонами email уведомлений</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.email-templates.create') }}"
               class="inline-flex items-center px-4 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                New Template
            </a>
            <a href="{{ route('admin.email-templates.settings') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition-colors">
                <i class="fas fa-cog mr-2"></i>
                Email Settings
            </a>
        </div>
    </div>

    <!-- Templates Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="js-templates-table" data-url="{{ route('admin.email-templates.index') }}"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableContainer = document.querySelector('.js-templates-table');
    if (!tableContainer) {
        console.error('Templates table container not found');
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
            key: 'name',
            label: 'Template Name',
            sortable: true,
            render: (value, row) => `
                <div>
                    <span class="text-sm font-medium text-gray-900">${value}</span>
                    <br>
                    <span class="text-xs text-gray-500">${row.key}</span>
                </div>
            `
        },
        {
            key: 'subject',
            label: 'Subject',
            sortable: true,
            render: (value) => `<span class="text-sm text-gray-700">${value}</span>`
        },
        {
            key: 'enabled',
            label: 'Status',
            sortable: true,
            width: '120px',
            render: (value) => value === 'Active'
                ? '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>'
                : '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Inactive</span>'
        },
        {
            key: 'updated_at',
            label: 'Last Updated',
            sortable: true,
            width: '140px',
            render: (value) => `<span class="text-sm text-gray-500">${value}</span>`
        },
        {
            key: 'actions',
            label: 'Actions',
            sortable: false,
            width: '220px',
            render: (value, row) => `
                <div class="flex gap-3">
                    <a href="/admin/email-templates/${row.id}/edit" class="text-blue-600 hover:text-blue-800" title="Edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="/admin/email-templates/create?from=${row.id}" class="text-cabinet-orange hover:text-orange-700" title="Duplicate">
                        <i class="fas fa-clone"></i> Duplicate
                    </a>
                </div>
            `
        }
    ];

    if (typeof window.mountComponent === 'function') {
        window.mountComponent('.js-templates-table', 'AjaxDataTable', {
            columns,
            dataUrl: tableContainer.dataset.url
        });
    } else {
        console.error('mountComponent is not available!');
    }
});
</script>
@endpush
