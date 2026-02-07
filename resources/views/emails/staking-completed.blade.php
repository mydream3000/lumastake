<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staking Completed - Lumastake</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #101221; padding: 40px 20px;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background-color: #101221; border-radius: 12px; overflow: hidden;">
                <!-- Logo Section -->
                <tr>
                    <td align="center" style="padding: 40px 20px 30px;">
                        <img src="https://lumastake.com/images/sidebar/logo-white.png" alt="Lumastake" style="height: 50px; width: auto;">
                    </td>
                </tr>

                <!-- Title Section -->
                <tr>
                    <td align="center" style="padding: 0 40px 30px;">
                        <h1 style="margin: 0; font-size: 28px; font-weight: 700; color: #000000;">
                            @if($autoRenewal)
                                Staking Completed & Renewed
                            @else
                                Staking Completed Successfully
                            @endif
                        </h1>
                    </td>
                </tr>

                <!-- Content Section -->
                <tr>
                    <td style="padding: 0 40px 20px;">
                        <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
                            Dear {{ $userName }},
                        </p>
                        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
                            Your {{ $days }}-day staking period has been successfully completed!
                        </p>
                    </td>
                </tr>

                <!-- Staking Details -->
                <tr>
                    <td style="padding: 0 40px 20px;">
                        <table cellpadding="0" cellspacing="0" width="100%" style="background: #f9fafb; border-radius: 8px; overflow: hidden;">
                            <tr>
                                <td style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
                                    <p style="color: #000000; font-size: 13px; margin: 0 0 6px;">Principal Amount</p>
                                    <p style="color: #000000; font-size: 20px; font-weight: 600; margin: 0;">${{ number_format($principalAmount, 2) }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
                                    <p style="color: #000000; font-size: 13px; margin: 0 0 6px;">Profit Earned ({{ $percentage }}% for {{ $days }} days)</p>
                                    <p style="color: #4da3ff; font-size: 20px; font-weight: 600; margin: 0;">${{ number_format($profitAmount, 2) }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px;">
                                    <p style="color: #000000; font-size: 13px; margin: 0 0 6px;">Total Received</p>
                                    <p style="color: #000000; font-size: 24px; font-weight: 700; margin: 0;">${{ number_format($totalAmount, 2) }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Auto-renewal or Completion Message -->
                <tr>
                    <td style="padding: 0 40px 30px;">
                        @if($autoRenewal)
                            <div style="background: #f0f7ff; border-left: 3px solid #4da3ff; border-radius: 4px; padding: 14px 16px;">
                                <p style="color: #000000; font-size: 14px; line-height: 1.6; margin: 0;">
                                    <strong style="color: #4da3ff;">Auto-Renewal Active:</strong> Your principal amount of ${{ number_format($principalAmount, 2) }} has been automatically restaked for another {{ $days }} days at {{ $percentage }}%. The profit of ${{ number_format($profitAmount, 2) }} has been added to your available balance.
                                </p>
                            </div>
                        @else
                            <div style="background: #f0f7ff; border-left: 3px solid #4da3ff; border-radius: 4px; padding: 14px 16px;">
                                <p style="color: #000000; font-size: 14px; line-height: 1.6; margin: 0;">
                                    The full amount of ${{ number_format($totalAmount, 2) }} (principal + profit) has been credited to your available balance. You can now withdraw these funds or start a new staking period.
                                </p>
                            </div>
                        @endif
                    </td>
                </tr>

                <!-- Call to Action -->
                <tr>
                    <td align="center" style="padding: 0 40px 30px;">
                        <a href="{{ url('/dashboard') }}" style="display: inline-block; background: linear-gradient(135deg, #4da3ff 0%, #3BA89F 100%); color: #000000; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 15px; font-weight: 600;">
                            View Dashboard
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

                <!-- Footer -->
                <tr>
                    <td align="center" style="padding: 24px 40px 40px; border-top: 1px solid #e5e7eb;">
                        <p style="margin: 0 0 8px 0; color: #000000; font-size: 12px;">
                            Support: <a href="mailto:support@lumastake.com" style="color: #4da3ff; text-decoration: none;">support@lumastake.com</a>
                        </p>
                        <p style="margin: 0; color: #000000; font-size: 12px;">
                            © {{ date('Y') }} Lumastake. All rights reserved.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
