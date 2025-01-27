<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Your Password</h1>
    <p>We received a request to reset your password. Click the link below to reset it:</p>
    <a href="{{ $resetLink }}" style="display: inline-block; padding: 10px 15px; color: white; background-color: blue; text-decoration: none; border-radius: 5px;">
        Reset Password
    </a>
    <p>If you didn’t request a password reset, please ignore this email.</p>
    <p>Thanks,<br>The {{ config('app.name') }} Team</p>
</body>
</html>
