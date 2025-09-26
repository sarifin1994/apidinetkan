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
                    <label for="lokasi" class="mb-1">Lokasi <small class="text-danger">*</small></label>
                    <select class="form-select" id="lokasi" name="lokasi" autocomplete="off" required>
                        <option value="">- Pilih Lokasi Server -</option>
                        <option value="indonesia">Indonesia</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="name" class="mb-1">Name <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder=""
                        autocomplete="off" required>
                </div>
                <div class="form-group mb-3">
                    <label for="host" class="mb-1">Host <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="host" name="host" placeholder=""
                        autocomplete="off" required>
                </div>

                <div class="form-group mb-3">
                    <label for="user" class="mb-1">User <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="user" name="user" placeholder=""
                        autocomplete="off" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password" class="mb-1">Password <small class="text-danger">*</small></label>
                    <input type="password" class="form-control" id="password" name="password" placeholder=""
                        autocomplete="off" required>
                </div>
                <div class="form-group mb-3">
                    <label for="port" class="mb-1">Port API <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="port" name="port" placeholder=""
                        autocomplete="off" required>
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
