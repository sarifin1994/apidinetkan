<!-- Modal Show -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Create License</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="license_id">
                <div class="form-group mb-3">
                    <label for="nama_lisensi_edit" class="mb-1">Nama License <small
                            class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="nama_lisensi_edit" name="nama_lisensi_edit"
                        placeholder="" autocomplete="off" required>
                </div>
                <div class="form-group mb-3">
                    <label for="deskripsi_edit" class="mb-1">Deskripsi <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="deskripsi_edit" name="deskripsi_edit" placeholder=""
                        autocomplete="off" required>
                </div>
                <div class="form-group mb-3">
                    <label for="spek_edit" class="mb-1">Spesifikasi <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="spek_edit" name="spek_edit" placeholder=""
                        autocomplete="off" required>
                </div>
                <!-- Harga Lisensi -->
                <div class="form-group mb-3">
                    <label for="price_edit" class="mb-1">Harga <small class="text-danger">*</small></label>
                    <input type="number" class="form-control" id="price_edit" name="price_edit"
                        placeholder="" required>
                </div>

                <!-- Limit Hotspot -->
                <div class="form-group mb-3">
                    <label for="limit_hs_edit" class="mb-1">Limit Hotspot <small class="text-danger">*</small></label>
                    <input type="number" class="form-control" id="limit_hs_edit" name="limit_hs_edit" placeholder=""
                        required>
                </div>

                <!-- Limit PPPoE -->
                <div class="form-group mb-3">
                    <label for="limit_pppoe_edit" class="mb-1">Limit PPPoE <small
                            class="text-danger">*</small></label>
                    <input type="number" class="form-control" id="limit_pppoe_edit" name="limit_pppoe_edit"
                        placeholder="" required>
                </div>
                <div class="form-group mb-3">
                    <label for="midtrans_edit" class="mb-1">Akses Payment Gateway <small
                            class="text-danger">*</small></label>
                    <select class="form-select" id="midtrans_edit" name="midtrans_edit" autocomplete="off" required>
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="olt_edit" class="mb-1">Akses OLT <small class="text-danger">*</small></label>
                    <select class="form-select" id="olt_edit" name="olt_edit" autocomplete="off" required>
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="custome_edit" class="mb-1">Custome ? <small class="text-danger">*</small></label>
                    <select class="form-select" id="custome_edit" name="custome_edit" autocomplete="off" required>
                    <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary text-white" id="update" type="submit">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
