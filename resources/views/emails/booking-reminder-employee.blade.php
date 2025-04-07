<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Mindway EAP</title>
</head>

<body>
    <p>Hi {{ $full_name }},</p>
    <p>This is a friendly reminder about your upcoming session::</p>
    <p>
        <strong>With: </strong> {{ $counselor_name }}<br />
        <strong>Date & Time: </strong> {{ $start_time }} ({{$timezone}})<br />
        @if($meeting_link)
        <strong>Join Link: </strong> {{ $meeting_link }}<br />
        @endif
    </p>
    @if($meeting_link)
        <p>Please join this link at the time of the session.</p>
    @endif
    <p>
        If you haven’t already, please take 5 minutes to <a href="{{$intake_link}}">complete the client intake and consent form.</a> If you've already completed it, feel free to disregard this step.
    </p>
    <p>
        Please note that cancellations/reschedules made within 24 hours of the session will count toward your session allocation.
    </p>
    <br />
    <p>We’re here to support you, and we look forward to your session.
    </p>
    <p>Warm regards,</p>
    <p>The Counselling Team at Mindway EAP</p>
</body>

</html>