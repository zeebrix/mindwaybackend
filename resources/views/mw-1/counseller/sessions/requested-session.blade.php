<div class="modal fade" id="requestedModal" tabindex="-1" aria-labelledby="requestedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestedModalLabel">This Request is Still Pending</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 id="clientNameDisplay"><strong>Client Name: </strong><span id="requestedNameValue"></span></h6>
                <h6 id="clientRequestDisplay"><strong>Request ID: </strong><span id="clientRequestValue"></span></h6>
                <h6>We will notify you by email once the employer has made a decision</h6>
                <div class="row mt-4">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-primary w-100">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Use event delegation for dynamically loaded elements
        $(document).on('click', '.requested-session-btn', function() {
            var customerName = $(this).data('customer_name');
            var requestedId = $(this).data('requestedid');

            $('#requestedNameValue').text(customerName);
            $('#clientRequestValue').text(requestedId);
        });
    });
</script>