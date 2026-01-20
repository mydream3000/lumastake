@extends('layouts.admin')
@section('title', 'Admin · Create Promo Code')
@section('header', 'Create Promo Code')
@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.promo.store') }}" method="POST">
            @csrf

            <!-- Promo Code -->
            <div class="mb-4">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Promo Code *</label>
                <input type="text" name="code" id="code" value="{{ old('code') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('code') border-red-500 @enderror"
                       placeholder="Enter promo code (e.g., WELCOME2024)">
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name (Optional)</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                       placeholder="e.g., Welcome Bonus, New Year Promo">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Internal name for identification in admin panel</p>
            </div>

            <!-- Bonus Amount -->
            <div class="mb-4">
                <label for="start_balance" class="block text-sm font-medium text-gray-700 mb-2">Bonus Amount (USD) *</label>
                <input type="number" name="start_balance" id="start_balance" value="{{ old('start_balance') }}" required step="0.01" min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('start_balance') border-red-500 @enderror"
                       placeholder="0.00">
                @error('start_balance')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Amount credited to user's balance upon registration</p>
            </div>

            <!-- Max Uses -->
            <div class="mb-4">
                <label for="max_uses" class="block text-sm font-medium text-gray-700 mb-2">Max Total Uses *</label>
                <input type="number" name="max_uses" id="max_uses" value="{{ old('max_uses', 100) }}" required min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('max_uses') border-red-500 @enderror"
                       placeholder="100">
                @error('max_uses')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Maximum number of total uses (0 = unlimited)</p>
            </div>

            <!-- Uses Per User -->
            <div class="mb-4">
                <label for="uses_per_user" class="block text-sm font-medium text-gray-700 mb-2">Uses Per User *</label>
                <input type="number" name="uses_per_user" id="uses_per_user" value="{{ old('uses_per_user', 1) }}" required min="1"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('uses_per_user') border-red-500 @enderror"
                       placeholder="1">
                @error('uses_per_user')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Maximum number of times one user can use this promo code</p>
            </div>

            <!-- Is Active -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                </label>
                <p class="mt-1 text-xs text-gray-500 ml-6">Only active promo codes can be used by users</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Promo Code
                </button>
                <a href="{{ route('admin.promo.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
