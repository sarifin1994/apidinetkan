<!-- Modal Show -->
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Create Admin</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="nama_admin" class="mb-1">Nama Admin <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="nama_admin" name="nama_admin" placeholder=""
                        autocomplete="off" required>
                    <small id="helper" class="form-text text-muted">Isi nama Admin, misalnya ACENG</small>
                </div>
                <div class="form-group mb-3">
                    <label for="username_admin" class="mb-1">Username <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="username_admin" name="username_admin" placeholder="AKAN DIGENERATE SYSTEM"
                        autocomplete="off" disabled>
                    <!-- <small id="helper" class="form-text text-muted">Isi Username</small> -->
                </div>
                <div class="form-group mb-3">
                    <label for="pass_admin" class="mb-1">Password Admin <small class="text-danger">*</small></label>
                    <input type="password" class="form-control" id="pass_admin" name="pass_admin" placeholder=""
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
