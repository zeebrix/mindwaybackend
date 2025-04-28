<!-- Loader -->
<div id="requestSessionLoader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:9999;">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>
<div class="modal fade" id="requestSessionModal" tabindex="-1" aria-labelledby="requestSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestSessionModalLabel">Request Additional Sessions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>This request will be sent to the manager of the client's organisation.</h6>
                <h6>Individual employee details will not be disclosed.</h6>
                <h6 id="clientNameDisplay"><strong>Client Name: </strong><span id="clientNameValue"></span></h6>
                <form action="{{ route('request.session') }}" method="POST">
                    @csrf
                    <input type="hidden" name="counselor_id" value="{{ $user_id ?? '' }}">
                    <input type="hidden" name="customerId" id="requestCustomerId">
                    <input type="hidden" name="appCustomerId" id="appCustomerId">
                    <input type="hidden" name="programId" id="programIdv">

                    <div class="row">
                        <div class="col-sm-12">
                            <h3>Reason</h3>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="request_work_related" name="work_related"
                                    value="Work Related" onclick="toggleAdditionalReasonsReq()">
                                <label class="form-check-label" for="request_work_related">Work Related</label>
                            </div>
                            <div id="requestAdditionalReasons" class="additional-reasons" style="display:none;">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="request_work_stress"
                                        name="work_stress" value="Work Stress">
                                    <label class="form-check-label" for="request_work_stress">Work Stress</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="request_workplace_conflicts"
                                        name="workplace_conflicts" value="Workplace Conflicts">
                                    <label class="form-check-label" for="request_workplace_conflicts">Workplace Conflicts</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="request_harassment_bullying"
                                        name="harassment_bullying" value="Harassment/Bullying">
                                    <label class="form-check-label" for="request_harassment_bullying">Harassment/Bullying</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="request_performance_issues"
                                        name="performance_issues" value="Performance Issues">
                                    <label class="form-check-label" for="request_performance_issues">Performance Issues</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="request_organisational_change"
                                        name="organisational_change" value="Organisational Change">
                                    <label class="form-check-label" for="request_organisational_change">Organisational Change</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="request_burnout" name="burnout"
                                        value="Burnout">
                                    <label class="form-check-label" for="request_burnout">Burnout</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="request_other" name="other"
                                        value="Other" onclick="toggleOtherInputReq()">
                                    <label class="form-check-label" for="request_other">Other</label>
                                </div>
                                <input type="text" id="request_other_reason" name="other_reason"
                                    placeholder="Please specify" class="form-control mt-2" style="display:none;">
                            </div>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="request_person_related"
                                    name="person_related" value="Person Related">
                                <label class="form-check-label" for="request_person_related">Person Related</label>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6><strong>Recomended Request: </strong> How many additional sessions ?</h6>

                    <div class="d-flex flex-wrap justify-content-start mt-4" style="gap: 10px;">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="request_session_count" 
                                   id="session-count-1" value="1" autocomplete="off" checked>
                            <label class="btn btn-outline-primary rounded-pill px-4" 
                                   for="session-count-1">1</label>
                        </div>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="request_session_count" 
                                   id="session-count-2" value="2" autocomplete="off">
                            <label class="btn btn-outline-primary rounded-pill px-4" 
                                   for="session-count-2">2</label>
                        </div>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="request_session_count" 
                                   id="session-count-3" value="3" autocomplete="off">
                            <label class="btn btn-outline-primary rounded-pill px-4" 
                                   for="session-count-3">3</label>
                        </div>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="request_session_count" 
                                   id="session-count-4" value="4" autocomplete="off">
                            <label class="btn btn-outline-primary rounded-pill px-4" 
                                   for="session-count-4">4</label>
                        </div>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="request_session_count" 
                                   id="session-count-5" value="5" autocomplete="off">
                            <label class="btn btn-outline-primary rounded-pill px-4" 
                                   for="session-count-5">5</label>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary w-100">Send Request</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to handle request session buttons
    function handleRequestSessionButtons(buttons) {
        buttons.each(function() {
            var $button = $(this);
            
            // Initialize tooltip
            $button.tooltip({
                trigger: 'hover',
                placement: 'top'
            });

            // Handle click event
            $button.off('click').on('click', function() {
                var customerId = $button.data('id');
                var programId = $button.data('program_id');
                var appCustomerId = $button.data('app_customer_id');
                var customerName = $button.data('customer_name');

                // Set values in modal
                $('#requestCustomerId').val(customerId); // breve cudtomer id
                $('#programIdv').val(programId); // program id 
                $('#appCustomerId').val(appCustomerId); // app customer id
                $('#clientNameValue').text(customerName);
            });
        });
    }

    // Initialize MutationObserver
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes) {
                $(mutation.addedNodes).each(function() {
                    var $node = $(this);
                    var buttons = $node.find('.request-session-btn');
                    if ($node.hasClass('request-session-btn')) {
                        buttons = buttons.add($node);
                    }
                    if (buttons.length) {
                        handleRequestSessionButtons(buttons);
                    }
                });
            }
        });
    });

    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Initialize existing buttons on page load
    $(document).ready(function() {
        handleRequestSessionButtons($('.request-session-btn'));
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

    // Toggle additional reasons
    function toggleAdditionalReasonsReq() {
        var $workRelated = $('#request_work_related');
        var $additionalReasons = $('#requestAdditionalReasons');
        
        if ($workRelated.prop('checked')) {
            $additionalReasons.show();
        } else {
            $additionalReasons.find('input[type="checkbox"]').prop('checked', false);
            $('#request_other_reason').hide().val('');
            $additionalReasons.hide();
        }
    }

    // Toggle other reason input
    function toggleOtherInputReq() {
        var $other = $('#request_other');
        var $otherReason = $('#request_other_reason');
        
        if ($other.prop('checked')) {
            $otherReason.show();
        } else {
            $otherReason.hide().val('');
        }
    }
</script>