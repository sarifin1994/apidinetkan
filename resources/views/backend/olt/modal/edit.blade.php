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
                    <input type="hidden" id="olt_id">
                    <div class="form-group mb-3">
                        <label for="name_edit" class="mb-1">Nama <small class="text-danger">*</small></label>
                        <input type="text" class="form-control"
                            id="name_edit" name="name_edit"
                            placeholder="name_edit" autocomplete="off" required>

                    </div>
                    <div class="form-group mb-3">
                        <label for="type_edit" class="mb-1">Tipe <small class="text-danger">*</small></label>
                        <select class="form-select" id="type_edit" name="type_edit" autocomplete="off" required>
                            <option value="HSGQ EPON">HSGQ EPON</option>
                            <option value="HSGQ GPON">HSGQ GPON</option>
                            <option value="HIOSO 2 PON">HIOSO 2 PON</option>
                            <option value="HIOSO 4 PON">HIOSO 4 PON</option>
                            <option value="c300" data-generation="C300" disabled>ZTE-C300</option>
                            <option value="c200" data-generation="C300" disabled>ZTE-C320</option>
                        </select>
                        {{-- <small id="helper" class="form-text text-muted"><i>Untuk tipe OLT ZTE masih dalam tahap development, alternatif bisa gunakan smartolt.com</i></small> --}}
    
                    </div>
                    <div class="form-group mb-3">
                        <label for="ip_edit" class="mb-1">IP Address <small class="text-danger">*</small></label>
                        <input type="text" class="form-control"
                            id="ip_edit" name="ip_edit" placeholder="ip_edit"
                            autocomplete="off">
                            <small id="helper" class="form-text text-muted">Masukan ip olt yang bisa diakses dari luar jaringan, contoh <small class="text-danger">id1.hostddns.us:8127</small></small>
                        </div>
                    <div class="form-group mb-3">
                        <label for="username_edit" class="mb-1">Username <small class="text-danger">*</small></label>
                        <input type="text" class="form-control"
                            id="username_edit" name="username_edit" placeholder="username_edit"
                            autocomplete="off">
                    </div>
                    <div class="form-group mb-3">
                        <label for="password_edit" class="mb-1">Password <small class="text-danger">*</small></label>
                        <input type="password" class="form-control"
                            id="password_edit" name="password_edit" placeholder="password_edit"
                            autocomplete="off">
                    </div>
                    <hr>
                    <span id="helper" class="text-muted">Jika kesulitan dalam menambahkan olt, silakan hubungi kami 😉</span>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-link" type="button" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button class="btn btn-primary" id="update" type="submit">
                        Save changes
                    </button>
                </div>
        </div>
    </div>
</div>