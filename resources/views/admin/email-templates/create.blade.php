@extends('layouts.admin')

@section('title', 'Create Email Template')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Create Email Template</h1>
            @if(($defaults['from_id'] ?? null))
                <p class="text-gray-600 mt-1">Based on template ID #{{ $defaults['from_id'] }}</p>
            @else
                <p class="text-gray-600 mt-1">Создание нового email шаблона</p>
            @endif
        </div>
        <a href="{{ route('admin.email-templates.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to List
        </a>
    </div>

    <!-- Available Variables (from source) -->
    @if(!empty($availableVariables))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-blue-900 mb-2">
                <i class="fas fa-info-circle mr-1"></i>
                Available Variables (copied from source)
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                @foreach($availableVariables as $var => $description)
                    <div class="text-xs">
                        <code class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{!! '$' . $var !!}</code>
                        <span class="text-gray-600 ml-1">- {{ $description }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.email-templates.store') }}" method="POST">
            @csrf
            <input type="hidden" name="from_id" value="{{ $defaults['from_id'] ?? '' }}">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Template Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Template Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $defaults['name'] ?? '') }}"
                           required
                           maxlength="255"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                           placeholder="Enter template name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Template Key -->
                <div>
                    <label for="key" class="block text-sm font-medium text-gray-700 mb-2">
                        Template Key <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="key"
                           name="key"
                           value="{{ old('key', $defaults['key'] ?? '') }}"
                           required
                           maxlength="255"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                           placeholder="unique_key">
                    @error('key')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Уникальный ключ будет использоваться для выбора шаблона при массовой рассылке</p>
                </div>
            </div>

            <!-- Subject -->
            <div class="mb-6">
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                    Email Subject <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="subject"
                       name="subject"
                       value="{{ old('subject', $defaults['subject'] ?? '') }}"
                       required
                       maxlength="255"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                       placeholder="Enter email subject">
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sender Name -->
            <div class="mb-6">
                <label for="sender_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Sender Name
                </label>
                <input type="text"
                       id="sender_name"
                       name="sender_name"
                       value="{{ old('sender_name', $defaults['sender_name'] ?? 'Lumastake') }}"
                       maxlength="255"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                       placeholder="Lumastake">
                @error('sender_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Имя отправителя, которое будет отображаться в письме (например: "Lumastake", "Welcome", "Support")</p>
            </div>

            <!-- Content -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <label for="content" class="block text-sm font-medium text-gray-700">
                        Email Content (HTML/Blade) <span class="text-red-500">*</span>
                    </label>
                    <button type="button" id="toggle-editor" class="px-3 py-1 bg-gray-200 text-gray-800 rounded-md text-xs font-medium">Toggle Editor</button>
                </div>
                <textarea id="content"
                          name="content"
                          rows="20"
                          required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange font-mono text-sm">
{!! old('content', $defaults['content'] ?? '') !!}
                </textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-500">Можно использовать Blade-переменные, например: {{ '{' }}{ $userName }}, {{ '{' }}{ $amount }} и т.п.</p>
            </div>

            <!-- Enabled -->
            <div class="mb-6">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="enabled" value="1" class="rounded border-gray-300" {{ old('enabled', $defaults['enabled'] ?? true) ? 'checked' : '' }}>
                    <span>Enabled</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-cabinet-orange text-white px-4 py-2 rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                    Create Template
                </button>
                <a href="{{ route('admin.email-templates.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('toggle-editor');
    let editorEnabled = false;

    toggleButton.addEventListener('click', () => {
        if (editorEnabled) {
            tinymce.get('content').remove();
            editorEnabled = false;
        } else {
            tinymce.init({
                selector: 'textarea#content',
                plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                toolbar_mode: 'floating',
            });
            editorEnabled = true;
        }
    });
});
</script>
@endpush
