<script>
    $(document).ready(function() {
        $('#Yajra-dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.viewsinglecourse-data") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title' },
                { data: 'subtitle', name: 'subtitle' },
                { data: 'duration', name: 'duration' },
                { data: 'total_play', name: 'total_play' },
                { data: 'image', name: 'image', orderable: false, searchable: false },
                { data: 'single_audio', name: 'single_audio', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
        });
    });
</script>