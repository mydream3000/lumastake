@extends('layouts.admin')

@section('title', 'Blog Posts')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between relative z-10">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Blog Posts</h1>
            <p class="text-gray-600 mt-1">Manage blog posts and articles</p>
        </div>
        <a href="{{ route('admin.blog.create') }}"
           class="inline-flex items-center px-4 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90 transition-colors cursor-pointer">
            <i class="fas fa-plus mr-2"></i>
            Create Post
        </a>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 relative z-0">
        <div class="js-blog-table" data-url="{{ route('admin.blog.index') }}"></div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableContainer = document.querySelector('.js-blog-table');
    if (!tableContainer) return;

    const columns = [
        {key: 'id', label: 'ID', sortable: true, width: '80px'},
        {key: 'title', label: 'Title', sortable: true},
        {key: 'author', label: 'Author', sortable: true, width: '150px'},
        {key: 'published_at', label: 'Published', sortable: true, width: '150px'},
        {key: 'is_active', label: 'Status', sortable: true, width: '120px'},
        {key: 'views', label: 'Views', sortable: true, width: '100px'},
        {key: 'color_scheme', label: 'Color', sortable: false, width: '100px', render: function(value) {
            return `<div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded border-2 border-gray-300" style="background-color: ${value}"></div>
            </div>`;
        }},
        {key: 'actions', label: 'Actions', sortable: false, width: '150px', render: function(value, row) {
            return `
                <div class="flex gap-2">
                    <a href="/admin/blog/${row.id}/edit" class="text-blue-600 hover:text-blue-800" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="/admin/blog/${row.id}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this post?');">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            `;
        }},
    ];

    window.mountComponent('.js-blog-table', 'AjaxDataTable', {
        columns: columns,
        dataUrl: tableContainer.dataset.url
    });
});
</script>
@endpush
@endsection
