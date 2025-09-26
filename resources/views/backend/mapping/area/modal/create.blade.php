<!-- Modal Show -->
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Create</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="kode_area" class="mb-1">POP <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="kode_area" name="kode_area" placeholder="" autocomplete="off" required>
                    <small id="helper" class="form-text text-muted">Isi singkatan, misalnya RCB</small>
                </div>
                <div class="form-group mb-3">
                    <label for="deskripsi" class="mb-1">Deskripsi <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="deskripsi" name="deskripsi" placeholder="" autocomplete="off" required>
                    <small id="helper" class="form-text text-muted">Isi deskripsi POP, misalnya RANCABANGO</small>
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
