<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    private function applyPaymentFilters($query)
    {
        $request = $this->request;
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhere('tx_hash', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by type (deposit/withdraw)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    public function query()
    {
        $query = Transaction::with('user:id,name,email')
            ->whereIn('type', ['deposit', 'withdraw']);
        return $this->applyPaymentFilters($query);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Type',
            'User',
            'Real Total Deposits',
            'Today Withdrawals',
            'Amount',
            'Status',
            'TX Hash',
            'Date',
        ];
    }

    public function map($payment): array
    {
        $realTotalDeposits = $payment->user ? $payment->user->cryptoTransactions()
            ->where('processed', true)
            ->whereIn('token', ['USDT', 'USDC'])
            ->sum('amount') : 0;

        $todayWithdrawals = $payment->user ? $payment->user->transactions()
            ->where('type', 'withdraw')
            ->whereDate('created_at', today())
            ->sum('amount') : 0;

        return [
            $payment->id,
            ucfirst($payment->type),
            $payment->user->name ?? 'N/A',
            $realTotalDeposits,
            $todayWithdrawals,
            $payment->amount,
            ucfirst($payment->status),
            $payment->tx_hash,
            $payment->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
