<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Mindway EAP</title>
</head>

<body>
    <p>Hi {{ $full_name }},</p>
    <p>Welcome to Mindway EAP! To get started, please set up your profile using the link below.</p>
    <ul>
        <a href="{{ $resetLink }}">Set Password</a>
    </ul>

    <p>
        Once your account is set up, you can log in to view and manage your sessions.

    </p>
    <p>If you have any questions, don’t hesitate to contact us.
    </p>
    <br />
    <p>Best regards,</p>
    <p>The Mindway EAP Team</p>
</body>

</html>
