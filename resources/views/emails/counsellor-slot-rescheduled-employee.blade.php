<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Mindway EAP</title>
</head>

<body>
    <p>Hi {{ $full_name }},</p>
    <p>Your session with {{$counselor_name}} has been rescheduled. Here are the new details.</p>
    <p>
    New Date & Time:  {{ $start_time }} ({{$timezone}})<br/>
    @if($meeting_link && $communication_method == 'Video Call')
    Join Link:  {{ $meeting_link }}
    @endif
    @if($communication_method == 'Phone Call')
    Phone call chosen:  {{ $phone }}
    @endif
    </p>
    <p>Please note that any cancellations within 24 hours of the session will count toward your session allocation.</p>
    <br />
    <p>If you haven’t already, please take 5 minutes to <a href="{{$intake_link}}">the client intake and consent form.</a>If you've already completed it, feel free to disregard this step.</p>
    <p>Best regards,</p>
    <p>The Mindway EAP Team</p>
</body>

</html>