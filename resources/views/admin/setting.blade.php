@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')


    <h5 class="mb-4">Settings</h5>

    <div class="col-12 me-3">
        <div class="card" style="border-radius: 20px">
        @if (session('success'))
                    <span class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </span>
                @endif
            <div style="padding: 127px 0px 0px 5px 20px !important" class="card-body d-flex justify-content-center">
           
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="col-12">

                        <form method="post" action="{{ route('admin.save-setting') }}">
                            @csrf

                            <!-- Enable 2FA Toggle -->

                            <div class="m-4">
                                <label for="enable_2fa">
                                    <input type="checkbox" id="enable_2fa" name="enable_2fa"
                                        {{ $user->uses_two_factor_auth ? 'checked' : '' }}>
                                    Enable Two-Factor Authentication
                                </label>
                            </div>

                            <!-- 2FA Setup Section -->
                            <div id="2fa-setup" style="display: {{ $user->uses_two_factor_auth ? 'block' : 'none' }}">
                                <p>
                                    Set up your two-factor authentication by scanning the barcode below.
                                    Alternatively, use the code: <strong id="2fa-secret">{{ $secret }}</strong>
                                </p>

                                <!-- QR Code -->
                                @if ($qrCodeUrl || session('qrCodeUrl'))
                                    <div class="mb-3" id="qr-code-container">
                                        {!! QrCode::size(200)->generate($qrCodeUrl ?? session('qrCodeUrl')) !!}
                                    </div>
                                @endif
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary mindway-btn-blue">Save Settings</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
