<?php

namespace Database\Seeders;

use App\Models\EmailSetting;
use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        // Create email settings singleton
        EmailSetting::firstOrCreate(
            ['id' => 1],
            [
                'sender_email' => 'no-reply@lumastake.com',
                'sender_name' => 'Lumastake',
                'support_email' => 'support@lumastake.com',
                'footer_text' => '© ' . date('Y') . ' Lumastake. All rights reserved.',
                'footer_support' => true,
            ]
        );

        $templates = [
            // Referral: Staking Created (to staker)
            [
                'key' => 'staking_created_notice',
                'name' => 'Staking Created Notice',
                'subject' => 'Your staking has been created successfully',
                'content' => $this->getStakingCreatedNoticeContent(),
                'variables' => [
                    'name' => 'User name',
                    'amount' => 'Staked amount',
                    'days' => 'Staking period (days)',
                    'percentage' => 'APR percentage',
                ],
            ],

            // Referral: Referral Staked (to referrer)
            [
                'key' => 'referral_staked_notice',
                'name' => 'Referral Staked Notice',
                'subject' => 'Your referral has started a stake',
                'content' => $this->getReferralStakedNoticeContent(),
                'variables' => [
                    'referrer_name' => 'Referrer name',
                    'referral_name' => 'Referral name',
                    'amount' => 'Staked amount',
                    'days' => 'Staking period (days)',
                    'percentage' => 'APR percentage',
                ],
            ],

            // Referral: Reward Received (to referrer)
            [
                'key' => 'referral_reward_received',
                'name' => 'Referral Reward Received',
                'subject' => 'You received a referral reward',
                'content' => $this->getReferralRewardReceivedContent(),
                'variables' => [
                    'referrer_name' => 'Referrer name',
                    'referral_name' => 'Referral name',
                    'profit' => 'Referral staking profit',
                    'reward_amount' => 'Reward amount',
                    'reward_percentage' => 'Reward percentage',
                    'staking_days' => 'Staking days',
                ],
            ],
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

            // KYC Started (new)
            [
                'key' => 'kyc_started',
                'name' => 'KYC Started',
                'subject' => 'You started identity verification',
                'content' => $this->getKycStartedContent(),
                'variables' => [
                    'userName' => 'User name',
                ],
            ],

            // KYC Submitted (new)
            [
                'key' => 'kyc_submitted',
                'name' => 'KYC Submitted',
                'subject' => 'Your verification has been submitted',
                'content' => $this->getKycSubmittedContent(),
                'variables' => [
                    'userName' => 'User name',
                ],
            ],
            // Staking Expiring Soon (NEW)
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
                'subject' => 'Deposit Successful - Lumastake',
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
                'subject' => 'Withdrawal Request Received - Lumastake',
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
                'subject' => 'Withdrawal Approved - Lumastake',
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
                'subject' => 'Withdrawal Rejected - Lumastake',
                'content' => file_get_contents(resource_path('views/emails/withdraw-rejected.blade.php')),
                'variables' => [
                    'userName' => 'User name',
                    'amount' => 'Withdrawal amount',
                    'reason' => 'Rejection reason',
                ],
            ],

            // Account Locked
            [
                'key' => 'account_locked',
                'name' => 'Account Locked',
                'subject' => 'Account Temporarily Locked - Security Alert',
                'content' => file_get_contents(resource_path('views/emails/account-locked.blade.php')),
                'variables' => [
                    'userName' => 'User name',
                    'lockedAt' => 'Lock timestamp',
                ],
            ],

            // Password Code Remainder (Login 2FA)
            [
                'key' => 'password_code_remainder',
                'name' => 'Password Code Remainder',
                'subject' => 'Login Verification Code',
                'content' => $this->getPasswordCodeRemainderContent(),
                'variables' => [
                    'userName' => 'User name',
                    'code' => '6-digit verification code',
                ],
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['key' => $template['key']],
                $template
            );
        }

        // Account Type Changed
        EmailTemplate::updateOrCreate(
            ['key' => 'account_type_changed'],
            [
                'key' => 'account_type_changed',
                'name' => 'Account Type Changed',
                'subject' => 'Account Type Changed Successfully',
                'content' => $this->getAccountTypeChangedContent(),
                'variables' => [
                    'userName' => 'User name',
                    'newAccountType' => 'New account type',
                ],
            ]
        );

        // Additional: Mass Credit Notification template (for bulk accruals)
        EmailTemplate::updateOrCreate(
            ['key' => 'mass_credit_notification'],
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
            ]
        );
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
        <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            Your staking period is ending soon! Your {{ $days }}-day staking of <strong style="color: #4da3ff;">${{ number_format($amount, 2) }}</strong> will complete on <strong>{{ $endDate }}</strong>.
        </p>
    </td>
</tr>

<!-- Staking Summary -->
<tr>
    <td style="padding: 0 40px 20px;">
        <table cellpadding="0" cellspacing="0" width="100%" style="background: #f9fafb; border-radius: 8px; overflow: hidden;">
            <tr>
                <td style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
                    <p style="color: #000000; font-size: 13px; margin: 0 0 6px;">Staked Amount</p>
                    <p style="color: #000000; font-size: 20px; font-weight: 600; margin: 0;">${{ number_format($amount, 2) }}</p>
                </td>
            </tr>
            <tr>
                <td style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
                    <p style="color: #000000; font-size: 13px; margin: 0 0 6px;">Expected Profit ({{ $percentage }}%)</p>
                    <p style="color: #4da3ff; font-size: 20px; font-weight: 600; margin: 0;">${{ number_format($profitAmount, 2) }}</p>
                </td>
            </tr>
            <tr>
                <td style="padding: 20px;">
                    <p style="color: #000000; font-size: 13px; margin: 0 0 6px;">Auto-Renewal Status</p>
                    @if($autoRenewal)
                        <p style="color: #4da3ff; font-size: 16px; font-weight: 600; margin: 0;">✓ Enabled</p>
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
            <div style="background: #f0f7ff; border-left: 3px solid #4da3ff; border-radius: 4px; padding: 14px 16px;">
                <p style="color: #000000; font-size: 14px; line-height: 1.6; margin: 0;">
                    <strong style="color: #4da3ff;">Auto-Renewal is Active:</strong> Your principal will be automatically restaked for another {{ $days }} days. Your profit will be added to your available balance. You can disable auto-renewal anytime in your dashboard.
                </p>
            </div>
        @else
            <div style="background: #fef2f2; border-left: 3px solid #FF451C; border-radius: 4px; padding: 14px 16px;">
                <p style="color: #000000; font-size: 14px; line-height: 1.6; margin: 0;">
                    <strong style="color: #FF451C;">Auto-Renewal is Disabled:</strong> Your funds (principal + profit) will be returned to your balance tomorrow. If you'd like to continue staking, please enable Auto-Renewal in your dashboard before the staking period ends.
                </p>
            </div>
        @endif
    </td>
</tr>

<!-- Call to Action -->
<tr>
    <td align="center" style="padding: 0 40px 30px;">
        <a href="{{ url('/dashboard') }}/staking" style="display: inline-block; background: linear-gradient(135deg, #4da3ff 0%, #3b82f6 100%); color: #000000; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 15px; font-weight: 600;">
            Manage Staking Settings
        </a>
    </td>
</tr>

<!-- Footer Notice -->
<tr>
    <td style="padding: 0 40px 40px;">
        <p style="margin: 0; color: #000000; opacity: 0.75; font-size: 13px; line-height: 1.6;">
            Thank you for choosing Lumastake for your staking needs. If you have any questions, please contact our support team.
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
        <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            Great news! Your identity verification has been <strong style="color:#4da3ff;">{{ $decision }}</strong>.
        </p>
        <div style="background: #f0f7ff; border-left: 3px solid #4da3ff; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
            <p style="color: #000000; font-size: 14px; margin: 0;">
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
        <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            Unfortunately, your identity verification was <strong style="color:#FF451C;">{{ $decision }}</strong>.
        </p>
        <div style="background: #fef2f2; border-left: 3px solid #FF451C; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
            <p style="color: #000000; font-size: 14px; margin: 0;">
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
        <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            You have started the identity verification process. Please complete all steps in the opened window.
        </p>
        <div style="background: #f9fafb; border-left: 3px solid #4da3ff; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
            <p style="color: #000000; font-size: 14px; margin: 0;">
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
        <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            Your identity verification has been submitted. Our team will review your information shortly.
        </p>
        <div style="background: #f9fafb; border-left: 3px solid #4da3ff; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
            <p style="color: #000000; font-size: 14px; margin: 0;">
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
        <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            We have credited <strong style="color:#4da3ff;">${{ $amount }}</strong> to your account balance.
        </p>
        @if(!empty($comment))
            <div style="background: #f9fafb; border-left: 3px solid #4da3ff; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
                <p style="color: #000000; font-size: 14px; margin: 0;">
                    Comment from administrator: {{ $comment }}
                </p>
            </div>
        @endif
    </td>
</tr>
@endsection
HTML;
    }

    private function getStakingCreatedNoticeContent(): string
    {
        return <<<'HTML'
@extends('emails.layouts.base')

@section('title', 'Staking Created')

@section('content')
<tr>
    <td style="padding: 0 40px 20px;">
        <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Hi {{ $name }},
        </p>
        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            Your staking of <strong style="color:#4da3ff;">${{ $amount }}</strong> for <strong>{{ $days }}</strong> days at <strong>{{ $percentage }}%</strong> APR has been created successfully.
        </p>
    </td>
</tr>
@endsection
HTML;
    }

    private function getReferralStakedNoticeContent(): string
    {
        return <<<'HTML'
@extends('emails.layouts.base')

@section('title', 'Your referral has started a stake')

@section('content')
<tr>
    <td style="padding: 0 40px 20px;">
        <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Hello {{ $referrer_name }},
        </p>
        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            Congratulations! Your referral <strong style="color:#4da3ff;">{{ $referral_name }}</strong> has staked <strong style="color:#4da3ff;">${{ $amount }}</strong> for <strong>{{ $days }}</strong> days at <strong>{{ $percentage }}%</strong> APR.
            You will receive a referral reward after this staking completes.
        </p>
    </td>
</tr>
@endsection
HTML;
    }

    private function getReferralRewardReceivedContent(): string
    {
        return <<<'HTML'
@extends('emails.layouts.base')

@section('title', 'Referral Reward Received')

@section('content')
<tr>
    <td style="padding: 0 40px 20px;">
        <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $referrer_name }},
        </p>
        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            Great news! You have received a referral reward of <strong style="color:#4da3ff;">${{ $reward_amount }}</strong> ({{ $reward_percentage }}%) from {{ $referral_name }}'s staking profit of <strong style="color:#4da3ff;">${{ $profit }}</strong>.
        </p>
        <p style="color: #000000; opacity: 0.75; font-size: 13px; line-height: 1.6; margin: 0 0 10px;">
            Staking period: {{ $staking_days }} days.
        </p>
    </td>
</tr>
@endsection
HTML;
    }

    private function getAccountTypeChangedContent(): string
    {
        return <<<'HTML'
@extends('emails.layouts.base')

@section('title', 'Account Type Changed')

@section('content')
<tr>
    <td style="padding: 0 40px 20px;">
        <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            Your account type has been successfully changed to <strong style="color: #4da3ff;">{{ $newAccountType }}</strong>.
        </p>
        <div style="background: #f9fafb; border-left: 3px solid #4da3ff; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
            <p style="color: #000000; font-size: 14px; margin: 0;">
                Please note that this change is permanent and cannot be reversed. All future staking deposits will follow {{ $newAccountType }} account rules.
            </p>
        </div>
        <p style="color: #000000; opacity: 0.75; font-size: 13px; line-height: 1.6; margin: 0;">
            If you did not make this change, please contact our support team immediately.
        </p>
    </td>
</tr>
@endsection
HTML;
    }

    private function getPasswordCodeRemainderContent(): string
    {
        return <<<'HTML'
@extends('emails.layouts.base')

@section('title', 'LOGIN VERIFICATION')

@section('content')
<!-- Content Section -->
<tr>
    <td style="padding: 0 40px;">
        <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 20px;">Hi {{ $userName }},</p>

        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 30px;">
            We detected a login attempt on your account. To continue, please verify your identity using the code below:
        </p>
    </td>
</tr>

<!-- Verification Code Section -->
<tr>
    <td align="center" style="padding: 0 40px 30px;">
        <table cellpadding="0" cellspacing="0" style="background: #f9fafb; border: 2px solid #d1d5db; border-radius: 12px; padding: 30px;">
            <tr>
                <td align="center">
                    <p style="margin: 0 0 10px; color: #000000; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">Your Verification Code</p>
                    <h2 style="margin: 0; color: #000000; font-size: 48px; font-weight: 700; letter-spacing: 12px; font-family: 'Courier New', monospace;">{{ $code }}</h2>
                </td>
            </tr>
        </table>
    </td>
</tr>

<!-- Copy Button Section -->
<tr>
    <td align="center" style="padding: 0 40px 30px;">
        <div style="display: inline-block; background-color: #E3FF3B; color: #262262; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 16px; cursor: pointer;">
            Copy Code
        </div>
        <p style="color: #000000; font-size: 12px; margin-top: 10px;">
            (Click the "Paste from clipboard" button on the login page after copying)
        </p>
    </td>
</tr>

<!-- Important Notice -->
<tr>
    <td style="padding: 0 40px 40px;">
        <table cellpadding="0" cellspacing="0" style="background: #fef2f2; border-left: 3px solid #FF451C; border-radius: 4px; padding: 15px 20px;">
            <tr>
                <td>
                    <p style="margin: 0; color: #000000; font-size: 14px; line-height: 1.5;">
                        <strong style="color: #FF451C;">Important:</strong> This code will expire in 10 minutes. If you did not attempt to login to Lumastake, please ignore this email and secure your account.
                    </p>
                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection
HTML;
    }
}
