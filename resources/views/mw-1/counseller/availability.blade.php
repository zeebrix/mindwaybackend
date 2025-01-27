<script>
    $(document).ready(function() {
        let timeZones = [];
        let phpvar = "{{ $counselorId ?? 0 }}";
        let counselorId = (window.location.pathname.split('/')[3]);
        counselorId = isNaN(counselorId) ? phpvar : parseInt(counselorId, 10);

        // Fetch timezone data directly from the public path
        fetch('/public/mw-1/timezones.json') // Direct path from the public directory
            .then(response => response.json())
            .then(data => {
                timeZones = data.timezones;
                initializeTimezoneList();
            })
            .catch(error => {
                console.error('Error fetching timezones:', error);
            });
        const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        let availability = initializeAvailability();
        let selectedTimezone = 'Australia/Sydney';

        // Fetch availability data on page load
        fetchAvailability(counselorId);

        function fetchAvailability() {
            $.ajax({
                url: '/fetch-counsellor-availability?counselorId='+counselorId, // Replace with your route
                method: 'GET',
                success: function(response) {
                    if (response.availability) {
                        response.availability.forEach(item => {
                            const day = days[item.day_of_week];
                            availability[day].available = true;
                            availability[day].start = item.start_time;
                            availability[day].end = item.end_time;
                        });
                    }
                    if (response.timezone) {
                        selectedTimezone = response.timeZones;
                        $('#selected-timezone').text(getTimezoneDisplay(response.timeZones));
                    }
                    renderSchedule();
                },
                error: function(xhr) {
                    console.error('Failed to fetch availability:', xhr.responseText);
                    alert('An error occurred while fetching availability data.');
                }
            });
        }

        function initializeAvailability() {
            return {
                'Mon': {
                    available: false,
                    start: '',
                    end: ''
                },
                'Tue': {
                    available: false,
                    start: '',
                    end: ''
                },
                'Wed': {
                    available: false,
                    start: '',
                    end: ''
                },
                'Thu': {
                    available: false,
                    start: '',
                    end: ''
                },
                'Fri': {
                    available: false,
                    start: '',
                    end: ''
                },
                'Sat': {
                    available: false,
                    start: '',
                    end: ''
                },
                'Sun': {
                    available: false,
                    start: '',
                    end: ''
                }
            };
        }

        function getTimezoneDisplay(timezoneId) {
            const tz = timeZones.find(tz => tz.id === timezoneId);
            return tz ? tz.display : timezoneId;
        }

        function renderSchedule() {
            const container = $('#availability-container');
            container.empty();
            days.forEach(day => {
                container.append(renderDayRow(day));
            });
        }

        // Timezone and day-row rendering functions remain unchanged
        function initializeTimezoneList() {
            const list = $('#timezone-list');
            timeZones.forEach(tz => {
                list.append(
                    $('<div>')
                    .addClass('timezone-item')
                    .text(tz.name)
                    .data('timezone', tz.name)
                    .click(function() {
                        selectTimezone($(this).data('timezone'), $(this).text());
                    })
                );
            });
        }

        function selectTimezone(tzId, tzDisplay) {
            selectedTimezone = tzId;
            $('#selected-timezone').text(tzDisplay);
            $('#timezoneModal').modal('hide');
        }

        function createTimeContainer(day) {
            const timeContainer = $('<div>')
                .addClass('time-container d-none')
                .append(
                    $('<input>')
                    .attr('type', 'time')
                    .addClass('form-control time-input mx-1')
                    .val(availability[day].start)
                    .on('change', function() {
                        const newValue = $(this).val();
                        if (newValue >= availability[day].end) {
                            alert('Start time must be before end time');
                            $(this).val(availability[day].start);
                            return;
                        }
                        availability[day].start = newValue;
                    }),
                    $('<span>').addClass('mx-2').text('–'),
                    $('<input>')
                    .attr('type', 'time')
                    .addClass('form-control time-input mx-1')
                    .val(availability[day].end)
                    .on('change', function() {
                        const newValue = $(this).val();
                        if (newValue <= availability[day].start) {
                            alert('End time must be after start time');
                            $(this).val(availability[day].end);
                            return;
                        }
                        availability[day].end = newValue;
                    })
                );
            return timeContainer;
        }

        function renderDayRow(day) {
            const dayData = availability[day];
            const row = $('<div>')
                .addClass('day-row d-flex align-items-center')
                .data('day', day);

            const indicator = $('<div>')
                .addClass('availability-indicator')
                .css({
                    'backgroundColor': dayData.available ? '#688EDC' : '#e5e7eb',
                    'boxShadow': dayData.available ? '0 2px 4px #688EDC' : 'none',
                    'transform': dayData.available ? 'scale(1)' : 'scale(0.95)'
                })
                .on('click', function() {
                    dayData.available = !dayData.available;

                    $(this).css({
                        'backgroundColor': dayData.available ? '#688EDC' : '#e5e7eb',
                        'boxShadow': dayData.available ? '0 2px 4px #688EDC' : 'none',
                        'transform': dayData.available ? 'scale(1)' : 'scale(0.95)'
                    });

                    const timeContainer = row.find('.time-container');
                    const unavailableText = row.find('.unavailable-text');

                    if (dayData.available) {
                        dayData.start = '09:00';
                        dayData.end = '17:00';
                        unavailableText.addClass('d-none');
                        timeContainer.removeClass('d-none');
                    } else {
                        unavailableText.removeClass('d-none');
                        timeContainer.addClass('d-none');
                    }
                });

            const dayLabel = $('<span>').addClass('day-label me-3').text(day);

            const timeContainer = createTimeContainer(day);
            const unavailableText = $('<span>')
                .addClass('unavailable-text' + (dayData.available ? ' d-none' : ''))
                .text('Unavailable');

            if (dayData.available) timeContainer.removeClass('d-none');

            row.append(indicator, dayLabel, timeContainer, unavailableText);
            return row;
        }

        function formatAvailabilityData() {
            const availabilityData = {
                timezone: selectedTimezone,
                counselorId:counselorId,
                availability: []
            };

            days.forEach((day, index) => {
                if (availability[day].available) {
                    availabilityData.availability.push({
                        day_of_week: index,
                        start_time: availability[day].start,
                        end_time: availability[day].end
                    });
                }
            });
            return availabilityData;
        }
        $('#saveButton').on('click', function() {
            const button = $(this);
            button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

            const formattedData = formatAvailabilityData();
            console.log(formattedData);
            $.ajax({
                url: '/availability-save', // Replace with the correct route URL
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: 'application/json',
                data: JSON.stringify(formattedData),
                success: function(response) {
                    console.log('Response from server:', response);
                    button.html('✓ Saved Successfully').addClass('btn-success').removeClass('btn-primary');

                    setTimeout(() => {
                        button.html('Save Changes').removeClass('btn-success').addClass('btn-primary').prop('disabled', false);
                    }, 2000);
                },
                error: function(xhr, status, error) {
                    console.error('Error saving availability:', xhr.responseText);
                    button.html('Save Changes').prop('disabled', false);
                    // Optionally, display a toast or alert here
                }
            });
        });

        initializeTimezoneList();
    });
</script>