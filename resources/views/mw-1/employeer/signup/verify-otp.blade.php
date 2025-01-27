<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Mindway - OTP Verification</title>
    <!-- base:css -->
    <link rel="stylesheet" href="{{ asset('/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('/css/vertical-layout-light/style.css') }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('/images/favicon.ico') }}" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f3f8ff;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .otp-container {
            display: flex;
            flex-direction: row;
            background: #EAF2FF;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            width: 898px;
        }

        .left-section {
            width: 348px;
            background: #E3ECFF;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 20px;
            text-align: center;
            border-radius: 0px 102px 1px 0px;
        }

        .left-section img {
            width: 150px;
            margin-bottom: 20px;
        }

        .left-section h1 {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .otp-form {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .otp-form h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .otp-form p {
            margin-bottom: 20px;
            font-size: 16px;
            color: #666;
        }

        .otp-input-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            border: 2px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s;
        }

        .otp-input:focus {
            border-color: #688EDC;
        }

        .btn-verify {
            background: #688EDC;
            color: white;
            padding: 10px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-verify:hover {
            background: #567BD1;
        }

        .helper-links {
            margin-top: 10px;
            font-size: 14px;
            text-align: center;
        }

        .helper-links a {
            color: #688EDC;
            text-decoration: none;
        }

        .helper-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="otp-container">
        <!-- Left Section -->
        <div class="left-section">
            <img src="{{ asset('/logo/loginLogo.png') }}" alt="Mindway Logo">
            <img src="{{ asset('/logo/logo.png') }}" width="147px" height="29px" alt="Mindway Logo">
            <h1>Your All-in-One</h1>
            <h1>Platform for </h1>
            <h1>Employee Well-Being.</h1>
        </div>

        <!-- OTP Form Section -->
        <div class="otp-form">
            <img src="{{ asset('/logo/logo.png') }}" width="147px" height="29px" alt="Mindway Logo">
            <h2>Verify Your Email</h2>
            <p>We sent you a 6-digit OTP to verify your identity</p>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ url('/manage-program/verify-otp') }}" id="otp-form">
                @csrf
                <div class="otp-input-container">
                    <input type="text" maxlength="1" class="otp-input" required>
                    <input type="text" maxlength="1" class="otp-input" required>
                    <input type="text" maxlength="1" class="otp-input" required>
                    <input type="text" maxlength="1" class="otp-input" required>
                    <input type="text" maxlength="1" class="otp-input" required>
                    <input type="text" maxlength="1" class="otp-input" required>
                </div>
                <input type="hidden" name="otp" id="otp-value">

                <div class="mt-3">
                    <button class="btn btn-block btn-lg font-weight-medium auth-form-btn btn-verify"
                        style="background-color: #688EDC;border-radius:20px;color:white">Verify OTP</button>
                </div>
                <div class="my-2 d-flex justify-content-center">

                    {{-- <button type="submit" class="btn-verify">Verify</button> --}}
            </form>

            <div class="helper-links">
                <p>Can’t find code? Check the spam folder or <a href="{{ url('/manage-program/resendotp') }}">resend
                        code</a></p>
            </div>
        </div>
    </div>
    <!-- base:js -->
    <script src="{{ asset('/vendors/js/vendor.bundle.base.js') }}"></script>
    <script>
        const otpInputs = document.querySelectorAll('.otp-input');

        otpInputs.forEach((input, index) => {
            // Handle typing in the input fields
            input.addEventListener('input', (event) => {
                const value = event.target.value;

                // Allow only numeric input
                if (!/^\d$/.test(value)) {
                    event.target.value = '';
                    return;
                }

                // Move to the next input field if it exists
                if (value && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            // Handle backspace key to move to the previous input field
            input.addEventListener('keydown', (event) => {
                if (event.key === 'Backspace' && !event.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });
        });

        // Handle pasting an OTP
        otpInputs[0].addEventListener('paste', (event) => {
            event.preventDefault();
            const pastedData = event.clipboardData.getData('text').trim();

            // Ensure pasted data is a 6-digit OTP
            if (!/^\d{6}$/.test(pastedData)) return;

            // Fill each input with the corresponding digit
            otpInputs.forEach((input, i) => {
                input.value = pastedData[i] || '';
            });

            // Focus the last filled input
            otpInputs[Math.min(pastedData.length - 1, otpInputs.length - 1)].focus();
        });

        // Handle form submission
        const otpForm = document.getElementById('otp-form');
        const otpValueField = document.getElementById('otp-value');

        otpForm.addEventListener('submit', (event) => {
            let otp = '';
            otpInputs.forEach(input => {
                otp += input.value;
            });
            otpValueField.value = otp; // Combine OTP inputs into a single hidden field
        });
    </script>

    <!-- endinject -->
</body>

</html>
