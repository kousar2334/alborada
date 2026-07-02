<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Subscription Expiry Reminder</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #e67e22; color: #fff; padding: 30px; text-align: center; }
        .body { padding: 30px; }
        .alert-box { background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 15px; margin: 20px 0; }
        .btn { display: inline-block; background: #e50914; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: bold; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #6c757d; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>⏰ Subscription Expiring Soon</h1>
    </div>
    <div class="body">
        <p>Hello <strong>{{ $user->name }}</strong>,</p>

        @php $daysLeft = now()->diffInDays($subscription->expires_at); @endphp

        <div class="alert-box">
            <strong>Your {{ $subscription->plan->title ?? 'IPTV' }} subscription expires in {{ $daysLeft }} day{{ $daysLeft != 1 ? 's' : '' }}!</strong>
            <br>Expiry date: <strong>{{ $subscription->expires_at?->format('M d, Y') }}</strong>
        </div>

        <p>Renew now to avoid interruption to your IPTV service. Choose from our plans and continue enjoying seamless streaming.</p>

        <p style="text-align:center;">
            <a href="{{ route('pricing.plans') }}" class="btn">Renew My Subscription</a>
        </p>

        <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
        <p>Need help? <a href="{{ route('member.tickets.create') }}">Contact support</a>.</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ get_setting('site_name', 'Moissanite Radiance') }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
