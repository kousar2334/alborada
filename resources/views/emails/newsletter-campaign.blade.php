<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $campaign->subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .1);
        }

        .header {
            background: #1a73e8;
            padding: 24px 32px;
        }

        .header h2 {
            color: #fff;
            margin: 0;
            font-size: 20px;
        }

        .body {
            padding: 32px;
            color: #333;
            line-height: 1.7;
        }

        .body p {
            margin: 0 0 16px;
        }

        .footer {
            background: #f4f4f4;
            padding: 16px 32px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }

        .footer a {
            color: #999;
        }

        /* Tracking pixel */
        .tracking {
            display: none;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <h2>{{ get_setting('site_name') }}</h2>
        </div>
        <div class="body">
            {!! $campaign->content !!}
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ get_setting('site_name') }}. All rights reserved.<br>
            <a href="{{ route('newsletter.unsubscribe', $subscriber->token) }}">Unsubscribe</a>
        </div>
    </div>

    {{-- Open tracking pixel --}}
    <img src="{{ route('newsletter.track.open', ['campaign' => $campaign->id, 'subscriber' => $subscriber->id]) }}"
        width="1" height="1" style="display:none;" alt="">
</body>

</html>
