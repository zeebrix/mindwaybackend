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
    .member-style {
        color: #72DC68 !important;
        background-color: #F5FFF6 !important;
    }

    .admin-style {
        color: #DCA268 !important;
        background-color: #FFFCF5 !important;
    }
</style>
@section('content')


    <div class="row">
        <div class="col-10 offset-1">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <img style="object-fit: contain;" height="46px" width="130px" class="popup"
                        src="{{ asset('storage/logo/' . $Program->logo) }}" alt="{{ $Program->company_name }} Logo">
                </div>
                <div>
                    @if ($is_trial)
                        <p><b style="color: #000000; font-weight:700px; font-size:15px">On Free Trial:</b> <span
                                style="color: #000000; font-size:15px">{{ $leftDays }} days left of trial</span></p>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <div class="d-flex align-items-center">
                        <h4 class="mb-0"><strong>Manage Employees</strong></h4>
                        <a href="#" class="mindway-btn btn btn-primary ms-2" data-bs-toggle="modal"
                            data-bs-target="#exampleModal" style="white-space: nowrap;">Add Individual</a>
                    </div>
                    <h4>Add and remove employees in your EAP program</h4>
                </div>
            </div>

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

            <div class="d-flex justify-content-start" style="align-items:baseline">
                <h3 class="fw-normal"><b>Employees</b> ({{ count($customers) }})</h3>

                <div class="search-input">
                    <i class="ti ti-search" style="font-size: 16px; margin-left: 15px;"></i>
                    <input style="width: 165px; margin-left: 10px; height: 40px;border-radius:20px;background-color:#F7F7F7"
                        type="text" id="searchInput" class="form-control" placeholder="Search details">
                </div>

                {{-- <div class="ms-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="search details"
                        style="width: 118px;border-radius:20px">
                </div> --}}
                <h2 class="fw-bold" style="margin-left: 65px;font-size:15px">Level</h2>
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
                                <td class="border-bottom-0" id = "changeLevel" style="width: 250px;" data-id="{{ $data->id }}" data-level="{{ $data->level }}"><span
                                        class="{{ $data->level == 'member' ? 'member-style' : 'admin-style'}} badge btn btn-primary theme-btn">{{$data->level??'member'}}</span>
                                    </td>
                                <td class="border-bottom-0">
                                    <button type="button"
                                        style="background-color: #E4E4E4 !important;color:#7C7C7C !important"
                                        class="mindway-btn btn btn-success btn-sm remove-btn"
                                        data-name="{{ $data->name }}" data-email="{{ $data->email }}"
                                        data-id="{{ $data->id }}">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    @include('mw-1.employeer.employees.add')

    <div class="modal" id="adminLevelModal" tabindex="-1" role="dialog" aria-labelledby="adminLevelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content" style="width:80%">
                <div class="modal-body">
                    <input type="hidden" value="" name="memberIdInput" id="memberIdInput">
                    <div class="d-flex flex-wrap justify-content-start" style="gap: 10px;">
                        <!-- Member -->
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="level" id="levelMember" value="member" autocomplete="off">
                            <label style="color:#72DC68 !important;background-color:#F5FFF6 !important" class="mindway-btn btn btn-sm btn-outline-primary rounded-pill px-4 plan-type-checkbox"
                                for="levelMember">Member</label>
                        </div>
                        <!-- Admin -->
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="level" id="levelAdmin" value="admin" autocomplete="off">
                            <label style="color:#DCA268 !important;background-color:#FFFCF5 !important" class="mindway-btn btn-sm plan-type-checkbox btn btn-outline-primary rounded-pill px-4"
                                for="levelAdmin">Admin</label>
                        </div>

                    </div>
                    <button type="submit" id="submitAdminLevel" style="place-self: center; display: flex; margin-top: 11px;"
                        class="btn btn-sm btn-primary mindway-btn-blue">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
                        alert('Admin level updated successfully!');
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
                    } else {
                        alert('Failed to update admin level.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the admin level.');
                });
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


        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.remove-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const employeeName = this.dataset.name;
                    const employeeEmail = this.dataset.email;
                    const customerId = this.dataset.id;

                    Swal.fire({
                        title: 'Are you sure?',
                        text: `Do you want to remove ${employeeName} (${employeeEmail})?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, remove!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Create a form dynamically and submit it
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = "{{ route('remove-customer') }}";

                            // Add CSRF token
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = '{{ csrf_token() }}';
                            form.appendChild(csrfInput);

                            // Add customerId
                            const idInput = document.createElement('input');
                            idInput.type = 'hidden';
                            idInput.name = 'customerId';
                            idInput.value = customerId;
                            form.appendChild(idInput);

                            // Add email
                            const emailInput = document.createElement('input');
                            emailInput.type = 'hidden';
                            emailInput.name = 'email';
                            emailInput.value = employeeEmail;
                            form.appendChild(emailInput);

                            // Append and submit form
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
