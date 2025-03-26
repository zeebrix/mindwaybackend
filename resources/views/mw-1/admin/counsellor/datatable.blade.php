
<script>
        $(document).ready(function() {
            $('#Yajra-dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.counsellor-data") }}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
            });
        });
    </script>