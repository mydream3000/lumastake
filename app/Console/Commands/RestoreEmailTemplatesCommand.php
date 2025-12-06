<?php

namespace App\Console\Commands;

use App\Models\EmailSetting;
use App\Models\EmailTemplate;
use Illuminate\Console\Command;

class RestoreEmailTemplatesCommand extends Command
{
    protected $signature = 'email-templates:restore {--force : Force restore even if templates exist}';
    protected $description = 'Restore email templates from seeder';

    public function handle()
    {
        $this->info('Restoring email templates...');

        try {
            // Create email settings singleton
            EmailSetting::firstOrCreate(
                ['id' => 1],
                [
                    'sender_email' => 'no-reply@arbitex.io',
                    'sender_name' => 'Arbitex',
                    'support_email' => 'support@arbitex.io',
                    'footer_text' => '© '.date('Y').' Arbitex. All rights reserved.',
                    'footer_support' => true,
                ]
            );

            $this->info('Email settings created/updated.');

            $templates = $this->getTemplates();
            $created = 0;
            $updated = 0;

            foreach ($templates as $template) {
                $exists = EmailTemplate::where('key', $template['key'])->exists();

                if ($exists && !$this->option('force')) {
                    $this->line("Skipped: {$template['name']} (already exists)");
                    continue;
                }

                EmailTemplate::updateOrCreate(
                    ['key' => $template['key']],
                    $template
                );

                if ($exists) {
                    $updated++;
                    $this->info("Updated: {$template['name']}");
                } else {
                    $created++;
                    $this->info("Created: {$template['name']}");
                }
            }

            $this->newLine();
            $this->info("Done! Created: {$created}, Updated: {$updated}");

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return 1;
        }
    }

    private function getTemplates(): array
    {
        return [
            // KYC Approved
            [
                'key' => 'kyc_approved',
                'name' => 'KYC Approved',
                'subject' => 'Your identity verification was approved',
                'content' => $this->getKycApprovedContent(),
                'variables' => [
                    'userName' => 'User name',
                    'decision' => 'Decision label',
                ],
            ],

            // KYC Declined
            [
                'key' => 'kyc_declined',
                'name' => 'KYC Declined',
                'subject' => 'Your identity verification was declined',
                'content' => $this->getKycDeclinedContent(),
                'variables' => [
                    'userName' => 'User name',
                    'decision' => 'Decision label',
                ],
            ],

            // KYC Started
            [
                'key' => 'kyc_started',
                'name' => 'KYC Started',
                'subject' => 'You started identity verification',
                'content' => $this->getKycStartedContent(),
                'variables' => [
                    'userName' => 'User name',
                ],
            ],

            // KYC Submitted
            [
                'key' => 'kyc_submitted',
                'name' => 'KYC Submitted',
                'subject' => 'Your verification has been submitted',
                'content' => $this->getKycSubmittedContent(),
                'variables' => [
                    'userName' => 'User name',
                ],
            ],

            // Staking Expiring Soon
            [
                'key' => 'staking_expiring_soon',
                'name' => 'Staking Expiring Soon',
                'subject' => 'Your Staking Ends Tomorrow',
                'content' => $this->getStakingExpiringSoonContent(),
                'variables' => [
                    'userName' => 'User name',
                    'amount' => 'Staking amount',
                    'days' => 'Staking period',
                    'percentage' => 'APR percentage',
                    'profitAmount' => 'Expected profit',
                    'endDate' => 'End date',
                    'autoRenewal' => 'Auto-renewal status (true/false)',
                ],
            ],

            // Staking Completed
            [
                'key' => 'staking_completed',
                'name' => 'Staking Completed',
                'subject' => 'Staking Completed Successfully',
                'content' => file_get_contents(resource_path('views/emails/staking-completed.blade.php')),
                'variables' => [
                    'userName' => 'User name',
                    'principalAmount' => 'Principal amount',
                    'profitAmount' => 'Profit earned',
                    'totalAmount' => 'Total amount',
                    'days' => 'Staking period',
                    'percentage' => 'APR percentage',
                    'autoRenewal' => 'Auto-renewal status',
                ],
            ],

            // Password Reset
            [
                'key' => 'password_reset',
                'name' => 'Password Reset Code',
                'subject' => 'Password Reset Code',
                'content' => file_get_contents(resource_path('views/emails/password-reset-code.blade.php')),
                'variables' => [
                    'code' => 'Reset code',
                    'userName' => 'User name',
                ],
            ],

            // Email Verification
            [
                'key' => 'email_verification',
                'name' => 'Email Verification Code',
                'subject' => 'Email Verification Code',
                'content' => file_get_contents(resource_path('views/emails/email-verification-code.blade.php')),
                'variables' => [
                    'code' => 'Verification code',
                    'userName' => 'User name',
                ],
            ],

            // Withdraw Code
            [
                'key' => 'withdraw_code',
                'name' => 'Withdrawal Verification Code',
                'subject' => 'Withdrawal Verification Code',
                'content' => file_get_contents(resource_path('views/emails/withdraw-code.blade.php')),
                'variables' => [
                    'code' => 'Verification code',
                    'userName' => 'User name',
                ],
            ],

            // Deposit Replenished
            [
                'key' => 'deposit_replenished',
                'name' => 'Deposit Successful',
                'subject' => 'Deposit Successful - Arbitex',
                'content' => file_get_contents(resource_path('views/emails/deposit_replenished.blade.php')),
                'variables' => [
                    'userName' => 'User name',
                    'amount' => 'Deposit amount',
                ],
            ],

            // Withdrawal Created
            [
                'key' => 'withdrawal_created',
                'name' => 'Withdrawal Request Created',
                'subject' => 'Withdrawal Request Received - Arbitex',
                'content' => file_get_contents(resource_path('views/emails/withdrawal-created.blade.php')),
                'variables' => [
                    'userName' => 'User name',
                    'amount' => 'Withdrawal amount',
                ],
            ],

            // Withdrawal Approved
            [
                'key' => 'withdrawal_approved',
                'name' => 'Withdrawal Approved',
                'subject' => 'Withdrawal Approved - Arbitex',
                'content' => file_get_contents(resource_path('views/emails/withdrawal-approved.blade.php')),
                'variables' => [
                    'userName' => 'User name',
                    'amount' => 'Withdrawal amount',
                ],
            ],

            // Withdrawal Rejected
            [
                'key' => 'withdrawal_rejected',
                'name' => 'Withdrawal Rejected',
                'subject' => 'Withdrawal Rejected - Arbitex',
                'content' => file_get_contents(resource_path('views/emails/withdraw-rejected.blade.php')),
                'variables' => [
                    'userName' => 'User name',
                    'amount' => 'Withdrawal amount',
                    'reason' => 'Rejection reason',
                ],
            ],

            // Mass Credit Notification
            [
                'key' => 'mass_credit_notification',
                'name' => 'Mass Credit Notification',
                'subject' => 'Funds credited to your balance',
                'content' => $this->getMassCreditNotificationContent(),
                'variables' => [
                    'userName' => 'User name',
                    'amount' => 'Credited amount',
                    'comment' => 'Admin comment',
                ],
            ],
        ];
    }

    private function getStakingExpiringSoonContent(): string
    {
        return <<<'HTML'
@extends('emails.layouts.base')

@section('title', 'Your Staking Ends Tomorrow')

@section('content')
<!-- Content Section -->
<tr>
    <td style="padding: 0 40px 20px;">
        <p style="color: #ffffff; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #cccccc; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            Your staking period is ending soon! Your {{ $days }}-day staking of <strong style="color: #4FD1C5;">${{ number_format($amount, 2) }}</strong> will complete on <strong>{{ $endDate }}</strong>.
        </p>
    </td>
</tr>

<!-- Staking Summary -->
<tr>
    <td style="padding: 0 40px 20px;">
        <table cellpadding="0" cellspacing="0" width="100%" style="background: rgba(255, 255, 255, 0.05); border-radius: 8px; overflow: hidden;">
            <tr>
                <td style="padding: 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                    <p style="color: #999999; font-size: 13px; margin: 0 0 6px;">Staked Amount</p>
                    <p style="color: #ffffff; font-size: 20px; font-weight: 600; margin: 0;">${{ number_format($amount, 2) }}</p>
                </td>
            </tr>
            <tr>
                <td style="padding: 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                    <p style="color: #999999; font-size: 13px; margin: 0 0 6px;">Expected Profit ({{ $percentage }}%)</p>
                    <p style="color: #4FD1C5; font-size: 20px; font-weight: 600; margin: 0;">${{ number_format($profitAmount, 2) }}</p>
                </td>
            </tr>
            <tr>
                <td style="padding: 20px;">
                    <p style="color: #999999; font-size: 13px; margin: 0 0 6px;">Auto-Renewal Status</p>
                    @if($autoRenewal)
                        <p style="color: #4FD1C5; font-size: 16px; font-weight: 600; margin: 0;">✓ Enabled</p>
                    @else
                        <p style="color: #FF451C; font-size: 16px; font-weight: 600; margin: 0;">✗ Disabled</p>
                    @endif
                </td>
            </tr>
        </table>
    </td>
</tr>

<!-- Important Notice -->
<tr>
    <td style="padding: 0 40px 30px;">
        @if($autoRenewal)
            <div style="background: rgba(79, 209, 197, 0.1); border-left: 3px solid #4FD1C5; border-radius: 4px; padding: 14px 16px;">
                <p style="color: #ffffff; font-size: 14px; line-height: 1.6; margin: 0;">
                    <strong style="color: #4FD1C5;">Auto-Renewal is Active:</strong> Your principal will be automatically restaked for another {{ $days }} days. Your profit will be added to your available balance. You can disable auto-renewal anytime in your dashboard.
                </p>
            </div>
        @else
            <div style="background: rgba(255, 69, 28, 0.1); border-left: 3px solid #FF451C; border-radius: 4px; padding: 14px 16px;">
                <p style="color: #ffffff; font-size: 14px; line-height: 1.6; margin: 0;">
                    <strong style="color: #FF451C;">Auto-Renewal is Disabled:</strong> Your funds (principal + profit) will be returned to your balance tomorrow. If you'd like to continue staking, please enable Auto-Renewal in your dashboard before the staking period ends.
                </p>
            </div>
        @endif
    </td>
</tr>

<!-- Call to Action -->
<tr>
    <td align="center" style="padding: 0 40px 30px;">
        <a href="{{ config('app.url') }}/dashboard/staking" style="display: inline-block; background: linear-gradient(135deg, #4FD1C5 0%, #3BA89F 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 15px; font-weight: 600;">
            Manage Staking Settings
        </a>
    </td>
</tr>

<!-- Footer Notice -->
<tr>
    <td style="padding: 0 40px 40px;">
        <p style="margin: 0; color: #8f8f8f; font-size: 13px; line-height: 1.6;">
            Thank you for choosing Arbitex for your staking needs. If you have any questions, please contact our support team.
        </p>
    </td>
</tr>
@endsection
HTML;
    }

    private function getKycApprovedContent(): string
    {
        return <<<'HTML'
@extends('emails.layouts.base')

@section('title', 'Identity Verification Approved')

@section('content')
<tr>
    <td style="padding: 0 40px 20px;">
        <p style="color: #ffffff; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #cccccc; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            Great news! Your identity verification has been <strong style="color:#4FD1C5;">{{ $decision }}</strong>.
        </p>
        <div style="background: rgba(79, 209, 197, 0.1); border-left: 3px solid #4FD1C5; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
            <p style="color: #ffffff; font-size: 14px; margin: 0;">
                You now have access to all features requiring verification.
            </p>
        </div>
    </td>
</tr>
@endsection
HTML;
    }

    private function getKycDeclinedContent(): string
    {
        return <<<'HTML'
@extends('emails.layouts.base')

@section('title', 'Identity Verification Declined')

@section('content')
<tr>
    <td style="padding: 0 40px 20px;">
        <p style="color: #ffffff; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #cccccc; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            Unfortunately, your identity verification was <strong style="color:#FF451C;">{{ $decision }}</strong>.
        </p>
        <div style="background: rgba(255, 69, 28, 0.1); border-left: 3px solid #FF451C; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
            <p style="color: #ffffff; font-size: 14px; margin: 0;">
                You can try again by re-submitting your documents. If you need assistance, please contact our support team.
            </p>
        </div>
    </td>
</tr>
@endsection
HTML;
    }

    private function getKycStartedContent(): string
    {
        return <<<'HTML'
@extends('emails.layouts.base')

@section('title', 'Identity Verification Started')

@section('content')
<tr>
    <td style="padding: 0 40px 20px;">
        <p style="color: #ffffff; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #cccccc; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            You have started the identity verification process. Please complete all steps in the opened window.
        </p>
        <div style="background: rgba(255, 255, 255, 0.05); border-left: 3px solid #4FD1C5; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
            <p style="color: #ffffff; font-size: 14px; margin: 0;">
                If the window was closed, you can restart verification from your profile page.
            </p>
        </div>
    </td>
</tr>
@endsection
HTML;
    }

    private function getKycSubmittedContent(): string
    {
        return <<<'HTML'
@extends('emails.layouts.base')

@section('title', 'Verification Submitted')

@section('content')
<tr>
    <td style="padding: 0 40px 20px;">
        <p style="color: #ffffff; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #cccccc; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            Your identity verification has been submitted. Our team will review your information shortly.
        </p>
        <div style="background: rgba(255, 255, 255, 0.05); border-left: 3px solid #4FD1C5; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
            <p style="color: #ffffff; font-size: 14px; margin: 0;">
                We will notify you by email once a decision has been made. You can also check your status in the profile.
            </p>
        </div>
    </td>
</tr>
@endsection
HTML;
    }

    private function getMassCreditNotificationContent(): string
    {
        return <<<'HTML'
@extends('emails.layouts.base')

@section('title', 'Funds credited to your balance')

@section('content')
<tr>
    <td style="padding: 0 40px 20px;">
        <p style="color: #ffffff; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #cccccc; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            We have credited <strong style="color:#4FD1C5;">${{ $amount }}</strong> to your account balance.
        </p>
        @if(!empty($comment))
            <div style="background: rgba(255, 255, 255, 0.05); border-left: 3px solid #4FD1C5; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
                <p style="color: #ffffff; font-size: 14px; margin: 0;">
                    Comment from administrator: {{ $comment }}
                </p>
            </div>
        @endif
    </td>
</tr>
@endsection
HTML;
    }
}
