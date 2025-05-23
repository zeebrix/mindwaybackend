@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')

<div class="row">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2>Welcome {{ $Counselor->name }} 👋</h2>
        </div>
        <h6 class="fw-bold">Upcoming Sessions ({{$upcomingBookings->count()}})</h6>

        <div class="table-responsive">
            <table class="table text-nowrap mb-0 align-middle">
                <tbody id="customersTable">
                    @if($upcomingBookings->isNotEmpty())
                    @foreach($upcomingBookings as $booking)
                    <tr style="border-bottom: none;">
                        <td colspan="3" style="padding: 0; border: none;">
                            <div class="card" style="border-radius: 8px; margin: 15px 15px 15px 15px;">
                                <div class="card-body">
                                    <div class="row">
                                        <!-- User Information -->
                                        <div class="col-md-3" style="border-right: 4px solid #D4D4D4;">
                                            <h5 class="fw-semibold">{{ optional($booking->user)->name ?? 'N/A' }}</h5>
                                            <p class="fw-bold mb-1">{{ optional($booking->user)->email ?? 'Email not provided' }}</p>
                                            <p class="fw-bold mb-0">{{ optional($booking->user)->max_session ?? 0 }} Session(s) Remaining</p>
                                        </div>
                                        <!-- Date & Time -->
                                        <div class="col-md-3" style="border-right: 4px solid #D4D4D4;">
                                            <p></p>

                                            <h5 class="fw-semibold" style="display: block;width: 100%;max-width: 300px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">Date & Time {{$booking?->counselor?->timezone}}</h5>
                                            <p class="fw-bold mb-0">
                                                {{ optional($booking->slot)->start_time?->setTimezone($timezone)->format('Y-m-d') ?? 'No date available' }}
                                                {{ optional($booking->slot)->start_time?->setTimezone($timezone)->format('H:i') ?? 'No time available' }}
                                            </p>
                                        </div>
                                        <!-- Actions -->
                                        <div class="col-md-3" style="border-right: 4px solid #D4D4D4;">
                                            <a class="btn btn-primary mindway-btn" href="{{ route('counselor.session.cancel', ['booking_id' => $booking->id, 'customer_id' => $booking->user_id,'customer_timezone' => $booking->user->timezone]) }}">
                                                Cancel
                                            </a>
                                            <a data-bs-toggle="modal" data-bs-target="#rebookSessionModal" class="btn btn-primary mindway-btn" href="{{ route('counselor.session.rebook', ['booking_id' => $booking->id]) }}">
                                                Rebook
                                            </a>
                                            <br>
                                            <a style="background-color: #688edc !important; color: white !important; margin-top: 10px;"
                                                class="btn btn-primary add-session-btn mindway-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#addSessionModal"
                                                data-id="{{ $booking?->user_id }}"
                                                data-counselor_id="{{ $booking?->counselor_id }}"
                                                data-slot_id="{{ $booking?->slot_id }}"
                                                data-name="{{ $booking?->counselor?->name }}"
                                                data-program_id="{{ $booking?->brevoUser?->program_id }}"
                                                data-customer_name="{{ $booking?->user?->name }}">
                                                Log Session
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <p style="margin: unset;">
                                                @if($booking->communication_method == 'Video Call')
                                                Video Call Chosen
                                                <br>
                                                <a target="_blank" href="{{$booking->meeting_link}}"
                                                    class="btn btn-primary mindway-btn">
                                                    JOIN MEETING
                                                </a>
                                                @else
                                                Phone Call Chosen
                                                <br>
                                                <strong>Call: {{$booking?->user?->phone}}</strong>
                                                @endif
                                            </p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="3" class="text-center">
                            <h5 class="fw-semibold">No upcoming bookings available.</h5>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 d-flex justify-content-center">
                {{ $upcomingBookings->links('pagination::bootstrap-4') }}
            </div>
        </div>

        <div class="modal fade" id="addSessionModal" tabindex="-1" aria-labelledby="addSessionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSessionModalLabel">Book Session</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('session.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="counselor_id" id="couselorId" value="">
                            <input type="hidden" name="customerId" id="customerId">
                            <input type="hidden" name="slot_id" id="slotId">
                            <input type="hidden" name="programId" id="programId">
                            <input type="hidden" name="type" value="upcomingSession">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sessionDate" class="form-label">Date</label>
                                    <input type="date" class="form-control" name="sessionDate" placeholder="Enter date" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="sessionType" class="form-label">Session Type</label>
                                    <input type="text" class="form-control" id="sessionType" name="sessionType"
                                        value="50min Counselling Sessions" placeholder="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <h3>Reason</h3>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="work_related" name="work_related"
                                            value="Work Related" onclick="toggleAdditionalReasons()">
                                        <label class="form-check-label" for="work_related">Work Related</label>
                                    </div>
                                    <div id="additionalReasons" class="additional-reasons" style="display:none;">
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="work_stress" name="work_stress"
                                                value="Work Stress">
                                            <label class="form-check-label" for="work_stress">Work Stress</label>
                                        </div>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="workplace_conflicts"
                                                name="workplace_conflicts" value="Workplace Conflicts">
                                            <label class="form-check-label" for="workplace_conflicts">Workplace
                                                Conflicts</label>
                                        </div>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="harassment_bullying"
                                                name="harassment_bullying" value="Harassment/Bullying">
                                            <label class="form-check-label"
                                                for="harassment_bullying">Harassment/Bullying</label>
                                        </div>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="performance_issues"
                                                name="performance_issues" value="Performance Issues">
                                            <label class="form-check-label" for="performance_issues">Performance
                                                Issues</label>
                                        </div>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="organisational_change"
                                                name="organisational_change" value="Organisational Change">
                                            <label class="form-check-label" for="organisational_change">Organisational
                                                Change</label>
                                        </div>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="burnout" name="burnout"
                                                value="Burnout">
                                            <label class="form-check-label" for="burnout">Burnout</label>
                                        </div>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="other" name="other"
                                                value="Other" onclick="toggleOtherInput()">
                                            <label class="form-check-label" for="other">Other</label>
                                        </div>
                                        <input type="text" id="other_reason" name="other_reason" placeholder="Please specify"
                                            class="form-control mt-2" style="display:none;">
                                    </div>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="person_related"
                                            name="person_related" value="Person Related">
                                        <label class="form-check-label" for="person_related">Person Related</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h3>New User</h3>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" id="new_user_yes" name="new_user"
                                            value="Yes">
                                        <label class="form-check-label" for="new_user_yes">Yes</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" id="new_user_no" name="new_user"
                                            value="No">
                                        <label class="form-check-label" for="new_user_no">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary w-100">ADD SESSION FOR PERSON
                                        NAME</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="rebookSessionModal" tabindex="-1" aria-labelledby="rebookSessionModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rebookSessionModal">Rebook Session</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Need to discuss</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function toggleAdditionalReasons() {
        const workRelatedCheckbox = document.getElementById('work_related');
        const additionalReasons = document.getElementById('additionalReasons');

        if (workRelatedCheckbox.checked) {
            additionalReasons.style.display = 'block';
        } else {
            additionalReasons.style.display = 'none';
            const checkboxes = additionalReasons.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => checkbox.checked = false);
            const otherInput = document.getElementById('other_reason');
            otherInput.style.display = 'none';
            otherInput.value = '';
        }
    }

    function toggleOtherInput() {
        const otherCheckbox = document.getElementById('other');
        const otherInput = document.getElementById('other_reason');

        if (otherCheckbox.checked) {
            otherInput.style.display = 'block';
        } else {
            otherInput.style.display = 'none';
            otherInput.value = '';
        }
    }
    $(document).ready(function() {
        // Listen for modal show event
        $('#addSessionModal').on('show.bs.modal', function(event) {
            // Get the button that triggered the modal
            var button = $(event.relatedTarget);

            // Extract data from the button's data attributes
            var customerId = button.data('id');
            var slotId = button.data('slot_id');
            var counselorName = button.data('name');
            var programId = button.data('program_id');
            var couselorId = button.data('counselor_id');
            var customerName = button.data('customer_name');

            // Populate the modal fields
            $('#customerId').val(customerId);
            $('#slotId').val(slotId);
            $('#counselorName').val(counselorName);
            $('#programId').val(programId);
            $('#couselorId').val(couselorId);

            $('#customerName').val(customerName);
        });
    });
</script>
@endsection