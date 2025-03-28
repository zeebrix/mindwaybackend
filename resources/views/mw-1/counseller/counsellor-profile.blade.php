@extends('mw-1.layout.app')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
@section('selected_menu', 'active')

<style>
    .form-title {
        text-align: center;
        font-size: 1.5em;
        font-weight: 600;
        margin-bottom: 1.5em;
        color: #333;
    }

    .form-group {
        margin-bottom: 1.5em;
    }

    .form-group label {
        font-weight: 500;
        margin-bottom: 0.5em;
        display: block;
        color: #555;
    }

    .form-control,
    select,
    textarea {
        width: 100%;
        padding: 0.8em;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1em;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 3px #007bff;
    }

    .radio-group {
        display: flex;
        gap: 1em;
        align-items: center;
    }

    .tab-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.5em 1em;
        border: 1px solid #ddd;
        border-radius: 20px;
        background-color: #f8f9fa;
        margin-right: 0.5em;
    }

    .tab-pill span {
        margin-right: 0.5em;
    }

    .tab-pill .close-btn {
        background: none;
        border: none;
        font-size: 1.2em;
        cursor: pointer;
        color: #dc3545;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
        padding: 0.8em 1.5em;
        font-size: 1em;
        color: #fff;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    #specialisations-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5em;
    }

    .alert {
        margin-bottom: 1em;
    }

    /* Description Css */
    .description-container {
        margin-top: 20px;
    }

    .description-label {
        font-size: 14px;
        color: #333;
        margin-bottom: 10px;
        display: block;
    }

    .description-box {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        font-size: 14px;
        line-height: 1.5;
        color: #555;
        resize: none;
        background-color: #ffffff;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
    }


    /* timezone modal */
    .timezone-search {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        margin-bottom: 10px;
    }

    .timezone-list {
        max-height: 250px;
        overflow-y: auto;
    }

    .timezone-item {
        padding: 10px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .timezone-item:hover {
        background-color: #f0f8ff;
    }

    .timezone-item.selected {
        background-color: #007bff;
        color: white;
    }

    .timezone-item:active {
        background-color: #cce5ff;
    }

    .timezone-item span {
        font-size: 14px;
        color: #555;
    }

    /* Custom close button */
    .btn-close {
        background-color: transparent;
        border: none;
        font-size: 1.5em;
        color: #007bff;
        cursor: pointer;
    }

    .tagify.form-control {
        padding: unset !important;
        display: flex;
        width: unset;
    }

    /* timezone modal ends here */
</style>

@section('content')

<div class="row">
    <div class="col-10 offset-1">
        @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
        @endif
        @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div>
        @endif
        @if ($errors->any())
        @foreach ($errors->all() as $error)
        <div class="alert alert-danger">{{ $error }}</div>
        @endforeach
        @endif

        <h3><b>Profile Setting of {{ $Counselor->name }}</b></h3>
        <br>
        <form action="{{ url('/profile-save') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-3 me-3">
                    <div class="card" style="border-radius: 20px">
                        <div style="height: 75" class="card-body d-flex flex-column justify-content-center p-4">
                            <p class="mb-1">My Role</p>
                            <h5>Counsellor</h5>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="card" style="border-radius: 20px">
                        <div style="height: 75px" class="card-body d-flex justify-content-left align-items-left p-4">
                            <div style="margin-right: 20px" class="d-flex flex-column">
                                <p style="margin-bottom: -3">Time Zone</p>
                                <b id="selected-timezone">{{ $Counselor->timezone }}</b>
                            </div>

                            <a href="#" class="timezone-link" data-bs-toggle="modal"
                                data-bs-target="#timezoneModal">change</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-3">
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

                <div class="col-7 me-3">
                    <div class="card" style="border-radius: 20px">
                        <div style="height: 75px" class="card-body d-flex flex-column justify-content-center p-4">
                            <div class="d-flex flex-wrap justify-content-start" style="gap: 10px;">
                                <div class="btn-group mt-2" role="group">
                                    <strong>Gender</strong>
                                </div>
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check"
                                        @if ($Counselor->gender == 'Male') checked @endif name="gender" id="male"
                                    value="Male" autocomplete="off">
                                    <label class="btn btn-outline-primary rounded-pill px-4"
                                        style="border-radius: 20px;" for="male">Male</label>
                                </div>

                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check"
                                        @if ($Counselor->gender == 'Female') checked @endif name="gender" id="female"
                                    value="Female" autocomplete="off">
                                    <label class="btn btn-outline-primary rounded-pill px-4"
                                        style="border-radius: 20px;" for="female">Female</label>
                                </div>

                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check"
                                        @if ($Counselor->gender == 'Other') checked @endif name="gender" id="other"
                                    value="Other" autocomplete="off">
                                    <label class="btn btn-outline-primary rounded-pill px-4"
                                        style="border-radius: 20px;" for="other">Other</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-4 me-3">
                <div class="card " style="border-radius: 20px;">
                    <div style="padding:10px 10px 5px 20px !important"
                        class="card-body d-flex flex-column justify-content-center p-4">
                        <label for="notice_periodId" class="form-label">Notice Period</label>
                        <div class="d-flex align-items-center mb-4">
                            <input style="border: none !important; box-shadow: none !important; margin-right: 10px;"
                                type="number" class="form-control" id="notice_periodId"
                                aria-describedby="notice_periodHelp" name="notice_period" placeholder="Enter Hours"
                                value="{{ $Counselor->notice_period }}">
                            <h4>hours</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 me-3">
                <div class="card" style="border-radius: 20px;">
                    <div style="padding: 10px 0px 5px 20px !important"
                        class="card-body d-flex flex-column justify-content-center p-4">
                        <label for="description" class="form-label">My Counsellor Page Description</label>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="col-11">
                                <textarea style="border: none !important; box-shadow: none !important;" name="description" class="form-control"
                                    id="description" rows="5" readonly>{{ $Counselor->description }}</textarea>
                            </div>
                            <div class="col-1" id="edit-description">
                                <i style="font-size:30px" class="ti ti-pencil"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
            $specialization = json_decode($Counselor->specialization, true);
            $language = $Counselor->language;
            $location = $Counselor->location;
            @endphp
            <span id="default-location" style="display: none;">{{$location}}</span>
            <span id="default-languages" style="display: none;">{{$language}}</span>
            <div class="col-12 me-3">
                <div class="card" style="border-radius: 20px">
                    <div class="card-body d-flex flex-column justify-content-center p-4">
                        <label class="form-label" for="specializations">Select Specialization:</label>
                        <input type="text" style="padding: unset;" id="tagsInput" name="tags" class="form-control" placeholder="Select Specialization" />
                    </div>
                </div>
            </div>
            <br>
            <div class="col-12 me-3">
                <div class="card" style="border-radius: 20px">
                    <div class="card-body d-flex flex-column justify-content-center p-4">
                        <label class="form-label" for="location">Select Location:</label>
                        <select id="location" class="form-control select2" name="location" style="width: 300px;">
                            <option value="">Select a location</option>
                        </select>
                    </div>
                </div>
            </div>
            <br>
            <div class="col-12 me-3">
                <div class="card" style="border-radius: 20px">
                    <div class="card-body d-flex flex-column justify-content-center p-4">
                        <label class="form-label" for="language">Select Language:</label>
                        <select id="language" class="form-control select2" multiple="multiple" name="language[]" style="width: 300px;">
                            <option value="">Select a language</option>
                        </select>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-12 me-3">
                    <div class="card" style="border-radius: 20px">
                        <div style="height: 150" class="card-body d-flex flex-column justify-content-center p-4">
                            <h5>My Other Calendars</h5>
                            <p class="mb-1">Link existing calendars to prevent double booking</p>
                            <a href="{{ route('auth.google.redirect', ['id' => $Counselor->id]) }}">Link to my google
                                calendar</a>
                            <span>{{ $Counselor->google_id }}</span>
                            <span>{{ $Counselor->google_name }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-8 me-3">
                    <div class="card" style="border-radius: 20px">
                        <div class="card-body d-flex flex-column justify-content-center p-4">
                            <h5>Select Your Preferred Communication Method</h5>
                            <div class="d-flex flex-wrap justify-content-start mt-4" style="gap: 10px;">
                                <div class="btn-group" role="group">
                                    <input type="checkbox" class="btn-check"
                                        @if (in_array('Phone Call', json_decode($Counselor->communication_method) ?? [])) checked @endif name="communication_methods[]"
                                    value="Phone Call" autocomplete="off" id="phone-call">
                                    <label class="btn btn-outline-primary rounded-pill px-4"
                                        style="border-radius: 20px;" for="phone-call">Phone Call</label>
                                </div>
                                <div class="btn-group" role="group">
                                    <input type="checkbox" class="btn-check"
                                        @if (in_array('Video Call', json_decode($Counselor->communication_method) ?? [])) checked @endif name="communication_methods[]"
                                    value="Video Call" autocomplete="off" id="video-call">
                                    <label class="btn btn-outline-primary rounded-pill px-4"
                                        style="border-radius: 20px;" for="video-call">Video Call</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 me-3">
                    <div class="card" style="border-radius: 20px">
                        <div style="padding:10px 0px 5px 20px !important"
                            class="card-body d-flex flex-column justify-content-center p-4">
                            <label for="intake_linkId" class="form-label">Intake Link</label>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="col-12">
                                    <input style=" border: none !important; box-shadow: none !important;"
                                        type="url" class="form-control" id="intake_linkId"
                                        aria-describedby="intake_linkHelp" name="intake_link"
                                        placeholder="Enter Intake Link" value="{{ $Counselor->intake_link }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="card" style="border-radius: 20px">
                            <div style="height: 75px; cursor: pointer;" class="card-body d-flex align-items-center p-4" id="uploadIntroTrigger">
                                <div style="margin-right: 20px; margin-top:5px" class="d-flex flex-column">
                                    <input type="file" id="uploadIntroInput" style="display: none;" accept="video/*" />
                                    <h5>Upload Intro Video</h5>
                                </div>
                                <div id="videoPreviewContainer">
                                    <img id="videoThumbnail" height="30px" width="40px" class="popup"
                                        src="{{ asset('mw-1/assets/images/play.png') }}" alt="logo image">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card" style="border-radius: 20px">
                            <div style="height: 100%;" id="uploadIntroTrigger">
                                <div id="videoPreviewContainer" class="d-flex justify-content-center mb-3" >
                                    @if($Counselor->intro_file ?? false)
                                    <video id="uploadedVideo" width="200" height="200">
                                        <source src="{{ asset('storage/Intro/'.$Counselor->intro_file) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="mindway-btn-blue btn btn-primary">Submit</button>
                </div>
        </form>
        <div class="modal fade" id="timezoneModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h5 class="modal-title">Select Time Zone</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="timezone-search">
                            <input type="text" class="form-control" id="timezone-search"
                                placeholder="Search time zones...">
                        </div>
                        <div class="timezone-list" id="timezone-list">
                            <!-- Default time zones will be added here initially -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let input = document.querySelector("#tagsInput");

        // Define predefined options
        let specializations = [
            "Stress",
            "Burnout",
            "Anxiety",
            "Depression",
            "Grief & Loss",
            "Sleep Difficulties",
            "Conflict Resolution",
            "Family & Relationship Issues",
            "Leader/Manager Support",
            "Addiction",
            "Trauma & PTSD",
            "Work-Life Balance",
            "Personal Development",
            "Career Counselling",
            "Mindfulness",
            "Coping Strategies",
            "Life Transitions",
            "Anger Management",
            "Confidence Building",
            "Parenting Support",
            "Sexuality & Identity Issues",
            "Workplace Bullying & Harassment",
            "Communication Skills",
            "Motivation & Goal Setting",
            "Eating Disorders",
            "Body Image Issues",
            "Cognitive Behavioural Therapy (CBT)",
            "Emotional Regulation",
            "Finding Purpose",
            "Personal Boundaries",
            "Phobias & Fears",
            "Spirituality & Faith Issues",
            "Domestic Violence Support",
            "Health & Wellness"
        ];
        let selectedSpecializations = @json($specialization ?? []);
        // Initialize Tagify with whitelist
        let tagify = new Tagify(input, {
            whitelist: specializations, // Predefined options
            enforceWhitelist: true, // Prevent custom input
            dropdown: {
                enabled: 0, // Show suggestions when typing
                maxItems: 100
            }
        });
        tagify.addTags(selectedSpecializations);
        document.querySelector("form").addEventListener("submit", function() {
            input.value = tagify.value.map(item => item.value).join(", ");
        });
    });
</script>
<script src="{{ asset('/mw-1/dropdowns.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#location').select2({
            placeholder: 'Select a location',
            allowClear: true,
            width: '100%'
        });
        $('#language').select2({
            placeholder: 'Select a language',
            allowClear: true,
            width: '100%'
        });
        loadLocation();
        loadLanguage();
    });
    $(document).ready(function() {
        let counselorId = (window.location.pathname.split('/')[3] || 0);
        counselorId = isNaN(counselorId) ? phpvar : parseInt(counselorId, 10);
        // Function to initialize the time zone list
        $(document).ready(function() {
            let timeZones = []; // Array to store time zone data

            // Function to initialize the time zone list
            function initializeTimezoneList() {
                const list = $('#timezone-list');
                list.empty(); // Clear the list before populating

                // Load the first 5 timezones (or fewer if there aren't enough)
                timeZones.slice(0, 5).forEach(tz => {
                    list.append(
                        $('<div>')
                        .addClass('timezone-item p-2')
                        .text(tz.name)
                        .data('timezone', tz.name)
                        .click(function() {
                            selectTimezone($(this).data('timezone'), $(this).text());
                        })
                    );
                });
            }

            // Function to load time zones (fetch only once)
            function loadTimeZones() {
                if (timeZones.length === 0) { // Fetch only if not already loaded
                    fetch('/public/mw-1/timezones.json')
                        .then(response => response.json())
                        .then(data => {
                            timeZones = data.timezones; // Assign fetched data
                            initializeTimezoneList(); // Populate the list
                        })
                        .catch(error => {
                            console.error('Error fetching timezones:', error);
                        });
                } else {
                    initializeTimezoneList(); // Use existing data
                }
            }

            // Listen for modal show event to load time zones
            $('#timezoneModal').on('show.bs.modal', function() {
                loadTimeZones();
            });

            // Search functionality
            $('#timezone-search').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                const filteredZones = timeZones.filter(tz =>
                    tz.name.toLowerCase().includes(searchTerm)
                );

                const list = $('#timezone-list');
                list.empty(); // Clear the list before populating

                filteredZones.forEach(tz => {
                    list.append(
                        $('<div>')
                        .addClass('timezone-item p-2')
                        .text(tz.name)
                        .data('timezone', tz.name)
                        .click(function() {
                            selectTimezone($(this).data('timezone'), $(this)
                                .text());
                        })
                    );
                });
            });
            // Function to handle time zone selection
            function selectTimezone(timezoneId, timezoneDisplay) {
                $('#selected-timezone').text(timezoneDisplay);
                console.log('Selected Time Zone:', timezoneId, timezoneDisplay);
                $('#timezoneModal').modal('hide'); // Close the modal after selection
                // Send AJAX request to save the selected timezone
                $.ajax({
                    url: '/save-timezone', // Replace with your actual endpoint
                    type: 'POST',
                    data: {
                        // counselorId: counselorId,
                        timezone: timezoneId, // The ID of the selected timezone
                        _token: $('meta[name="csrf-token"]').attr(
                            'content'), // CSRF token for security (if using Laravel or similar)
                    },
                    success: function(response) {
                        toastr.success("TimeZone updated successfully");

                    },
                    error: function(xhr, status, error) {
                        toastr.error("Failed to update time zone. Please try again");
                        // console.error('Error saving time zone:', error);

                    }
                });
            }
        });

    });
</script>
<script>
    document.getElementById('edit-description').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default link behavior
        const descriptionBox = document.getElementById('description');
        descriptionBox.removeAttribute('readonly'); // Remove the readonly attribute
        descriptionBox.focus(); // Optional: focus the textarea for immediate editing
    });

    // Image save functionality
    document.getElementById('uploadLogoTrigger').addEventListener('click', function() {
        document.getElementById('uploadLogoInput').click();
    });
    document.getElementById('uploadIntroTrigger').addEventListener('click', function() {
        document.getElementById('uploadIntroInput').click();
    });
    document.getElementById('uploadIntroInput').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        // Prepare FormData
        const formData = new FormData();
        formData.append('intro_video', file);
        formData.append('counselorId', "{{$Counselor->id}}");
        // Send AJAX request
        fetch("{{ url('/save-counsellor-intro-video') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status == 'success') {
                    toastr.success("Intro Video Added Successfully");
                    setTimeout(() => {
                        location.reload();
                    }, 4000);
                } else {
                    toastr.error("Error uploading File");
                    // alert("Error uploading logo");
                }
            })
            .catch(error => {
                toastr.error("An unexpected error occurred");
                // console.error("Error:", error);
                // alert("An unexpected error occurred.");
            });
    });
    document.getElementById('uploadLogoInput').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        // Prepare FormData
        const formData = new FormData();
        formData.append('logo', file);
        // Send AJAX request
        fetch("{{ url('/save-counsellor-logo') }}", {
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
                    }, 4000);
                } else {
                    toastr.error("Error uploading logo");
                    // alert("Error uploading logo");
                }
            })
            .catch(error => {
                toastr.error("An unexpected error occurred");
                // console.error("Error:", error);
                // alert("An unexpected error occurred.");
            });
    });
</script>
@endsection