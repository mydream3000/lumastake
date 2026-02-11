@extends('layouts.admin')

@section('title', 'Edit Email Template')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Edit Email Template</h1>
            <p class="text-gray-600 mt-1">{{ $template->name }}</p>
        </div>
        <div class="flex gap-3">
            <button type="button"
                    onclick="sendTestEmail()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-paper-plane mr-2"></i>
                Send Test
            </button>
            <a href="{{ route('admin.email-templates.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Available Variables -->
    @if($availableVariables && count($availableVariables) > 0)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">
            <i class="fas fa-info-circle mr-1"></i>
            Available Variables
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
            @foreach($availableVariables as $var => $description)
            <div class="text-xs">
                <code class="bg-blue-100 text-blue-800 px-2 py-1 rounded">${{ $var }}</code>
                <span class="text-gray-600 ml-1">- {{ $description }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.email-templates.update', $template) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Template Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Template Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $template->name) }}"
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
                        Template Key
                    </label>
                    <input type="text"
                           id="key"
                           value="{{ $template->key }}"
                           disabled
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                    <p class="mt-1 text-xs text-gray-500">Ключ шаблона (не редактируется)</p>
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
                       value="{{ old('subject', $template->subject) }}"
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
                       value="{{ old('sender_name', $template->sender_name ?? 'Lumastake') }}"
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
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange font-mono text-sm"
                          placeholder="Enter email content">{{ old('content', $template->content) }}</textarea>
                <p class="mt-1 text-sm text-gray-500">
                    <i class="fas fa-lightbulb text-yellow-500"></i>
                    Tip: Use @extends('emails.layouts.base') to inherit base email layout with logo and footer
                </p>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Active -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox"
                           name="enabled"
                           value="1"
                           {{ old('enabled', $template->enabled) ? 'checked' : '' }}
                           class="w-4 h-4 text-cabinet-orange border-gray-300 rounded focus:ring-cabinet-orange">
                    <span class="ml-2 text-sm text-gray-700">Enabled (template will be used for email notifications)</span>
                </label>
                @error('enabled')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-cabinet-orange text-white px-4 py-2 rounded-lg hover:bg-cabinet-orange/90 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Template
                </button>
                <a href="{{ route('admin.email-templates.index') }}"
                   class="flex-1 text-center bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Test Email Modal -->
<div id="testEmailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Send Test Email</h3>
        <div class="mb-4">
            <label for="testEmail" class="block text-sm font-medium text-gray-700 mb-2">
                Email Address
            </label>
            <input type="email"
                   id="testEmail"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"
                   placeholder="your@email.com">
        </div>
        <div class="flex gap-3">
            <button type="button"
                    onclick="confirmSendTest()"
                    class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Send
            </button>
            <button type="button"
                    onclick="closeTestModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                Cancel
            </button>
        </div>
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

function sendTestEmail() {
    document.getElementById('testEmailModal').classList.remove('hidden');
}

function closeTestModal() {
    document.getElementById('testEmailModal').classList.add('hidden');
}

async function confirmSendTest() {
    const email = document.getElementById('testEmail').value;

    if (!email) {
        window.showToast('Please enter an email address', 'error');
        return;
    }

    try {
        const response = await fetch('{{ route('admin.email-templates.send-test', $template) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ test_email: email })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            window.showToast(data.message, 'success');
            closeTestModal();
        } else {
            window.showToast(data.message || 'Failed to send test email', 'error');
        }
    } catch (error) {
        window.showToast('Error occurred while sending test email', 'error');
        console.error(error);
    }
}

// Close modal when clicking outside
document.getElementById('testEmailModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeTestModal();
    }
});
</script>
@endpush
