<script>
    $(document).ready(function() {
        $('#Yajra-dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.viewemoji-data") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'emoji', name: 'emoji', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
        });
    });
</script>