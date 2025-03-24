<script>
    $(document).ready(function() {
        let programId = "{{ $Program->id }}"; // Get program ID from Laravel request

        // Initialize DataTable
        var table = $('#Yajra-dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.programs-employees-data") }}',
                data: function(d) {
                    d.programId = programId; // Pass programId parameter to the server
                }
            },
            columns: [
                { data: 'name_email', name: 'name_email' },
                { data: 'level', name: 'level' },
                { data: 'max_session', name: 'max_session' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                { targets: "_all", className: "text-left" } // Optional: Align text properly
            ]
        });

    });
</script>

<style>
    /* Hide the table headers */
    #Yajra-dataTable thead {
        display: none;
    }

    /* Hide the length dropdown */
    #Yajra-dataTable_length {
        display: none;
    }

    /* Style for the level badges */
    .badge {
        cursor: pointer; /* Make the badge clickable */
    }

    .member-style {
        color: #72DC68 !important;
        background-color: #F5FFF6 !important;
    }

    .admin-style {
        color: #DCA268 !important;
        background-color: #FFFCF5 !important;
    }
</style>