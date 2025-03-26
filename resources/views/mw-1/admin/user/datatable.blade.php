<script>
        $(document).ready(function() {
            $('#Yajra-dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.users-data") }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'goal_id', name: 'goal_id' },
                    { data: 'improve', name: 'improve' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
            });
        });
    </script>