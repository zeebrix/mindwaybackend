<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Mindway EAP</title>
</head>

<body>
    <p>Hi {{ $full_name }},</p>
    <p>The session with {{$customer_name}} has been rescheduled. Here are the updated details:</p>
    <p>
        <strong> Date & Time: </strong> {{$start_time}} ({{$timezone}})
        <strong> Client: </strong> {{$full_name}}
        <strong> Company: </strong> {{$company_name}}
        @if($meeting_link)
            <strong> Meeting Link : </strong> {{$meeting_link}}
        @endif
        @if($phone)
            <strong> Phone call chosen : </strong> {{$phone}}
        @endif
        <strong> Employee Email : </strong> {{$employee_email}}
        <strong> Phone: </strong> {{$employee_phone}}
        <strong> Sessions Remaining:</strong> {{$max_session}}
    </p>
    Join the Call
    <br />

    <p>Best regards,</p>
    <p>The Mindway EAP Team</p>
</body>

</html>