<!-- Modal Show -->
<div class="modal fade" id="show_session" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">PPPoE Session</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row px-3">
                    <table id="sessionTable" style="font-size:12px" class="table table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>Start</th>
                                <th>Stop</th>
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
                {{-- <button class="btn btn-secondary" type="submit" id="refresh">
                    Refresh
                </button> --}}
                <button class="btn btn-primary" type="button" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
