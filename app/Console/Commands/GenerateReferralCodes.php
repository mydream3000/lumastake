<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateReferralCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:generate-referral-codes
                            {--type= : Filter by account type (normal, islamic)}
                            {--force : Regenerate codes even for users who already have them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate referral codes for users without them (or all users with --force)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating referral codes...');

        // Build query
        $query = User::query();

        // Filter by account type if specified
        if ($this->option('type')) {
            $type = $this->option('type');
            if (!in_array($type, ['normal', 'islamic'])) {
                $this->error('Invalid account type. Use: normal or islamic');
                return 1;
            }
            $query->where('account_type', $type);
            $this->info("Filtering by account type: {$type}");
        }

        // Filter by users without referral_code (unless --force is used)
        if (!$this->option('force')) {
            $query->whereNull('referral_code');
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->warn('No users found matching criteria.');
            return 0;
        }

        $this->info("Found {$users->count()} user(s) to process.");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $updated = 0;
        $skipped = 0;

        foreach ($users as $user) {
            // Generate unique referral code
            $referralCode = $this->generateUniqueReferralCode();

            if ($referralCode) {
                $user->referral_code = $referralCode;
                $user->saveQuietly(); // Save without triggering observers
                $updated++;
            } else {
                $this->newLine();
                $this->warn("Failed to generate unique code for user ID: {$user->id}");
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info("✅ Referral codes generated successfully!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total processed', $users->count()],
                ['Updated', $updated],
                ['Skipped', $skipped],
            ]
        );

        // Show some examples
        if ($updated > 0) {
            $this->newLine();
            $this->info('📋 Examples of generated codes:');
            $examples = User::whereNotNull('referral_code')
                ->when($this->option('type'), fn($q) => $q->where('account_type', $this->option('type')))
                ->latest('updated_at')
                ->limit(5)
                ->get(['id', 'name', 'email', 'account_type', 'referral_code']);

            $this->table(
                ['ID', 'Name', 'Email', 'Type', 'Referral Code'],
                $examples->map(fn($u) => [
                    $u->id,
                    Str::limit($u->name, 20),
                    Str::limit($u->email, 30),
                    $u->account_type ?? 'N/A',
                    $u->referral_code,
                ])
            );
        }

        return 0;
    }

    /**
     * Generate unique referral code
     */
    private function generateUniqueReferralCode(): ?string
    {
        $maxAttempts = 10;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $code = Str::upper(Str::random(8));

            if (!User::where('referral_code', $code)->exists()) {
                return $code;
            }
        }

        return null; // Failed to generate unique code after max attempts
    }
}
