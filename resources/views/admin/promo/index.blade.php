@extends('layouts.admin')
@section('title', 'Admin · Promo Codes')
@section('header', 'Promo Codes')
@section('content')

@if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Header with Create Button -->
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900">All Promo Codes</h2>
        <a href="{{ route('admin.promo.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Create Promo Code
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bonus Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uses</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Per User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($promoCodes as $promo)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono font-semibold text-gray-900">{{ $promo->code }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $promo->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-green-600 font-semibold">${{ number_format($promo->start_balance, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $promo->used_count }} / {{ $promo->max_uses }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $promo->uses_per_user }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($promo->is_active)
                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('admin.promo.edit', $promo) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.promo.destroy', $promo) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this promo code?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        No promo codes found. <a href="{{ route('admin.promo.create') }}" class="text-blue-600 hover:underline">Create one now</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($promoCodes->hasPages())
    <div class="p-6 border-t border-gray-200">
        {{ $promoCodes->links() }}
    </div>
    @endif
</div>
@endsection
