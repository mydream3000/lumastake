<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailTemplate;

class RestoreDepositTemplateCommand extends Command
{
    protected $signature = 'app:restore-deposit-template';
    protected $description = 'Restores the default email template for deposit confirmation';

    public function handle()
    {
        $this->info('Restoring deposit confirmation email template...');

        EmailTemplate::updateOrCreate(
            ['key' => 'deposit_confirmed'],
            [
                'name' => 'Deposit Confirmed',
                'subject' => 'Your Deposit has been Confirmed',
                'sender_name' => 'Arbitex',
                'content' => '<!DOCTYPE html><html><body><h1>Deposit Confirmed</h1><p>Hello {{ $userName }},</p><p>Your deposit of ${{ $amount }} has been successfully confirmed and added to your balance.</p><p>Thank you for using Arbitex!</p></body></html>',
                'variables' => [
                    'userName' => 'User Name',
                    'amount' => 'Deposit Amount',
                ],
                'enabled' => true,
            ]
        );

        $this->info('Deposit confirmation email template has been restored successfully.');
        return 0;
    }
}
