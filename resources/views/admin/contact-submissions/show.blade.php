@extends('layouts.admin')

@section('title', 'Submission #' . $submission->reference)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.contact-submissions.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-2 inline-block">&larr; Back to Submissions</a>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Submission #{{ $submission->reference }}</h1>
            <p class="text-gray-600 mt-1">Submitted on {{ $submission->created_at->format('F d, Y \a\t H:i') }}</p>
        </div>
        <div>
            @switch($submission->status)
                @case('new')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">New</span>
                    @break
                @case('in_progress')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">In Progress</span>
                    @break
                @case('resolved')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">Resolved</span>
                    @break
                @case('closed')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Closed</span>
                    @break
            @endswitch
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Message -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Message</h2>
                <div class="bg-gray-50 border-l-4 border-blue-500 rounded-r-lg p-4">
                    <p class="text-gray-800 whitespace-pre-wrap">{{ $submission->message }}</p>
                </div>
            </div>

            <!-- Update Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Update Status</h2>

                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.contact-submissions.update-status', $submission) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="new" {{ $submission->status === 'new' ? 'selected' : '' }}>New</option>
                                <option value="in_progress" {{ $submission->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $submission->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $submission->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Admin Note</label>
                            <textarea name="admin_note" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Internal notes about this submission...">{{ old('admin_note', $submission->admin_note) }}</textarea>
                        </div>

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>

            <!-- Delete -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-red-600 mb-4">Danger Zone</h2>
                <form method="POST" action="{{ route('admin.contact-submissions.destroy', $submission) }}"
                      onsubmit="return confirm('Are you sure you want to delete this submission?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                        Delete Submission
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Contact Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Contact Information</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $submission->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</dt>
                        <dd class="mt-1">
                            <a href="mailto:{{ $submission->email }}" class="text-sm text-blue-600 hover:underline">{{ $submission->email }}</a>
                        </dd>
                    </div>
                    @if($submission->phone)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $submission->phone }}</dd>
                    </div>
                    @endif
                    @if($submission->country)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Country</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $submission->country }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</dt>
                        <dd class="mt-1 text-sm font-mono font-bold text-blue-600">#{{ $submission->reference }}</dd>
                    </div>
                </dl>
            </div>

            <!-- User Info -->
            @if($submission->user)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Registered User</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $submission->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">User ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $submission->user->id }}</dd>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('admin.users.show', $submission->user) }}" class="text-sm text-blue-600 hover:underline font-medium">View User Profile &rarr;</a>
                    </div>
                </dl>
            </div>
            @endif

            @if($submission->admin_note)
            <!-- Admin Note -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Admin Note</h2>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $submission->admin_note }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
