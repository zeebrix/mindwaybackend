@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
    <div class="card w-100">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Welcome {{ $Counselor->name }}</h2>
            </div>
            <h6>Upcoming Sessions ({{$upcomingBookings->count()}})</h6>

            <div class="table-responsive">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark fs-4">
                        <tr>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Session Detail</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Date</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Action</h6>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="customersTable">
                    @if($upcomingBookings->isNotEmpty())
                    @foreach($upcomingBookings as $booking)
                    <tr class="customer-row">
                        <td class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">
                                <h5>{{ optional($booking->user)->name ?? 'N/A' }}</h5>
                                <p>{{ optional($booking->user)->email ?? 'Email not provided' }}</p>
                                <p>{{ optional($booking->user)->max_session ?? 0 }} Session(s) Remaining</p>
                            </h6>
                        </td>
                        <td class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">
                                {{ optional($booking->slot)->date?->format('Y-m-d') ?? 'No date available' }}
                                {{ optional($booking->slot)->start_time?->format('H:i') ?? 'No time available' }}
                            </h6>
                        </td>
                        <td class="border-bottom-0">
                            <a class="btn btn-primary mindway-btn"
                                href="{{ route('counselor.session.cancel', ['booking_id' => $booking->id, 'customer_id' => $booking->user_id]) }}">
                                Cancel
                            </a>
                            <a class="btn btn-primary mindway-btn" href="{{ route('counselor.session.rebook', ['booking_id' => $booking->id]) }}">Rebook</a>
                            <br>
                            <a style="margin-left: 20px;"
                                class="btn btn-primary mt-2 add-session-btn mindway-btn-blue" data-bs-toggle="modal"
                                
                                        data-bs-target="#addSessionModal" data-id="{{ $booking?->user_id  }}"
                                         data-couselor_id="{{ $booking?->counselor_id  }}"
                                        data-slot_id="{{ $booking?->slot_id }}"
                                        data-name="{{ $booking?->counselor?->name }}" data-program_id="{{$booking?->brevoUser?->program_id}}"
                                        data-customer_name="{{ $booking?->user?->name }}">
                                Add Session
                            </a>
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

            {{-- Sessions of the Counsellor --}}
            <br>
            <hr><br>


            <h2>All Counselling Sessions</h2>

            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control"
                    placeholder="Search by Name, Email, Company Name, or Counsellor Name">
            </div>

            <div class="table-responsive">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark fs-4">
                        <tr>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Sr. No</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Name</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Company Name</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Email</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Counsellor Name</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Session Date</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Session Type</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Max Session</h6>
                            </th>
                            {{-- <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Actions</h6>
                            </th> --}}
                        </tr>
                    </thead>
                    <tbody id="customersTable">
                        @php($count = 0)
                        @foreach ($CounselorSession as $data)
                            @php($count++)
                            <tr class="customer-row">
                                <td class="border-bottom-0">
                                    <h6 class="fw-normal mb-0">{{ $count }}</h6>
                                </td>
                                <td class="border-bottom-0 customer-name">
                                    <h6 class="fw-semibold mb-1">{{ $data->name }}</h6>
                                </td>
                                <td class="border-bottom-0 customer-company">
                                    <h6 class="fw-semibold mb-1">{{ $data->company_name }}</h6>
                                </td>
                                <td class="border-bottom-0 customer-email">
                                    <h6 class="fw-semibold mb-1">{{ $data->email }}</h6>
                                </td>
                                <td class="border-bottom-0 customer-counsellor">
                                    <h6 class="fw-semibold mb-1">{{ $data->counselor->name ?? '' }}</h6>
                                </td>
                                <td class="border-bottom-0">
                                    <p class="mb-0 fw-normal">{{ $data->session_date }}</p>
                                </td>
                                <td class="border-bottom-0">
                                    <p class="mb-0 fw-normal">{{ $data->session_type }}</p>
                                </td>
                                <td class="border-bottom-0">
                                    <p class="mb-0 fw-normal">{{ $data->max_session ?? 0 }}</p>
                                </td>
                              
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

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
                <form action="{{ route('admin.sessions.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="customerId" id="customerId">
                    <input type="hidden" name="programId" id="programId">
                     <input type="hidden" name="counselor_id" id="couselorId" value="">
                    <input type="hidden" name="slot_id" id="slotId">
                   
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
@endsection

@section('js')
    <script>
        document.getElementById('searchInput').addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#customersTable .customer-row');

            rows.forEach(row => {
                const name = row.querySelector('.customer-name')?.innerText.toLowerCase() || '';
                const company = row.querySelector('.customer-company')?.innerText.toLowerCase() || '';
                const email = row.querySelector('.customer-email')?.innerText.toLowerCase() || '';
                const counsellor = row.querySelector('.customer-counsellor')?.innerText.toLowerCase() || '';

                if (
                    name.includes(searchValue) ||
                    company.includes(searchValue) ||
                    email.includes(searchValue) ||
                    counsellor.includes(searchValue)
                ) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
    <script>
  $(document).ready(function() {
   // Listen for modal show event
   $('#addSessionModal').on('show.bs.modal', function(event) {
      // Get the button that triggered the modal
      var button = $(event.relatedTarget);

      // Extract data from the button's data attributes
      var customerId = button.data('id');
      var counselorName = button.data('name');
      var programId = button.data('program_id');
      var customerName = button.data('customer_name');
      var slotId = button.data('slot_id');
      var couselorId = button.data('couselor_id');
      

      // Populate the modal fields
      $('#counselorName').val(counselorName);
      $('#programId').val(programId);
      $('#customerName').val(customerName);
      $('#slotId').val(slotId);
      $('#couselorId').val(couselorId);
      
   });
});
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
</script>
@endsection
