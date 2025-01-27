<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Mindway EAP</title>
</head>
<body>
    <p>Hi {{ $full_name }},</p>
    <p>Welcome to your Mindway EAP 14-day trial! Your account is ready to be set up. To get started, please complete your admin profile by clicking the link below:</p>
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
    During your trial, you’ll have full access to explore our platform and experience the support we offer. If you have any questions, we’re here to help.
    </p>
    <p>We hope you enjoy using Mindway EAP!.</p>
    <br/>
    <p>Best wishes,</p>
    <p>The Mindway EAP Team</p>
</body>
</html>
