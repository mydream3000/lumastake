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
            Thank you for choosing Lumastake for your staking needs. If you have any questions, please contact our support team.
        </p>
    </td>
</tr>
@endsection