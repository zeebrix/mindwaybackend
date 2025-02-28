<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Mindway EAP</title>
</head>

<body>
    <p>Hi {{ $full_name }},</p>
    <p>Your session with {{$counselor_name}} has been confirmed. Below are the details:</p>
    <p>
    <strong>Date & Time:  </strong> {{ $start_time }} ({{$timezone}})<br/>
    <strong>Duration:  </strong> {{ $duration }}<br/>
    @if($meeting_link)
        <strong>Join Link:  </strong>   {{ $meeting_link }}<br/>
    @endif
    @if($communication_method == 'Phone Call')
        <strong>Phone call chosen:  </strong>   {{ $phone }}<br/>
    @endif
    <strong> Available Sessions Remaining:</strong>  {{ $max_session }}<br/>
    </p>
    <p>
        Prior to your session, please take 5 minutes to <a href="{{$intake_link}}">complete client intake and consent form.</a>  If you've already completed it, feel free to disregard this step.
    </p>
    <p>Need to make changes? You can reschedule or cancel your session easily through our app. Please note that any changes made within 24 hours of your session will count toward your session allocation.
    </p>
    <br />
    <p>Best regards,</p>
    <p>The Mindway EAP Team</p>
</body>

</html>