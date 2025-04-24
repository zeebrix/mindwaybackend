<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow-Up: Counseling Session Extension Request</title>
</head>
<body>
    <p>Hi {{ $admin_name }},</p>
    <p>Just following up on the request sent on {{ $request_date }} regarding additional counselling sessions for an employee. As a reminder:</p>
    
    <p>
        <strong>Reason for sessions:</strong> {{ $reason }}<br/>
        <strong>Additional sessions requested:</strong> {{ $additional_sessions }}<br/>
        <strong>Request ID:</strong> #R{{ $request_id }}
    </p>
    
    <p>This request is still pending your review. Please click the link below to approve or deny:</p>
    <p><a class="btn btn-primary mindway-btn" href="{{ $review_link }}">Review Request</a></p>
    
    <p>Timely action will help ensure the employee continues to receive the support they need. If you have any questions or require more information, feel free to reply to this email.</p>
    <p>Thanks in advance for your attention to this matter.</p>
    <br/>
    <p>Best regards,</p>
    <p>Mindway EAP Team</p>
</body>
</html>