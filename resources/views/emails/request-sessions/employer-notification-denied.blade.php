<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Notification – Sessions Denied (Request #{{ $request_id }})</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .details { margin: 15px 0; padding-left: 10px; border-left: 3px solid #e53935; }
        .detail-item { margin-bottom: 8px; }
        .footer { margin-top: 20px; color: #666; font-size: 0.9em; }
        .status-deny { color: #e53935; font-weight: bold; }
    </style>
</head>
<body>
    <p>Hi {{ $admin_name }},</p>
    <p>This is a confirmation that a response has been recorded for <strong>Request ID {{ $request_id }}</strong>.</p>
    
    <div class="details">
        <div class="detail-item">
            <strong>Sessions Approved:</strong> <span class="status-deny">No</span>
        </div>
        <div class="detail-item">
            <strong>Quantity:</strong> {{ $approved_quantity }}
        </div>
        <div class="detail-item">
            <strong>Date of Denial:</strong> {{ $denial_date }}
        </div>
    </div>

    <p>No further action is required at this time.</p>
    <p>If you have any questions, feel free to reply to this email.</p>
    
    <div class="footer">
        <p>Best regards,</p>
        <p><strong>Mindway EAP Team</strong></p>
    </div>
</body>
</html>