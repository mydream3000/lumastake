<!DOCTYPE html>
<html>
<head>
    <title>Staking Expiring Soon</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #FF451C; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { border: 1px solid #ddd; border-top: none; padding: 20px; border-radius: 0 0 5px 5px; }
        .button { display: inline-block; padding: 10px 20px; background-color: #FF451C; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Staking Expiring Soon</h1>
        </div>
        <div class="content">
            <p>Hello {{ $user->name }},</p>
            <p>We would like to inform you that your staking plan is expiring in <strong>{{ $daysLeft }} day(s)</strong>.</p>

            <div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 15px 0;">
                <p style="margin: 5px 0;"><strong>Plan:</strong> {{ $stake->days }} Days</p>
                <p style="margin: 5px 0;"><strong>Amount:</strong> ${{ number_format($stake->amount, 2) }}</p>
                <p style="margin: 5px 0;"><strong>End Date:</strong> {{ $stake->end_date->format('Y-m-d') }}</p>
            </div>

            <p>Once completed, your principal amount and earned profit will be credited to your balance automatically.</p>

            <a href="{{ route('login') }}" class="button">Login to Dashboard</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Lumastake. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
