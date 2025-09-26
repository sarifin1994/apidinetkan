<!-- Modal Show -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="modal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Edit</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="wa_id">
                    <div class="form-group mb-3">
                        <label for="wa_url_edit" class="mb-1">URL WA Server <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="wa_url_edit" name="wa_url_edit" placeholder=""
                            autocomplete="off" disabled>
                    </div>
                    <div class="form-group mb-3">
                        <label for="wa_api_edit" class="mb-1">API Key <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="wa_api_edit" name="wa_api_edit" placeholder=""
                            autocomplete="off" required>
                    </div>
    
                    <div class="form-group mb-3">
                        <label for="wa_sender_edit" class="mb-1">Sender <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="wa_sender_edit" name="wa_sender_edit" placeholder=""
                            autocomplete="off" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="wa_server_edit" class="mb-1">Server <small class="text-danger">*</small></label>
                        <select class="form-select" name="wa_server_edit" id="wa_server_edit">
                            <option vlaue="mpwa">MPWA</option>
                            <option vlaue="radiusqu">Radiusqu</option>
                        </select>
                    </div>
    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary text-white" id="update" type="submit">
                        Save changes
                    </button>
                </div>
        </div>
    </div>
</div>