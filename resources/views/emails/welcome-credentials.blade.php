<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome — Your IPTV Credentials</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #1a1a2e; color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .body { padding: 30px; }
        .credentials-box { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 20px; margin: 20px 0; }
        .credential-item { margin-bottom: 12px; }
        .credential-label { font-size: 12px; color: #6c757d; text-transform: uppercase; letter-spacing: 1px; }
        .credential-value { font-size: 16px; font-weight: bold; color: #212529; font-family: monospace; word-break: break-all; }
        .url-box { background: #e8f5e9; border-left: 4px solid #28a745; padding: 10px 15px; margin: 8px 0; border-radius: 0 4px 4px 0; }
        .url-code { word-break: break-all; }
        .btn { display: inline-block; background: #e50914; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: bold; margin: 10px 0; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #6c757d; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Welcome to {{ get_setting('site_name', 'Moissanite Visions') }}!</h1>
    </div>
    <div class="body">
        <p>Hello <strong>{{ $user->name }}</strong>,</p>
        <p>Your <strong>{{ $subscription->plan->title ?? 'IPTV' }}</strong> subscription is now active. Here are your credentials:</p>

        <div class="credentials-box">
            @if(($credentials['device_type'] ?? 'm3u') === 'mag')
            <div class="credential-item">
                <div class="credential-label">MAC Address</div>
                <div class="credential-value">{{ $credentials['mac'] }}</div>
            </div>
            @else
            <div class="credential-item">
                <div class="credential-label">Username</div>
                <div class="credential-value">{{ $credentials['username'] }}</div>
            </div>
            <div class="credential-item">
                <div class="credential-label">Password</div>
                <div class="credential-value">{{ $credentials['password'] }}</div>
            </div>
            @endif
        </div>

        @if(!empty($credentials['m3u_url']))
        <p><strong>{{ ($credentials['device_type'] ?? 'm3u') === 'mag' ? 'Your Portal URL:' : 'Your M3U Playlist URL:' }}</strong></p>
        <div class="url-box"><code class="url-code">{{ $credentials['m3u_url'] }}</code></div>
        @endif

        <p>Your subscription is valid until <strong>{{ $subscription->expires_at?->format('M d, Y') }}</strong>.</p>

        <p>
            <a href="{{ route('member.setup.guide') }}" class="btn">View Setup Guide</a>
        </p>

        <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
        <p>Need help? <a href="{{ route('member.tickets.create') }}">Open a support ticket</a> and we'll assist you.</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ get_setting('site_name', 'Moissanite Visions') }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
