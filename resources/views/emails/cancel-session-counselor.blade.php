<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Mindway EAP</title>
</head>
 
<body>
    <p>Hi {{ $full_name }},</p>
    <p>The session with {{$customer_name}} scheduled for {{$start_time}} ({{$timezone}}) has been cancelled.
    </p>
    <p> <strong>Client Details: </strong></p>
    <p>
        <strong>Client Name: </strong>{{$customer_name}}<br/>
        <strong> Company: </strong> {{$company_name}}<br/>
        <strong> Sessions Remaining:</strong> {{$max_session}}<br/>
    </p>
    <br />

    <p>Best regards,</p>
    <p>The Mindway EAP Team</p>
</body>

</html>