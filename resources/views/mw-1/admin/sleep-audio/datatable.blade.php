<script>
    $(document).ready(function() {
        $('#Yajra-dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.viewsleepaudio-data") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'course_id', name: 'course_id' },
                { data: 'duration', name: 'duration' },
                { data: 'total_play', name: 'total_play' },
                { data: 'audio', name: 'audio', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
        });
    });
</script>