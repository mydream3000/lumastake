@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.payments.index') }}" class="text-cabinet-orange hover:text-cabinet-orange/80 mb-2 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Payments
            </a>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Payment Details #{{ $payment->id }}</h1>
        </div>
        <div class="flex items-center gap-3">
            @if($payment->type === 'withdraw' && $payment->status === 'pending')
                <button onclick="approvePayment({{ $payment->id }})"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Approve
                </button>
                <button onclick="rejectPayment({{ $payment->id }})"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Reject
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Payment Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Type</label>
                        <div class="flex items-center gap-2">
                            @if($payment->type === 'deposit')
                                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-arrow-down mr-1"></i> Deposit
                                </span>
                                @if((bool)$payment->is_real || !empty($payment->tx_hash))
                                    <span class="inline-flex px-3 py-1 text-xs font-black rounded-full bg-red-600 text-white uppercase italic tracking-wider ml-2">Real Money</span>
                                @endif
                            @else
                                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-orange-100 text-orange-800">
                                    <i class="fas fa-arrow-up mr-1"></i> Withdrawal
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <div>
                            @if($payment->status === 'completed')
                                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Completed
                                </span>
                            @elseif($payment->status === 'pending')
                                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            @elseif($payment->status === 'rejected')
                                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i> Rejected
                                </span>
                            @else
                                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Amount</label>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($payment->amount, 2) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Date</label>
                        <p class="text-sm text-gray-900">{{ $payment->created_at->format('d, M, Y H:i') }}</p>
                    </div>

                    @if($payment->description)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                        <p class="text-sm text-gray-900">{{ $payment->description }}</p>
                    </div>
                    @endif

                    @if($payment->tx_hash)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Transaction Hash</label>
                        <div class="flex items-center gap-2">
                            <code class="text-sm bg-gray-100 px-3 py-2 rounded border border-gray-200 flex-1 overflow-x-auto">{{ $payment->tx_hash }}</code>
                            <button onclick="copyToClipboard('{{ $payment->tx_hash }}')"
                                    class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors"
                                    title="Copy">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Metadata -->
            @if($payment->meta)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($payment->meta as $key => $value)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                        <p class="text-sm text-gray-900">{{ is_array($value) ? json_encode($value) : $value }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Details -->
            @if($payment->details)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Transaction Details</h2>
                <pre class="text-sm bg-gray-100 p-4 rounded border border-gray-200 overflow-x-auto">{{ json_encode($payment->details, JSON_PRETTY_PRINT) }}</pre>
            </div>
            @endif
        </div>

        <!-- User Info Sidebar -->
        <div class="space-y-6">
            <!-- User Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">User Information</h2>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                        <p class="text-sm font-medium text-gray-900">{{ $payment->user->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <p class="text-sm text-gray-900">{{ $payment->user->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Current Balance</label>
                        <p class="text-sm font-semibold text-gray-900">${{ number_format($payment->user->balance, 2) }}</p>
                    </div>

                    <div class="pt-3 border-t border-gray-200">
                        <a href="{{ route('admin.users.show', $payment->user) }}"
                           class="inline-flex items-center text-sm text-cabinet-orange hover:text-cabinet-orange/80">
                            View User Profile
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h2>

                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-cabinet-orange rounded-full mt-1.5"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Created</p>
                            <p class="text-xs text-gray-500">{{ $payment->created_at->format('d, M, Y H:i') }}</p>
                        </div>
                    </div>

                    @if($payment->updated_at != $payment->created_at)
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-gray-400 rounded-full mt-1.5"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Last Updated</p>
                            <p class="text-xs text-gray-500">{{ $payment->updated_at->format('d, M, Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        window.showToast('Copied to clipboard', 'success');
    }).catch(() => {
        window.showToast('Failed to copy', 'error');
    });
}

async function approvePayment(paymentId) {
    if (!confirm('Are you sure you want to approve this withdrawal?')) {
        return;
    }

    try {
        const response = await fetch(`/admin/payments/${paymentId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (data.success) {
            window.showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            window.showToast(data.message || 'Failed to approve payment', 'error');
        }
    } catch (error) {
        window.showToast('Error approving payment', 'error');
        console.error(error);
    }
}

async function rejectPayment(paymentId) {
    if (!confirm('Are you sure you want to reject this withdrawal? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`/admin/payments/${paymentId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (data.success) {
            window.showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            window.showToast(data.message || 'Failed to reject payment', 'error');
        }
    } catch (error) {
        window.showToast('Error rejecting payment', 'error');
        console.error(error);
    }
}
</script>
@endpush
