<script>
   $(document).ready(function() {
    let status = "{{ request()->get('status', 'pending') }}"; // Default to 'pending' if not set

    $('#Yajra-dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.request-session-data") }}',
            data: function(d) {
                d.status = status;
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'requested_date', name: 'requested_date' },
            { data: 'requested', name: 'requested' },
            { data: 'customre_brevo_data_id', name: 'customre_brevo_data_id' },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                className: 'text-center' 
            }
        ],
        // Optional: Add these for better UX
        responsive: true,
        pageLength: 25,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>'
        }
    });
});
</script>
