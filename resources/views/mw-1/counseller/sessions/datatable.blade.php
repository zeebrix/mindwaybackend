<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#Yajra-dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.counsellersesions-data") }}',
            },
            columns: [
                // { data: 'count', name: 'count', orderable: false, searchable: false }, // Row number
                { data: 'name_email', name: 'name_email', orderable: false, searchable: false }, // Name and Email
                { data: 'company_name', name: 'company_name', orderable: false, searchable: false }, // Company Name
                { data: 'max_session', name: 'max_session', orderable: false, searchable: false }, // Max Session
                { data: 'action', name: 'action', orderable: false, searchable: false } // Action (Log button)
            ],
            columnDefs: [
                { targets: "_all", className: "text-left" } // Optional: Align text properly
            ],
            dom: 'lrtip' // Remove the search bar (filter input)
        });

        // Custom search input handling
        $('#searchInput').on('input', function() {
            var searchText = $(this).val().toLowerCase();
            table.search(searchText).draw(); // Trigger DataTable search
        });
    });
</script>


<style>
    /* Hide the table headers */
    /* #Yajra-dataTable thead {
        display: none;
    } */

    /* Hide the length dropdown */
    #Yajra-dataTable_length {
        display: none;
    }

    /* Hide the search bar */
    .dataTables_filter {
        display: none;
    }
</style>

