<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal Approved - Lumastake</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #101221; padding: 40px 20px;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background-color: #101221; border-radius: 12px; overflow: hidden;">
                <!-- Logo Section -->
                <tr>
                    <td align="center" style="padding: 40px 20px 30px;">
                        <img src="{{ asset('images/sidebar/logo-white.png') }}" alt="Lumastake" style="height: 50px; width: auto;">
                    </td>
                </tr>

                <!-- Title Section -->
                <tr>
                    <td align="center" style="padding: 0 40px 30px;">
                        <h1 style="margin: 0; font-size: 28px; font-weight: 700; color: #ffffff;">
                            Withdrawal Approved
                        </h1>
                    </td>
                </tr>

                <!-- Content Section -->
                <tr>
                    <td style="padding: 0 40px 20px;">
                        <p style="color: #ffffff; font-size: 16px; line-height: 1.6; margin: 0 0 16px;">
                            Dear {{ $userName }},
                        </p>
                        <p style="color: #cccccc; font-size: 15px; line-height: 1.6; margin: 0 0 22px;">
                            Good news! Your withdrawal request has been approved and processed successfully.
                        </p>
                        <div style="background: rgba(72, 187, 120, 0.1); border-left: 3px solid #48BB78; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
                            <p style="color: #ffffff; font-size: 14px; margin: 0 0 4px;">Withdrawal Amount:</p>
                            <p style="color: #48BB78; font-size: 24px; font-weight: 700; margin: 0;">${{ $amount }}</p>
                        </div>
                        @if($comment)
                        <div style="background: rgba(66, 153, 225, 0.1); border-left: 3px solid #4299E1; border-radius: 4px; padding: 14px 16px; margin-bottom: 20px;">
                            <p style="color: #ffffff; font-size: 14px; margin: 0 0 8px; font-weight: 600;">Transaction Details:</p>
                            <p style="color: #cccccc; font-size: 13px; line-height: 1.5; margin: 0; word-break: break-all;">{{ $comment }}</p>
                        </div>
                        @endif
                        <p style="color: #cccccc; font-size: 15px; line-height: 1.6; margin: 0 0 12px;">
                            The funds have been sent to your specified wallet address. Depending on the network, it may take a few minutes to several hours for the transaction to be confirmed on the blockchain.
                        </p>
                        <p style="color: #cccccc; font-size: 15px; line-height: 1.6; margin: 0;">
                            <strong style="color: #ffffff;">Status:</strong> <span style="color: #48BB78;">Approved & Processed</span>
                        </p>
                    </td>
                </tr>

                <!-- Footer Notice -->
                <tr>
                    <td style="padding: 0 40px 40px;">
                        <p style="margin: 18px 0 0; color: #8f8f8f; font-size: 13px; line-height: 1.6;">
                            Thank you for using Lumastake. If you have any questions, please don't hesitate to contact our support team.
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="padding: 24px 40px 40px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                        <p style="margin: 0 0 8px 0; color: #666666; font-size: 12px;">
                            Support: <a href="mailto:support@lumastake.com" style="color: #4FD1C5; text-decoration: none;">support@lumastake.com</a>
                        </p>
                        <p style="margin: 0; color: #666666; font-size: 12px;">
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
