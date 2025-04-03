<script>
    $(document).ready(function() {
        let status = "{{ request()->get('status') }}"; // Get status from Laravel request

        $('#Yajra-dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.programs-data") }}',
                data: function(d) {
                    d.status = status; // Pass status parameter to the server
                }
            },
            columns: [
                { data: 'company_name', name: 'company_name' },
                { data: 'max_lic', name: 'max_lic' },
                { data: 'max_session', name: 'max_session' },
                { data: 'code', name: 'code' },
                { data: 'renewal_date', name: 'renewal_date' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
        });
    });
</script>
