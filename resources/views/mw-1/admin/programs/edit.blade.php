@extends('mw-1.layout.app')
@section('selected_menu', 'active')

<style>
    .search-input {
        position: relative;
        width: 18%;
    }

    .search-input input {
        width: 10%;
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

    /* Default styles for the buttons */
    .plan-type-checkbox {
        color: #6c757d;
        /* Default text color */
        background-color: #f8f9fa;
        /* Default background color */
        border: 1px solid #ced4da;
        /* Default border */
        transition: all 0.3s ease;
        /* Smooth transition */
    }


    .btn-check:checked+.plan-type-checkbox {
        color: white;
        background-color: #007bff;
        border-color: #007bff;
    }

    .member-style {
        color: #72DC68 !important;
        background-color: #F5FFF6 !important;
    }

    .admin-style {
        color: #DCA268 !important;
        background-color: #FFFCF5 !important;
    }
    
    
    
    
    /* Custom modal styles */
    .custom-modal {
        background: rgba(240, 240, 240, 1);
        /* border-radius: 8px; */
        padding: 70px;
        width: 500px;
        margin: auto;

        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    .custom-modal h5 {
        margin-bottom: 20px;
        font-size: 18px;
        font-weight: bold;
    }

    .custom-modal input {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        border: 0px solid white;
        border-radius: 0;
        /* Square corners */
        font-size: 16px;
    }

    .custom-modal button {
        width: 50%;
        /* Set button width to 50% */
        padding: 12px;
        background-color: #fff;
        /* White background */
        border: 0px solid white;
        border-radius: 0;
        /* Square corners */
        cursor: pointer;
        font-size: 16px;
        margin: 0 auto;
        /* Center the button horizontally */
        display: block;
        /* Ensures margin auto works */
        text-align: center;
    }

    .department-item {
        display: flex;
        align-items: center;
        font-size: 14px;
        margin-right: 15px;
    }

    .department-item span {
        margin-right: 5px;
        font-weight: 500;
        color: black;
    }

    .department-item button {
        background-color: transparent;
        border: none;
        color: blue;
        cursor: pointer;
        font-size: 14px;
        padding: 0;
    }

    .department-item button:hover {
        text-decoration: underline;
    }
</style>

@section('content')
    <h1 class="fw-bolder">
        @if ($Program?->program_type == 0)
            Deactivated :
        @else
            Manage
            @endif {{ $Program->company_name }} @if ($Program?->program_type == 2)
                Trial
            @endif Program
    </h1>
    <div>
        <div>
            <h4 class="fw-bold">Company Details</h4>
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif

            <form id="updateProgramForm" action="{{ url('/manage-admin/update-program', ['id' => $Program->id]) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    @include('mw-1.admin.programs.input-edit-component', [
                        'name' => 'company_name',
                        'label' => 'Company Name',
                        'type' => 'text',
                        'placeholder' => 'Enter Company Name',
                        'is_required' => true,
                        'css' => '',
                        'id' => '',
                        'class' => 'remove-req',
                        'value' => $Program->company_name,
                    ])
                    
                    
                    
                                      <div class="d-flex align-items-center">
                        <!-- Card for Access Code -->
                        <div class="card me-3" style="border-radius: 20px; width: auto;">
                            <div style="padding:10px 10px 5px 20px !important"
                                class="card-body d-flex flex-column justify-content-center p-4">
                                <label for="codeId" class="form-label">Access Code</label>
                                <input style="border: none !important; box-shadow: none !important; margin-right: 10px;"
                                    type="text" class="form-control" id="codeId" aria-describedby="codeHelp"
                                    name="code" placeholder="ACCESSCODE" value="{{ $Program->code }}">
                            </div>
                        </div>

                        <!-- Card for Departments (takes the remaining width) -->
                        <div class="card me-3" style="border-radius: 20px; flex-grow: 1;">
                            <div style="padding:10px 10px 5px 20px !important"
                                class="card-body d-flex flex-column justify-content-center p-4">
                                <label for="departId" class="form-label">Departments</label>

                                <div id="departmentList" class="d-flex align-items-center flex-wrap gap-2"></div>

                                <!-- Align button to the left -->
                                <button type="button" class="btn btn-link text-start" data-bs-toggle="modal"
                                    data-bs-target="#add-department" style="text-decoration: none; margin-left: 0;">
                                    Add Department
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden input field for departments array -->
                    <input type="hidden" name="departments" id="departments">

                    @include('mw-1.admin.programs.input-edit-component', [
                        'name' => 'max_lic',
                        'label' => 'Licenses',
                        'type' => 'number',
                        'placeholder' => '5',
                        'is_required' => true,
                        'css' => '',
                        'id' => '',
                        'class' => '',
                        'value' => $Program->max_lic,
                    ])
                    <div class="col-12 me-3">
                        <div class="card" style="border-radius: 20px;height:55px;padding:10px 0px 5px 20px !important">
                            <div style="height: 75px; cursor: pointer;" class="" id="uploadLogoTrigger">
                                <div style="margin-top: 5px;" class="d-flex">
                                    <label for="logoId" class="form-label mb-1" style="margin:unset;font-weight:400;">
                                        <span class="fw-normal" style="margin-right: 10px;">Upload Logo</span>
                                    </label>
                                    <input type="file" class="form-control" id="logoId" name="logo"
                                        style="margin: unset;border: none !important;box-shadow: none !important;margin-bottom: -20px;padding: unset;font-weight:500;display:none">
                                    <div>
                                        @if ($Program->logo != '')
                                            <img id="previewImage" style="width: 31px;height:30px"
                                                class="popup object-fit-contain" class="popup"
                                                src="{{ asset('storage/logo') }}/{{ $Program->logo }}" alt="logo image">
                                        @else
                                            <img id="previewImage" style="width: 31px;height:30px"
                                                class="popup object-fit-contain" class="popup"
                                                src="{{ asset('mw-1/assets/images/upload.png') }}" alt="logo image">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <label class="form-label">Employees Visible ?</label>
                        <div class="d-flex flex-wrap justify-content-start" style="gap: 10px;">
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" @if ($Program?->allow_employees == '1') checked @endif
                                    name="allow_employees" id="yes-employee" value="1" autocomplete="off">
                                <label class="btn btn-outline-primary rounded-pill px-4 plan-type-checkbox"
                                    style="border-radius: 20px;" for="yes-employee">Yes
                            </div>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" @if ($Program?->allow_employees == 0) checked @endif
                                    name="allow_employees" id="no-employee" value="0" autocomplete="off">
                                <label class="plan-type-checkbox btn btn-outline-primary rounded-pill px-4"
                                    style="border-radius: 20px;" for="no-employee">No</label>
                            </div>
                        </div>

                    <input type="hidden" name="program_type" value="{{ $Program->program_type }}">
                    @include('mw-1.admin.programs.input-edit-component', [
                        'name' => 'link',
                        'label' => 'Booking Link',
                        'type' => 'text',
                        'placeholder' => 'Enter Booking Link',
                        'is_required' => true,
                        'css' => '',
                        'id' => '',
                        'class' => '',
                        'value' => $Program->link,
                    ])


                    @include('mw-1.admin.programs.input-edit-component', [
                        'name' => 'max_session',
                        'label' => 'Max Session',
                        'type' => 'text',
                        'placeholder' => 'Enter Max Session',
                        'is_required' => true,
                        'css' => '',
                        'id' => $Program->id,
                        'class' => '',
                        'value' => $Program->max_session,
                    ])


                    {{-- <h4><strong>Assign Admin User</strong></h4>
                    @include('mw-1.admin.programs.input-edit-component', [
                        'name' => 'full_name',
                        'label' => 'Full Name',
                        'type' => 'text',
                        'placeholder' => 'Enter Full Name',
                        'is_required' => true,
                        'css' => '',
                        'id' => '',
                        'class' => '',
                        'value' => $employee->name ?? '',
                    ])

                    @include('mw-1.admin.programs.input-edit-component', [
                        'name' => 'admin_email',
                        'label' => 'Email',
                        'type' => 'email',
                        'placeholder' => 'Enter admin email',
                        'is_required' => true,
                        'css' => '',
                        'id' => '',
                        'class' => '',
                        'value' => $employee->email ?? '',
                    ]) --}}

                </div>


                @if ($Program->program_type == 2)
                    @include('mw-1.admin.programs.input-edit-component', [
                        'name' => 'trial_expire',
                        'label' => 'Trial Expire Date',
                        'type' => 'date',
                        'placeholder' => 'Enter Expire Date',
                        'is_required' => true,
                        'css' => $Program->program_type == 2 ? 'display: block;' : 'display: none;',
                        'id' => 'trial_expire',
                        'class' => 'rem-trial-expire',
                        'value' => \Carbon\Carbon::parse($Program->trial_expire ?? '')->format('Y-m-d'),
                    ])
                @endif

                @if ($Program->program_type == 1)
                    <div id="active-program">
                        <span class="fw-bolder mb-3" style="color: #000000;">Payment/Pricing</span>
                        <br>
                        <label class="form-label">Plan Type</label>

                        <!-- Radio buttons for plan types -->
                        <div class="d-flex flex-wrap justify-content-start" style="gap: 10px;">
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" @if ($ProgramPlan?->plan_type == 'Pay As You Go') checked @endif
                                    name="plan_type" id="active" value="Pay As You Go" autocomplete="off"
                                    @if ($Program->program_type == 1) required @endif>
                                <label class="btn btn-outline-primary rounded-pill px-4 plan-type-checkbox"
                                    style="border-radius: 20px;" for="active">Pay As You Go</label>
                            </div>

                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" @if ($ProgramPlan?->plan_type == 'Standard') checked @endif
                                    name="plan_type" id="trial" value="Standard" autocomplete="off"
                                    @if ($Program->program_type == 1) required @endif>
                                <label class="plan-type-checkbox btn btn-outline-primary rounded-pill px-4"
                                    style="border-radius: 20px;" for="trial">Standard</label>
                            </div>
                        </div>

                        <!-- Additional radio button for Premium -->
                        <div class="d-flex flex-wrap justify-content-start" style="gap: 10px; margin-bottom: 20px;">
                            <div class="btn-group mt-2" role="group">
                                <input type="radio" class="btn-check" @if ($ProgramPlan?->plan_type == 'Premium') checked @endif
                                    name="plan_type" id="deactivated" value="Premium" autocomplete="off"
                                    @if ($Program->program_type == 1) required @endif>
                                <label class="plan-type-checkbox btn btn-outline-primary rounded-pill px-4 w-auto"
                                    style="border-radius: 20px;" for="deactivated">Premium</label>
                            </div>
                        </div>
                    </div>

                    <div>

                        @include('mw-1.admin.programs.input-edit-component', [
                            'name' => 'annual_fee',
                            'label' => 'Annual Fee',
                            'type' => 'number',
                            'placeholder' => 'Enter Annual Fee',
                            'is_required' => true,
                            'css' => '',
                            'id' => '',
                            'class' => 'remove-req',
                            'value' => $ProgramPlan?->annual_fee ?? '',
                        ])

                        @include('mw-1.admin.programs.input-edit-component', [
                            'name' => 'cost_per_session',
                            'label' => 'Cost per session',
                            'type' => 'number',
                            'placeholder' => 'Enter Cost per Session',
                            'is_required' => true,
                            'css' => '',
                            'id' => '',
                            'class' => 'remove-req',
                            'value' => $ProgramPlan?->cost_per_session ?? '',
                        ])


                        @include('mw-1.admin.programs.input-edit-component', [
                            'name' => 'renewal_date',
                            'label' => 'Renewal Date',
                            'type' => 'text',
                            'placeholder' => 'Enter Renewal Date',
                            'is_required' => true,
                            'css' => '',
                            'id' => 'renewal_dateId',
                            'class' => 'remove-req',
                            'value' => \Carbon\Carbon::parse($ProgramPlan?->renewal_date)->format('d/m'),
                        ])
                        <label class="form-label">GST? +10%</label>
                        <div class="d-flex flex-wrap justify-content-start" style="gap: 10px;">
                            <!-- Active Button -->
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" @if ($ProgramPlan?->gst_registered == '1') checked @endif
                                    name="gst_registered" id="yes" value="1" autocomplete="off">
                                <label class="btn btn-outline-primary rounded-pill px-4 plan-type-checkbox"
                                    style="border-radius: 20px;" for="yes">Yes
                            </div>
                            <!-- Trial Button -->
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" @if ($ProgramPlan?->gst_registered == 0) checked @endif
                                    name="gst_registered" id="no" value="0" autocomplete="off">
                                <label class="plan-type-checkbox btn btn-outline-primary rounded-pill px-4"
                                    style="border-radius: 20px;" for="no">No</label>
                            </div>

                            <!-- Deactivated Button -->
                        </div>
                    </div>
                @endif

                {{-- @if ($ProgramPlan->type ?? '' == 0)
                        <div class="d-flex mt-2" style="justify-content: space-evenly;">
                            <button id="submitButton" type="submit"
                                class="btn btn-primary mb-2 mr-3 mindway-btn-blue">Make Active Program</button>
                            <button id="submitButton" type="submit"
                                class="btn btn-primary mb-2 mr-3 mindway-btn-blue">Extend Trial By 14 days</button>
                            <button id="submitButton" type="submit"
                                class="btn btn-primary mb-2 mr-3 mindway-btn-blue">Delete Permanently</button>
                        </div>
                    @endif --}}

        </div>

        @if ($Program->program_type == 1)
            <div class="d-flex justify-content-center">
                <a class="btn btn-primary mb-2 mindway-btn-blue me-2"
                    href="{{ url('/manage-admin/deactive-program/' . $Program->id . '/deactivate') }}">
                    Deactivate
                </a>
                <button id="submitButton" type="submit" class="btn btn-primary mb-2 mindway-btn-blue">Update
                    Program</button>
            </div>
        @endif

        @if ($Program->program_type == 0)
            <div class="d-flex justify-content-center flex-wrap">
                <a class="btn btn-primary mindway-btn-blue mx-2 mb-2"
                    href="{{ url('/manage-admin/deactive-program/' . $Program->id . '/active') }}">
                    Make Active Program
                </a>
                <a class="btn btn-primary mindway-btn-blue mx-2 mb-2"
                    href="{{ url('/manage-admin/deactive-program/' . $Program->id . '/extend_trial') }}">
                    Extend Trial By 14 days
                </a>
                <a class="btn btn-primary mindway-btn-blue mx-2 mb-2"
                    href="{{ url('/manage-admin/deactive-program/' . $Program->id . '/delete') }}">
                    Delete Permanently
                </a>
                <button id="submitButton" type="submit" class="btn btn-primary mx-2 mb-2 mindway-btn-blue">
                    Update Program
                </button>
            </div>
        @endif

        @if ($Program->program_type == 2)
            <div class="d-flex justify-content-center">
                <a class="btn btn-primary mb-2 mindway-btn-blue mx-2"
                    href="{{ url('/manage-admin/deactive-program/' . $Program->id . '/deactivate') }}">
                    Deactivate
                </a>
                <a class="btn btn-primary mb-2 mindway-btn-blue mx-2"
                    href="{{ url('/manage-admin/deactive-program/' . $Program->id . '/active') }}">
                    Update to active program
                </a>
                <button id="submitButton" type="submit" class="btn btn-primary mb-2 mindway-btn-blue mx-2">Update
                    Program</button>
            </div>
        @endif


        </form>
        @include('mw-1.admin.programs.add-bulk', ['programId' => $Program->id])
        @include('mw-1.admin.programs.add-employee-modal', ['programId' => $Program->id])

        <div class="d-flex justify-content-start align-items-center mb-4">
            <b> <a href="#" class="me-3 text-dark" style="text-decoration: none; font-size:20px;font-weight:20px"
                    data-bs-toggle="modal" data-bs-target="#exampleModal">Add Individual</a></b>



            <b> <a href="#" class="text-dark" style="text-decoration: none;font-size:20px;font-weight:20px"  data-bs-toggle="modal" data-bs-target="#addSessionModalBulk">Add
                Bulk</a></b>
        </div>
        <div class="card w-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-start" style="align-items:baseline">
                    <h3 class="fw-normal">Employees ({{ count($customers) }})</h3>

                    <div class="search-input">
                        <i class="ti ti-search" style="font-size: 16px; margin-left: 15px;"></i>
                        <input
                            style="width: 195px; margin-left: 10px; height: 40px;border-radius:20px;background-color:#F7F7F7"
                            type="text" id="searchInput" class="form-control" placeholder="Search details">
                    </div>

                    <h2 class="fw-bold" style="margin-left: 60px;font-size:15px">Level</h2>
                    <h2 class="fw-bold" style="margin-left: 195px;font-size:15px">Session Left</h2>
                </div>

                <div class="table-responsive">
                    <table class="table text-nowrap mb-0 align-middle" id="employeeTable">
                        <tbody>
                            @foreach ($customers as $data)
                                <tr>
                                    <td class="border-bottom-0" style="width: 350px;"><span
                                            class=" fw-semibold">{{ $data->name }}</span><br>
                                        <span class=" fw-normal">{{ $data->email }}</span>
                                    </td>
                                    <td class="border-bottom-0" id="changeLevel" style="width: 250px;"
                                        data-id="{{ $data->id }}" data-level="{{ $data->level }}"><span
                                            class="badge btn btn-primary theme-btn {{ $data->level == 'member' ? 'member-style' : 'admin-style' }}">{{ $data->level }}</span>
                                    </td>
                                    <td>{{ intval($data->max_session) }}


                                        <a href="{{ route('plus-session', ['customerId' => $data->id, 'programId' => $Program->id]) }}"
                                            class="mindway-btn btn btn-success btn-sm remove-btn"
                                            style="background-color: #E4E4E4 !important;color:#7C7C7C !important;margin-left: 10px;">
                                            Add
                                        </a>
                                    </td>
                                    <td class="border-bottom-0">

                                        <a href="{{ route('remove-cusomer-program', ['customerId' => $data->id, 'programId' => $Program->id]) }}"
                                            class="mindway-btn btn btn-success btn-sm remove-btn"
                                            style="background-color: #E4E4E4 !important;color:#7C7C7C !important">
                                            Remove
                                            <i class="typcn typcn-view btn-icon-append"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <!-- Modal UI -->
    <div class="modal" id="adminLevelModal" tabindex="-1" role="dialog" aria-labelledby="adminLevelModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content" style="width:80%">
                <div class="modal-body">
                    <input type="hidden" value="" name="memberIdInput" id="memberIdInput">
                    <div class="d-flex flex-wrap justify-content-start" style="gap: 10px;">
                        <!-- Member -->
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="level" id="levelMember" value="member"
                                autocomplete="off">
                            <label style="color:#72DC68 !important;background-color:#F5FFF6 !important"
                                class="mindway-btn btn btn-sm btn-outline-primary rounded-pill px-4 plan-type-checkbox"
                                for="levelMember">Member</label>
                        </div>
                        <!-- Admin -->
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="level" id="levelAdmin" value="admin"
                                autocomplete="off">
                            <label style="color:#DCA268 !important;background-color:#FFFCF5 !important"
                                class="mindway-btn btn-sm plan-type-checkbox btn btn-outline-primary rounded-pill px-4"
                                for="levelAdmin">Admin</label>
                        </div>

                    </div>
                    <button type="submit" id="submitAdminLevel"
                        style="place-self: center; display: flex; margin-top: 11px;"
                        class="btn btn-sm btn-primary mindway-btn-blue">Confirm</button>
                </div>
            </div>
        </div>
    </div>


<div class="modal fade" id="add-department" tabindex="-1" aria-labelledby="add-departmentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-none bg-transparent">
                <div class="custom-modal">
                    <h5>Department Name</h5>
                    <input type="text" id="departmentNameInput">
                    <button id="addDepartmentButton">Add</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        const renewalDateInput = document.getElementById('renewal_date');

        if (renewalDateInput) {
            renewalDateInput.addEventListener('input', function(e) {
                let value = e.target.value;
                value = value.replace(/[^0-9]/g, ''); // Remove non-numeric characters
                if (value.length > 2) {
                    value = value.slice(0, 2) + '/' + value.slice(2); // Add '/' after the first two digits
                }
                value = value.slice(0, 5); // Limit to 5 characters
                e.target.value = value;
            });
        }

        function makeFieldsRequired(fieldIds) {
            fieldIds.forEach((id) => {
                const inputElement = document.getElementById(id);
                if (inputElement) {
                    inputElement.setAttribute('required', 'required');
                    console.log(`Field with id '${id}' is now required.`);
                } else {
                    console.error(`Field with id '${id}' not found.`);
                }
            });
        }
        let type = "{{ $Program->program_type }}";
        $(document).ready(function() {
            if (type == 1) {
                $('#active-program').css('display', 'block');
                const fields = ["annual_feeId", "cost_per_sessionId", "renewal_dateId"];
                makeFieldsRequired(fields);
            }
        });
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#employeeTable tbody tr');

            rows.forEach(row => {
                const name = row.querySelector('td:nth-child(1)').innerText.toLowerCase();
                const email = row.querySelector('td:nth-child(2)').innerText.toLowerCase();

                if (name.includes(filter) || email.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
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

        document.querySelectorAll('#changeLevel').forEach(td => {
            td.addEventListener('click', function() {
                // Get user ID and current level
                const memberId = this.getAttribute('data-id');
                var currentLevel = this.getAttribute("data-level");

                // Set the modal values
                document.getElementById('memberIdInput').value = memberId;
                if (currentLevel === 'member') {
                    document.getElementById('levelMember').checked = true;
                } else if (currentLevel === 'admin') {
                    document.getElementById('levelAdmin').checked = true;
                }
                // Show the modal
                $('#adminLevelModal').modal('show');
            });
        });
        // Handle the submit button click
        document.getElementById("submitAdminLevel").addEventListener("click", function() {
            var memberId = document.getElementById("memberIdInput").value;
            const newLevel = document.querySelector('input[name="level"]:checked').value;

            if (!memberId) {
                alert('Please select a member.');
                return;
            }

            // Send the API request to update admin level
            fetch('/update-customer-level', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        member_id: memberId,
                        admin_level: newLevel
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success("Admin level updated successfully!");

                        // alert('Admin level updated successfully!');
                        setTimeout(() => {
                            const td = document.querySelector(`#changeLevel[data-id="${memberId}"]`);
                            const span = td.querySelector('span');
                            span.textContent = newLevel;
                            if (newLevel === 'member') {
                                span.classList.add('member-style');
                                span.classList.remove('admin-style');
                            } else {
                                span.classList.add('admin-style');
                                span.classList.remove('member-style');
                            }
                            td.setAttribute('data-level', newLevel);
                            $('#adminLevelModal').modal('hide'); // Hide the modal
                        }, 3000);

                    } else {
                        alert('Failed to update admin level.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the admin level.');
                });
        });



        const programTypeSelect = document.getElementById('program_typeSelect');
        if (programTypeSelect) {
            programTypeSelect.addEventListener('change', function() {
                const pricingSection = document.getElementById('pricing');
                const trialExpire = document.getElementById('trial_expire');
                // Show or hide the trial expiration section
                if (trialExpire) {
                    if (this.value === '2') {
                        trialExpire.style.display = 'block';
                    } else {
                        trialExpire.style.display = 'none';
                    }
                }
                // Show or hide the pricing section
                if (pricingSection) {
                    if (this.value === '1') {
                        pricingSection.style.display = 'block';
                    } else {
                        pricingSection.style.display = 'none';
                    }
                }
                // Manage required attribute for `.remove-req` fields
                const inputFieldsRemoveReq = document.querySelectorAll('.remove-req');
                if (inputFieldsRemoveReq) {
                    inputFieldsRemoveReq.forEach(field => {
                        if (this.value === '2' || this.value === '0') {
                            field.removeAttribute('required');
                        } else {
                            field.required = true;
                        }
                    });
                }
                // Manage required attribute for `.rem-trial-expire` fields
                const inputFieldsRemTrialExpire = document.querySelectorAll('.rem-trial-expire');
                if (inputFieldsRemTrialExpire) {
                    inputFieldsRemTrialExpire.forEach(field => {
                        if (this.value === '1' || this.value === '0') {
                            field.removeAttribute('required');
                        } else {
                            field.required = true;
                        }
                    });
                }
            });
        }




        document.getElementById('uploadLogoTrigger').addEventListener('click', function() {
            document.getElementById('logoId').click();
        });

        document.getElementById('logoId').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

      
    document.addEventListener('DOMContentLoaded', function () {
        const departmentList = document.getElementById('departmentList');
        const departmentsInput = document.getElementById('departments');
        const departmentNameInput = document.getElementById('departmentNameInput');
        const addDepartmentButton = document.getElementById('addDepartmentButton');

        // Initialize departments array from server-side data
        let departments = @json($programDepart);

        // Function to update the displayed department list
        function updateDepartmentList() {
            departmentList.innerHTML = '';
            departments.forEach((department, index) => {
                const departmentItem = document.createElement('div');
                departmentItem.classList.add('department-item');

                // Department name
                const departmentName = document.createElement('span');
                departmentName.textContent = department;

                // Remove button
                const removeButton = document.createElement('button');
                removeButton.textContent = 'Remove';
                removeButton.addEventListener('click', function () {
                    removeDepartment(index);
                });

                departmentItem.appendChild(departmentName);
                departmentItem.appendChild(removeButton);
                departmentList.appendChild(departmentItem);
            });

            // Update hidden input value
            departmentsInput.value = JSON.stringify(departments);
        }

        addDepartmentButton.addEventListener('click', function() {
                const departmentName = departmentNameInput.value.trim();

                if (departmentName) {
                    // Add to departments array
                    departments.push(departmentName);

                    // Update the list and hidden input
                    updateDepartmentList();

                    // Clear input field and close modal
                    departmentNameInput.value = '';
                    const modal = bootstrap.Modal.getInstance(document.getElementById('add-department'));
                    modal.hide();
                }
            });

        // Function to remove a department
        function removeDepartment(index) {
            departments.splice(index, 1); // Remove the department from the array
            updateDepartmentList(); // Update the displayed list and hidden input
        }

        // Initial render of the department list
        updateDepartmentList();
    });


    </script>
@endsection
