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
            <button class="absolute right-6 top-6 text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <!-- Header -->
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Book 50min Session in for {{$customer->name}}</h1>

            <!-- Communication Type Toggle -->
            @if($counselor->communication_method && $counselor->communication_method != 'null' && $counselor->communication_method != null)
            <p class="text-gray-500 mb-6">Communication Preference</p>

            <div class="flex gap-2 mb-8">
                @if(in_array('Phone Call',Json_decode($counselor->communication_method)))
                <button type="button" class="communication-type px-6 py-2.5 rounded-full text-gray-700 bg-gray-100 hover:bg-gray-200 transition-all" data-type="Phone Call">
                    Phone Call
                </button>
                @endif
                @if(in_array('Video Call',Json_decode($counselor->communication_method)))

                <button type="button" class="communication-type px-6 py-2.5 rounded-full text-gray-700 bg-gray-100 hover:bg-gray-200 transition-all" data-type="Video Call">
                    Video Call
                </button>
                @endif
            </div>
            @endif
            <div id="loader" class="hidden fixed top-0 left-0 w-full h-full bg-white bg-opacity-50 flex items-center justify-center z-50">
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
            <p class="text-sm text-gray-500 mt-6">Timezone: Sydney/Melbourne</p>

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
@section('js')
<script>
    let communication_method = null;

    function showLoader() {
        document.getElementById('loader').classList.remove('hidden');
    }

    function hideLoader() {
        document.getElementById('loader').classList.add('hidden');
    }

    function toggleCommunication() {
        const communicationButtons = document.querySelectorAll('.communication-type');

        communicationButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active styles from all buttons
                communicationButtons.forEach(btn => {
                    btn.classList.remove('text-white', 'bg-[#688EDC]', 'hover:bg-[#688EDC]');
                    btn.classList.add('text-gray-700', 'bg-gray-100', 'hover:bg-gray-200');
                });

                // Add active style to the clicked button
                button.classList.remove('text-gray-700', 'bg-gray-100', 'hover:bg-gray-200');
                button.classList.add('text-white', 'bg-[#688EDC]', 'hover:bg-[#688EDC]');

                // Optional: capture selected type value
                communication_method = button.getAttribute('data-type');
                console.log('Selected communication type:', communication_method);
            });
        });
    }
    document.addEventListener('DOMContentLoaded', function() {


        toggleCommunication();
        const modal = new bootstrap.Modal(document.getElementById('bookSlot'));
        let selectedDate = null;
        let selectedTime = null;
        let slot_id = null;
        const token = 'Waseem#2023MobAPP';

        const today = new Date();
        let currentDate = new Date(today.getFullYear(), today.getMonth());

        // 1. Fetch available dates (with header)
        async function fetchAvailableDates(year, month) {
            showLoader();
            try {
                const response = await fetch(`/api/customer/counselor/calendar?counselor_id=6&year=${year}&month=${month + 1}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'app-auth-token': token
                    }
                });
                if (!response.ok) {
                    console.error("Failed to fetch available dates");
                    return [];
                }
                const data = await response.json();
                return data.dates || [];
            } finally {
                hideLoader();
            }
        }

        // 2. Generate calendar UI
        async function generateCalendar(year, month) {
            const availableDates = await fetchAvailableDates(year, month);
            const availableDateSet = new Set(availableDates.map(d => d.date));

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const firstDayIndex = (firstDay.getDay() + 6) % 7;
            const daysInMonth = lastDay.getDate();

            let calendarHTML = '';

            for (let i = 0; i < firstDayIndex; i++) {
                calendarHTML += '<div class="h-10"></div>';
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const formattedDate = date.toISOString().split('T')[0];

                const isAvailable = availableDateSet.has(formattedDate);
                const isFutureOrToday = date >= new Date(today.getFullYear(), today.getMonth(), today.getDate());

                const isDisabled = !isAvailable || !isFutureOrToday;
                const isSelected = selectedDate === formattedDate;
                const classes = [
                    'calendar-day',
                    'h-10 w-10 mx-auto rounded-full transition-all',
                    isDisabled ? 'disabled opacity-30 cursor-not-allowed' : 'hover:bg-gray-100 bg-blue-100 text-blue-800',
                    isSelected ? 'selected border border-blue-700' : ''
                ].join(' ');

                calendarHTML += `<button class="${classes}" data-date="${formattedDate}" ${isDisabled ? 'disabled' : ''}>${day}</button>`;
            }

            document.getElementById('calendarDays').innerHTML = calendarHTML;
            document.getElementById('currentMonth').textContent = new Date(year, month).toLocaleString('default', {
                month: 'long',
                year: 'numeric'
            });

            document.querySelectorAll('.calendar-day').forEach(day => {
                day.addEventListener('click', function() {
                    if (this.disabled) return;
                    selectedDate = this.dataset.date;
                    document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
                    this.classList.add('selected');
                    showTimeSlots(selectedDate);

                });
            });

            togglePrevButton();
        }

        // 3. Previous / Next Month
        document.getElementById('prevMonth').addEventListener('click', function() {
            if (currentDate.getFullYear() === today.getFullYear() && currentDate.getMonth() === today.getMonth()) return;
            currentDate.setMonth(currentDate.getMonth() - 1);
            generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
        });

        document.getElementById('nextMonth').addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
        });

        function togglePrevButton() {
            const prevBtn = document.getElementById('prevMonth');
            const isCurrentMonth = currentDate.getFullYear() === today.getFullYear() && currentDate.getMonth() === today.getMonth();
            prevBtn.disabled = isCurrentMonth;
            prevBtn.style.opacity = isCurrentMonth ? 0.4 : 1;
            prevBtn.style.cursor = isCurrentMonth ? 'not-allowed' : 'pointer';
        }

        // 4. Show time slots
        async function showTimeSlots(date) {
            showLoader();
            try {
                const response = await fetch(`/api/customer/available-slots?counselor_id=6&date=${date}`, {
                    headers: {
                        'app-auth-token': token
                    }
                });
                const data = await response.json();
                const slots = data || [];
                const timeSlotsDiv = document.getElementById('timeSlots');
                document.getElementById('timeSlotContainer').classList.remove('hidden');

                timeSlotsDiv.innerHTML = slots.map(slot => {
                    const localStart = new Date(slot.start_time);
                    const localEnd = new Date(slot.end_time);

                    const startFormatted = localStart.toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    });

                    const endFormatted = localEnd.toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    });

                    return `
            <button class="time-slot px-6 py-3 rounded-full bg-blue-50 hover:bg-blue-100 transition-all text-gray-900"
                data-time="${slot.start_time}"
                data-id="${slot.id}">
                ${startFormatted}
            </button>`;
                }).join('');

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
        async function reserveSlot(slot_id) {
            const customer_id = "{{ $customer->app_customer_id }}";
            const csrfToken = '{{ csrf_token() }}';
            try {
                const response = await fetch(`/api/customer/reserved-slot`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'app-auth-token': token
                    },
                    body: JSON.stringify({
                        customer_id: customer_id,
                        slot_id: slot_id
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Reservation response:', data);

                

            } catch (error) {
                console.error('Error reserving slot:', error);
            }
        }


        // 5. Confirm booking
        document.getElementById('confirmButton').addEventListener('click', function() {
            if (!selectedDate || !selectedTime) {
                alert('Please select both a date and time.');
                return;
            }

            const localDateTime = new Date(selectedTime);
            const formattedTime = localDateTime.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
            const localDate = new Date(selectedTime);
            const formattedDate = localDate.toISOString().split('T')[0]; // yyyy-mm-dd
            // Optional: update person name dynamically
            const personName = "{{$customer->name}}"; // Replace this with dynamic name if available
            document.getElementById('bookSlotLabel').textContent = `Book New Session in for ${personName}`;
            document.querySelector('button[type="submit"]').textContent = `Confirm and Send`;
            modal.show();
            document.getElementById('customer_id').value = "{{$customer->app_customer_id}}";
            document.getElementById('counselor_id').value = "{{$counselor->id}}";
            document.getElementById('slot_id').value = slot_id;
            document.getElementById('communication_type').value = communication_method;

        });

        // Initialize calendar
        generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
    });


    document.getElementById('bookSlotForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    showLoader();
    const csrfToken = '{{ csrf_token() }}';
    const token = 'Waseem#2023MobAPP';

    const form = this; // 'this' is the form element now
    const formData = new FormData(form);

    const payload = {};
    formData.forEach((value, key) => {
        payload[key] = value;
    });
    if(payload.communication_method == '')
    {
        hideLoader();
        alert('Please select communication type first before Proceed.');
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
        console.log('Booking response:', result);
        if (result.status=="confirmed")
        {
            alert('Session booked successfully!');
            $('#bookSlot').modal('hide');
            window.location.href = "/counseller/dashboard";
        } else {
            alert(result.message || 'Booking failed.');
        }
    } catch (error) {
        hideLoader();
        console.error('Error booking slot:', error);
        alert(error);
    }
});


</script>


@endsection