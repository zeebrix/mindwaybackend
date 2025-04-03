<script>
    $(document).ready(function() {
        $('#Yajra-dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.viewsleepscreen-data") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'audio_title', name: 'audio_title' },
                { data: 'duration', name: 'duration' },
                { data: 'total_play', name: 'total_play' },
                { data: 'sleep_audio', name: 'sleep_audio', orderable: false, searchable: false },
                { data: 'image', name: 'image', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
        });
    });
</script>