@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
    <style>
        .plan-type-checkbox {
            border: 1px solid #F2F2F2;
            color: #000000;
            font-weight: 400;
            font-size: 13px;
        }

        body {
            color: #F9F9F9;
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
    <h1 class="fw-bolder mb-2">{{ $type == 1 ? 'Create New Active Program' : 'Create Trial Program' }}</h1>
    <div>
        <div>
            <h4 class="fw-bolder mb-2">Company Details</h4>
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            @endif

            <form action="{{ url('/manage-admin/store-program') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="program_type" value="{{ $type }}">
                <div class="row">
                    @include('mw-1.admin.programs.input-component', [
                        'name' => 'company_name',
                        'label' => 'Company Name',
                        'type' => 'text',
                        'placeholder' => 'Enter Company Name',
                        'is_required' => true,
                        'css' => '',
                        'id' => '',
                        'class' => '',
                    ])



                        <div class="d-flex align-items-center">
                        <!-- Card for Access Code -->
                        <div class="card me-3" style="border-radius: 20px; width: auto;">
                            <div style="padding:10px 10px 5px 20px !important"
                                class="card-body d-flex flex-column justify-content-center p-4">
                                <label for="codeId" class="form-label">Access Code</label>
                                <input style="border: none !important; box-shadow: none !important; margin-right: 10px;"
                                    type="text" class="form-control" id="codeId" aria-describedby="codeHelp"
                                    name="code" placeholder="ACCESSCODE" value="{{ old('code') }}">
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


                    @include('mw-1.admin.programs.input-component', [
                        'name' => 'max_lic',
                        'label' => 'Licenses',
                        'type' => 'number',
                        'placeholder' => '1000',
                        'is_required' => true,
                        'css' => '',
                        'id' => '',
                        'class' => '',
                    ])
                    @include('mw-1.admin.programs.input-component', [
                        'name' => 'max_session',
                        'label' => 'Max Sessions',
                        'type' => 'text',
                        'placeholder' => '5',
                        'is_required' => true,
                        'css' => '',
                        'id' => '',
                        'class' => '',
                    ])
                    @include('mw-1.admin.programs.input-component', [
                        'name' => 'link',
                        'label' => 'Booking Link',
                        'type' => 'url',
                        'placeholder' => 'https:://',
                        'is_required' => true,
                        'css' => '',
                        'id' => '',
                        'class' => '',
                    ])

                    <div class="col-12 me-3">
                        <div class="card" style="border-radius: 20px;height:55px;padding:10px 0px 5px 20px !important">
                            <div style="height: 75px; cursor: pointer;" class="" id="uploadLogoTrigger">
                                <div style="margin-top: 5px;" class="d-flex">
                                    <label for="logoId" class="form-label mb-1" style="margin:unset;font-weight:400;">
                                        <span class="fw-normal" style="margin-right: 10px;">Upload Logo</span>
                                    </label>
                                    <input type="file" class="form-control" id="logoId" name="logo"
                                        style="margin: unset;border: none !important;box-shadow: none !important;margin-bottom: -20px;padding: unset;font-weight:500;display:none"
                                        required>
                                    <div>

                                        <img id="previewImage" style="width: 31px;height:30px"
                                            class="popup object-fit-contain" class="popup"
                                            src="{{ asset('mw-1/assets/images/upload.png') }}" alt="logo image">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <span class="fw-bolder mb-3" style="color: #000000;">Assign Admin User</span>
                    @include('mw-1.admin.programs.input-component', [
                        'name' => 'full_name',
                        'label' => 'Full Name',
                        'type' => 'text',
                        'placeholder' => 'Ryder Mckenzie',
                        'is_required' => true,
                        'css' => '',
                        'id' => '',
                        'class' => '',
                    ])

                    @include('mw-1.admin.programs.input-component', [
                        'name' => 'admin_email',
                        'label' => 'Email',
                        'type' => 'email',
                        'placeholder' => 'your@email.com',
                        'is_required' => true,
                        'css' => '',
                        'id' => '',
                        'class' => '',
                    ])

                    @if ($type == 2)
                        @include('mw-1.admin.programs.input-component', [
                            'name' => 'trial_expire',
                            'label' => 'Trial Expire Date',
                            'type' => 'date',
                            'placeholder' => 'Enter Expire Date',
                            'is_required' => true,
                            'css' => $type == 2 ? 'display: block;' : 'display: none;',
                            'id' => 'trial_expire',
                            'class' => 'rem-trial-expire',
                        ])
                    @endif



                    <div id="active-program" class="d-none;" style="display: none;">
                        <span class="fw-bolder mb-3" style="color: #000000;">Payment/Pricing</span>
                        <label class="form-label">Plan Type</label>

                        <div class="d-flex flex-wrap justify-content-start" style="gap: 10px;">
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="plan_type" id="active"
                                    value="Pay As You Go" autocomplete="off">
                                <label class="btn btn-outline-primary rounded-pill px-4 plan-type-checkbox"
                                    style="border-radius: 20px;" for="active">Pay As You Go</label>
                            </div>

                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="plan_type" id="trial" value="Standard"
                                    autocomplete="off">
                                <label class="plan-type-checkbox btn btn-outline-primary rounded-pill px-4"
                                    style="border-radius: 20px;" for="trial">Standard</label>
                            </div>



                        </div>
                        <div class="d-flex flex-wrap justify-content-start" style="gap: 10px; margin-bottom:20px">

                            <div class="btn-group mt-2" role="group">
                                <input type="radio" class="btn-check" name="plan_type" id="deactivated" value="Premium"
                                    autocomplete="off">
                                <label class=" plan-type-checkbox btn btn-outline-primary rounded-pill px-4 w-auto"
                                    style="border-radius: 20px;" for="deactivated">Premium</label>
                            </div>
                        </div>


                        @include('mw-1.admin.programs.input-component', [
                            'name' => 'annual_fee',
                            'label' => 'Annual Fee',
                            'type' => 'number',
                            'placeholder' => 'Enter Annual Fee',
                            'is_required' => false,
                            'css' => '',
                            'id' => '',
                            'class' => 'remove-req',
                        ])
                        @include('mw-1.admin.programs.input-component', [
                            'name' => 'cost_per_session',
                            'label' => 'Cost per Session',
                            'type' => 'number',
                            'placeholder' => 'Enter Cost per Session',
                            'is_required' => false,
                            'css' => '',
                            'id' => '',
                            'class' => 'remove-req',
                        ])
                        @include('mw-1.admin.programs.input-component', [
                            'name' => 'renewal_date',
                            'label' => 'Renewal Date',
                            'type' => 'text',
                            'placeholder' => 'dd/mm',
                            'is_required' => false,
                            'css' => '',
                            'id' => 'renewal_date',
                            'class' => 'remove-req',
                        ])
                        <label class="form-label">GST? +10%</label>
                        <div class="d-flex flex-wrap justify-content-start" style="gap: 10px;">
                            <!-- Active Button -->
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="gst_registered" id="yes" value="yes"
                                    autocomplete="off">
                                <label class="btn btn-outline-primary rounded-pill px-4 plan-type-checkbox"
                                    style="border-radius: 20px;" for="yes">Yes
                            </div>

                            <!-- Trial Button -->
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="gst_registered" id="no" value="no"
                                    autocomplete="off">
                                <label class="plan-type-checkbox btn btn-outline-primary rounded-pill px-4"
                                    style="border-radius: 20px;" for="no">No</label>
                            </div>

                            <!-- Deactivated Button -->


                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">

                    <button type="submit" class="btn btn-primary mindway-btn-blue"
                        style="margin-left: 20px;">{{ $type == '2' ? 'Create Trial Program' : 'Create Active Program' }}</a>

                </div>
            </form>
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
        let type = "{{ $type }}";
        $(document).ready(function() {
            if (type == 1) {
                $('#active-program').css('display', 'block');
                const fields = ["annual_feeId", "cost_per_sessionId", "renewal_dateId"];
                makeFieldsRequired(fields);
            }
        });


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

        document.getElementById('renewal_date').addEventListener('input', function(e) {
            let value = e.target.value;

            // Remove non-numeric characters
            value = value.replace(/[^0-9]/g, '');

            // Add `/` after 2 digits for day
            if (value.length > 2) {
                value = value.slice(0, 2) + '/' + value.slice(2);
            }

            // Limit the input to 5 characters (DD/MM)
            value = value.slice(0, 5);

            e.target.value = value;
        });

        document.addEventListener('DOMContentLoaded', function() {
            const departmentList = document.getElementById('departmentList');
            const departmentsInput = document.getElementById('departments');
            const departmentNameInput = document.getElementById('departmentNameInput');
            const addDepartmentButton = document.getElementById('addDepartmentButton');
            let departments = [];

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
                    removeButton.addEventListener('click', function() {
                        removeDepartment(index);
                    });

                    departmentItem.appendChild(departmentName);
                    departmentItem.appendChild(removeButton);
                    departmentList.appendChild(departmentItem);
                });

                // Update hidden input value
                departmentsInput.value = JSON.stringify(departments);
            }

            // Function to add a department
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
        });
    </script>
@endsection