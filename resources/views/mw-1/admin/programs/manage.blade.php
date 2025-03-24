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
            <h5 class="card-title fw-bolder" style="color:#000000">Programs</h5>
        </div>

        <div class="mb-4 col-12">

            <nav class="navbar navbar-expand-lg navbar-light bg-white">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <!-- Active Tab -->
                        <li class="nav-item {{ request()->get('status') == '1' ? 'active-tab' : '' }}  {{ $status == '1' ? 'active-tab' : '' }}" style="margin-right: 10px;">
                            <a class="nav-link fw-bolder" href="/manage-admin/view-programs?status=1">Active</a>
                        </li>

                        <!-- Trials Tab -->
                        <li class="nav-item {{ request()->get('status') == '2' ? 'active-tab' : '' }}" style="margin-right: 10px;">
                            <a class="nav-link fw-bolder" href="/manage-admin/view-programs?status=2">Trials</a>
                        </li>

                        <!-- Deactivated Tab -->
                        <li class="nav-item {{ request()->get('status') == '0' ? 'active-tab' : '' }}">
                            <a class="nav-link fw-bolder" href="/manage-admin/view-programs?status=0">Deactivated</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <hr class="p-0 m-0" style="border: 1px solid #AEAEAE;">
            <div class="input-group mt-3">
                <!-- <div class="form-outline">
                    <input style="border-radius: 20px;width:150px" type="search" placeholder="Search" name="search" class="form-control" />
                </div> -->
                @if(request()->get('status') != '0')
                    <div class="form-outline">
                        <a href="{{route('admin.program.add',['type' => request()->get('status')])}}" class="btn btn-primary mindway-btn-blue" style="margin-left: 20px;">{{ request()->get('status') == '1' ? 'Create Active Program' : 'Create Trial Program' }}</a>
                    </div>
                @endif
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

        <div class="table-responsive">
            <table class="table text-nowrap mb-0 align-middle" id="Yajra-dataTable">
                <thead class="text-dark fs-4">
                    <tr>
                        <th class="border-bottom-0">
                            <h6 class="fw-bold mb-0">Company</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-medium mb-0" style="color:#000000">Licenses</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-medium mb-0" style="color:#000000">Max Session</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-medium mb-0" style="color:#000000">Added</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-medium mb-0" style="color:#000000">Renewal</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-medium mb-0" style="color:#000000"></h6>
                        </th>
                    </tr>
                </thead>
                <!-- <tbody>
                    @php($count = 0)
                    @foreach ($Programs as $data)
                    @php($count++)
                    <tr>
                        <td class="border-bottom-0 col-1">
                            <h6 class="fw-normal mb-0">{{ $data->company_name }}</h6>
                        </td>
                        <td class="border-bottom-0 col-2">
                            <h6 class="fw-bolder mb-1"  style="color:#000000">{{ $data->max_lic }}</h6>
                        </td>

                        <td class="border-bottom-0 col-2">
                            <p class="mb-0 fw-bolder"  style="color:#000000">{{ $data->max_session }}</p>
                        </td>
                        <td class="border-bottom-0 col-2">
                            <p class="mb-0 fw-bolder"  style="color:#000000">{{ $data->code }}</p>
                        </td>
                        <td class="border-bottom-0 col-2">
                            <p class="mb-0 fw-bolder"  style="color:#000000">{{ $data?->programPlan?->renewal_date ? ($data->programPlan->renewal_date)->format('m/d') : ''}}</p>
                        </td>
                        <td class="border-bottom-0">
                            <a href="{{ url('/manage-admin/program', ['id' => $data->id]) }}"
                                class="btn btn-success btn-sm btn-icon-text mr-3 mindway-btn-blue">
                                Manage
                                <i class="typcn typcn-view btn-icon-append"></i>
                            </a> 
                            <a href="{{ url('/manage-admin/delete-quote', ['id' => $data->id]) }}"  class="btn btn-danger btn-sm btn-icon-text">
                                                        Delete
                                                        <i class="typcn typcn-delete-outline btn-icon-append"></i>
                                                      </a> 
                        </td>
                    </tr>
                    @endforeach
                </tbody> -->
            </table>
        </div>
    </div>
</div>
@endsection


@section('js')
    @include('mw-1.admin.programs.datatable')
@endsection

<!-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the search input element
        const searchInput = document.querySelector('input[name="search"]');

        // Get the table rows
        const tableRows = document.querySelectorAll('table tbody tr');

        // Event listener for search input
        searchInput.addEventListener('input', function () {
            const searchValue = searchInput.value.toLowerCase();

            // Loop through all rows and hide those that do not match the search term
            tableRows.forEach(function(row) {
                const companyName = row.querySelector('td:nth-child(1) h6').textContent.toLowerCase();
                const licenses = row.querySelector('td:nth-child(2) h6').textContent.toLowerCase();
                const maxSession = row.querySelector('td:nth-child(3) p').textContent.toLowerCase();
                const added = row.querySelector('td:nth-child(4) p').textContent.toLowerCase();
                const renewal = row.querySelector('td:nth-child(5) p').textContent.toLowerCase();

                // Check if any of the columns match the search query
                if (
                    companyName.includes(searchValue) ||
                    licenses.includes(searchValue) ||
                    maxSession.includes(searchValue) ||
                    added.includes(searchValue) ||
                    renewal.includes(searchValue)
                ) {
                    row.style.display = ''; // Show row
                } else {
                    row.style.display = 'none'; // Hide row
                }
            });
        });
    });
</script> -->
