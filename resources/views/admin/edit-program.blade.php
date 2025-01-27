<!DOCTYPE html>
<html lang="en">

<!-- Load the latest version of jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Load Bootstrap JavaScript (including popper.js) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
{{-- head add --}}

@include('admin.head')
<style>
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

    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->

        {{-- admin header add --}}
        @include('admin.header')

        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_settings-panel.html -->
            {{-- skins color add --}}
            @include('admin.skins-color')



            {{-- add side bar --}}

            @include('admin.sidebar')


            <div class="main-panel">
                <div class="content-wrapper">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
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

                    <div class="row">
                        <div class="col-md-12">

                            <h2>Update Program</h2>

                            <form action="{{ url('/manage-admin/update-program', ['id' => $Program->id]) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-6 ">
                                        <label for="company_name">Comapny Name</label>
                                        <input type="text" class="form-control" placeholder="Enter Comapny Name"
                                            name="company_name" value="{{ $Program->company_name }}" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="email">Account Controller Email</label>
                                        <input type="email" class="form-control"
                                            placeholder="Enter Account Controller Email" name="email"
                                            value="{{ $Program->email }}" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <h5>Counselling Session</h5>
                                        <div class="d-flex align-items-center bg-white p-3 rounded">
                                            <strong class="mr-3">Add Counseling Session</strong>
                                            <button type="button" class="btn btn-outline-secondary" data-toggle="modal"
                                                data-target="#addSessionModal">
                                                <i class="fas fa-plus"></i> <!-- Font Awesome plus icon -->
                                            </button>
                                        </div>
                                    </div>




                                    <div class="form-group col-md-6">
                                        <label for="password">New Password</label>
                                        <input type="text" class="form-control" name="password"
                                            placeholder="Enter new password" value="{{ $Program->password }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="max_lic">Max Licenses</label>
                                        <input type="number" class="form-control" placeholder="Enter Max Licenses"
                                            name="max_lic" required value="{{ $Program->max_lic }}">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="code">Access Code</label>
                                        <input type="text" class="form-control" placeholder="Enter code"
                                            name="code" required value="{{ $Program->code }}">
                                    </div>
                                </div>
                                <div class="row">
                                    @if ($Program->logo != '')
                                        <div class="form-group col-md-6">
                                            <label for="">Old Logo</label>
                                            <img height="50px" width="50px" class="popup"
                                                src="{{ asset('storage/logo') }}/{{ $Program->logo }}"
                                                alt="emoji image">
                                        </div>
                                    @endif

                                    <div class="form-group col-md-6">
                                        <label for="logo">Logo</label>
                                        <input type="file" class="form-control" placeholder="Upload Logo"
                                            name="logo">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="link">Booking Link</label>
                                        <input type="url" class="form-control" placeholder="Enter Booking Link"
                                            name="link" required value="{{ $Program->link }}">
                                    </div>
                                </div>
                                <div class="row">



                                    <div class="form-group col-md-6">
                                        <label for="link">Max Session</label>
                                        <input type="text" class="form-control" placeholder="Enter Max Session"
                                            name="max_session" id="max_session" required
                                            value="{{ $Program->max_session }}">
                                    </div>

                                    <center>
                                        <button id="submitButton" type="submit" class="btn btn-primary">Update</button>
                                    </center>
                            </form>
                        </div>
                        <!-- Modal -->
                        <div class="modal fade" id="addSessionModal" tabindex="-1" role="dialog"
                            aria-labelledby="addSessionModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addSessionModalLabel">Add Counselling Session for
                                            Company Name Here</h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                        <form action="{{ route('sessions.store') }}" method="POST">
                                            @csrf
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="sessionDate">Date</label>
                                                    <input type="date" class="form-control rounded-input"
                                                        name="sessionDate" placeholder="Enter date">
                                                    <input type="hidden" name="programId"
                                                        value="{{ $Program->id }}">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="sessionType">Session Type</label>
                                                    <input type="text" class="form-control rounded-input"
                                                        id="ip2" name="sessionType"
                                                        placeholder="Enter session type">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <h3>Reason</h3>
                                                    <div class="form-check form-switch mt-2">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="work_related" name="work_related"
                                                            value="Work Related" onclick="toggleAdditionalReasons()">
                                                        <label class="form-check-label" for="work_related">Work
                                                            Related</label>
                                                    </div>
                                                    <div id="additionalReasons" class="additional-reasons"
                                                        style="display:none;">
                                                        <div class="form-check form-switch mt-2">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="work_stress" name="work_stress"
                                                                value="Work Stress">
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
                                                                id="organisational_change"
                                                                name="organisational_change"
                                                                value="Organisational Change">
                                                            <label class="form-check-label"
                                                                for="organisational_change">Organisational
                                                                Change</label>
                                                        </div>
                                                        <div class="form-check form-switch mt-2">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="burnout" name="burnout" value="Burnout">
                                                            <label class="form-check-label"
                                                                for="burnout">Burnout</label>
                                                        </div>
                                                        <div class="form-check form-switch mt-2">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="other" name="other" value="Other"
                                                                onclick="toggleOtherInput()">
                                                            <label class="form-check-label"
                                                                for="other">Other</label>
                                                        </div>
                                                        <input type="text" id="other_reason" name="other_reason"
                                                            placeholder="Please specify"
                                                            style="display:none; margin-left: 1rem;">
                                                    </div>
                                                    <div class="form-check form-switch mt-2">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="person_related" name="person_related"
                                                            value="Person Related">
                                                        <label class="form-check-label" for="person_related">Person
                                                            Related</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <h3>New User</h3>
                                                    <div class="form-check form-switch mt-2">
                                                        <input class="form-check-input" type="radio"
                                                            id="new_user_yes" name="new_user" value="Yes">
                                                        <label class="form-check-label" for="new_user_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check form-switch mt-2">
                                                        <input class="form-check-input" type="radio"
                                                            id="new_user_no" name="new_user" value="No">
                                                        <label class="form-check-label" for="new_user_no">No</label>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row mt-4">
                                                <div class="col-sm-12">
                                                    <button type="submit" class="btn btn-primary">ADD SESSION FOR
                                                        COMPANY NAME</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pro-details" style="text-align: CENTER;">
                        <h4>{{ $Program->company_name }} Program</h4>
                        <h5 class="lic_code"><span>{{ $Program->code }}</span></h5>
                        <h2 class="lic_precent">
                            {{ number_format(round(($Program->customers()->count() / $Program->max_lic) * 100), 2) }} %
                        </h2>
                        <h6>Licenses Claimed</h6>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="table-responsive pt-3">
                                    <table class="table table-striped project-orders-table">
                                        <thead>
                                            <tr>

                                                <th>Sr.no</th>

                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Counselling Sessions</th>
                                                <th></th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php($count = 0)
                                            @foreach ($Program->customers as $data)
                                                @php($count++)
                                                <tr>
                                                    <td>{{ $count }}</td>

                                                    <td>{{ $data->name }}</td>
                                                    <td>{{ $data->email }}</td>
                                                    <td>{{ intval($data->pivot->session) }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <a href="{{ route('plus-session', ['customerId' => $data->id, 'programId' => $Program->id]) }}"
                                                                class="btn btn-success btn-sm btn-icon-text mr-3">
                                                                +
                                                            </a>
                                                            <a href="{{ route('minus-session', ['customerId' => $data->id, 'programId' => $Program->id]) }}"
                                                                class="btn btn-success btn-sm btn-icon-text mr-3">
                                                                -
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <a href="{{ route('remove-cusomer-program', ['customerId' => $data->id, 'programId' => $Program->id]) }}"
                                                                class="btn btn-success btn-sm btn-icon-text mr-3">
                                                                Remove
                                                                <i class="typcn typcn-view btn-icon-append"></i>
                                                            </a>
                                                            <!-- <a href="{{ url('/manage-admin/delete-quote', ['id' => $data->id]) }}"  class="btn btn-danger btn-sm btn-icon-text">
                                    Delete
                                    <i class="typcn typcn-delete-outline btn-icon-append"></i>
                                  </a> -->
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <script>
                    $(document).ready(function() {
                        $('#submitButton').click(function() {
                            // Submit the form
                            $('form').submit();
                        });
                    });
                </script>

                @include('admin.footer')
                <!-- partial -->
            </div>

            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->

    </div>


    <!-- container-scroller -->

    <!-- base:js -->
    @include('admin.js')
    <!-- End custom js for this page-->
</body>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Latest compiled JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

</html>
