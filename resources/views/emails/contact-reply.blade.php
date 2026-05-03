<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Re: {{ $originalSubject }}</title>
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
        }

        .body p {
            line-height: 1.7;
            margin: 0 0 16px;
        }

        .reply-box {
            background: #f8f9fa;
            border-left: 4px solid #1a73e8;
            padding: 16px 20px;
            border-radius: 4px;
            margin: 20px 0;
            white-space: pre-wrap;
        }

        .footer {
            background: #f4f4f4;
            padding: 16px 32px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <h2>{{ get_setting('site_name') }}</h2>
        </div>
        <div class="body">
            <p>Dear {{ $senderName }},</p>
            <p>Thank you for contacting us. Here is our reply to your message regarding
                <strong>"{{ $originalSubject }}"</strong>:</p>
            <div class="reply-box">{{ $replyMessage }}</div>
            <p>If you have any further questions, feel free to reach out to us again.</p>
            <p>Best regards,<br><strong>{{ get_setting('site_name') }} Team</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ get_setting('site_name') }}. All rights reserved.
        </div>
    </div>
</body>

</html>
