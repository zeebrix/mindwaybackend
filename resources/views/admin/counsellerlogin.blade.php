<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Mindway</title>
  <!-- base:css -->
  <link rel="stylesheet" href="{{ asset('/vendors/typicons/typicons.css') }}">
  <link rel="stylesheet" href="{{ asset('/vendors/css/vendor.bundle.base.css') }}">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
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

    .login-container {
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
      flex: 0.6;
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
      position: relative;
      z-index: 2;
    }

    .left-section h1 {
      font-size: 20px;
      font-weight: 600;
      color: #333;
      margin-bottom: 10px;
      position: relative;
      z-index: 2;
    }

    .login-form {
      flex: 1;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .login-form h2 {
      font-size: 24px;
      font-weight: 700;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      font-size: 14px;
      margin-bottom: 5px;
      color: #666;
    }

    .form-group input {
      width: 100%;
      height: 100% !important;
      border-radius: 20px;
    }

    .btn-login {
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

    .btn-login:hover {
      background: #688EDC;
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
  <div class="login-container">
    <!-- Left Section -->
    <div class="left-section">
      <img src="{{ asset('/logo/loginLogo.png') }}" alt="Mindway Logo">
      <img src="{{ asset('/logo/logo.png') }}" width="147px" height="29px" alt="Mindway Logo">
      <h1>The #1 EAP platform, </h1>
      <h1> in the one place.</h1>

    </div>

    <!-- Login Form Section -->
    <div class="login-form">
      <img src="{{ asset('/logo/logo.png') }}" width="147px" height="29px" alt="Mindway Logo">
      <h2 style="margin-left: 20px;
    margin-top: 6px;">Log In to Counsellor Portal</h2>


      <form class="pt-3" method="POST" action="{{ url('/counsellersesion') }}">
        @csrf
        <div class="form-group">
          <label class="form-label" style="font-weight: 600;">Your Email</label>
          <input type="email" name="email" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="your@email.com">
        </div>
        <div class="form-group">
          <label class="form-label" style="font-weight: 600;">Your Password</label>
          <input type="password" name="password" class="form-control form-control-lg" id="exampleInputPassword1" placeholder="*********">
        </div>
        @if(session()->has('error'))
        <span style="color:red" id="login-error">
          {{ session()->get('error') }}
        </span>
        @endif
        <div class="mt-3">
          <button
            type="submit"
            class="btn btn-block btn-lg font-weight-medium auth-form-btn"
            style="background-color: #688EDC; border-radius: 20px; color: white; {{ session('account_locked') ? 'opacity: 0.6; cursor: not-allowed;' : '' }}"
            {{ session('account_locked') ? 'disabled' : '' }}>
            {{ session('account_locked') ? 'Account Locked' : 'Login' }}
          </button>

        </div>
        <div class="my-2 d-flex justify-content-center">

      </form>

      <div class="helper-links">
        <a href="{{route('password.request','counsellor')}}" style="color:#8A8A8E;">Forgot password?</a>
      </div>
    </div>
  </div>
  <!-- container-scroller -->
  <!-- base:js -->
  <script src="{{ asset('/vendors/js/vendor.bundle.base.js') }}"></script>
  <!-- endinject -->
  <!-- inject:js -->
  <script src="{{ asset('/js/off-canvas.js') }}"></script>
  <script src="{{ asset('/js/hoverable-collapse.js') }}"></script>
  <script src="{{ asset('/js/template.js') }}"></script>
  <script src="{{ asset('/js/settings.js') }}"></script>
  <script src="{{ asset('/js/todolist.js') }}"></script>
</body>

</html>