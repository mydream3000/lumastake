<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWithdrawCancellationJob;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function deposits(Request $request)
    {
        return view('cabinet.transactions.deposits');
    }

    public function getDepositsData(Request $request)
    {
        $query = Transaction::where('user_id', auth()->id())
            ->where(function($q) {
                // Real deposits (blockchain or admin-created)
                $q->where(function($sub) {
                    $sub->where('type', 'deposit')
                        ->where(function($inner) {
                            $inner->whereNotNull('tx_hash')
                                  ->orWhere('is_real', true);
                        });
                })
                // Promo code bonuses
                ->orWhere('type', 'promo');
            })
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $transactions = $query->get();

        return response()->json([
            'success' => true,
            'data' => $transactions->map(function ($transaction, $index) {
                return [
                    'id' => $transaction->id,
                    'number' => $index + 1,
                    'type' => $transaction->type === 'promo'
                        ? ($transaction->meta['promo_code'] ?? 'Promo Bonus')
                        : ucfirst(str_replace('_', ' ', $transaction->type)),
                    'amount' => '$' . number_format($transaction->amount, 2),
                    'created_at' => $transaction->created_at->toISOString(),
                    'status' => $transaction->status,
                ];
            }),
        ]);
    }

    public function withdraw(Request $request)
    {
        return view('cabinet.transactions.withdraw');
    }

    public function getWithdrawData(Request $request)
    {
        $query = Transaction::where('user_id', auth()->id())
            ->where('type', 'withdraw')
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $transactions = $query->get();

        return response()->json([
            'success' => true,
            'data' => $transactions->map(function ($transaction, $index) {
                // Определяем валюту из meta
                $meta = is_array($transaction->meta) ? $transaction->meta : [];
                $token = $meta['token'] ?? 'USDT';

                return [
                    'id' => $transaction->id,
                    'number' => $index + 1,
                    'type' => ucfirst(str_replace('_', ' ', $transaction->type)),
                    'amount' => '$' . number_format($transaction->amount, 2),
                    'withdraw_fee' => '$' . number_format($meta['withdraw_fee'] ?? 0, 2),
                    'withdrawal_currency' => $token,
                    'created_at' => $transaction->created_at->toISOString(),
                    'status' => $transaction->status,
                    'can_cancel' => $transaction->status === 'pending',
                    'details' => $transaction,
                ];
            }),
        ]);
    }

    public function getTransactionDetails(Request $request, $id)
    {
        $transaction = Transaction::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        $meta = is_array($transaction->meta) ? $transaction->meta : [];

        // For promo transactions, show promo code as type and promo name as description
        if ($transaction->type === 'promo') {
            $type = $meta['promo_code'] ?? 'Promo Bonus';
            $description = $meta['promo_name'] ?? $transaction->description;
        } else {
            $type = ucfirst(str_replace('_', ' ', $transaction->type));
            $description = $transaction->description;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $transaction->id,
                'type' => $type,
                'amount' => '$' . number_format($transaction->amount, 2),
                'status' => $transaction->status,
                'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $transaction->updated_at->format('Y-m-d H:i:s'),
                'description' => $description,
                'tx_hash' => $transaction->tx_hash,
                'meta' => $transaction->type === 'promo' ? null : $meta,
            ]
        ]);
    }

    public function cancelWithdraw(Request $request, $id)
    {
        $transaction = Transaction::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('type', 'withdraw')
            ->where('status', 'pending')
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found or cannot be cancelled'
            ], 404);
        }

        // Отправляем задачу в очередь
        ProcessWithdrawCancellationJob::dispatch($transaction->id, 'Cancelled by user');

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal cancellation is being processed'
        ]);
    }
}
