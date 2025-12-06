@extends('layouts.admin')

@section('title', 'Email Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Email Settings</h1>
            <p class="text-gray-600 mt-1">Глобальные настройки отправки email</p>
        </div>
        <a href="{{ route('admin.email-templates.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Templates
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.email-templates.update-settings') }}" method="POST">
            @csrf
            @method('PUT')

            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">
                Sender Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Sender Email -->
                <div>
                    <label for="sender_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Sender Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email"
                           id="sender_email"
                           name="sender_email"
                           value="{{ old('sender_email', $settings->sender_email) }}"
                           required
                           maxlength="255"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                           placeholder="no-reply@lumastake.com">
                    <p class="mt-1 text-xs text-gray-500">Email отправителя для всех писем</p>
                    @error('sender_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sender Name -->
                <div>
                    <label for="sender_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Sender Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="sender_name"
                           name="sender_name"
                           value="{{ old('sender_name', $settings->sender_name) }}"
                           required
                           maxlength="255"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                           placeholder="Lumastake">
                    <p class="mt-1 text-xs text-gray-500">Имя отправителя для всех писем</p>
                    @error('sender_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2 mt-6">
                Support Information
            </h3>

            <!-- Support Email -->
            <div class="mb-6">
                <label for="support_email" class="block text-sm font-medium text-gray-700 mb-2">
                    Support Email <span class="text-red-500">*</span>
                </label>
                <input type="email"
                       id="support_email"
                       name="support_email"
                       value="{{ old('support_email', $settings->support_email) }}"
                       required
                       maxlength="255"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                       placeholder="support@lumastake.com">
                <p class="mt-1 text-xs text-gray-500">Email для контактов в футере писем</p>
                @error('support_email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2 mt-6">
                Footer Settings
            </h3>

            <!-- Footer Support -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox"
                           name="footer_support"
                           value="1"
                           {{ old('footer_support', $settings->footer_support) ? 'checked' : '' }}
                           class="w-4 h-4 text-cabinet-orange border-gray-300 rounded focus:ring-cabinet-orange">
                    <span class="ml-2 text-sm text-gray-700">Show support email in footer</span>
                </label>
                @error('footer_support')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Footer Text -->
            <div class="mb-6">
                <label for="footer_text" class="block text-sm font-medium text-gray-700 mb-2">
                    Footer Text
                </label>
                <textarea id="footer_text"
                          name="footer_text"
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                          placeholder="© 2025 Lumastake. All rights reserved.">{{ old('footer_text', $settings->footer_text) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Текст копирайта в футере писем (если пусто, будет использован по умолчанию)</p>
                @error('footer_text')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="flex gap-3 mt-6 pt-4 border-t">
                <button type="submit"
                        class="flex-1 bg-cabinet-orange text-white px-4 py-2 rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Settings
                </button>
                <a href="{{ route('admin.email-templates.index') }}"
                   class="flex-1 text-center bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">
            <i class="fas fa-info-circle mr-1"></i>
            Important Information
        </h3>
        <ul class="text-sm text-blue-800 space-y-1">
            <li>• Эти настройки применяются ко всем email шаблонам</li>
            <li>• Изменения вступают в силу немедленно</li>
            <li>• Для тестирования отправки используйте кнопку "Send Test" на странице редактирования шаблона</li>
        </ul>
    </div>
</div>
@endsection
