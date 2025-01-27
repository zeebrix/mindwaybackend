<!DOCTYPE html>
<html lang="en">

{{-- head add --}}

@include('admin.head')
<style>
    .main-panel {
        transition: width 0.25s ease, margin 0.25s ease;
        width: calc(100% - 236px);
        min-height: calc(100vh - 4.625rem);
        display: -webkit-flex;
        display: flex;
        -webkit-flex-direction: column;
        flex-direction: column;
        align-items: center;
        margin: 0 auto;

    }

    .pro-details {
        margin: auto;
        width: 50%;
        padding: 30px 10px;
    }

    h5.lic_code {
        margin: 15px;
    }

    h5.lic_code span {
        background-color: #007bff;
        padding: 5px;
        border-radius: 9px;
        color: #fff;
    }

    h2.lic_precent {
        color: #844fc1;
        margin: 0;
    }

    <style>.pro-details {
        margin: auto;
        width: 50%;
        padding: 30px 10px;
    }

    h5.lic_code {
        margin: 15px;
    }

    h5.lic_code span {
        background-color: #007bff;
        padding: 5px;
        border-radius: 9px;
        color: #fff;
    }

    h2.lic_precent {
        color: #844fc1;
        margin: 0;
    }

    #addSessionModal .form-control {
        border-radius: 25px;
        border: 2px solid #609;
        padding: 20px;
        width: 100%;
        /* Adjust width as needed */
        height: 45px;
        /* Adjust height as needed */
        margin-bottom: 10px;
        /* Add spacing between input fields */
    }

    #addSessionModal .form-control::placeholder {
        color: #999;
        /* Change placeholder text color if needed */
    }

    /* Add custom CSS for rounded input fields */
    .rounded-input {
        border: none;
        border-radius: 10px;
        /* Adjust border-radius as needed */
        background-color: #f2f2f2;
        /* Optional: Add background color */
        padding: 10px;
        /* Optional: Add padding */
    }

    .square-btn {
        border-radius: 0;
        /* Remove border-radius */
        background-color: #e0e0d1;
        color: black;
    }

    .square-btn:hover {
        background-color: inherit;
        /* Use the same background color as the parent */
        color: black;
        /* Set text color to black */
        text-decoration: none;
        /* Remove underline or any other text decoration */
    }
</style>

<body>

    <div class="container-scroller" style="background-color: #E8F1FF;">
        @php
            use Illuminate\Support\Facades\Auth;
            use App\Models\CustomreBrevoData;
            use App\Models\Session;
            use App\Models\Program;
            $userId = Auth::guard('programs')->id();
            $Program = Program::with('customers')->get();
            $sessions = Session::where('program_id', $userId)->get();
            $customers = CustomreBrevoData::all();
        @endphp


        <!-- users/index.blade.php -->




        <div class="container">
            <div class="container">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <!-- Mindway -->
                <h1 style="margin-left: 20px;">Mindway</h1>
                <h4 style="margin-left: 20px;">Search Available Staff For Counselling</h4>

                <!-- Search Bar -->
                <div class="row mt-4 mb-4">
                    <div class="col-md-6 offset-md-3" style="margin-left: 20px;">
                        <input type="text" id="searchInput" class="form-control rounded-pill"
                            style="width: 800px; height: 60px;"
                            placeholder="Type employee name, email address, or company">
                    </div>
                </div>

                <br>
                <div class="row" id="staffprogram">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="table-responsive pt-3">
                                <table class="table table-striped project-orders-table">
                                    <thead>
                                        <tr>
                                            <th><strong>Sr.no</strong></th>
                                            <th><strong>Name</strong></th>
                                            <th><strong>Email</strong></th>
                                            <th><strong>Max Value</strong></th>
                                            <th><strong>Company Name</strong></th>
                                            <th><strong>Add Session</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody id="userData">
                                        @php($count = 0)
                                        @foreach ($customers as $customer)
                                            @php($count++)
                                            <tr>
                                                <td><strong>{{ $count }}</strong></td>
                                                <td><strong>{{ $customer->name }}</strong></td>
                                                <td><strong>{{ $customer->email }}</strong></td>
                                                <td><strong>{{ $customer->max_session }}</strong></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <strong>{{ $customer->company_name }}</strong>
                                                    </div>
                                                </td>
                                                <td>

                                                <td>
                                                    <button type="button" class="btn btn-primary add-session-btn"
                                                        data-toggle="modal" data-target="#addSessionModal"
                                                        data-id="{{ $customer->id }}"
                                                        data-name="{{ $customer->company_name }}"
                                                        data-program_id="{{ $customer->program_id }}"
                                                        data-customer_name="{{ $customer->name }}">Add Session</button>
                                                </td>


                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="addSessionModal" tabindex="-1" role="dialog"
                    aria-labelledby="addSessionModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addSessionModalLabel">Add Counselling Session For Person Name</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('session.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="counselor_id" value="{{ $user_id ?? '' }}">

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="sessionDate">Date</label>
                                            <input type="date" class="form-control rounded-input" name="sessionDate"
                                                placeholder="Enter date">
                                            <input type="hidden" name="customerId" id="customerId">
                                            <input type="hidden" name="programId" id="programId">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="sessionType">Session Type</label>
                                            <input type="text" class="form-control rounded-input" id="sessionType"
                                                name="sessionType" value="50min Counselling Sessions" placeholder="">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <h3>Reason</h3>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" id="work_related"
                                                    name="work_related" value="Work Related"
                                                    onclick="toggleAdditionalReasons()">
                                                <label class="form-check-label" for="work_related">Work Related</label>
                                            </div>
                                            <div id="additionalReasons" class="additional-reasons"
                                                style="display:none;">
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" id="work_stress"
                                                        name="work_stress" value="Work Stress">
                                                    <label class="form-check-label" for="work_stress">Work
                                                        Stress</label>
                                                </div>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="workplace_conflicts" name="workplace_conflicts"
                                                        value="Workplace Conflicts">
                                                    <label class="form-check-label"
                                                        for="workplace_conflicts">Workplace Conflicts</label>
                                                </div>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="harassment_bullying" name="harassment_bullying"
                                                        value="Harassment/Bullying">
                                                    <label class="form-check-label"
                                                        for="harassment_bullying">Harassment/Bullying</label>
                                                </div>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="performance_issues" name="performance_issues"
                                                        value="Performance Issues">
                                                    <label class="form-check-label"
                                                        for="performance_issues">Performance Issues</label>
                                                </div>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="organisational_change" name="organisational_change"
                                                        value="Organisational Change">
                                                    <label class="form-check-label"
                                                        for="organisational_change">Organisational Change</label>
                                                </div>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" id="burnout"
                                                        name="burnout" value="Burnout">
                                                    <label class="form-check-label" for="burnout">Burnout</label>
                                                </div>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" id="other"
                                                        name="other" value="Other" onclick="toggleOtherInput()">
                                                    <label class="form-check-label" for="other">Other</label>
                                                </div>
                                                <input type="text" id="other_reason" name="other_reason"
                                                    placeholder="Please specify"
                                                    style="display:none; margin-left: 1rem;">
                                            </div>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" id="person_related"
                                                    name="person_related" value="Person Related">
                                                <label class="form-check-label" for="person_related">Person
                                                    Related</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <h3>New User</h3>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="radio" id="new_user_yes"
                                                    name="new_user" value="Yes">
                                                <label class="form-check-label" for="new_user_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="radio" id="new_user_no"
                                                    name="new_user" value="No">
                                                <label class="form-check-label" for="new_user_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-primary">ADD SESSION FOR PERSON
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
        <!-- container-scroller -->

        <!-- base:js -->
        @include('admin.js')
        <!-- End custom js for this page-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Latest compiled JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
                    $('#userData tr').filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1)
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
</body>

</html>
