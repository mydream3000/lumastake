<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal Request Created - Lumastake</title>
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
                            Withdrawal Request Created
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
                            Your withdrawal request has been successfully created and is now pending approval by our team.
                        </p>
                        <div style="background: rgba(251, 176, 59, 0.1); border-left: 3px solid #FBB03B; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
                            <p style="color: #000000; font-size: 14px; margin: 0 0 4px;">Token:</p>
                            <p style="color: #FBB03B; font-size: 18px; font-weight: 700; margin: 0 0 12px;">{{ $token ?? 'USDT' }}</p>
                            <p style="color: #000000; font-size: 14px; margin: 0 0 4px;">Withdrawal Amount:</p>
                            <p style="color: #FBB03B; font-size: 24px; font-weight: 700; margin: 0 0 12px;">{{ $amount }} {{ $token ?? 'USDT' }}</p>
                            <p style="color: #000000; font-size: 13px; margin: 0 0 2px;">Network:</p>
                            <p style="color: #000000; font-size: 14px; margin: 0 0 12px;">
                                @if(($network ?? 'tron') === 'ethereum')
                                    Ethereum (ERC-20)
                                @elseif(($network ?? 'tron') === 'tron')
                                    TRON (TRC-20)
                                @elseif(($network ?? 'tron') === 'bsc')
                                    BNB Chain (BEP-20)
                                @endif
                            </p>
                            <p style="color: #000000; font-size: 13px; margin: 0 0 2px;">Wallet Address:</p>
                            <p style="color: #000000; font-size: 12px; font-family: monospace; margin: 0; word-break: break-all;">{{ $walletAddress }}</p>
                        </div>
                        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0 0 12px;">
                            We will process your request shortly. You will receive a notification once your withdrawal is approved or if additional information is needed.
                        </p>
                        <p style="color: #000000; font-size: 15px; line-height: 1.6; margin: 0;">
                            <strong style="color: #000000;">Status:</strong> Pending Approval
                        </p>
                    </td>
                </tr>

                <!-- Footer Notice -->
                <tr>
                    <td style="padding: 0 40px 40px;">
                        <p style="margin: 18px 0 0; color: #000000; opacity: 0.75; font-size: 13px; line-height: 1.6;">
                            If you did not create this withdrawal request, please contact our support team immediately.
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
