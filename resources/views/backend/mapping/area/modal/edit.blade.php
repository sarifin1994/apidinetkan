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
                    <input type="hidden" id="area_id">
                    <div class="form-group mb-3">
                        <label for="kode_area_edit" class="mb-1">POP</label>
                        <input type="text" class="form-control"
                            id="kode_area_edit" name="kode_area"
                            autocomplete="off" disabled>
                    </div>
                    <div class="form-group mb-3">
                        <label for="deskripsi_edit" class="mb-1">Deskripsi</label>
                        <input type="text" class="form-control"
                            id="deskripsi_edit" name="deskripsi" autocomplete="off" required>
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