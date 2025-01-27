<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Mindway EAP</title>
</head>
<body>
    <p>Hi {{ $full_name }},</p>
    <p>Congratulations on joining Mindway EAP! We’re excited to partner with you in supporting your team’s well-being.</p>
    <p>To complete your admin profile and get started, follow these links:</p>
    <ul>
        <li><a class="btn btn-primary mindway-btn" href="{{route('program.signup')}}">Set Up Admin Profile</a></li>
        <li><a class="btn btn-primary mindway-btn" href="{{route('program.login')}} ">Log In to Admin Portal</a></li>
    </ul>
    <p><strong>Program Details:</strong></p>
    <p>
        Company Name: {{ $company_name }}<br/>
        Access Code: {{ $access_code }}
    </p>
    <p>
        Once your profile is set up, you can log in to the admin portal to manage your program and explore all its features.
    </p>
    <p>Your dedicated program manager will share more details shortly regarding onboarding. However, keep these emails safe to manage the above links.</p>
    <br/>
    <p>Warm regards,</p>
    <p>The Mindway EAP Team</p>
</body>
</html>
