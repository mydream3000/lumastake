<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class UsersExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    private function applyUserFilters($query)
    {
        $request = $this->request;
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

        return $query;
    }

    public function query()
    {
        $query = User::query();
        return $this->applyUserFilters($query);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Balance',
            'Real deposits',
            'Tier',
            'Referral Level',
            'Role',
            'Verification',
            'Status',
            'Withdraw',
        ];
    }

    public function map($user): array
    {
        // Note: 'real_deposits' is calculated on the fly in the controller for the view.
        // For a full export, this might need a more performant approach if the user base is large.
        // A simple calculation is added here for demonstration.
        $realDeposits = $user->cryptoTransactions()
            ->where('processed', true)
            ->whereIn('token', ['USDT', 'USDC'])
            ->sum('amount');

        return [
            $user->id,
            $user->name,
            $user->balance,
            $realDeposits,
            $user->current_tier,
            $user->referral_level_id,
            $user->is_admin ? 'Admin' : 'User',
            ucfirst($user->verification_status),
            $user->blocked ? 'Blocked' : 'Active',
            $user->withdrawal_blocked ? 'Blocked' : 'Allowed',
        ];
    }
}
