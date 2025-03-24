@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
    <div class="card w-100">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title fw-semibold">View Music</h5>
                <div>
                    <a href="{{ url('/manage-admin/add-music') }}" class="btn btn-primary">Add New</a>
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
                                <h6 class="fw-semibold mb-0">Id</h6>
                            </th>

                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Title</h6>
                            </th>


                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Subtitle</h6>
                            </th>

                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Duration</h6>
                            </th>


                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Total Play</h6>
                            </th>

                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Image</h6>
                            </th>


                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Music audio</h6>
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
@endsection


@section('js')
    @include('mw-1.admin.music.datatable')
@endsection
