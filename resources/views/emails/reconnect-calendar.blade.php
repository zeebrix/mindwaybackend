<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Calendar Disconnected - Mindway EAP</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Hi {{ $full_name }},</p>

    <p>This is an automated notification from <strong>Mindway EAP</strong>.</p>

    <p>We noticed that your Google Calendar has been disconnected. To ensure uninterrupted scheduling and service, please log in to your Mindway EAP portal and reconnect your Google Calendar as soon as possible.</p>

    <p>
        <strong>Action required:</strong><br>
        <a href="{{ route('counseller.login') }}" style="color: #1a73e8;">Click here to log in and reconnect your calendar</a>
    </p>

    <p>Best regards,<br>
    The Mindway EAP Team</p>
</body>

</html>
