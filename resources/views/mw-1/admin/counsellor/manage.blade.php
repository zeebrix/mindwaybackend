@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
<div class="card w-100">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title fw-semibold">All Counsellors</h5>
            <div>
                <button class="btn btn-primary btn btn-primary mindway-btn-blue" data-bs-toggle="modal" data-bs-target="#addCounsellorModal">
                    Add New Counsellor
                </button>
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
                        <!-- <th class="border-bottom-0">Sr. No</th> -->
                        <th class="border-bottom-0">Name</th>
                        <th class="border-bottom-0">Email</th>
                        <th class="border-bottom-0">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Add Counsellor Modal -->
<div class="modal fade" id="addCounsellorModal" tabindex="-1" aria-labelledby="addCounsellorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ url('/manage-admin/add-counsellor') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addCounsellorModalLabel">Add New Counsellor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" cols="50" placeholder="Enter your description here..." required></textarea>

                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="tags">Enter Specialization:</label>
                        <input type="text" id="tagsInput" class="form-control" placeholder="Type and press Enter" />
                        <ul id="tagsList" style="margin-top: 10px; padding: 0; list-style: none;"></ul>
                        <input type="hidden" name="tags" id="hiddenTags" />
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Communication Method </label>
                        <select class="form-select" id="communication_method" multiple name="communication_method[]" required>
                            <option value="Phone Call">Phone Call</option>
                            <option value="Video Call">Video Call</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="timezone">Select Timezone:</label>
                        <select id="timezone" name="timezone" class="form-control">
                            <option value="">Select a timezone</option>
                            @foreach ($timezones['timezones'] as $timezone)
                            <option value="{{ $timezone['name'] }}" >
                                {{ $timezone['name'] }}
                            </option>
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary mindway-btn-blue" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary mindway-btn-blue">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('js')
    @include('mw-1.admin.counsellor.datatable')
@endsection