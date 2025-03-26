<script>
    $(document).ready(function() {
        $('#Yajra-dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.viewcourse-data") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'course_title', name: 'course_title' },
                { data: 'course_description', name: 'course_description' },
                { data: 'course_thumbnail', name: 'course_thumbnail', orderable: false, searchable: false },
                { data: 'course_duration', name: 'course_duration' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
        });
    });
</script>