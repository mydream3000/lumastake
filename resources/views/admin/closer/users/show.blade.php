@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('admin.closer.users.index') }}" class="text-cabinet-orange hover:text-cabinet-orange/80 mb-2 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Users
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- User Info -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">User Information</h2>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                    <p class="text-sm text-gray-900">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                    <p class="text-sm text-gray-900">
                        @if($user->phone)
                            @php
                                $phone = trim((string)$user->phone);
                            @endphp
                            @if(str_starts_with($phone, '+'))
                                {{ $phone }}
                            @else
                                {{ trim(($user->country_code ? $user->country_code . ' ' : '') . $phone) }}
                            @endif
                        @else
                            <span class="text-gray-400">Not provided</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Account Type</label>
                    <p class="text-sm">
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $user->account_type === 'islamic' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($user->account_type ?? 'Normal') }}
                        </span>
                    </p>
                </div>
                @if($user->country)
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Country</label>
                    <p class="text-sm text-gray-900">{{ $user->country }}</p>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Registered</label>
                    <p class="text-sm text-gray-900">{{ $user->created_at->format('d, M, Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Add Note -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
             x-data="{
                 comment: '',
                 status: '',
                 saving: false,
                 async save() {
                     this.saving = true;
                     try {
                         const response = await fetch('{{ route('admin.closer.users.save-note', $user) }}', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                 'Accept': 'application/json',
                             },
                             body: JSON.stringify({ comment: this.comment, status: this.status || null }),
                         });
                         const data = await response.json();
                         if (data.success) {
                             window.showToast('Note saved', 'success');
                             setTimeout(() => window.location.reload(), 800);
                         } else {
                             window.showToast(data.message || 'Error', 'error');
                         }
                     } catch (e) {
                         window.showToast('Error saving note', 'error');
                     }
                     this.saving = false;
                 }
             }">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Add Note</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                    <textarea x-model="comment" rows="3" placeholder="Write your comment..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select x-model="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-orange">
                        <option value="">-- Select --</option>
                        <option value="int">Interested</option>
                        <option value="no-int">Not Interested</option>
                        <option value="re-call">Re-call</option>
                        <option value="fake">Fake</option>
                    </select>
                </div>
                <button @click="save()" :disabled="saving"
                        class="w-full px-4 py-2 bg-cabinet-orange text-white rounded-lg font-medium hover:bg-cabinet-orange/90 transition-colors disabled:opacity-50">
                    <span x-text="saving ? 'Saving...' : 'Save Note'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Notes History -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Notes History (Last 5)</h2>

        @if($notes->count() > 0)
            <div class="space-y-3">
                @foreach($notes as $note)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-cabinet-orange/30 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-900">{{ $note->closer->name ?? 'Unknown' }}</span>
                                @if($note->status)
                                    @php
                                        $statusColors = [
                                            'int' => 'bg-green-100 text-green-800',
                                            'no-int' => 'bg-red-100 text-red-800',
                                            're-call' => 'bg-yellow-100 text-yellow-800',
                                            'fake' => 'bg-gray-100 text-gray-800',
                                        ];
                                        $statusLabels = [
                                            'int' => 'Interested',
                                            'no-int' => 'Not Interested',
                                            're-call' => 'Re-call',
                                            'fake' => 'Fake',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full {{ $statusColors[$note->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$note->status] ?? $note->status }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500">{{ $note->created_at->format('d, M, Y H:i') }}</span>
                        </div>
                        @if($note->comment)
                            <p class="text-sm text-gray-700">{{ $note->comment }}</p>
                        @else
                            <p class="text-sm text-gray-400 italic">No comment</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 py-8">No notes yet</p>
        @endif
    </div>
</div>
@endsection
