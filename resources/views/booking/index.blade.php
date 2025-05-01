@extends('mw-1.layout.app')
@section('selected_menu', 'active')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .calendar-day.selected {
        background-color: #687EDC;
        color: white;
    }

    .time-slot.selected {
        background-color: #687EDC;
        color: white;
    }
</style>
<style>
    .loader {
        border-top-color: #687EDC;
        animation: spin 1s linear infinite;
    }

    .transition-opacity {
        transition: opacity 0.3s ease;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

@endsection
<div class="row" style="padding-left: 100px;">
    <div class="min-h-screen flex items-center justify-center">
        <div class="rounded-[32px] p-8 max-w-3xl w-full relative">
            <!-- Close Button -->
            <!-- <button class="absolute right-6 top-6 text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button> -->

            <!-- Header -->
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Book 50min Session in for {{$customer->name}}</h1>

            <!-- Communication Type Toggle -->
            @if($counselor->communication_method && $counselor->communication_method != 'null' && $counselor->communication_method != null)
            <p class="text-gray-500 mb-6">Communication Preference</p>

            <div class="flex gap-2 mb-8">
                @if(in_array('Phone Call',Json_decode($counselor->communication_method)))
                <button type="button" class="communication-type px-6 py-2.5 rounded-full transition-all" data-type="Phone Call">
                    Phone Call
                </button>
                @endif
                @if(in_array('Video Call',Json_decode($counselor->communication_method)))

                <button type="button" class="communication-type px-6 py-2.5 rounded-full transition-all" data-type="Video Call">
                    Video Call
                </button>
                @endif
            </div>
            @endif
            <div id="loader" class="hidden fixed top-0 left-0 w-full h-full bg-white bg-opacity-50 flex items-center justify-center z-[9999]">
                <div class="loader border-t-4 border-blue-500 border-solid rounded-full w-10 h-10 animate-spin"></div>
            </div>

            <!-- Booking Form -->
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Calendar -->
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Select a date</h3>
                    <div class="calendar bg-white rounded-lg">
                        <div class="flex justify-between items-center mb-4">
                            <button id="prevMonth" class="p-1 hover:bg-gray-100 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </button>
                            <h4 id="currentMonth" class="text-lg font-medium text-gray-900">April 2025</h4>
                            <button id="nextMonth" class="p-1 hover:bg-gray-100 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-center text-sm mb-2">
                            <div class="text-gray-500 font-medium">Mon</div>
                            <div class="text-gray-500 font-medium">Tue</div>
                            <div class="text-gray-500 font-medium">Wed</div>
                            <div class="text-gray-500 font-medium">Thu</div>
                            <div class="text-gray-500 font-medium">Fri</div>
                            <div class="text-gray-500 font-medium">Sat</div>
                            <div class="text-gray-500 font-medium">Sun</div>
                        </div>
                        <div id="calendarDays" class="grid grid-cols-7 gap-1">
                            <!-- Calendar days will be inserted here by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Time Slots -->
                <div id="timeSlotContainer" class="hidden">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Select a time</h3>
                    <div class="grid grid-cols-2 gap-3" id="timeSlots">
                        <!-- Time slots will be inserted here by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Timezone -->
            <p id="timezoneDisplay" class="text-sm text-gray-500 mt-6"></p>

            <!-- Confirm Button -->
            <button id="confirmButton" class="w-full mt-8 bg-[#688EDC] text-white py-3.5 rounded-full hover:bg-[#688EDC] transition-all font-medium">
                Confirm Time and Book
            </button>
        </div>
    </div>
</div>
<div class="modal fade" id="bookSlot" tabindex="-1" aria-labelledby="bookSlotLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookSlotLabel">Add Counselling Session For Person Name</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bookSlotForm" action="" method="POST">
                    @csrf
                    <input type="hidden" id="counselor_id" name="counselor_id" value="">
                    <input type="hidden" id="slot_id" name="slot_id" value="">
                    <input type="hidden" id="customer-timezone" name="customer_timezone" value="">
                    <input type="hidden" id="communication_type" name="communication_method" value="">
                    <input type="hidden" id="customer_id" name="customer_id" value="">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="employee-name" class="form-label">Employee Name</label>
                            <input type="text" class="form-control" id="employee_name" name="employee_name"
                                value="" placeholder="Enter employee Name">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="employee-name" class="form-label">Employee Email</label>
                            <input type="email" class="form-control" id="employee_email" name="employee_email"
                                value="" placeholder="Enter employee Email">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="employee-phone" class="form-label">Employee Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="" placeholder="Enter employee Phone">
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex align-items-center">
                            <span class="fw-semibold me-2">Time Zone:</span>
                            <span class="fw-normal me-2">
                                <span id="customer-timezone-div">Set Timezone</span> -
                                <a href="#" class="timezone-link" data-bs-toggle="modal" data-bs-target="#timezoneModal">change</a>
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex align-items-center">
                            <span id="instruction"></span>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary w-100"></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/luxon@3/build/global/luxon.min.js"></script>

<script>
    function getValidTimezone(tz) {
        const timezoneMap = {
            'PKT': 'Asia/Karachi',
            'NZST': 'Pacific/Auckland',
            'GMT': 'Etc/GMT',
            'AWST': 'Australia/Perth',
            'AEDT': 'Australia/Sydney',
            'AEST': 'Australia/Sydney'
        };

        // If it's a valid IANA timezone, return it
        if (luxon.DateTime.local().setZone(tz).isValid) {
            return tz;
        }

        // Otherwise, check the mapping
        return timezoneMap[tz] || null;
    }
    const counselorTimezone = "{{ $counselor->timezone ?? 'Australia/Adelaide' }}";
    let customerTimezone = "{{ $customer?->customer?->timezone ?? 'null' }}";
    customerTimezone = getValidTimezone(customerTimezone);
    console.log(customerTimezone);
    document.getElementById('timezoneDisplay').textContent = `Timezone: ${counselorTimezone}`;
</script>


<div class="modal fade" id="timezoneModal" tabindex="-1" aria-labelledby="timezoneModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="timezoneModalLabel">Select Timezone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <select id="timezoneSelect" class="form-select" style="width: 100%;"></select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveTimezone">Save</button>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
    let timeZones = [];
    let selectedTime = null;
    $('#timezoneModal').on('show.bs.modal', function() {
        loadTimeZones();
    });

    function loadTimeZones() {
        const select = $('#timezoneSelect');
        const currentSelected = $('#customer-timezone-div').text().trim();
        if (timeZones.length === 0) {
            fetch('/public/mw-1/timezones.json')
                .then(response => response.json())
                .then(data => {
                    timeZones = data.timezones;

                    // Populate Select2 options
                    let options = timeZones.map(tz => `<option value="${tz.name}">${tz.name}</option>`);
                    select.html(options);

                    // Initialize Select2 if not already
                    select.select2({
                        dropdownParent: $('#timezoneModal'),
                        width: '100%'
                    });
                    select.val(currentSelected).trigger('change');
                })
                .catch(error => {
                    toastr.error("Error fetching timezones");
                });
        } else {
            // Already loaded, just populate Select2
            let options = timeZones.map(tz => `<option value="${tz.name}">${tz.name}</option>`);
            select.html(options);

            select.select2({
                dropdownParent: $('#timezoneModal'),
                width: '100%'
            });
            select.val(currentSelected).trigger('change');
        }
    }


    $('#saveTimezone').on('click', function() {
        let selectedTimezone = $('#timezoneSelect').val();
        $('#selected-timezone').text(selectedTimezone);
        $('#customer-timezone').val(selectedTimezone);
        $('#customer-timezone-div').text(selectedTimezone);


        const bookingDateTime = luxon.DateTime.fromISO(selectedTime, {
            zone: 'utc'
        }).setZone(counselorTimezone);
        const customerDateTime = bookingDateTime.setZone(selectedTimezone);
        const instructionText = `This session will be booked for ${bookingDateTime .toFormat('h:mma')} your time (${counselorTimezone}) and sent to the employee at ${customerDateTime.toFormat('h:mma')} their time (${selectedTimezone})`;
        document.getElementById('instruction').innerText = instructionText;



        $('#timezoneModal').modal('hide');
        $('#bookSlot').modal('show');
    });
</script>
<script>
    let communication_method = null;
    document.addEventListener('DOMContentLoaded', function() {
        const DateTime = luxon.DateTime;
        const counselorTimeZone = "{{ $counselor->timezone ?? 'Australia/Adelaide' }}"; // Counselor's time zone
        const token = 'Waseem#2023MobAPP';
        const counselor_id = "{{$counselor->id}}";
        const customer_id = "{{ $customer->app_customer_id ?? $customer->id }}";
        const csrfToken = '{{ csrf_token() }}';

        const modal = new bootstrap.Modal(document.getElementById('bookSlot'));

        // Show loader
        function showLoader() {
            document.getElementById('loader').classList.remove('hidden');
        }

        function hideLoader() {
            document.getElementById('loader').classList.add('hidden');
        }

        // Toggle communication method
        function toggleCommunication() {
            const buttons = document.querySelectorAll('.communication-type');
            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    buttons.forEach(btn => btn.classList.remove('text-white', 'bg-[#688EDC]', 'hover:bg-[#688EDC]'));
                    button.classList.add('text-white', 'bg-[#688EDC]', 'hover:bg-[#688EDC]');
                    communication_method = button.getAttribute('data-type');
                });
            });
        }

        toggleCommunication();

        const today = DateTime.now().setZone(counselorTimeZone);
        let currentDate = today.startOf('month');
        let selectedDate = null;
        let slot_id = null;

        // Fetch available dates for the month
        async function fetchAvailableDates(year, month) {
            showLoader();
            try {
                const response = await fetch(`/api/customer/counselor/calendar?counselor_id=${counselor_id}&year=${year}&month=${month}`, {
                    headers: {
                        'app-auth-token': token
                    }
                });
                const data = await response.json();
                return data.dates || [];
            } finally {
                hideLoader();
            }
        }

        // Generate the calendar for the selected month
        // Generate the calendar for the selected month
        async function generateCalendar(year, month) {
            // Fetch available dates for the given month and year
            const availableDates = await fetchAvailableDates(year, month);

            // Convert the available dates' first slot times from UTC to counselor's timezone
            const availableSet = new Set(availableDates.map(d => {
                const convertedTime = convertFirstSlotTimeToCounselorTZ(d.first_slot_time);
                return convertedTime.toISODate(); // Get the date part in counselor's timezone
            }));

            // Ensure firstDay is correctly converted to the counselor's timezone (Australia/Adelaide)
            const firstDay = DateTime.fromObject({
                year,
                month,
                day: 1
            }, {
                zone: counselorTimeZone
            });
            const daysInMonth = firstDay.daysInMonth;

            // Calculate the firstDayIndex for rendering the calendar
            const firstDayIndex = (firstDay.weekday + 6) % 7; // Luxon: 1=Monday, 7=Sunday

            let html = '';
            // Create empty divs for days before the first day
            for (let i = 0; i < firstDayIndex; i++) {
                html += '<div class="h-10"></div>';
            }

            // Loop through the days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const date = DateTime.fromObject({
                    year,
                    month,
                    day
                }, {
                    zone: counselorTimeZone
                });
                const formattedDate = date.toISODate(); // Date in the counselor's timezone
                const isAvailable = availableSet.has(formattedDate);
                const isFutureOrToday = date >= today.startOf('day');

                const isDisabled = !isAvailable || !isFutureOrToday;
                const isSelected = selectedDate === formattedDate;
                const classes = [
                    'calendar-day h-10 w-10 mx-auto rounded-full transition-all',
                    isDisabled ? 'disabled opacity-30 cursor-not-allowed' : 'hover:bg-gray-100 bg-blue-100 text-blue-800',
                    isSelected ? 'selected border border-blue-700' : ''
                ].join(' ');

                // Add the day button to the calendar
                html += `<button class="${classes}" data-date="${formattedDate}" ${isDisabled ? 'disabled' : ''}>${day}</button>`;
            }

            // Inject the calendar into the DOM
            document.getElementById('calendarDays').innerHTML = html;
            document.getElementById('currentMonth').textContent = firstDay.toFormat('LLLL yyyy'); // Month in counselor's timezone

            // Add event listeners for day buttons
            document.querySelectorAll('.calendar-day').forEach(day => {
                day.addEventListener('click', function() {
                    if (this.disabled) return;
                    selectedDate = this.dataset.date;
                    document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
                    this.classList.add('selected');
                    showTimeSlots(selectedDate);
                });
            });

            // Enable/disable the previous month button
            togglePrevButton();
        }

        // Helper function to convert first slot time from UTC to counselor's timezone
        function convertFirstSlotTimeToCounselorTZ(firstSlotTimeUTC) {
            const slotTime = DateTime.fromISO(firstSlotTimeUTC, {
                zone: 'utc'
            }); // Parse UTC time
            const convertedTime = slotTime.setZone(counselorTimeZone); // Convert to counselor's timezone
            return convertedTime; // Return the converted date-time in counselor's timezone
        }



        // Previous month button click handler
        document.getElementById('prevMonth').addEventListener('click', () => {
            if (currentDate.hasSame(today, 'month')) return;
            currentDate = currentDate.minus({
                months: 1
            });
            generateCalendar(currentDate.year, currentDate.month);
        });

        // Next month button click handler
        document.getElementById('nextMonth').addEventListener('click', () => {
            currentDate = currentDate.plus({
                months: 1
            });
            generateCalendar(currentDate.year, currentDate.month);
        });

        // Toggle previous button state
        function togglePrevButton() {
            const prevBtn = document.getElementById('prevMonth');
            const isCurrent = currentDate.hasSame(today, 'month');
            prevBtn.disabled = isCurrent;
            prevBtn.style.opacity = isCurrent ? 0.4 : 1;
            prevBtn.style.cursor = isCurrent ? 'not-allowed' : 'pointer';
        }

        // Show time slots for a selected date
        // Show time slots for a selected date
        // Show time slots for a selected date
        async function showTimeSlots(date) {
            showLoader();
            try {
                const response = await fetch(`/api/customer/available-slots?customer_timezone=${counselorTimeZone}&counselor_id=${counselor_id}&date=${date}`, {
                    headers: {
                        'app-auth-token': token
                    }
                });
                const data = await response.json();
                const timeSlotsDiv = document.getElementById('timeSlots');
                document.getElementById('timeSlotContainer').classList.remove('hidden');

                // Convert the selected date to the counselor's timezone (Australia/Adelaide)
                const selectedDateInCounselorTZ = DateTime.fromISO(date, {
                    zone: counselorTimeZone
                }).startOf('day'); // Start of day in counselor timezone
                console.log('Selected date in counselor timezone:', selectedDateInCounselorTZ.toString());

                timeSlotsDiv.innerHTML = data
                    .map(slot => {
                        // Convert the slot's start time from UTC to the counselor's timezone
                        const start = DateTime.fromISO(slot.start_time, {
                            zone: 'utc'
                        }).setZone(counselorTimeZone);
                        const slotDateInCounselorTZ = start.startOf('day'); // Only compare date, not time
                        const selectedDateFormatted = selectedDateInCounselorTZ.toISODate(); // Format selected date

                        // If the slot date in counselor timezone matches the selected date, display it
                        if (slotDateInCounselorTZ.toISODate() === selectedDateFormatted) {
                            const startFormatted = start.toFormat('hh:mm a');
                            return `<button class="time-slot px-6 py-3 rounded-full bg-blue-50 hover:bg-blue-100 transition-all text-gray-900"
                            data-time="${slot.start_time}" data-id="${slot.id}">${startFormatted}</button>`;
                        }
                        return ''; // Skip slot if the date doesn't match
                    })
                    .join(''); // Combine the slot buttons

                // Add event listeners to time slots for booking
                document.querySelectorAll('.time-slot').forEach(slot => {
                    slot.addEventListener('click', function() {
                        selectedTime = this.dataset.time;
                        slot_id = this.dataset.id;
                        document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                        this.classList.add('selected');
                        reserveSlot(slot_id);
                    });
                });

            } finally {
                hideLoader();
            }
        }



        // Reserve the selected slot
        async function reserveSlot(id) {
            try {
                await fetch(`/api/customer/reserved-slot`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'app-auth-token': token
                    },
                    body: JSON.stringify({
                        customer_id,
                        slot_id: id
                    })
                });
            } catch (e) {
                console.error(e);
            }
        }

        // Confirm the selected booking
        document.getElementById('confirmButton').addEventListener('click', () => {
            if (!communication_method) {
                alert('Please select communication type first.');
                return;
            }
            if (!selectedDate || !selectedTime) {
                alert('Please select both a date and time.');
                return;
            }

            document.getElementById('bookSlotLabel').textContent = `Book New Session for {{$customer->name}}`;
            document.querySelector('button[type="submit"]').textContent = `Confirm and Send`;

            modal.show();
            document.getElementById('customer_id').value = customer_id;
            document.getElementById('counselor_id').value = counselor_id;
            document.getElementById('slot_id').value = slot_id;
            document.getElementById('communication_type').value = communication_method;
            document.getElementById('customer-timezone').value = customerTimezone;
            document.getElementById('customer-timezone-div').innerText = (customerTimezone && customerTimezone != 'null') ? customerTimezone : 'Set Timezone';
            if (customerTimezone && customerTimezone != 'null') {
                const bookingDateTime = luxon.DateTime.fromISO(selectedTime, {
                    zone: 'utc'
                }).setZone(counselorTimezone);
                const customerDateTime = bookingDateTime.setZone(customerTimezone);
                const instructionText = `This session will be booked for ${bookingDateTime .toFormat('h:mma')} your time (${counselorTimezone}) and sent to the employee at ${customerDateTime.toFormat('h:mma')} their time (${customerTimezone})`;
                document.getElementById('instruction').innerText = instructionText;
            }
            document.getElementById('employee_name').value = "{{$customer->name}}";
            document.getElementById('employee_email').value = "{{$customer->email}}";
            document.getElementById('phone').value = "{{$customer?->customer?->phone}}";
        });

        // Form submission
        document.getElementById('bookSlotForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            showLoader();

            const formData = new FormData(this);
            const payload = Object.fromEntries(formData.entries());

            if (!payload.communication_method) {
                hideLoader();
                alert('Please select communication type first.');
                return;
            }
            if (!payload.customer_timezone) {
                hideLoader();
                alert('Please select customer timezone to proceed.');
                return;
            }

            try {
                const response = await fetch(`/api/customer/book-slot`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'app-auth-token': token
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();
                hideLoader();
                if (result.status === "confirmed") {
                    alert('Session booked successfully!');
                    $('#bookSlot').modal('hide');
                    window.location.href = "/counseller/dashboard";
                } else {
                    alert(result.message || 'Booking failed.');
                }
            } catch (err) {
                hideLoader();
                console.error(err);
                alert(err);
            }
        });

        // Generate the calendar for the current month
        generateCalendar(currentDate.year, currentDate.month);
    });
</script>


@endsection