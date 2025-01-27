@extends('mw-1.layout.app')
@section('selected_menu', 'active')

@section('content')

<style>
    .availability-indicator {
        width: 34px;
        height: 28px;
        display: inline-block;
        margin-right: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 10px;
        position: relative;
    }

    .availability-indicator:hover {
        transform: scale(1.1);
    }

    .time-input {
        width: 120px;
        display: inline-block;
    }

    .day-row {
        padding: 12px 0;
        /* border-bottom: 1px solid #eee; */
        transition: background-color 0.3s ease;
    }

    /* .day-row:hover {
        background-color: #f8f9fa;
    } */

    .day-row:last-child {
        border-bottom: none;
    }

    .day-label {
        min-width: 50px;
        font-weight: 500;
    }

    .unavailable-text {
        color: #6c757d;
        font-style: italic;
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
    /* timezone modal ends here */
</style>


    <div class="row">
        <div class="col-10 offset-1">
<div class="card-body p-4">
    <h2 class="card-title mb-4" style="font-weight:700">My Availability</h2>

    <div class="mb-4">
        <div class="d-flex align-items-center">
            <span class="fw-semibold me-2">Time Zone:</span>
            <span class="fw-normal me-2">
                <span id="selected-timezone">{{$currentTimezone}}</span> -
                <a href="#" class="timezone-link" data-bs-toggle="modal" data-bs-target="#timezoneModal">change</a>
            </span>
        </div>
    </div>

    <div id="availability-container">
        <!-- Days will be populated by JavaScript -->
    </div>

    <div class="mt-4">
        <button class="btn btn-primary w-50" id="saveButton">Save Changes</button>
    </div>
</div>
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

        $(document).ready(function() {
            $('#timezone').select2({
                placeholder: 'Select a timezone',
                allowClear: true
            });
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
                        fetch('public/mw-1/timezones.json')
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
                            // alert('Failed to update time zone. Please try again.');
                        }
                    });
                }
            });

        });
</script>

@include("mw-1.counseller.availability")
@endsection
