<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PaymentsExport;
use App\Http\Controllers\Controller;
use App\Mail\TemplatedMail;
use App\Models\CryptoTransaction;
use App\Models\Transaction;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments (API endpoint for DataTable)
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $query = Transaction::with('user:id,name,email')
                // Показываем в Payments только реальные платежи: депозиты и выводы
                ->whereIn('type', ['deposit', 'withdraw']);

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

            // Sorting
            $sortField = $request->get('sort', 'created_at');
            $sortDirection = $request->get('direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $payments = $query->paginate($perPage);

            // Порог подтверждений по сетям из конфига
            $minConf = config('crypto.min_confirmations', []);

            // Хелпер для условий по подтверждениям в разных сетях
            $confirmedOnChain = function ($q) use ($minConf) {
                $q->where(function ($netQ) use ($minConf) {
                    if (empty($minConf)) {
                        $netQ->where('confirmations', '>=', 1);
                        return;
                    }
                    foreach ($minConf as $net => $min) {
                        $netQ->orWhere(function ($qq) use ($net, $min) {
                            $qq->where('network', $net)->where('confirmations', '>=', (int) $min);
                        });
                    }
                });
            };

            // Получаем уникальных пользователей из текущей страницы
            $userIds = $payments->pluck('user_id')->unique()->toArray();

            // Подсчитываем реальные депозиты для каждого пользователя
            $realDepositsByUser = [];
            $realDepositsTodayByUser = [];
            $todayWithdrawalsByUser = [];

            if (!empty($userIds)) {
                // tx_hash блокчейн-транзакций, чтобы не считать их дважды
                $cryptoTxHashes = CryptoTransaction::where('processed', true)->pluck('tx_hash')->filter()->all();

                $realDepositsByUser = CryptoTransaction::query()
                    ->selectRaw('user_id, SUM(amount) as total')
                    ->whereIn('user_id', $userIds)
                    ->where('processed', true)
                    ->whereIn('token', ['USDT', 'USDC'])
                    ->where($confirmedOnChain)
                    ->groupBy('user_id')
                    ->pluck('total', 'user_id')
                    ->toArray();

                // Добавляем ручные "Real Money" депозиты (исключаем блокчейн-депозиты)
                $manualRealDepositsByUser = Transaction::query()
                    ->selectRaw('user_id, SUM(amount) as total')
                    ->whereIn('user_id', $userIds)
                    ->where('type', 'deposit')
                    ->where('status', 'confirmed')
                    ->where('is_real', true)
                    ->when(!empty($cryptoTxHashes), fn($q) => $q->whereNotIn('tx_hash', $cryptoTxHashes))
                    ->groupBy('user_id')
                    ->pluck('total', 'user_id')
                    ->toArray();

                foreach ($manualRealDepositsByUser as $uid => $amount) {
                    $realDepositsByUser[$uid] = (float)($realDepositsByUser[$uid] ?? 0) + (float)$amount;
                }

                $realDepositsTodayByUser = CryptoTransaction::query()
                    ->selectRaw('user_id, SUM(amount) as total')
                    ->whereIn('user_id', $userIds)
                    ->where('processed', true)
                    ->whereIn('token', ['USDT', 'USDC'])
                    ->where($confirmedOnChain)
                    ->whereDate('created_at', today())
                    ->groupBy('user_id')
                    ->pluck('total', 'user_id')
                    ->toArray();

                // Добавляем ручные "Real Money" депозиты за сегодня (исключаем блокчейн-депозиты)
                $manualRealDepositsTodayByUser = Transaction::query()
                    ->selectRaw('user_id, SUM(amount) as total')
                    ->whereIn('user_id', $userIds)
                    ->where('type', 'deposit')
                    ->where('status', 'confirmed')
                    ->where('is_real', true)
                    ->when(!empty($cryptoTxHashes), fn($q) => $q->whereNotIn('tx_hash', $cryptoTxHashes))
                    ->whereDate('created_at', today())
                    ->groupBy('user_id')
                    ->pluck('total', 'user_id')
                    ->toArray();

                foreach ($manualRealDepositsTodayByUser as $uid => $amount) {
                    $realDepositsTodayByUser[$uid] = (float)($realDepositsTodayByUser[$uid] ?? 0) + (float)$amount;
                }

                // Сумма выводов за сегодня по пользователям (любой статус)
                $todayWithdrawalsByUser = Transaction::query()
                    ->selectRaw('user_id, SUM(amount) as total')
                    ->whereIn('user_id', $userIds)
                    ->where('type', 'withdraw')
                    ->whereDate('created_at', today())
                    ->groupBy('user_id')
                    ->pluck('total', 'user_id')
                    ->toArray();
            }

            return response()->json([
                'data' => $payments->map(function ($payment) use ($realDepositsByUser, $realDepositsTodayByUser, $todayWithdrawalsByUser) {
                    return [
                        'id' => $payment->id,
                        'user_id' => $payment->user_id,
                        'user_name' => $payment->user->name,
                        'user_email' => $payment->user->email,
                        'type' => $payment->type,
                        'amount' => $payment->amount,
                        'status' => $payment->status,
                        'is_real' => (bool)$payment->is_real || !empty($payment->tx_hash),
                        'tx_hash' => $payment->tx_hash,
                        'wallet_address' => $payment->wallet_address,
                        'real_total_deposits' => $realDepositsByUser[$payment->user_id] ?? 0,
                        'today_real_deposits' => $realDepositsTodayByUser[$payment->user_id] ?? 0,
                        'today_withdrawals' => $todayWithdrawalsByUser[$payment->user_id] ?? 0,
                        'created_at' => $payment->created_at->format('d, M, Y H:i'),
                        'updated_at' => $payment->updated_at->format('d, M, Y H:i'),
                    ];
                }),
                'total' => $payments->total(),
                'per_page' => $payments->perPage(),
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
            ]);
        }

        // Порог подтверждений по сетям из конфига
        $minConf = config('crypto.min_confirmations', []);

        // Хелпер для условий по подтверждениям в разных сетях
        $confirmedOnChain = function ($q) use ($minConf) {
            $q->where(function ($netQ) use ($minConf) {
                if (empty($minConf)) {
                    $netQ->where('confirmations', '>=', 1);
                    return;
                }
                foreach ($minConf as $net => $min) {
                    $netQ->orWhere(function ($qq) use ($net, $min) {
                        $qq->where('network', $net)->where('confirmations', '>=', (int) $min);
                    });
                }
            });
        };

        // Реальные депозиты (USDT + USDC) — всего и за сегодня
        // tx_hash блокчейн-транзакций, чтобы не считать их дважды
        $cryptoTxHashes = CryptoTransaction::where('processed', true)->pluck('tx_hash')->filter()->all();

        $totalRealDeposits = (float) CryptoTransaction::query()
            ->where('processed', true)
            ->whereIn('token', ['USDT', 'USDC'])
            ->where($confirmedOnChain)
            ->sum('amount');

        // Добавляем ручные "Real Money" депозиты (исключаем блокчейн-депозиты)
        $totalRealDeposits += (float) Transaction::where('type', 'deposit')
            ->where('status', 'confirmed')
            ->where('is_real', true)
            ->when(!empty($cryptoTxHashes), fn($q) => $q->whereNotIn('tx_hash', $cryptoTxHashes))
            ->sum('amount');

        $realDepositsToday = (float) CryptoTransaction::query()
            ->where('processed', true)
            ->whereIn('token', ['USDT', 'USDC'])
            ->where($confirmedOnChain)
            ->whereDate('created_at', today())
            ->sum('amount');

        // Добавляем ручные "Real Money" депозиты за сегодня (исключаем блокчейн-депозиты)
        $realDepositsToday += (float) Transaction::where('type', 'deposit')
            ->where('status', 'confirmed')
            ->where('is_real', true)
            ->when(!empty($cryptoTxHashes), fn($q) => $q->whereNotIn('tx_hash', $cryptoTxHashes))
            ->whereDate('created_at', today())
            ->sum('amount');

        // Статистика транзакций
        $totalDeposits = Transaction::where('type', 'deposit')
            ->where('status', 'confirmed')
            ->sum('amount');

        $totalWithdrawals = Transaction::where('type', 'withdraw')
            ->where('status', 'confirmed')
            ->sum('amount');

        $pendingDeposits = Transaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->sum('amount');

        $pendingWithdrawals = Transaction::where('type', 'withdraw')
            ->where('status', 'pending')
            ->sum('amount');

        return view('admin.payments.index', compact(
            'totalRealDeposits',
            'realDepositsToday',
            'totalDeposits',
            'totalWithdrawals',
            'pendingDeposits',
            'pendingWithdrawals'
        ));
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        $filename = 'payments-' . date('Y-m-d-His') . '.' . $format;

        return Excel::download(new PaymentsExport($request), $filename);
    }

    /**
     * Show payment details
     */
    public function show(Transaction $payment)
    {
        $payment->load('user');

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Approve pending withdrawal
     */
    public function approve(Request $request, Transaction $payment, TelegramBotService $telegramBotService, \App\Services\TierService $tierService)
    {
        if ($payment->type !== 'withdraw') {
            return response()->json([
                'success' => false,
                'message' => 'Only withdrawals can be approved',
            ], 400);
        }

        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending withdrawals can be approved',
            ], 400);
        }

        // Validate comment (optional)
        $validated = $request->validate([
            'comment' => 'nullable|string|max:500',
        ]);

        // Вычитаем баланс ТОЛЬКО при approve
        $user = $payment->user;

        // Списываем средства с баланса (админ может подтвердить вывод даже если баланс недостаточен)
        // Это может создать отрицательный баланс, но это решение админа
        $user->decrement('balance', $payment->amount);

        // Пересчитываем tier после списания баланса
        $tierService->recalculateUserTier($user->fresh());

        // Update status to confirmed and save comment if provided
        $updateData = ['status' => 'confirmed'];
        if (!empty($validated['comment'])) {
            $updateData['notes'] = 'Approved by admin: ' . $validated['comment'];
        }
        $payment->update($updateData);

        // Отправляем уведомление в Telegram о подтверждении админом
        try {
            $telegramBotService->sendWithdrawConfirmed($payment);
        } catch (\Exception $e) {
            \Log::error('Failed to send telegram notification on approve', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Отправляем email пользователю об одобрении вывода (используем шаблон из БД)
        try {
            Mail::mailer('failover')
                ->to($payment->user->email)
                ->send(new TemplatedMail(
                    'withdrawal_approved',
                    [
                        'userName' => $payment->user->name,
                        'amount' => number_format($payment->amount, 2),
                        'comment' => $validated['comment'] ?? null,
                    ],
                    $payment->user_id
                ));
        } catch (\Throwable $e) {
            \Log::error('Failed to send withdrawal approved email', [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
                'email' => $payment->user->email,
                'error' => $e->getMessage(),
            ]);
        }

        // Log the approval
        \Log::info('Admin approved withdrawal', [
            'admin_id' => auth()->id(),
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'amount' => $payment->amount,
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal approved and balance deducted',
        ]);
    }

    /**
     * Reject/cancel pending withdrawal
     */
    public function reject(Request $request, Transaction $payment, TelegramBotService $telegramBotService)
    {
        if ($payment->type !== 'withdraw') {
            return response()->json([
                'success' => false,
                'message' => 'Only withdrawals can be rejected',
            ], 400);
        }

        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending withdrawals can be rejected',
            ], 400);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // НЕ возвращаем баланс - он не списывался при создании заявки
        // Баланс списывается только при approve

        // Update status to cancelled
        $payment->update([
            'status' => 'cancelled',
            'notes' => 'Rejected by admin: ' . $validated['reason'],
        ]);

        // Отправляем уведомление в Telegram об отмене
        $telegramBotService->sendWithdrawRejected($payment, $validated['reason']);

        // Отправляем email пользователю с причиной отказа (используем шаблон из БД)
        try {
            Mail::mailer('failover')
                ->to($payment->user->email)
                ->send(new TemplatedMail(
                    'withdrawal_rejected',
                    [
                        'userName' => $payment->user->name,
                        'amount' => number_format($payment->amount, 2),
                        'reason' => $validated['reason'],
                    ],
                    $payment->user_id
                ));
        } catch (\Throwable $e) {
            \Log::error('Failed to send withdraw rejection email', [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
                'email' => $payment->user->email,
                'error' => $e->getMessage(),
            ]);
        }

        // Log the rejection
        \Log::info('Admin rejected withdrawal', [
            'admin_id' => auth()->id(),
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'amount' => $payment->amount,
            'reason' => $validated['reason'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal rejected',
        ]);
    }

    /**
     * Mark withdrawal as completed
     */
    public function complete(Request $request, Transaction $payment, TelegramBotService $telegramBotService, \App\Services\TierService $tierService)
    {
        if ($payment->type !== 'withdraw') {
            return response()->json([
                'success' => false,
                'message' => 'Only withdrawals can be completed',
            ], 400);
        }

        if (!in_array($payment->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending or confirmed withdrawals can be completed',
            ], 400);
        }

        $validated = $request->validate([
            'tx_hash' => 'required|string|max:255',
        ]);

        // Если статус pending - значит approve не было, вычитаем баланс
        // Админ может завершить вывод даже если баланс недостаточен (это решение админа)
        if ($payment->status === 'pending') {
            $user = $payment->user;
            $user->decrement('balance', $payment->amount);

            // Пересчитываем tier после списания баланса
            $tierService->recalculateUserTier($user->fresh());
        }

        // Update status to confirmed (completed не существует в enum)
        $payment->update([
            'status' => 'confirmed',
            'tx_hash' => $validated['tx_hash'],
        ]);

        // Создаем или получаем CryptoTransaction для TxID (только если есть wallet_address)
        $cryptoTransaction = null;
        if ($payment->wallet_address) {
            try {
                $cryptoTransaction = CryptoTransaction::updateOrCreate(
                    ['tx_hash' => $validated['tx_hash']],
                    [
                        'user_id' => $payment->user_id,
                        'network' => $payment->network ?? 'tron',
                        'token' => 'USDT',
                        'address' => $payment->wallet_address,
                        'amount' => $payment->amount,
                        'confirmations' => 100,
                        'processed' => true,
                    ]
                );
            } catch (\Exception $e) {
                \Log::error('Failed to create CryptoTransaction', [
                    'payment_id' => $payment->id,
                    'tx_hash' => $validated['tx_hash'],
                    'error' => $e->getMessage(),
                ]);
                // Продолжаем выполнение, даже если не удалось создать crypto_transaction
            }
        }

        // Отправляем уведомление в Telegram о подтверждении вывода
        try {
            $telegramBotService->sendWithdrawConfirmed($payment, $cryptoTransaction);
        } catch (\Exception $e) {
            \Log::error('Failed to send telegram notification', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Log the completion
        \Log::info('Admin completed withdrawal', [
            'admin_id' => auth()->id(),
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'amount' => $payment->amount,
            'tx_hash' => $validated['tx_hash'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal completed with transaction hash',
        ]);
    }

    /**
     * Get payment statistics
     */
    public function stats()
    {
        // Минимальные подтверждения для сетей
        $minConf = config('crypto.min_confirmations', []);
        $confirmedOnChain = function ($q) use ($minConf) {
            $q->where(function ($netQ) use ($minConf) {
                if (empty($minConf)) {
                    $netQ->where('confirmations', '>=', 1);
                    return;
                }
                foreach ($minConf as $net => $min) {
                    $netQ->orWhere(function ($qq) use ($net, $min) {
                        $qq->where('network', $net)->where('confirmations', '>=', (int) $min);
                    });
                }
            });
        };

        // tx_hash блокчейн-транзакций, чтобы не считать их дважды
        $cryptoTxHashes = CryptoTransaction::where('processed', true)->pluck('tx_hash')->filter()->all();

        $totalRealDeposits = (float) CryptoTransaction::query()
            ->where('processed', true)
            ->whereIn('token', ['USDT', 'USDC'])
            ->where($confirmedOnChain)
            ->sum('amount');

        // Добавляем ручные "Real Money" депозиты (исключаем блокчейн-депозиты)
        $totalRealDeposits += (float) Transaction::where('type', 'deposit')
            ->where('status', 'confirmed')
            ->where('is_real', true)
            ->when(!empty($cryptoTxHashes), fn($q) => $q->whereNotIn('tx_hash', $cryptoTxHashes))
            ->sum('amount');

        $realDepositsToday = (float) CryptoTransaction::query()
            ->where('processed', true)
            ->whereIn('token', ['USDT', 'USDC'])
            ->where($confirmedOnChain)
            ->whereDate('created_at', today())
            ->sum('amount');

        // Добавляем ручные "Real Money" депозиты за сегодня (исключаем блокчейн-депозиты)
        $realDepositsToday += (float) Transaction::where('type', 'deposit')
            ->where('status', 'confirmed')
            ->where('is_real', true)
            ->when(!empty($cryptoTxHashes), fn($q) => $q->whereNotIn('tx_hash', $cryptoTxHashes))
            ->whereDate('created_at', today())
            ->sum('amount');

        $stats = [
            'deposits' => [
                'total' => Transaction::where('type', 'deposit')->where('status', 'confirmed')->sum('amount'),
                'count' => Transaction::where('type', 'deposit')->count(),
                'pending' => Transaction::where('type', 'deposit')->where('status', 'pending')->count(),
                'confirmed' => Transaction::where('type', 'deposit')->where('status', 'confirmed')->count(),
                'real_total' => (float) $totalRealDeposits,
                'real_today' => (float) $realDepositsToday,
            ],
            'withdrawals' => [
                'total' => Transaction::where('type', 'withdraw')->where('status', 'confirmed')->sum('amount'),
                'count' => Transaction::where('type', 'withdraw')->count(),
                'pending' => Transaction::where('type', 'withdraw')->where('status', 'pending')->count(),
                'confirmed' => Transaction::where('type', 'withdraw')->where('status', 'confirmed')->count(),
                'cancelled' => Transaction::where('type', 'withdraw')->where('status', 'cancelled')->count(),
            ],
        ];

        return response()->json($stats);
    }
}
