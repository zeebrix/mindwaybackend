<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counseling Session Extension Request</title>
</head>
<body>
    <p>Hi {{ $admin_name }} ,</p>
    <p>An employee currently engaged in counselling has reached or is nearing their session limit. The counsellor working with them has recommended additional sessions, as outlined below:</p>
    
    <p>
        <strong>Reason for sessions:</strong> {{ $reason }}<br/>
        <strong>Additional sessions requested:</strong> {{ $additional_sessions }}<br/>
        <strong>Request ID:</strong> R#{{ $request_id }}
    </p>
    
    <p>To review and respond to this request, please click the link below:</p>
    <p><a class="btn btn-primary mindway-btn" href="{{ $review_link }}">Review Request</a></p>
    
    <p>A prompt response will help ensure the employee continues to receive timely support.</p>
    <p>If you have any questions, feel free to reply to this email.</p>
    <br/>
    <p>Best regards,</p>
    <p>Mindway EAP Team</p>
</body>
</html>