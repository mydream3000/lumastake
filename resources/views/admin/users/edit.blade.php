@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('admin.users.show', $user) }}" class="text-cabinet-orange hover:text-cabinet-orange/80 mb-2 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to User
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Edit User: {{ $user->name }}</h1>
        <p class="text-gray-600 mt-1">{{ $user->email }}</p>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">User Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-cabinet-orange focus:border-cabinet-orange @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-cabinet-orange focus:border-cabinet-orange @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Balance -->
                <div>
                    <label for="balance" class="block text-sm font-medium text-gray-700 mb-1">Balance ($)</label>
                    <input type="number" name="balance" id="balance" value="{{ old('balance', $user->balance) }}" step="0.01" min="0"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-cabinet-orange focus:border-cabinet-orange @error('balance') border-red-500 @enderror">
                    @error('balance')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Tier -->
                <div>
                    <label for="current_tier" class="block text-sm font-medium text-gray-700 mb-1">Current Tier</label>
                    <select name="current_tier" id="current_tier"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-cabinet-orange focus:border-cabinet-orange @error('current_tier') border-red-500 @enderror">
                        <option value="">No Tier</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('current_tier', $user->current_tier) == $i ? 'selected' : '' }}>Tier {{ $i }}</option>
                        @endfor
                    </select>
                    @error('current_tier')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Settings</h2>

            <div class="space-y-4">
                <!-- Is Admin -->
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_admin" value="0">
                    <input type="checkbox" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                           class="w-5 h-5 text-cabinet-orange border-gray-300 rounded focus:ring-cabinet-orange">
                    <span class="text-sm font-medium text-gray-700">Administrator</span>
                </label>

                <!-- Active -->
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" name="active" value="1" {{ old('active', $user->active) ? 'checked' : '' }}
                           class="w-5 h-5 text-cabinet-orange border-gray-300 rounded focus:ring-cabinet-orange">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4">
            <button type="submit" class="px-6 py-2 bg-cabinet-orange text-white rounded-lg hover:bg-cabinet-orange/90 transition-colors font-medium">
                <i class="fas fa-save mr-2"></i>
                Save Changes
            </button>
            <a href="{{ route('admin.users.show', $user) }}" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
