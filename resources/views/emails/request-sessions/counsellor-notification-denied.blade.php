<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Denial Notification – Request ID #{{ $request_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { color: #d32f2f; font-size: 1.2em; margin-bottom: 20px; }
        .details { margin: 20px 0; padding: 15px; background-color: #f5f5f5; border-radius: 5px; }
        .detail-item { margin-bottom: 10px; }
        .status-deny { color: #d32f2f; font-weight: bold; }
        .footer { margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee; color: #666; font-size: 0.9em; }
        .highlight { background-color: #ffebee; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="header">❌ Session Denial Notification – Request ID #{{ $request_id }}</div>
    
    <p>Hi {{ $counsellor_name }},</p>
    <p>This is a confirmation that a response has been recorded for <strong>Request ID {{ $request_id }}</strong>.</p>
    
    <div class="details">
        <div class="detail-item">
            <strong>Employee Name:</strong> {{ $employee_name }}
        </div>
        <div class="detail-item">
            <strong>Employee Email:</strong> {{ $employee_email }}
        </div>
        <div class="detail-item">
            <strong>Sessions Approved:</strong> <span class="status-deny">{{ $approved_status }}</span>
        </div>
        <div class="detail-item">
            <strong>Quantity:</strong> {{ $approved_quantity }}
        </div>
        <div class="detail-item">
            <strong>Date of Denial:</strong> {{ $approval_date }}
        </div>
    </div>

    <p><span class="highlight">No further action is required.</span> If you need to discuss this decision, please reply to this email.</p>
    
    <div class="footer">
        <p>If you have any questions, feel free to reply to this email.</p>
        <p>Best regards,</p>
        <p><strong>Mindway EAP Team</strong></p>
    </div>
</body>
</html>