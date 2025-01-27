<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Mindway EAP</title>
</head>

<body>
    <p>Hi {{ $full_name }},</p>
    <p>We’re notifying you that you’ve been made an admin for Mindway EAP. To manage the program effectively, please set up your admin account here:</p>
    <ul>
        <li><a class="btn btn-primary mindway-btn" href="{{route('program.signup')}}">Create Admin Account</a></li>
    </ul>

    <p>
    If you’ve already set up your account in our app, you can log in directly to access the admin portal:
    </p>
    <ul>
        <li><a class="btn btn-primary mindway-btn" href="{{route('program.login')}}">Log In to Admin Portal        </a></li>
    </ul>
    <p>If you have any questions, don’t hesitate to contact us.
    </p>
    <br />
    <p><strong>Program Details:</strong></p>
    <p>
    <strong>Company Name:</strong> {{ $company_name }}<br/>
    <strong> Access Code: </strong>{{ $access_code }}
    </p>
    <p>Feel free to contact us if you have questions about your new role or need guidance.
    </p>
    <br />
    <br />
    <p>Best regards,</p>
    <p>The Mindway EAP Team</p>
</body>

</html>