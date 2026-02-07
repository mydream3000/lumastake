<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Successful - Lumastake</title>
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
                            Deposit Successful
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
                            Your deposit has been successfully credited to your account!
                        </p>
                        <div style="background: #f0f7ff; border-left: 3px solid #4da3ff; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
                            <p style="color: #000000; font-size: 14px; margin: 0 0 4px;">Deposited Amount:</p>
                            <p style="color: #4da3ff; font-size: 24px; font-weight: 700; margin: 0;">${{ $amount }} {{ $token ?? 'USDT' }}@if(!empty($networkLabel)) <span style="color:#9ca3af; font-size:14px; font-weight:600;"> ({{ $networkLabel }})</span>@endif</p>
                        </div>
                        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0;">
                            You can now use these funds for staking or withdraw them at any time.
                        </p>
                    </td>
                </tr>

                <!-- Footer Notice -->
                <tr>
                    <td style="padding: 0 40px 40px;">
                        <p style="margin: 18px 0 0; color: #000000; opacity: 0.75; font-size: 13px; line-height: 1.6;">
                            If you have any questions, please contact our support team.
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
