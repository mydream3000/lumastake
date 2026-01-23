<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Models\CryptoTransaction;
use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Apply request filters to the users query (shared between index and bulk actions)
     */
    private function applyUserFilters(Request $request, $query)
    {
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Filter by status (account access)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('blocked', false);
            } elseif ($request->status === 'blocked') {
                $query->where('blocked', true);
            }
        }

        // Filter by role
        if ($request->filled('role')) {
            if ($request->role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->role === 'user') {
                $query->where('is_admin', false);
            }
        }

        // Filter by account type
        if ($request->filled('account_type')) {
            $query->where('account_type', $request->account_type);
        }

        // Filter by tier
        if ($request->filled('tier')) {
            $query->where('current_tier', $request->tier);
        }

        // Filter by verification status (kyc)
        if ($request->filled('verification')) {
            $verification = strtolower($request->verification);
            if (in_array($verification, ['verified', 'pending', 'unverified'])) {
                $query->where('verification_status', $verification);
            }
        }

        // Filter by referral level (1..5)
        if ($request->filled('ref_level')) {
            $level = (int) $request->ref_level;
            if ($level >= 1 && $level <= 5) {
                $query->where('referral_level_id', $level);
            }
        }

        // Filter by favorite (admin's personal favorites)
        if ($request->filled('favorite') && $request->favorite === '1') {
            // Only apply if table exists
            if (\Illuminate\Support\Facades\Schema::hasTable('admin_favorite_users')) {
                $adminId = Auth::id();
                $query->whereHas('favoritedByAdmins', function ($q) use ($adminId) {
                    $q->where('admin_id', $adminId);
                });
            }
        }

        return $query;
    }

    /**
     * Display a listing of users (API endpoint for DataTable)
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $query = User::query();
            $query = $this->applyUserFilters($request, $query);

            // Sorting
            $sortField = $request->get('sort', 'created_at');
            $sortDirection = $request->get('direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $users = $query->paginate($perPage);

            // Get current admin's favorite user IDs
            $adminId = Auth::id();
            $favoriteUserIds = [];

            // Check if table exists before querying (safe for migrations)
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('admin_favorite_users')) {
                    $favoriteUserIds = \DB::table('admin_favorite_users')
                        ->where('admin_id', $adminId)
                        ->pluck('user_id')
                        ->toArray();
                }
            } catch (\Exception $e) {
                // Table doesn't exist yet or other error - just use empty array
                \Log::warning('Could not fetch favorite users: ' . $e->getMessage());
            }

            // Calculate REAL deposits per user (on-chain, confirmed, processed, USDT) for users on current page
            $realDepositsByUser = [];
            try {
                $userIds = $users->pluck('id')->all();
                if (!empty($userIds)) {
                    $minConf = config('crypto.min_confirmations', []);
                    // Build per-network confirmations condition
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

                    $realDepositsByUser = CryptoTransaction::query()
                        ->selectRaw('user_id, SUM(amount) as total')
                        ->whereIn('user_id', $userIds)
                        ->where('processed', true)
                        ->whereIn('token', ['USDT', 'USDC'])
                        ->where($confirmedOnChain)
                        ->groupBy('user_id')
                        ->pluck('total', 'user_id')
                        ->toArray();
                }
            } catch (\Throwable $e) {
                \Log::warning('Could not compute real deposits per user: ' . $e->getMessage());
            }

            return response()->json([
                'data' => $users->map(function ($user) use ($favoriteUserIds, $realDepositsByUser) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'country' => $user->country,
                        'account_type' => $user->account_type,
                        'balance' => $user->balance,
                        'real_deposits' => (float) ($realDepositsByUser[$user->id] ?? 0),
                        'current_tier' => $user->current_tier,
                        'referral_level_id' => $user->referral_level_id,
                        'verification_status' => $user->verification_status,
                        'is_admin' => $user->is_admin,
                        'active' => $user->active,
                        'blocked' => $user->blocked,
                        'withdrawal_blocked' => (bool) ($user->withdrawal_blocked ?? false),
                        'is_favorite' => in_array($user->id, $favoriteUserIds),
                        'active_stakes_count' => $user->stakingDeposits()->where('status', 'active')->count(),
                        'email_verified_at' => $user->email_verified_at?->format('d, M, Y'),
                        'created_at' => $user->created_at->format('d, M, Y'),
                        'last_login' => $user->last_login?->format('d, M, Y H:i'),
                    ];
                }),
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ]);
        }

        // Provide enabled email templates list for mass email action
        $templates = EmailTemplate::where('enabled', true)->get(['key','name']);
        return view('admin.users.index', compact('templates'));
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        $filename = 'users-' . date('Y-m-d-His') . '.' . $format;

        return Excel::download(new UsersExport($request), $filename);
    }

    /**
     * Show user details
     */
    public function show(User $user)
    {
        $user->load(['stakingDeposits', 'transactions', 'earnings']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show edit form
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'balance' => 'required|numeric|min:0',
            'current_tier' => 'nullable|integer|min:1|max:10',
            'is_admin' => 'boolean',
            'active' => 'boolean',
        ]);

        $user->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
            ]);
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully');
    }

    /**
     * Toggle user blocked status (account access)
     */
    public function toggleStatus(User $user)
    {
        $user->update(['blocked' => !$user->blocked]);

        return response()->json([
            'success' => true,
            'message' => $user->blocked ? 'User blocked' : 'User activated',
            'blocked' => $user->blocked,
        ]);
    }

    /**
     * Toggle admin status
     */
    public function toggleAdmin(User $user)
    {
        if (!Auth::user()->is_super_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Only super admins can change admin privileges',
            ], 403);
        }

        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own status',
            ], 403);
        }

        $user->update(['is_admin' => !$user->is_admin]);

        return response()->json([
            'success' => true,
            'message' => $user->is_admin ? 'Admin access granted' : 'Admin access revoked',
            'is_admin' => $user->is_admin,
        ]);
    }

    /**
     * Login as user (impersonate)
     */
    public function loginAs(User $user)
    {
        // Нельзя войти под другим админом
        if ($user->is_admin) {
            return redirect()->back()
                ->with('error', 'Нельзя войти под другим администратором');
        }

        // Сохраняем ID текущего админа в сессии
        session(['admin_impersonate_id' => Auth::id()]);

        // Логинимся под пользователем
        Auth::login($user);

        return redirect()->route('cabinet.dashboard')
            ->with('success', "Вы вошли под пользователем: {$user->name}");
    }

    /**
     * Return to admin account
     */
    public function returnToAdmin()
    {
        $adminId = session('admin_impersonate_id');

        if (!$adminId) {
            return redirect()->route('cabinet.dashboard')
                ->with('error', 'Сессия impersonation не найдена');
        }

        $admin = User::find($adminId);

        if (!$admin || !$admin->is_admin) {
            session()->forget('admin_impersonate_id');
            return redirect()->route('cabinet.dashboard')
                ->with('error', 'Admin пользователь не найден');
        }

        // Забываем impersonation ПЕРЕД логином
        session()->forget('admin_impersonate_id');

        // Логинимся как админ
        Auth::login($admin);

        return redirect()->route('admin.users.index')
            ->with('success', 'Вы вернулись в админку');
    }

    /**
     * Adjust user balance (add or subtract)
     */
    public function adjustBalance(Request $request, User $user)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'type' => 'required|in:add,subtract',
            'reason' => 'required|string|max:255',
        ]);

        $oldBalance = $user->balance;

        if ($validated['type'] === 'add') {
            $user->increment('balance', $validated['amount']);

            // Активируем пользователя при пополнении баланса
            $referralService = app(ReferralService::class);
            $referralService->activateUser($user->fresh());

            // Обновляем Tier немедленно после пополнения
            app(\App\Services\TierService::class)->recalculateUserTier($user->fresh());
        } else {
            $user->decrement('balance', max(0, $validated['amount']));
            // При уменьшении баланса также обновим Tier
            app(\App\Services\TierService::class)->recalculateUserTier($user->fresh());
        }

        // Log the adjustment
        \Log::info('Admin balance adjustment', [
            'admin_id' => Auth::id(),
            'user_id' => $user->id,
            'old_balance' => $oldBalance,
            'new_balance' => $user->fresh()->balance,
            'amount' => $validated['amount'],
            'type' => $validated['type'],
            'reason' => $validated['reason'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Balance adjusted successfully',
            'new_balance' => $user->fresh()->balance,
        ]);
    }

    /**
     * Delete user (with full cascade deletion of all related data)
     * ONLY available to super admins
     */
    public function destroy(User $user, Request $request)
    {
        // Check if current user is super admin
        if (!Auth::user()->is_super_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Only super admins can delete users',
            ], 403);
        }

        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account',
            ], 403);
        }

        $force = $request->boolean('force', false);

        // Prevent deleting users with active stakes (unless forced)
        $activeStakesCount = $user->stakingDeposits()->where('status', 'active')->count();
        if (!$force && $activeStakesCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete user with {$activeStakesCount} active staking deposit(s). Use force delete if you want to proceed.",
            ], 403);
        }

        // Collect deletion statistics for logging
        $stats = [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'balance' => $user->balance,
            'transactions_count' => $user->transactions()->count(),
            'staking_deposits_count' => $user->stakingDeposits()->count(),
            'active_stakes_count' => $activeStakesCount,
            'earnings_count' => $user->earnings()->count(),
            'crypto_addresses_count' => $user->cryptoAddresses()->count(),
            'crypto_transactions_count' => $user->cryptoTransactions()->count(),
            'referrals_count' => $user->referrals()->count(), // Will not be deleted, just nullified
            'deleted_by_admin_id' => Auth::id(),
            'deleted_by_admin_email' => Auth::user()->email,
            'forced' => $force,
        ];

        \DB::beginTransaction();
        try {
            // Delete user (cascade will handle all related data automatically)
            $user->delete();

            // Log the deletion
            \Log::info('User deleted by admin', $stats);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
                'deleted_data' => [
                    'transactions' => $stats['transactions_count'],
                    'stakings' => $stats['staking_deposits_count'],
                    'earnings' => $stats['earnings_count'],
                    'crypto_data' => $stats['crypto_addresses_count'] + $stats['crypto_transactions_count'],
                    'referrals_nullified' => $stats['referrals_count'],
                ],
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Failed to delete user', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Bulk: Adjust balances for selected users and send notification
     */
    public function bulkAdjustBalance(Request $request)
    {
        $validated = $request->validate([
            'all' => 'sometimes|boolean',
            'user_ids' => 'required_without:all|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'comment' => 'required|string|max:255',
            'email' => 'boolean',
        ]);

        $amount = (float)$validated['amount'];
        $comment = $validated['comment'];
        $sendEmail = (bool)($validated['email'] ?? false);
        $isAll = $request->boolean('all');

        $processedIds = [];

        if ($isAll) {
            $baseQuery = $this->applyUserFilters($request, User::query());
            $total = (clone $baseQuery)->count();
            if ($total === 0) {
                return response()->json(['success' => false, 'message' => 'No users match current filters'], 422);
            }

            $baseQuery->select('id')->orderBy('id')->chunkById(200, function ($chunk) use ($amount, $comment, &$processedIds) {
                DB::transaction(function () use ($chunk, $amount, $comment, &$processedIds) {
                    foreach ($chunk as $u) {
                        // Lock row and apply
                        $user = User::lockForUpdate()->find($u->id);
                        if (!$user) continue;
                        $before = $user->balance;
                        $user->increment('balance', $amount);

                        // Recalculate Tier immediately after mass credit
                        app(\App\Services\TierService::class)->recalculateUserTier($user->fresh());

                        Transaction::create([
                            'user_id' => $user->id,
                            'type' => 'deposit',
                            'status' => 'confirmed',
                            'amount' => $amount,
                            'description' => 'Admin mass credit',
                            'meta' => ['comment' => $comment, 'admin_id' => Auth::id()],
                        ]);

                        Log::info('Bulk credit applied', [
                            'admin_id' => Auth::id(),
                            'user_id' => $user->id,
                            'before' => $before,
                            'after' => $user->fresh()->balance,
                            'amount' => $amount,
                            'comment' => $comment,
                        ]);

                        $processedIds[] = $user->id;
                    }
                });
            });
        } else {
            $ids = $validated['user_ids'];

            DB::transaction(function () use ($ids, $amount, $comment, &$processedIds) {
                $users = User::whereIn('id', $ids)->lockForUpdate()->get();
                foreach ($users as $u) {
                    $before = $u->balance;
                    $u->increment('balance', $amount);

                    // Recalculate Tier immediately after mass credit
                    app(\App\Services\TierService::class)->recalculateUserTier($u->fresh());

                    Transaction::create([
                        'user_id' => $u->id,
                        'type' => 'deposit',
                        'status' => 'confirmed',
                        'amount' => $amount,
                        'description' => 'Admin mass credit',
                        'meta' => ['comment' => $comment, 'admin_id' => Auth::id()],
                    ]);

                    Log::info('Bulk credit applied', [
                        'admin_id' => Auth::id(),
                        'user_id' => $u->id,
                        'before' => $before,
                        'after' => $u->fresh()->balance,
                        'amount' => $amount,
                        'comment' => $comment,
                    ]);

                    $processedIds[] = $u->id;
                }
            });
        }

        if ($sendEmail) {
            try {
                $template = EmailTemplate::getByKey('mass_credit_notification');
                if ($template) {
                    // Send in chunks to avoid memory overhead
                    collect($processedIds)->chunk(200)->each(function ($idsChunk) use ($amount, $comment) {
                        $users = User::whereIn('id', $idsChunk->all())->get(['id','email','name']);
                        foreach ($users as $u) {
                            Mail::to($u->email)->send(new TemplatedMail(
                                'mass_credit_notification',
                                [
                                    'userName' => $u->name ?? $u->email,
                                    'amount' => number_format($amount, 2),
                                    'comment' => $comment,
                                ],
                                $u->id,
                                'bulk_credit'
                            ));
                        }
                    });
                } else {
                    Log::warning('Email template mass_credit_notification not found or disabled');
                }
            } catch (\Throwable $e) {
                Log::error('Bulk credit emails failed', ['error' => $e->getMessage()]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Funds credited successfully']);
    }

    /**
     * Bulk: Block selected users
     */
    public function bulkBlock(Request $request)
    {
        $validated = $request->validate([
            'all' => 'sometimes|boolean',
            'user_ids' => 'required_without:all|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        if ($request->boolean('all')) {
            $baseQuery = $this->applyUserFilters($request, User::query());
            $total = (clone $baseQuery)->count();
            if ($total === 0) {
                return response()->json(['success' => false, 'message' => 'No users match current filters'], 422);
            }
            $baseQuery->select('id')->orderBy('id')->chunkById(500, function ($chunk) {
                User::whereIn('id', $chunk->pluck('id'))->update(['blocked' => true]);
            });
        } else {
            User::whereIn('id', $validated['user_ids'])->update(['blocked' => true]);
        }

        return response()->json(['success' => true, 'message' => 'Selected users have been blocked']);
    }

    /**
     * Bulk: Unblock selected users
     */
    public function bulkUnblock(Request $request)
    {
        $validated = $request->validate([
            'all' => 'sometimes|boolean',
            'user_ids' => 'required_without:all|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        if ($request->boolean('all')) {
            $baseQuery = $this->applyUserFilters($request, User::query());
            $total = (clone $baseQuery)->count();
            if ($total === 0) {
                return response()->json(['success' => false, 'message' => 'No users match current filters'], 422);
            }
            $baseQuery->select('id')->orderBy('id')->chunkById(500, function ($chunk) {
                User::whereIn('id', $chunk->pluck('id'))->update(['blocked' => false]);
            });
        } else {
            User::whereIn('id', $validated['user_ids'])->update(['blocked' => false]);
        }

        return response()->json(['success' => true, 'message' => 'Selected users have been unblocked']);
    }

    /**
     * Bulk: Block withdrawals for selected users
     */
    public function bulkBlockWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'all' => 'sometimes|boolean',
            'user_ids' => 'required_without:all|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        if ($request->boolean('all')) {
            $baseQuery = $this->applyUserFilters($request, User::query());
            $total = (clone $baseQuery)->count();
            if ($total === 0) {
                return response()->json(['success' => false, 'message' => 'No users match current filters'], 422);
            }
            $baseQuery->select('id')->orderBy('id')->chunkById(500, function ($chunk) {
                User::whereIn('id', $chunk->pluck('id'))->update(['withdrawal_blocked' => true]);
            });
        } else {
            User::whereIn('id', $validated['user_ids'])->update(['withdrawal_blocked' => true]);
        }

        return response()->json(['success' => true, 'message' => 'Withdrawals have been blocked for selected users']);
    }

    /**
     * Bulk: Unblock withdrawals for selected users
     */
    public function bulkUnblockWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'all' => 'sometimes|boolean',
            'user_ids' => 'required_without:all|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        if ($request->boolean('all')) {
            $baseQuery = $this->applyUserFilters($request, User::query());
            $total = (clone $baseQuery)->count();
            if ($total === 0) {
                return response()->json(['success' => false, 'message' => 'No users match current filters'], 422);
            }
            $baseQuery->select('id')->orderBy('id')->chunkById(500, function ($chunk) {
                User::whereIn('id', $chunk->pluck('id'))->update(['withdrawal_blocked' => false]);
            });
        } else {
            User::whereIn('id', $validated['user_ids'])->update(['withdrawal_blocked' => false]);
        }

        return response()->json(['success' => true, 'message' => 'Withdrawals have been unblocked for selected users']);
    }

    /**
     * Bulk: Send email to selected users using an existing template key
     */
    public function bulkSendEmail(Request $request)
    {
        $validated = $request->validate([
            'all' => 'sometimes|boolean',
            'user_ids' => 'required_without:all|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'template_key' => 'required|string|exists:email_templates,key',
        ]);

        $template = EmailTemplate::getByKey($validated['template_key']);
        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template disabled or not found'], 422);
        }

        if ($request->boolean('all')) {
            $baseQuery = $this->applyUserFilters($request, User::query());
            $total = (clone $baseQuery)->count();
            if ($total === 0) {
                return response()->json(['success' => false, 'message' => 'No users match current filters'], 422);
            }

            $baseQuery->select('id','email','name')->orderBy('id')->chunkById(200, function ($chunk) use ($validated, $template) {
                foreach ($chunk as $u) {
                    try {
                        $data = $this->buildBulkEmailData($template, $u);
                        Mail::to($u->email)->send(new TemplatedMail(
                            $validated['template_key'],
                            $data,
                            $u->id,
                            'bulk_email'
                        ));
                    } catch (\Throwable $e) {
                        Log::error('Bulk email failed for user', ['user_id' => $u->id, 'error' => $e->getMessage()]);
                    }
                }
            });
        } else {
            $users = User::whereIn('id', $validated['user_ids'])->get(['id','email','name']);
            foreach ($users as $u) {
                try {
                    $data = $this->buildBulkEmailData($template, $u);
                    Mail::to($u->email)->send(new TemplatedMail(
                        $validated['template_key'],
                        $data,
                        $u->id,
                        'bulk_email'
                    ));
                } catch (\Throwable $e) {
                    Log::error('Bulk email failed for user', ['user_id' => $u->id, 'error' => $e->getMessage()]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Emails have been queued']);
    }

    /**
     * Toggle favorite status for a user (admin's personal favorite)
     */
    public function toggleFavorite(User $user)
    {
        // Check if table exists
        if (!\Illuminate\Support\Facades\Schema::hasTable('admin_favorite_users')) {
            return response()->json([
                'success' => false,
                'message' => 'Favorites feature is not available yet. Please run migrations.',
            ], 503);
        }

        $adminId = Auth::id();

        $exists = \DB::table('admin_favorite_users')
            ->where('admin_id', $adminId)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            \DB::table('admin_favorite_users')
                ->where('admin_id', $adminId)
                ->where('user_id', $user->id)
                ->delete();

            return response()->json([
                'success' => true,
                'is_favorite' => false,
                'message' => 'User removed from favorites',
            ]);
        } else {
            \DB::table('admin_favorite_users')->insert([
                'admin_id' => $adminId,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'is_favorite' => true,
                'message' => 'User added to favorites',
            ]);
        }
    }

    /**
     * Bulk: Delete selected users (SUPER ADMIN ONLY)
     */
    public function bulkDelete(Request $request)
    {
        // Check if current user is super admin
        if (!Auth::user()->is_super_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Only super admins can delete users',
            ], 403);
        }

        $validated = $request->validate([
            'all' => 'sometimes|boolean',
            'user_ids' => 'required_without:all|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'force' => 'sometimes|boolean',
        ]);

        $force = $request->boolean('force', false);
        $currentAdminId = Auth::id();
        $deletedCount = 0;
        $skippedCount = 0;
        $errors = [];

        if ($request->boolean('all')) {
            $baseQuery = $this->applyUserFilters($request, User::query());
            $total = (clone $baseQuery)->count();

            if ($total === 0) {
                return response()->json(['success' => false, 'message' => 'No users match current filters'], 422);
            }

            $baseQuery->select('id')->orderBy('id')->chunkById(100, function ($chunk) use ($currentAdminId, $force, &$deletedCount, &$skippedCount, &$errors) {
                foreach ($chunk as $u) {
                    $user = User::find($u->id);
                    if (!$user) {
                        continue;
                    }

                    // Skip deleting own account
                    if ($user->id === $currentAdminId) {
                        $skippedCount++;
                        $errors[] = "Skipped user #{$user->id}: Cannot delete your own account";
                        continue;
                    }

                    // Check for active stakes
                    $activeStakesCount = $user->stakingDeposits()->where('status', 'active')->count();
                    if (!$force && $activeStakesCount > 0) {
                        $skippedCount++;
                        $errors[] = "Skipped user #{$user->id} ({$user->email}): {$activeStakesCount} active stake(s)";
                        continue;
                    }

                    try {
                        DB::beginTransaction();
                        $user->delete();
                        DB::commit();
                        $deletedCount++;
                        Log::info('User bulk deleted by super admin', [
                            'user_id' => $user->id,
                            'user_email' => $user->email,
                            'admin_id' => $currentAdminId,
                            'forced' => $force,
                        ]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $skippedCount++;
                        $errors[] = "Failed to delete user #{$user->id} ({$user->email}): {$e->getMessage()}";
                        Log::error('Bulk delete failed for user', [
                            'user_id' => $user->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            });
        } else {
            $ids = $validated['user_ids'];
            $users = User::whereIn('id', $ids)->get();

            foreach ($users as $user) {
                // Skip deleting own account
                if ($user->id === $currentAdminId) {
                    $skippedCount++;
                    $errors[] = "Skipped user #{$user->id}: Cannot delete your own account";
                    continue;
                }

                // Check for active stakes
                $activeStakesCount = $user->stakingDeposits()->where('status', 'active')->count();
                if (!$force && $activeStakesCount > 0) {
                    $skippedCount++;
                    $errors[] = "Skipped user #{$user->id} ({$user->email}): {$activeStakesCount} active stake(s)";
                    continue;
                }

                try {
                    DB::beginTransaction();
                    $user->delete();
                    DB::commit();
                    $deletedCount++;
                    Log::info('User bulk deleted by super admin', [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'admin_id' => $currentAdminId,
                        'forced' => $force,
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    $skippedCount++;
                    $errors[] = "Failed to delete user #{$user->id} ({$user->email}): {$e->getMessage()}";
                    Log::error('Bulk delete failed for user', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $message = "Deleted {$deletedCount} user(s)";
        if ($skippedCount > 0) {
            $message .= ", skipped {$skippedCount} user(s)";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'deleted' => $deletedCount,
            'skipped' => $skippedCount,
            'errors' => $errors,
        ]);
    }

    /**
     * Build data array for bulk email using template variables with sane defaults
     */
    private function buildBulkEmailData(EmailTemplate $template, User $user): array
    {
        $vars = $template->variables ?? [];
        $data = [];
        foreach ($vars as $key => $desc) {
            switch ($key) {
                case 'userName':
                    $data[$key] = $user->name ?: $user->email;
                    break;
                case 'amount':
                case 'principalAmount':
                case 'profitAmount':
                case 'totalAmount':
                    $data[$key] = 0;
                    break;
                case 'code':
                    $data[$key] = (string) random_int(100000, 999999);
                    break;
                case 'days':
                    $data[$key] = 30;
                    break;
                case 'percentage':
                    $data[$key] = 10.5;
                    break;
                case 'autoRenewal':
                    $data[$key] = false;
                    break;
                case 'endDate':
                    $data[$key] = now()->addDays(1)->format('M d, Y');
                    break;
                case 'reason':
                case 'comment':
                    $data[$key] = '';
                    break;
                default:
                    $data[$key] = '';
            }
        }
        // Base defaults to reduce risk of undefined variables even if template->variables is empty
        $baseDefaults = [
            'userName' => $user->name ?: $user->email,
            'amount' => 0,
            'principalAmount' => 0,
            'profitAmount' => 0,
            'totalAmount' => 0,
            'code' => (string) random_int(100000, 999999),
            'days' => 30,
            'percentage' => 10.5,
            'autoRenewal' => false,
            'endDate' => now()->addDays(1)->format('M d, Y'),
            'reason' => '',
            'comment' => '',
        ];
        foreach ($baseDefaults as $k => $v) {
            if (!array_key_exists($k, $data)) {
                $data[$k] = $v;
            }
        }
        return $data;
    }
}
