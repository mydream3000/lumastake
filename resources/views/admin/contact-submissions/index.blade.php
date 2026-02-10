@extends('layouts.admin')

@section('title', 'Contact Submissions')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Contact Submissions</h1>
        <p class="text-gray-600 mt-1">User feedback and support inquiries</p>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.contact-submissions.index') }}"
           class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
            All ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.contact-submissions.index', ['status' => 'new']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'new' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
            New ({{ $counts['new'] }})
        </a>
        <a href="{{ route('admin.contact-submissions.index', ['status' => 'in_progress']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'in_progress' ? 'bg-yellow-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
            In Progress ({{ $counts['in_progress'] }})
        </a>
        <a href="{{ route('admin.contact-submissions.index', ['status' => 'resolved']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'resolved' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
            Resolved ({{ $counts['resolved'] }})
        </a>
        <a href="{{ route('admin.contact-submissions.index', ['status' => 'closed']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'closed' ? 'bg-gray-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
            Closed ({{ $counts['closed'] }})
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.contact-submissions.index') }}" class="flex gap-3">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email or reference..."
                   class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.contact-submissions.index', request('status') ? ['status' => request('status')] : []) }}"
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">Clear</a>
            @endif
        </form>
    </div>

    <!-- Submissions Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($submissions as $submission)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-bold text-blue-600">#{{ $submission->reference }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $submission->full_name }}</div>
                                @if($submission->user)
                                    <div class="text-xs text-gray-500">Registered user (ID: {{ $submission->user_id }})</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="mailto:{{ $submission->email }}" class="text-sm text-blue-600 hover:underline">{{ $submission->email }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($submission->status)
                                    @case('new')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">New</span>
                                        @break
                                    @case('in_progress')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">In Progress</span>
                                        @break
                                    @case('resolved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Resolved</span>
                                        @break
                                    @case('closed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Closed</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $submission->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.contact-submissions.show', $submission) }}" class="text-blue-600 hover:text-blue-900 font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No submissions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
