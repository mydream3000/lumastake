@extends('emails.layouts.base')

@section('title', 'Promo Code Applied')

@section('content')
<!-- Content Section -->
<tr>
    <td style="padding: 0 40px 20px;">
        <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
            Dear {{ $userName }},
        </p>
        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
            Your promo code has been successfully applied! A bonus has been credited to your account.
        </p>
    </td>
</tr>

<!-- Promo Details -->
<tr>
    <td style="padding: 0 40px 20px;">
        <table cellpadding="0" cellspacing="0" width="100%" style="background: #f9fafb; border-radius: 8px; overflow: hidden;">
            <tr>
                <td style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
                    <p style="color: #000000; font-size: 13px; margin: 0 0 6px;">Promo Code</p>
                    <p style="color: #000000; font-size: 20px; font-weight: 600; margin: 0;">{{ $promoCode }}</p>
                </td>
            </tr>
            <tr>
                <td style="padding: 20px;">
                    <p style="color: #000000; font-size: 13px; margin: 0 0 6px;">Bonus Amount</p>
                    <p style="color: #4da3ff; font-size: 20px; font-weight: 600; margin: 0;">${{ $amount }}</p>
                </td>
            </tr>
        </table>
    </td>
</tr>

<!-- Call to Action -->
<tr>
    <td align="center" style="padding: 0 40px 30px;">
        <a href="{{ url('/dashboard/deposit') }}" style="display: inline-block; background: linear-gradient(135deg, #4da3ff 0%, #3b82f6 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 15px; font-weight: 600;">
            Go to Deposits
        </a>
    </td>
</tr>

<!-- Footer Notice -->
<tr>
    <td style="padding: 0 40px 40px;">
        <p style="margin: 0; color: #000000; opacity: 0.75; font-size: 13px; line-height: 1.6;">
            You can use these funds for staking or withdraw them at any time. If you have any questions, please contact our support team.
        </p>
    </td>
</tr>
@endsection
