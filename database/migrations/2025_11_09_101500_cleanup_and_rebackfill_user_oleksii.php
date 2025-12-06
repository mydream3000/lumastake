<?php

use App\Models\User;
use App\Models\Tier;
use App\Models\TierPercentage;
use App\Models\Transaction;
use App\Models\StakingDeposit;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $email = 'yosya1989@gmail.com';

            /** @var User|null $user */
            $user = User::query()->where('email', $email)->first();
            if (!$user) {
                // If user not found, skip silently to avoid breaking deploy
                return;
            }

            // 1) Preflight: ensure needed tiers and percentages exist
            $requirements = [
                ['tier' => 'Starter', 'days' => 10],
                ['tier' => 'Bronze',  'days' => 30],
                ['tier' => 'Bronze',  'days' => 60],
                ['tier' => 'Gold',    'days' => 90],
            ];
            foreach ($requirements as $req) {
                $tier = Tier::query()->where('name', $req['tier'])->first();
                if (!$tier) {
                    return; // skip silently if config absent
                }
                $hasPercent = TierPercentage::query()
                    ->where('tier_id', $tier->id)
                    ->where('days', $req['days'])
                    ->exists();
                if (!$hasPercent) {
                    return; // skip silently if config absent
                }
            }

            // 2) Full cleanup of user's history (per request)
            Transaction::query()->where('user_id', $user->id)->delete();
            StakingDeposit::query()->where('user_id', $user->id)->delete();

            // Helpers
            $getPercentage = function (int $tierId, int $days): float {
                $p = TierPercentage::query()
                    ->where('tier_id', $tierId)
                    ->where('days', $days)
                    ->value('percentage');
                if ($p === null) {
                    throw new \RuntimeException("Tier percentage not found for tier_id={$tierId}, days={$days}");
                }
                return (float)$p;
            };

            $insertTx = function (array $attrs): Transaction {
                $tx = new Transaction();
                $tx->timestamps = false; // keep provided created_at/updated_at
                $tx->forceFill($attrs)->save();
                return $tx;
            };

            $insertStaking = function (array $attrs): StakingDeposit {
                $sd = new StakingDeposit();
                $sd->timestamps = false; // keep provided created_at/updated_at
                $sd->forceFill($attrs)->save();
                return $sd;
            };

            $createStakingPack = function (array $opts) use ($user, $getPercentage, $insertTx, $insertStaking) {
                $amount       = (float)$opts['amount'];
                $tierName     = (string)$opts['tier'];
                $days         = (int)$opts['days'];
                $startDateStr = (string)$opts['start_date']; // 'Y-m-d'
                $addProfitTx  = (bool)($opts['add_profit_tx'] ?? true);
                $note         = (string)($opts['note'] ?? 'manual backfill');

                $tier = Tier::query()->where('name', $tierName)->first();
                if (!$tier) {
                    throw new \RuntimeException("Tier '{$tierName}' not found");
                }

                $start = Carbon::parse($startDateStr)->setTime(10, 0, 0);
                $end   = (clone $start)->addDays($days)->setTime(10, 0, 0);
                $now   = Carbon::now('UTC');
                $isCompleted = $end->lte($now);

                $percentage = $getPercentage($tier->id, $days);
                $profit = round($amount * $percentage / 100, 2);

                // 1) DEPOSIT (exact historical timestamps)
                $insertTx([
                    'user_id'     => $user->id,
                    'type'        => 'deposit',
                    'status'      => 'confirmed',
                    'amount'      => $amount,
                    'description' => "Deposit before staking {$tierName} {$days}d",
                    'meta'        => [
                        'source' => 'migration: backfill Oleksii',
                        'note'   => $note,
                    ],
                    'created_at'  => $start->copy()->subMinutes(5),
                    'updated_at'  => $start->copy()->subMinutes(5),
                ]);

                // 2) STAKING_DEPOSIT
                $staking = $insertStaking([
                    'user_id'       => $user->id,
                    'tier_id'       => $tier->id,
                    'amount'        => $amount,
                    'days'          => $days,
                    'percentage'    => $percentage,
                    'earned_profit' => $isCompleted ? $profit : 0,
                    'start_date'    => $start->toDateString(),
                    'end_date'      => $end->toDateString(),
                    'status'        => $isCompleted ? 'completed' : 'active',
                    'auto_renewal'  => false,
                    'created_at'    => $start,
                    'updated_at'    => $start,
                ]);

                // 3) STAKING transaction
                $insertTx([
                    'user_id'     => $user->id,
                    'type'        => 'staking',
                    'status'      => 'confirmed',
                    'amount'      => $amount,
                    'description' => "Staking {$tierName} {$days}d",
                    'meta'        => [
                        'staking_deposit_id' => $staking->id,
                        'source' => 'migration: backfill Oleksii',
                    ],
                    'created_at'  => $start,
                    'updated_at'  => $start,
                ]);

                // 4) PROFIT transaction (if completed)
                if ($isCompleted && $addProfitTx) {
                    $insertTx([
                        'user_id'     => $user->id,
                        'type'        => 'profit',
                        'status'      => 'confirmed',
                        'amount'      => $profit,
                        'description' => "Profit for staking #{$staking->id} ({$tierName} {$days}d)",
                        'meta'        => [
                            'staking_deposit_id' => $staking->id,
                            'source' => 'migration: backfill Oleksii',
                        ],
                        'created_at'  => $end,
                        'updated_at'  => $end,
                    ]);
                }
            };

            // ====== Scenario 1: Starter, 300, two cycles (10d each) ======
            // 1/2: 04.06.2025 -> 14.06.2025
            $createStakingPack([
                'amount'     => 300,
                'tier'       => 'Starter',
                'days'       => 10,
                'start_date' => '2025-06-04',
                'note'       => 'Starter cycle 1/2',
            ]);
            // 2/2: 14.06.2025 -> 24.06.2025
            $createStakingPack([
                'amount'     => 300,
                'tier'       => 'Starter',
                'days'       => 10,
                'start_date' => '2025-06-14',
                'note'       => 'Starter cycle 2/2',
            ]);
            // Withdrawal after two Starter cycles: 312 — 24.06.2025
            $withdraw1At = Carbon::parse('2025-06-24')->setTime(15, 0, 0);
            $insertTx([
                'user_id'     => $user->id,
                'type'        => 'withdraw',
                'status'      => 'confirmed',
                'amount'      => 312,
                'description' => 'Withdrawal after two Starter cycles',
                'meta'        => [ 'source' => 'migration: backfill Oleksii' ],
                'created_at'  => $withdraw1At,
                'updated_at'  => $withdraw1At,
            ]);

            // ====== Scenario 2: Bronze, 1000, 30 days ======
            $createStakingPack([
                'amount'     => 1000,
                'tier'       => 'Bronze',
                'days'       => 30,
                'start_date' => '2025-06-27',
                'note'       => 'Bronze 30d',
            ]);
            $withdraw2At = Carbon::parse('2025-07-28')->setTime(12, 0, 0);
            $insertTx([
                'user_id'     => $user->id,
                'type'        => 'withdraw',
                'status'      => 'confirmed',
                'amount'      => 1100,
                'description' => 'Withdrawal after Bronze 30d',
                'meta'        => [ 'source' => 'migration: backfill Oleksii' ],
                'created_at'  => $withdraw2At,
                'updated_at'  => $withdraw2At,
            ]);

            // ====== Scenario 3: Bronze, 2300, 60 days ======
            $createStakingPack([
                'amount'     => 2300,
                'tier'       => 'Bronze',
                'days'       => 60,
                'start_date' => '2025-07-29',
                'note'       => 'Bronze 60d',
            ]);
            $withdraw3At = Carbon::parse('2025-09-30')->setTime(12, 0, 0);
            $insertTx([
                'user_id'     => $user->id,
                'type'        => 'withdraw',
                'status'      => 'confirmed',
                'amount'      => 2800,
                'description' => 'Withdrawal after Bronze 60d',
                'meta'        => [ 'source' => 'migration: backfill Oleksii' ],
                'created_at'  => $withdraw3At,
                'updated_at'  => $withdraw3At,
            ]);

            // ====== Scenario 4: Gold, 27000, 90 days (active depending on run date) ======
            $createStakingPack([
                'amount'       => 27000,
                'tier'         => 'Gold',
                'days'         => 90,
                'start_date'   => '2025-10-03',
                'note'         => 'Gold 90d',
                'add_profit_tx'=> true,
            ]);
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $email = 'yosya1989@gmail.com';
            $user = User::query()->where('email', $email)->first();
            if (!$user) { return; }

            // Remove only the data created by the backfill (cannot restore previous user data)
            Transaction::query()
                ->where('user_id', $user->id)
                ->where(function ($q) {
                    $q->where('meta->source', 'migration: backfill Oleksii')
                      ->orWhereIn('created_at', [
                          '2025-06-24 15:00:00',
                          '2025-07-28 12:00:00',
                          '2025-09-30 12:00:00',
                      ]);
                })
                ->delete();

            StakingDeposit::query()
                ->where('user_id', $user->id)
                ->whereIn('start_date', [
                    '2025-06-04',
                    '2025-06-14',
                    '2025-06-27',
                    '2025-07-29',
                    '2025-10-03',
                ])
                ->delete();
        });
    }
};
