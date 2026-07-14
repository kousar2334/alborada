<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Link</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #1a1a2e; color: #fff; padding: 30px; text-align: center; }
        .body { padding: 30px; }
        .plan-box { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 20px; margin: 20px 0; text-align: center; }
        .btn { display: inline-block; background: #28a745; color: #fff; padding: 14px 40px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 16px; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #6c757d; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ get_setting('site_name', 'Moissanite Visions') }}</h1>
        <p style="margin:0;opacity:.8;">Secure Payment Link</p>
    </div>
    <div class="body">
        <p>Hello <strong>{{ $user->name }}</strong>,</p>
        <p>A payment link has been created for you to subscribe to the following plan:</p>

        <div class="plan-box">
            <h3 style="margin:0 0 8px;">{{ $plan->title }}</h3>
            <p style="margin:0;font-size:28px;font-weight:bold;color:#28a745;">${{ number_format($plan->price, 2) }}</p>
            <p style="margin:4px 0 0;color:#6c757d;">{{ $plan->duration_days }} days — {{ $plan->max_connections }} connection(s)</p>
        </div>

        <p style="text-align:center;">
            <a href="{{ $paymentUrl }}" class="btn">Complete Payment</a>
        </p>

        <p style="color:#6c757d;font-size:13px;">This link expires in 3 days. Do not share it with others.</p>

        <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
        <p>Need help? <a href="{{ route('member.tickets.create') }}">Contact support</a>.</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ get_setting('site_name', 'Moissanite Visions') }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
