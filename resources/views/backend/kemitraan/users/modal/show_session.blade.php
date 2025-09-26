<!-- Modal Show -->
<div class="modal fade" id="show_session" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">User Session</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body table-responsive">
                    <input type="hidden" id="session_username">
                    <table id="sessionTable" style="font-size:14px" class="table table-responsive table-hover display nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>Start</th>
                                <th>Stop</th>
                                <th>Username</th>
                                <th>IP</th>
                                <th>MAC</th>
                                <th>Rx</th>
                                <th>Tx</th>
                                <th>Uptime</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-danger me-2" type="submit" id="clear_session">
                    Clear Session
                </button>
                <button class="btn btn-primary" type="button" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
