<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\PendingWithdrawal;
use App\Models\Transaction;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'google_id',
        'phone', 'country_code', 'phone_country', 'country', 'gender',
        'balance', 'deposited', 'current_tier',
        'referral_code', 'referred_by', 'active', 'referral_level_id',
        'blocked', 'withdrawal_blocked', 'failed_login_attempts', 'account_locked_at',
        'verification_status', 'verification_document_path',
        'email_verification_code', 'email_verification_code_expires_at',
        'new_email', 'email_change_code', 'email_change_code_expires_at',
        'login_2fa_code', 'login_2fa_code_expires_at', 'login_2fa_verified', 'login_2fa_last_verified_at',
        'account_type', 'account_type_changed_at',
        'is_admin', 'is_super_admin', 'is_closer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
            'is_closer' => 'boolean',
            'active' => 'boolean',
            'blocked' => 'boolean',
            'withdrawal_blocked' => 'boolean',
            'balance' => 'decimal:2',
            'deposited' => 'decimal:2',
            'email_verification_code_expires_at' => 'datetime',
            'email_change_code_expires_at' => 'datetime',
            'account_locked_at' => 'datetime',
            'login_2fa_code_expires_at' => 'datetime',
            'login_2fa_verified' => 'boolean',
            'login_2fa_last_verified_at' => 'datetime',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function stakingDeposits(): HasMany
    {
        return $this->hasMany(StakingDeposit::class);
    }

    public function cryptoAddresses(): HasMany
    {
        return $this->hasMany(CryptoAddress::class);
    }

    public function cryptoTransactions(): HasMany
    {
        return $this->hasMany(CryptoTransaction::class);
    }

    public function referralLevel(): BelongsTo
    {
        return $this->belongsTo(ReferralLevel::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(Earning::class);
    }

    /**
     * Closer notes left about this user
     */
    public function closerNotes(): HasMany
    {
        return $this->hasMany(CloserUserNote::class, 'user_id');
    }

    /**
     * Favorite users (for admins): users that this admin has marked as favorite
     */
    public function favoriteUsers()
    {
        return $this->belongsToMany(User::class, 'admin_favorite_users', 'admin_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Favorited by admins: admins who have marked this user as favorite
     */
    public function favoritedByAdmins()
    {
        return $this->belongsToMany(User::class, 'admin_favorite_users', 'user_id', 'admin_id')
            ->withTimestamps();
    }

    /**
     * Получить все профиты от стейкингов
     */
    public function profitEarnings(): HasMany
    {
        return $this->earnings()->where('type', 'profit');
    }

    /**
     * Получить все реферальные награды
     */
    public function referralRewardEarnings(): HasMany
    {
        return $this->earnings()->where('type', 'referral_reward');
    }

    /**
     * Получить общую сумму профита от стейкингов
     */
    public function getTotalProfitAttribute(): float
    {
        return (float) $this->earnings()->where('type', 'profit')->sum('amount');
    }

    /**
     * Получить общую сумму реферальных наград
     */
    public function getTotalReferralRewardsAttribute(): float
    {
        return (float) $this->earnings()->where('type', 'referral_reward')->sum('amount');
    }

    /**
     * Get the avatar URL (uploaded or default)
     */
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('images/default-avatar.svg');
    }

    /**
     * Get available balance for staking/operations:
     * user balance minus funds reserved in pending withdrawals (non-expired).
     */
    public function getAvailableBalanceAttribute(): float
    {
        $reservedPendingCodes = (float) PendingWithdrawal::where('user_id', $this->id)
            ->where('code_expires_at', '>=', now())
            ->sum('amount');

        $reservedPendingTransactions = (float) Transaction::where('user_id', $this->id)
            ->where('type', 'withdraw')
            ->where('status', 'pending')
            ->sum('amount');

        $reserved = $reservedPendingCodes + $reservedPendingTransactions;

        return max(0, (float) $this->balance - $reserved);
    }

    /**
     * Amount currently reserved by user's pending (non-expired) withdrawals.
     */
    public function getReservedByWithdrawalsAttribute(): float
    {
        $reservedPendingCodes = (float) PendingWithdrawal::where('user_id', $this->id)
            ->where('code_expires_at', '>=', now())
            ->sum('amount');

        $reservedPendingTransactions = (float) Transaction::where('user_id', $this->id)
            ->where('type', 'withdraw')
            ->where('status', 'pending')
            ->sum('amount');

        return $reservedPendingCodes + $reservedPendingTransactions;
    }

    /**
     * Virtual referral_level accessor for compatibility
     * IMPORTANT: Do not access $this->referralLevel as a dynamic property here to avoid PHP 8.3 undefined property errors.
     */
    public function getReferralLevelAttribute(): ?int
    {
        // Safely get related model if it is loaded
        $related = $this->getRelationValue('referralLevel');
        if ($related && isset($related->level)) {
            return (int) $related->level;
        }

        // Fallback to stored column on users table
        return $this->referral_level_id ? (int) $this->referral_level_id : null;
    }
}
