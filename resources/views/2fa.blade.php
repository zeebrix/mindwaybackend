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
     .mindway-btn-blue {
      border: unset !important;
      white-space: nowrap !important;
      background: #688EDC !important;
      color: #ffffff !important;
      border-radius: 20px !important;
    }
  </style>
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-8 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo">
                <img src="{{ asset('/logo/logo.png') }}" alt="logo">
              </div>
              <h5 class="font-weight-light">{{ __('Two Factor Authentication') }}.</h5>
              <p>{{ __('Please enter your one-time password to complete your login.') }}</p>

              <form method="POST" action="{{ route('2fa.verify') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="one_time_password" class="col-md-4 col-form-label text-md-right">{{ __('One Time Password') }}</label>

                                <div class="col-md-6">
                                    <input id="one_time_password" type="text" class="form-control @error('one_time_password') is-invalid @enderror" name="one_time_password" required autofocus>

                                    @error('one_time_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary btn-sm mindway-btn-blue">
                                        {{ __('Verify') }}
                                    </button>
                                </div>
                            </div>
                        </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
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
  <!-- endinject -->
</body>

</html>
