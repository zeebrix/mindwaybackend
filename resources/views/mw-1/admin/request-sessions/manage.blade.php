@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
<style>
    .nav-item .nav-link {
        padding: 10px 15px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .nav-item .nav-link:hover {
        background-color: unset;
    }

    .active-tab .nav-link {
        background-color: unset;
        color: #688EDC;
        font-weight: 700;
        border-radius: 5px;
    }
</style>
<div class="w-100">
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title fw-bolder" style="color:#000000">Requested Session</h5>
        </div>

        <div class="mb-4 col-12">

            <nav class="navbar navbar-expand-lg navbar-light bg-white">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <!-- Active Tab -->
                        <li class="nav-item {{ request()->get('status') == 'pending' ? 'active-tab' : '' }}" style="margin-right: 10px;">
                            <a class="nav-link fw-bolder" href="/manage-admin/view-request-session?status=pending">Pending</a>
                        </li>

                        <!-- Trials Tab -->
                        <li class="nav-item {{ request()->get('status') == 'accepted' ? 'active-tab' : '' }}" style="margin-right: 10px;">
                            <a class="nav-link fw-bolder" href="/manage-admin/view-request-session?status=accepted">Accepted</a>
                        </li>

                        <!-- Deactivated Tab -->
                        <li class="nav-item {{ request()->get('status') == 'denied' ? 'active-tab' : '' }}">
                            <a class="nav-link fw-bolder" href="/manage-admin/view-request-session?status=denied">Denied</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <hr class="p-0 m-0" style="border: 1px solid #AEAEAE;">
            
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

        <div class="table-responsive">
            <table class="table text-nowrap mb-0 align-middle" id="Yajra-dataTable">
                <thead class="text-dark fs-4">
                    <tr>
                        <th class="border-bottom-0">
                            <h6 class="fw-bold mb-0">Request ID</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-medium mb-0" style="color:#000000">Date Requested</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-medium mb-0" style="color:#000000">Requested</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-medium mb-0" style="color:#000000">Empl. ID</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-medium mb-0" style="color:#000000">Action</h6>
                        </th>
                    </tr>
                </thead>
                
            </table>
        </div>
    </div>
</div>
@if (request()->get('status') == 'pending')
    @include('mw-1.admin.request-sessions.review')
@else
    @include('mw-1.admin.request-sessions.onlydisplay')
@endif


@endsection


@section('js')

@include('mw-1.admin.request-sessions.datatable')
    
<script>$(document).ready(function() {
    const requestedModal = new bootstrap.Modal(document.getElementById('requestedModal'));
    
    const status = "{{ request()->get('status') }}";
    
    if (status === 'denied' && status !== 'pending') {
        $('#hideit').hide();    
    }

    $(document).on('click', '.review-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');    
        $.get(url, function(data) {
            if(data.success) {

                // Populate all the fields in the modal
                $('#requestedNameValue').text(data.client_name || 'N/A');
                $('#requestedEmailValue').text(data.client_email || 'N/A');
                $('#requestedIDValue').text(data.client_id || 'N/A');
                $('#CounsellorNameValue').text(data.counselor_name || 'N/A');
                $('#reasonsValue').text(data.reasons || 'N/A');
                $('#clientRequestValue').text(data.request_id || 'N/A');
                
                if(status == 'pending'){
                    $('#requestedDateValue').text(data.requested_date || 'N/A');
                }else if(status == 'accepted'){
                    $('#approvedDateValue').text(data.approved_date || 'N/A');
                    $('#deniedDate').hide();    
                }else{
                    $('#approvedDate').hide();    
                    $('#deniedDateValue').text(data.denied_date || 'N/A');
                }
                
                
                
                // Set the hidden input field with the request ID
                const requestedIdElement = document.getElementById('requestedId');
                if (requestedIdElement) {
                    requestedIdElement.value = data.request_id;
                }

                // Set value for 'requestedIdDeny' if the element exists
                const requestedIdDenyElement = document.getElementById('requestedIdDeny');
                if (requestedIdDenyElement) {
                    requestedIdDenyElement.value = data.request_id;
                }  // Set the requested days radio button
                if(data.requested_days) {
                    const days = parseInt(data.requested_days);
                    if(days >= 1 && days <= 5) {
                        $(`#session-count-${days}`).prop('checked', true);
                    }
                }
                
                requestedModal.show();
            }
        }).fail(function() {
            alert('Failed to load request details');
        });
    });
});
</script>
@endsection