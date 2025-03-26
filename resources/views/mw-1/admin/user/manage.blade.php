@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
    <div class="card w-100">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title fw-semibold">Users</h5>
            </div>
            <div class="table-responsive">
                <table class="table text-nowrap mb-0 align-middle" id="Yajra-dataTable">
                    <thead class="text-dark fs-4">
                        <tr>
                            <!-- <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Sr. No</h6>
                            </th> -->
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">ID</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Name</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Email</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Goal Id</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Improve</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Status</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Created at</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Actions</h6>
                            </th>
                        </tr>
                    </thead>
                  
                </table>
            </div>
        </div>
    </div>


@section('js')
    @include('mw-1.admin.user.datatable')
@endsection
   
@endsection
