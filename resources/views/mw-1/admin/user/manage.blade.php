@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
    <div class="card w-100">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title fw-semibold">Users</h5>
                <div class="col-4">
                    <input type="text" id="searchInput" placeholder="Search by Name or Email" class="form-control"
                        onkeyup="filterTable()" />
                </div>
            </div>
            <div class="table-responsive">
                <table class="table text-nowrap mb-0 align-middle" id="userTable">
                    <thead class="text-dark fs-4">
                        <tr>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Sr. No</h6>
                            </th>
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
                    <tbody>
                        @php($count = 0)
                        @foreach ($getCustomer as $data)
                            @php($count++)
                            <tr>
                                <td class="border-bottom-0">
                                    <h6 class="fw-normal mb-0">{{ $count }}</h6>
                                </td>
                                <td class="border-bottom-0">
                                    <h6 class="fw-semibold mb-1">{{ $data->id }}</h6>
                                </td>
                                <td class="border-bottom-0">
                                    <p class="mb-0 fw-normal">{{ $data->name }}</p>
                                </td>
                                <td class="border-bottom-0">
                                    <p class="mb-0 fw-normal">{{ $data->email }}</p>
                                </td>
                                <td class="border-bottom-0">
                                    <p class="mb-0 fw-normal">{{ $data->goal_id }}</p>
                                </td>
                                <td class="border-bottom-0">
                                    @if ($data->improve == null)
                                        <p class="mb-0 fw-normal">Not selected</p>
                                    @else
                                        <p class="mb-0 fw-normal">{{ $data->improve }}</p>
                                    @endif
                                </td>
                                <td class="border-bottom-0">
                                    <p class="mb-0 fw-normal">{{ $data->status }}</p>
                                </td>
                                <td class="border-bottom-0">
                                    <p class="mb-0 fw-normal">{{ $data->created_at }}</p>
                                </td>
                                <td class="border-bottom-0">
                                    <a class="btn btn-danger btn-sm btn-icon-text"
                                        href="{{ url('/manage-admin/delete-customer', ['id' => $data->id]) }}">
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

    <script>
        function filterTable() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const table = document.getElementById('userTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const nameCell = rows[i].getElementsByTagName('td')[2];
                const emailCell = rows[i].getElementsByTagName('td')[3];

                if (nameCell || emailCell) {
                    const nameText = nameCell.textContent || nameCell.innerText;
                    const emailText = emailCell.textContent || emailCell.innerText;

                    if (nameText.toLowerCase().includes(searchInput) || emailText.toLowerCase().includes(searchInput)) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        }
    </script>
@endsection
