<!-- Modal Show -->
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Tambah</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="wa_url" class="mb-1">URL WA Server <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="wa_url" name="wa_url" placeholder=""
                        autocomplete="off" required>
                </div>
                <div class="form-group mb-3">
                    <label for="wa_api" class="mb-1">API Key <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="wa_api" name="wa_api" placeholder=""
                        autocomplete="off" required>
                </div>

                <div class="form-group mb-3">
                    <label for="wa_sender" class="mb-1">Sender <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="wa_sender" name="wa_sender" placeholder=""
                        autocomplete="off" required>
                </div>

                <div class="form-group mb-3">
                    <label for="wa_server" class="mb-1">Server <small class="text-danger">*</small></label>
                    <select class="form-select" name="wa_server" id="wa_server">
                        <option vlaue="mpwa">MPWA</option>
                        <option vlaue="radiusqu">Radiusqu</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary text-white" id="store" type="submit">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
