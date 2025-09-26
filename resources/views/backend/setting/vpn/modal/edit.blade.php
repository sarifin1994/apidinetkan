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
                    <input type="hidden" id="vpn_id">
                    <div class="form-group mb-3">
                        <label for="lokasi_edit" class="mb-1">Lokasi <small class="text-danger">*</small></label>
                        <select class="form-select" id="lokasi_edit" name="lokasi_edit" autocomplete="off" disabled>
                            <option value="">- Pilih Lokasi Server -</option>
                            <option value="indonesia">Indonesia</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="name_edit" class="mb-1">Name <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="name_edit" name="name_edit" placeholder=""
                            autocomplete="off" required disabled>
                    </div>
                    <div class="form-group mb-3">
                        <label for="host_edit" class="mb-1">Host <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="host_edit" name="host_edit" placeholder=""
                            autocomplete="off" required disabled>
                    </div>
    
                    <div class="form-group mb-3">
                        <label for="user_edit" class="mb-1">User <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="user_edit" name="user_edit" placeholder=""
                            autocomplete="off" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password_edit" class="mb-1">Password <small class="text-danger">*</small></label>
                        <input type="password" class="form-control" id="password_edit" name="password_edit"
                            placeholder="Kosongkan jika tidak ingin mengubah password" autocomplete="off" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="port_edit" class="mb-1">Port API <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="port_edit" name="port_edit" placeholder=""
                            autocomplete="off" required>
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