<div class="modal fade" id="addSessionModal" tabindex="-1" aria-labelledby="addSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSessionModalLabel">Add Counselling Session for Company Name Here</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('sessions.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sessionDate" class="form-label">Date</label>
                                <input type="date" class="form-control" name="sessionDate" placeholder="Enter date">
                                <input type="hidden" name="programId" value="{{ $Program->id }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sessionType" class="form-label">Session Type</label>
                                <input type="text" class="form-control" name="sessionType" placeholder="Enter session type">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <h3>Reason</h3>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="work_related" name="work_related" value="Work Related" onclick="toggleAdditionalReasons()">
                                <label class="form-check-label" for="work_related">Work Related</label>
                            </div>
                            <div id="additionalReasons" class="additional-reasons" style="display:none;">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="work_stress" name="work_stress" value="Work Stress">
                                    <label class="form-check-label" for="work_stress">Work Stress</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="workplace_conflicts" name="workplace_conflicts" value="Workplace Conflicts">
                                    <label class="form-check-label" for="workplace_conflicts">Workplace Conflicts</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="harassment_bullying" name="harassment_bullying" value="Harassment/Bullying">
                                    <label class="form-check-label" for="harassment_bullying">Harassment/Bullying</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="performance_issues" name="performance_issues" value="Performance Issues">
                                    <label class="form-check-label" for="performance_issues">Performance Issues</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="organisational_change" name="organisational_change" value="Organisational Change">
                                    <label class="form-check-label" for="organisational_change">Organisational Change</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="burnout" name="burnout" value="Burnout">
                                    <label class="form-check-label" for="burnout">Burnout</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="other" name="other" value="Other" onclick="toggleOtherInput()">
                                    <label class="form-check-label" for="other">Other</label>
                                </div>
                                <input type="text" id="other_reason" name="other_reason" placeholder="Please specify" class="form-control mt-2" style="display:none;">
                            </div>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="person_related" name="person_related" value="Person Related">
                                <label class="form-check-label" for="person_related">Person Related</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <h3>New User</h3>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="radio" id="new_user_yes" name="new_user" value="Yes">
                                <label class="form-check-label" for="new_user_yes">Yes</label>
                            </div>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="radio" id="new_user_no" name="new_user" value="No">
                                <label class="form-check-label" for="new_user_no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-primary">ADD SESSION FOR COMPANY NAME</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
