@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
    <div class="card w-100">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title fw-semibold">Courses</h5>
                <div>
                    <a href="{{ url('/manage-admin/add-course') }}" class="btn btn-primary">Add New</a>
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
                <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark fs-4">
                        <tr>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Sr. No</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Id</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Course title</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Course description</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Course thumbnail</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Course duration</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Created at</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Actions</h6>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($count = 0)
                        @foreach ($viewCourse as $data)
                            @php($count++)
                            <tr>
                                <td class="border-bottom-0">
                                    <h6 class="fw-normal mb-0">{{ $count }}</h6>
                                </td>
                                <td class="border-bottom-0">
                                    <h6 class="fw-semibold mb-1">{{ $data->id }}</h6>
                                </td>
                                <td class="border-bottom-0">
                                    <p class="mb-0 fw-normal">{{ $data->course_title }}</p>
                                </td>
                                <td class="border-bottom-0">
                                    <p class="mb-0 fw-normal">{{ \Illuminate\Support\Str::words($data->course_description, 5, '...') }}</p>
                                </td>
                                <td class="border-bottom-0">
                                    <img height="50px" width="50px" class="popup"
                                        src="{{ asset('storage/course') }}/{{ $data->course_thumbnail }}"
                                        alt="">
                                </td>
                                <td class="border-bottom-0">
                                    <p class="mb-0 fw-normal">{{ $data->course_duration }}</p>
                                </td>
                                <td class="border-bottom-0">
                                    <p class="mb-0 fw-normal">{{ $data->created_at }}</p>
                                </td>
                                <td class="border-bottom-0">
                                    <a href="{{ url('/manage-admin/edit-course', ['id' => $data->id]) }}"
                                        class="btn btn-success btn-sm btn-icon-text mr-3">
                                        Edit
                                        <i class="typcn typcn-edit btn-icon-append"></i>
                                    </a>
                                    <a href="{{ url('/manage-admin/delete-course', ['id' => $data->id]) }}"
                                        class="btn btn-danger btn-sm btn-icon-text">
                                        Delete
                                        <i class="typcn typcn-delete-outline btn-icon-append"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
