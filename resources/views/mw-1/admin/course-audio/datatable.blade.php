<script>
    $(document).ready(function() {
        $('#Yajra-dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.viewaudio-data") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'session_id', name: 'session_id' },
                { data: 'duration', name: 'duration' },
                { data: 'total_play', name: 'total_play' },
                { data: 'audio_title', name: 'audio_title' },
                { data: 'audio', name: 'audio', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
        });
    });
</script>