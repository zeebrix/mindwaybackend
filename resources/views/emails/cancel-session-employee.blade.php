<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Mindway EAP</title>
</head>

<body>
    <p>Hi {{ $full_name }},</p>
    <p>Your session with {{$counselor_name}} scheduled for {{$start_time}} ({{$timezone}}) has been cancelled.
    </p>
    <p>
        If you’d like to rebook, please use the app to schedule a new session.
    </p>
    <br />
    <p>
        We’re here to support you if you have any questions.
    </p>
    <p>Best regards,</p>
    <p>The Mindway EAP Team</p>
</body>

</html>