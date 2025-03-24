<script>
    $(document).ready(function() {
        $('#Yajra-dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.viewsleepcourse-data") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'category_id', name: 'category_id' },
                { data: 'title', name: 'title' },
                { data: 'description', name: 'description' },
                { data: 'thumbnail', name: 'thumbnail', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
        });
    });
</script>