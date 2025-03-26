
<script>
        $(document).ready(function() {
            $('#Yajra-dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.counsellor-session") }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'company_name', name: 'company_name' },
                    { data: 'email', name: 'email' },
                    { data: 'counselor_name', name: 'counselor_name' },
                    { data: 'session_date', name: 'session_date' },
                    { data: 'session_type', name: 'session_type' },
                    { data: 'max_session', name: 'max_session' }
                ],
            });
        });
    </script>