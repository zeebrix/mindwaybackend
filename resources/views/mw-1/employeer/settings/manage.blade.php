@extends('mw-1.layout.app')

@section('selected_menu', 'active')

<style>
    .nav-link {
        transition: color 0.3s ease, background-color 0.3s ease;
        /* Smooth transition */
    }


    .nav-link {
        text-decoration: none;
        /* Removes underline or any text decoration */
    }

    a:hover {
        text-decoration: none;
        color: black;
        /* Black color on hover */
    }

    .nav-link.active {
        background-color: #fff;
        /* Change this to your preferred active tab color */
        color: #688EDC !important;
        border-radius: 5px;
        padding: 8px 15px;
    }
</style>

@section('content')

    <div class="row">
        <div class="col-10 offset-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <img height="46px" width="130px" class="popup" src="{{ asset('storage/logo/' . $Program->logo) }}"
                        alt="{{ $Program->company_name }} Logo">
                </div>
                <div>
                    @if ($is_trial)
                        <p><b>On Free Trial:</b> {{ $leftDays }} days left of trial</p>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Settings</h4>
            </div>

            <div class="mb-4 col-12">

                <nav class="navbar navbar-expand-lg navbar-light bg-white">
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item" style="margin-right: 20px;">
                                <a class="nav-link fw-semibold" id="overviewTab" href="#">Overview</a>
                            </li>
                            @if (!$is_trial)
                                <li class="nav-item">
                                    <a class="nav-link fw-semibold" id="planPaymentTab" href="#">Plan & Payment</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </nav>

            </div>

            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            @endif

            <div id="overview">
                <div class="col-12 me-3">
                    <div class="card" style="border-radius: 20px">
                        <div style="padding:10px 0px 5px 20px !important"
                            class="card-body d-flex flex-column justify-content-center p-4">
                            <label for="company_nameId" class="form-label">Organization Name</label>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="col-11">
                                    <input style=" border: none !important; box-shadow: none !important;" type="text"
                                        class="form-control" id="company_nameId" aria-describedby="company_nameHelp"
                                        name="company_name" placeholder="Enter Company Name" readonly
                                        value="{{ $Program->company_name }}">
                                </div>
                                <div class="col-1" id="edit-name">
                                    <i style="font-size:30px" class="ti ti-pencil"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 me-3">
                    <div class="card" style="border-radius: 20px">
                        <div style="padding:15px 0px 5px 20px !important"
                            class="card-body d-flex flex-column justify-content-center">
                            <label for="accessCodeId" class="form-label">Access Code (3-8 characters)</label>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="col-10">
                                    <h5>{{ $Program->code }}</h5>
                                </div>
                                <div class="col-2">
                                    Contact us to change
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <div class="card" style="border-radius: 20px">
                            <div style="height: 75px; cursor: pointer;" class="card-body d-flex align-items-center p-4"
                                id="uploadLogoTrigger">
                                <div style="margin-right: 20px; margin-top:5px" class="d-flex flex-column">
                                    <input type="file" id="uploadLogoInput" style="display: none;" />
                                    <h5>Upload Logo</h5>
                                </div>
                                <div>
                                    <img height="30px" width="40px" class="popup"
                                        src="{{ asset('mw-1/assets/images/upload.png') }}" alt="logo image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="col-12 me-3">
                    <div class="card" style="border-radius: 20px">
                        <div style="padding:20px 0px 5px 20px !important"
                            class="card-body d-flex flex-column justify-content-center p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="col-10">
                                    <h5>License {{ $Program->max_lic }}</h5>
                                </div>
                                <div class="col-2">
                                    Contact us to change
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <br>
                <hr><br>
                <div class="d-flex justify-content-center align-items-center mb-4">
                    <form method="post" action="{{ route('program.save-setting') }}">
                        @csrf

                        <!-- Enable 2FA Toggle -->
                        <div class="mb-3">
                            <label for="enable_2fa" style="display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" id="enable_2fa" name="enable_2fa"
                                    {{ $user->is_2fa_enabled ? 'checked' : '' }}>
                                <span>Enable Two-Factor Authentication</span>
                            </label>
                        </div>

                        <!-- 2FA Setup Section -->
                        <div id="2fa-setup" style="display: {{ $user->is_2fa_enabled ? 'block' : 'none' }}">
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

            <div id="plan_payment">

                <div class="col-12 me-3">
                    <div class="card" style="border-radius: 20px">
                        <div style="padding:15px 0px 5px 20px !important"
                            class="card-body d-flex flex-column justify-content-center">
                            <label for="accessCodeId" class="form-label">Plan Selected</label>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="col-10">
                                    @if ($plan?->plan_type == 1)
                                        Pay As You Go
                                    @elseif ($plan?->plan_type == 2)
                                        Standard
                                    @elseif ($plan?->plan_type == 3)
                                        Premium
                                    @endif
                                </div>
                                <div class="col-2">
                                    Contact us to change
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                @if ($plan?->gst_registered == 1)
                    @php
                        $gst = true;
                    @endphp
                @else
                    @php
                        $gst = false;
                    @endphp
                @endif

                <div class="col-12 me-3">
                    <div class="card" style="border-radius: 20px">
                        <div style="padding:15px 0px 5px 20px !important"
                            class="card-body d-flex flex-column justify-content-center">
                            <label for="accessCodeId" class="form-label">Subscription Fee</label>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="col-10">
                                    $ {{ $plan?->annual_fee }}/Year @if ($gst)
                                        + GST
                                    @endif
                                </div>
                                <div class="col-2">
                                    Contact us to change
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 me-3">
                    <div class="card" style="border-radius: 20px">
                        <div style="padding:15px 0px 5px 20px !important"
                            class="card-body d-flex flex-column justify-content-center">
                            <label for="accessCodeId" class="form-label">Session Cost</label>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="col-10">
                                    $ {{ $plan?->cost_per_session }}/Session @if ($gst)
                                        + GST
                                    @endif
                                </div>
                                <div class="col-2">
                                    Contact us to change
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 me-3">
                    <div class="card" style="border-radius: 20px">
                        <div style="padding:15px 0px 5px 20px !important"
                            class="card-body d-flex flex-column justify-content-center">
                            <label for="accessCodeId" class="form-label">Session Limit</label>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="col-10">
                                    {{ $Program->max_session }} per employee, year
                                </div>
                                <div class="col-2">
                                    Contact us to change
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 me-3">
                    <div class="card" style="border-radius: 20px">
                        <div style="padding:15px 0px 5px 20px !important"
                            class="card-body d-flex flex-column justify-content-center">
                            <label for="accessCodeId" class="form-label">Renewal Date</label>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="col-10">
                                    {{ $plan?->renewal_date }}
                                </div>
                                <div class="col-2">
                                    Contact us to change
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3 text-center">
                    To change any settings, please contact your account manager directly
                </div>

            </div>

        </div>
    </div>

@endsection


@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const overviewTab = document.getElementById('overviewTab');
            const overviewDiv = document.getElementById('overview');
            const planPaymentDiv = document.getElementById('plan_payment');

            // Check if the Plan & Payment tab exists (i.e., the program is not on trial)
            const planPaymentTab = document.getElementById('planPaymentTab');

            // Initial state
            overviewDiv.style.display = 'block'; // Show overview by default
            if (planPaymentDiv) {
                planPaymentDiv.style.display = 'none'; // Hide plan_payment by default if it exists
            }
            overviewTab.classList.add('active'); // Mark overview tab as active by default

            // Event listeners for tab clicks
            overviewTab.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default link behavior
                overviewDiv.style.display = 'block';
                if (planPaymentDiv) {
                    planPaymentDiv.style.display = 'none';
                }

                // Set active class
                overviewTab.classList.add('active');
                if (planPaymentTab) {
                    planPaymentTab.classList.remove('active');
                }
            });

            if (planPaymentTab) {
                planPaymentTab.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent default link behavior
                    overviewDiv.style.display = 'none';
                    if (planPaymentDiv) {
                        planPaymentDiv.style.display = 'block';
                    }

                    // Set active class
                    planPaymentTab.classList.add('active');
                    overviewTab.classList.remove('active');
                });
            }

            $('#edit-name').click(function() {
                // Remove readonly and reset CSS styles
                $('#company_nameId').removeAttr('readonly');
                // Update the HTML of the #edit-name element to show the Save button
                $('#edit-name').html(
                    '<a href="#" id="save-name-form" class="mindway-btn btn btn-primary" style="white-space: nowrap;">Save</a>'
                );

                // Attach a click event listener to the Save button dynamically
                $('#save-name-form').click(function(e) {
                    e.preventDefault(); // Prevent default anchor click behavior

                    // Get the value from the input field
                    var companyName = $('#company_nameId').val();

                    // Send the AJAX request to save the name
                    $.ajax({
                        url: '/manage-program/save-name', // Ensure this matches your Laravel route
                        method: 'POST',
                        data: {
                            company_name: companyName,
                            _token: $('meta[name="csrf-token"]').attr(
                                'content') // Include CSRF token
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                // Reset input to readonly and restore the edit icon
                                $('#company_nameId').attr('readonly', true);
                                $('#edit-name').html(
                                    '<i style="font-size:30px" class="ti ti-pencil"></i>'
                                );
  toastr.success("Company Name Updated Successfully");

                            } else {
                                alert('Failed to update company name: ' + response
                                    .message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error updating company name:', error);
                            toastr.error("An error occurred while updating the company name");
                            // alert('An error occurred while updating the company name.');
                        }
                    });
                });
            });

            // Image save functionality
            document.getElementById('uploadLogoTrigger').addEventListener('click', function() {
                document.getElementById('uploadLogoInput').click();
            });

            document.getElementById('uploadLogoInput').addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;
                var companyName = $('#company_nameId').val();
                // Prepare FormData
                const formData = new FormData();
                formData.append('logo', file);
                formData.append('company_name', companyName);
                // Send AJAX request
                fetch("{{ url('/manage-program/save-program-logo', ['id' => $Program->id]) }}", {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        },
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status == 'success') {
                          toastr.success("Logo Updated Successfully");
                            setTimeout(() => {
                            location.reload();
                            }, 3000);
                        } else {
                            toastr.error("Error uploading logo");
                            // alert("Error uploading logo");
                        }
                    })
                    .catch(error => {
                        toastr.error("Error uploading logo");
                        // console.error("Error:", error);
                        // alert("An unexpected error occurred.");
                    });
            });
        });
    </script>

@endsection
