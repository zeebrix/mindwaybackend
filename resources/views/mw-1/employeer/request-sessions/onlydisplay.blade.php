
<div class="modal fade" id="requestedModal" tabindex="-1" aria-labelledby="requestedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <!-- <h5 id="clientRequestDisplay"><strong>Request ID #R</strong><span id="clientRequestValue"></span></h5> -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

            <h4 id="clientRequestDisplay"><strong>Session Request #R</strong><span id="clientRequestValue"></span></h4>
            <h6 id="approvedDate"><strong>Approved Date : </strong><span id="approvedDateValue"></span></h6>
            <h6 id="deniedDate"><strong>Denied Date : </strong><span id="deniedDateValue"></span></h6>
            <hr>
                <!-- <h6 id="clientNameDisplay"><strong>Client Name: </strong><span id="requestedNameValue"></span></h6>
                <h6 id="clientEmailDisplay"><strong>Client Email: </strong><span id="requestedEmailValue"></span></h6>
                <h6 id="clientIDDisplay"><strong>Client ID: </strong><span id="requestedIDValue"></span></h6>
                <h6 id="CounsellorName"><strong>Counsellor: </strong><span id="CounsellorNameValue"></span></h6> -->
                <h6 id="reasons"><strong>Reason (s) for the sessions: </strong><span id="reasonsValue"></span></h6>
                <h6 id="addDays"><strong>Additional Sessions Requested: </strong><span id="addDaysValue"></span></h6>
                

              
                    <hr>
                    <div id="hideit">
                    <h6><strong>How many additional sessions would you like to approve ?</strong></h6>
                    <p><span id="addDaysValue1"></span> have been recommended based on the employee requirements</p>    
                   
                    
                    <div class="d-flex flex-wrap justify-content-start mt-4" style="gap: 10px;">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="request_session_count" 
                                   id="session-count-1" value="1" autocomplete="off" disabled>
                            <label class="btn btn-outline-primary rounded-pill px-4" 
                                   for="session-count-1">1</label>
                        </div>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="request_session_count" 
                                   id="session-count-2" value="2" autocomplete="off" disabled>
                            <label class="btn btn-outline-primary rounded-pill px-4" 
                                   for="session-count-2">2</label>
                        </div>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="request_session_count" 
                                   id="session-count-3" value="3" autocomplete="off" disabled>
                            <label class="btn btn-outline-primary rounded-pill px-4" 
                                   for="session-count-3">3</label>
                        </div>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="request_session_count" 
                                   id="session-count-4" value="4" autocomplete="off" disabled>
                            <label class="btn btn-outline-primary rounded-pill px-4" 
                                   for="session-count-4">4</label>
                        </div>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="request_session_count" 
                                   id="session-count-5" value="5" autocomplete="off" disabled>
                            <label class="btn btn-outline-primary rounded-pill px-4" 
                                   for="session-count-5">5</label>
                        </div>
                    </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-sm-12">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-primary w-100">Close</button>
                            
                        <!-- <button type="submit" class="btn btn-primary w-100">Approve Sessions</button> -->
                        </div>
                    </div>
                           </div>
        </div>
    </div>
</div>