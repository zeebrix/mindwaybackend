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
    
    /* Table styles matching image 2 */
    .request-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }
    
    .request-table thead th {
        color: #000;
        font-weight: 600;
        padding: 12px 15px;
        border: none;
        background-color: transparent;
        font-size: 14px;
        text-align: left;
    }
    
    .request-table tbody tr {
        background-color: #F8F9FA; /* Light gray background */
        border-radius: 8px;
    }
    
    .request-table tbody td {
        padding: 15px;
        vertical-align: middle;
        border: none;
        font-size: 14px;
    }
    
    .request-table tbody td:first-child {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }
    
    .request-table tbody td:last-child {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    
    .request-id {
        color: #688EDC;
        font-weight: 600;
    }
    
    .review-btn {
        background-color: #688EDC;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .pagination {
        margin-top: 20px;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #688EDC;
        border-color: #688EDC;
    }
    
    .pagination .page-link {
        color: #688EDC;
    }
</style>
<div class="w-100">
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title fw-bolder" style="color:#000000">Requested Session</h5>
        </div>

        <div class="mb-4 col-12">
        <nav class="navbar navbar-expand-lg navbar-light bg-white" style="overflow-x: hidden; width: 100%;">
    <div class="collapse navbar-collapse" id="navbarSupportedContent" style="max-width: 100%; overflow-x: auto;">
        <ul class="navbar-nav mr-auto tabs-container" style="
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                white-space: nowrap;
                padding-left: 0;
                margin-bottom: 0;
                list-style: none;
                max-width: 100%;
                scrollbar-width: thin;
                scrollbar-color: #ccc #f9f9f9;
            ">
            <!-- Pending Tab -->
            <li class="nav-item {{ request()->get('status') == 'pending' ? 'active-tab' : '' }}" style="margin-right: 10px; flex-shrink: 0;">
                <a class="nav-link" href="/manage-program/view-request-session?status=pending" style="
                        padding-left: 25px;
                        padding-right: 25px;
                        border-radius: 25px;
                        transition: all 0.3s ease;
                        color: {{ request()->get('status') == 'pending' ? '#fff' : '#000' }};
                        font-weight: {{ request()->get('status') == 'pending' ? '700' : '500' }};
                        background-color: {{ request()->get('status') == 'pending' ? '#688EDC' : '#F5F9FF' }};
                        border: {{ request()->get('status') == 'pending' ? 'none' : '1px solid #F5F9FF' }};
                    ">Pending</a>
            </li>

            <!-- Accepted Tab -->
            <li class="nav-item {{ request()->get('status') == 'accepted' ? 'active-tab' : '' }}" style="margin-right: 10px; flex-shrink: 0;">
                <a class="nav-link" href="/manage-program/view-request-session?status=accepted" style="
                        padding-left: 25px;
                        padding-right: 25px;
                        border-radius: 25px;
                        transition: all 0.3s ease;
                        color: {{ request()->get('status') == 'accepted' ? '#fff' : '#000' }};
                        font-weight: {{ request()->get('status') == 'accepted' ? '700' : '500' }};
                        background-color: {{ request()->get('status') == 'accepted' ? '#688EDC' : '#F5F9FF' }};
                        border: {{ request()->get('status') == 'accepted' ? 'none' : '1px solid #F5F9FF' }};
                    ">Accepted</a>
            </li>

            <!-- Denied Tab -->
            <li class="nav-item {{ request()->get('status') == 'denied' ? 'active-tab' : '' }}" style="margin-right: 10px; flex-shrink: 0;">
                <a class="nav-link" href="/manage-program/view-request-session?status=denied" style="
                        padding-left: 25px;
                        padding-right: 25px;
                        border-radius: 25px;
                        transition: all 0.3s ease;
                        color: {{ request()->get('status') == 'denied' ? '#fff' : '#000' }};
                        font-weight: {{ request()->get('status') == 'denied' ? '700' : '500' }};
                        background-color: {{ request()->get('status') == 'denied' ? '#688EDC' : '#F5F9FF' }};
                        border: {{ request()->get('status') == 'denied' ? 'none' : '1px solid #F5F9FF' }};
                    ">Denied</a>
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
            <table class="table request-table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Date Requested</th>
                        <th>Sessions</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                    <tr>
                        <td class="request-id">#{{ $request->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->request_date)->format('m/d/Y') }}</td>

                        <?php
                            $stat = request()->get('status');
                        ?>
                        @if( $stat == 'pending')
                        <td>{{ $request->request_days }} Recommended</td>
                        @elseif($stat == 'accepted')
                        <td>{{ $request->request_days }} Approved</td>
                        @else
                        <td>0</td>
                        @endif

                        <td class="text-center">
                            <a href="{{ route('reviewSessionRequest', ['id' => $request->id, 'status' => request()->get('status', 'pending')]) }}" 
                               class="review-btn mindway-btn" style="background-color: #688EDC !important; color: #F7F7F7 !important">
                               Review Request
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Simple Pagination -->
        <div class="row mt-4">
            <div class="col-md-12 d-flex justify-content-center">
                {{ $requests->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

@if (request()->get('status') == 'pending')
    @include('mw-1.employeer.request-sessions.review')
@else
    @include('mw-1.employeer.request-sessions.onlydisplay')
@endif
@endsection

@section('js')
<script>
$(document).ready(function() {

    $('#requestedModal form').on('submit', function() {
        $('#requestSessionLoader').fadeIn();
    });
    const requestedModal = new bootstrap.Modal(document.getElementById('requestedModal'));
    
    const status = "{{ request()->get('status') }}";
    
    // if (status === 'denied' && status !== 'pending') {
    //     $('#hideit').hide();    
    // }

    $(document).on('click', '.review-btn', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');    
        $.get(url, function(data) {
            if(data.success) {
                // Populate modal fields
                $('#requestedNameValue').text(data.client_name || 'N/A');
                $('#requestedEmailValue').text(data.client_email || 'N/A');
                $('#requestedIDValue').text(data.client_id || 'N/A');
                $('#CounsellorNameValue').text(data.counselor_name || 'N/A');
                $('#reasonsValue').text(data.reasons || 'N/A');
                $('#clientRequestValue').text(data.request_id || 'N/A');
                $('#addDaysValue').text(data.requested_days || 'N/A');
                $('#addDaysValue1').text(data.requested_days || 'N/A');
                
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