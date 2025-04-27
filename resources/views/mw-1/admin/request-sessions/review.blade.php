<!-- Loader -->
<div id="requestSessionLoader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:9999;">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<div class="modal fade" id="requestedModal" tabindex="-1" aria-labelledby="requestedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="clientRequestDisplay"><strong>Request ID #R</strong><span id="clientRequestValue"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 id="clientNameDisplay"><strong>Client Name: </strong><span id="requestedNameValue"></span></h6>
                <h6 id="clientEmailDisplay"><strong>Client Email: </strong><span id="requestedEmailValue"></span></h6>
                <h6 id="clientIDDisplay"><strong>Client ID: </strong><span id="requestedIDValue"></span></h6>
                <h6 id="CounsellorName"><strong>Counsellor: </strong><span id="CounsellorNameValue"></span></h6>
                <h6 id="reasons"><strong>Reason (s) for the sessions: </strong><span id="reasonsValue"></span></h6>
                <h6 id="requestedDate"><strong>Date Requested: </strong><span id="requestedDateValue"></span></h6>
              
                <!-- Approve Sessions Form -->
                <form action="{{ route('admin.approve.session') }}" method="POST" class="mb-3">
                    @csrf
                    <input type="hidden" name="requestedId" id="requestedId">

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
                            <button type="submit" class="btn btn-primary w-100">Approve Sessions</button>
                        </div>
                    </div>
                </form>

                <!-- Deny Sessions Form -->
                <form action="{{ route('admin.deny.session') }}" method="POST">
                    @csrf
                    <input type="hidden" name="requestedId" id="requestedIdDeny">
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-danger w-100">Deny Sessions</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
