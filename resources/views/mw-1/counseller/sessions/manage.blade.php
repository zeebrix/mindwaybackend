@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')

    <style>
        .search-input {
            position: relative;
            width: 100%;
        }

        .search-input input {
            width: 100%;
            padding-left: 40px;
            /* Leave space for the icon */
        }

        .search-input .ti-search {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #ccc;
            /* Adjust icon color */
            pointer-events: none;
            /* Make the icon non-clickable */
        }
  .table {
        table-layout: fixed; /* Ensures fixed column widths */
        width: 100%; /* Ensures the table takes full width */
    }
    .col-number {
        width: 5%; /* Adjust as needed */
    }
    .col-employee {
        width: 20%; /* Adjust as needed */
    }
    .col-company {
        width: 15%; /* Adjust as needed */
    }
    .col-remaining {
        width: 10%; /* Adjust as needed */
    }
    .col-actions {
        width: 10%; /* Adjust as needed */
    }


    </style>


    <div class="row">
        <div class="col-10 offset-1">

    <div class="mb-4">
        <h2><b>Add Sessions to Employees</b></h2>
        <div class="search-input">
            <i class="ti ti-search" style="font-size: 30px;"></i>
            <input style="height: 50px;border-radius:20px;background-color:#F7F7F7" type="text" id="searchInput" class="form-control"
                placeholder="Search by name, email, or company">
        </div>
    </div>
    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div>
    @endif

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


   <div class="table-responsive">
    <table class="table text-nowrap mb-0 align-middle">
        <thead class="text-dark fs-4">
            <tr>
                <th class="col-number">
                    <h6 class="fw-semibold mb-0">#</h6>
                </th>
                <th class="col-employee">
                    <h6 class="fw-semibold mb-0">Employee Details</h6>
                </th>
                <th class="col-company">
                    <h6 class="fw-semibold mb-0">Company</h6>
                </th>
                <th class="col-remaining">
                    <h6 class="fw-semibold mb-0">Remaining</h6>
                </th>
                <th class="col-actions">
                    <h6 class="fw-semibold mb-0"></h6>
                </th>
            </tr>
        </thead>
        <tbody id="customersTable">
            @php($count = 0)
            @foreach ($customers as $data)
                @php($count++)
                <tr class="customer-row" style="border-bottom: 1px solid #dee2e6;">
                    <td class="col-number">
                        <h6 class="fw-normal mb-0">{{ $count }}</h6>
                    </td>
                    <td class="col-employee">
                        <h6 class="fw-semibold mb-1">
                            <b>{{ $data->name }}</b>
                        </h6>
                        <p class="mb-0 fw-semibold">{{ $data->email }}</p>
                    </td>
                    <td class="col-company">
                        <h6 class="mb-0 fw-bold"><b>{{ $data->company_name }}</b></h6>
                    </td>
                    <td class="col-remaining">
                        <h6 class="mb-0 fw-semibold"><b>{{ $data->max_session }}</b></h6>
                    </td>
                    <td class="col-actions">
                        <button type="button" class="btn btn-primary add-session-btn mindway-btn" style="background-color: #688EDC !important;color:#F7F7F7 !important" data-bs-toggle="modal"
                            data-bs-target="#addSessionModal" data-id="{{ $data->id }}"
                            data-name="{{ $data->company_name }}" data-program_id="{{ $data->program_id }}"
                            data-customer_name="{{ $data->name }}">
                            Log
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>




    <div class="modal fade" id="addSessionModal" tabindex="-1" aria-labelledby="addSessionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSessionModalLabel">Add Counselling Session For Person Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('session.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="counselor_id" value="{{ $user_id ?? '' }}">
                        <input type="hidden" name="type" value="newSession">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sessionDate" class="form-label">Date</label>
                                <input type="date" class="form-control" name="sessionDate" placeholder="Enter date" required>
                                <input type="hidden" name="customerId" id="customerId">
                                <input type="hidden" name="programId" id="programId">
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
                                        <input class="form-check-input" type="checkbox" id="work_stress"
                                            name="work_stress" value="Work Stress">
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
                                    <input type="text" id="other_reason" name="other_reason"
                                        placeholder="Please specify" class="form-control mt-2" style="display:none;">
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


    </div>
    </div>
@endsection

@section('js')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var addSessionButtons = document.querySelectorAll('.add-session-btn');
            var modal = document.getElementById('addSessionModal');
            var customerIdInput = modal.querySelector('input[name="customerId"]');
            var programIdInput = modal.querySelector('input[name="programId"]');
            var modalTitle = modal.querySelector('.modal-title');
            var submitButton = modal.querySelector('button[type="submit"]');

            addSessionButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var customerId = button.getAttribute('data-id');
                    var companyName = button.getAttribute('data-name');
                    var programId = button.getAttribute('data-program_id');
                    var customerName = button.getAttribute('data-customer_name');

                    // Set the input values and the modal title
                    customerIdInput.value = customerId;
                    programIdInput.value = programId;
                    modalTitle.textContent = 'Add Counselling Session for ' + customerName;
                    submitButton.textContent = 'ADD SESSION FOR ' + customerName;
                });
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            $('#searchInput').on('input', function() {
                var searchText = $(this).val().toLowerCase();
                $('#customersTable .customer-row').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
                });
            });
        });
    </script>
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
    </script>
@endsection
