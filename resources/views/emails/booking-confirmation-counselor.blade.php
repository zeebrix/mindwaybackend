<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Mindway EAP</title>
</head>

<body>
    <p>Hi {{ $full_name }},</p>
    <p>You have a new counselling session scheduled. Here are the details: </p>
    <p>
        <strong> Client Name:   </strong> {{ $client_name }}<br />
        <strong> Company:       </strong> {{ $company_name }}<br />
         <strong> Employee Email : </strong> {{$employee_email}}<br />
        <strong> Phone: </strong> {{$employee_phone}}<br />
        <strong> Date & Time:   </strong> {{ $start_time }} ({{$timezone}})<br />
        <strong> Sessions Remaining: </strong> {{ $max_session }}<br />
        @if($meeting_link)
            <strong> Join Link:     </strong> {{ $meeting_link }}<br />
        @endif
    </p>
    <p>If you have any questions, feel free to reach out to the bookings team.
    </p>
    <p>Best regards,</p>
    <p>The Mindway EAP Team</p>
</body>

</html>