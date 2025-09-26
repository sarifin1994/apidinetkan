<!-- Modal Show -->
<div class="modal fade" id="edit_komisi" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Edit Mitra</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <input type="hidden" id="mitra_id">
                    <label for="nama_mitra_edit_komisi" class="mb-1">Nama Mitra <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="nama_mitra_edit_komisi" name="nama_mitra_edit_komisi" placeholder=""
                        autocomplete="off" disabled>
                    <small id="helper" class="form-text text-muted">Isi nama mitra, misalnya ACENG</small>
                </div>
                <div class="form-group mb-3">
                    <label for="balance_mitra_edit_komisi" class="mb-1">Saldo Nama Mitra <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="balance_mitra_edit_komisi" name="balance_mitra_edit_komisi" placeholder="" autocomplete="off" disabled>
                </div>
                <div class="form-group mb-3">
                    <label for="jenis" class="mb-1">Jenis <small class="text-danger">*</small></label>
                    <select class="form-select" id="jenis" name="jenis" autocomplete="off" required>
                        <option value="tambah" selected>Menambahkan</option>
                        <option value="kurang">Mengurangi</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="id_invoice" class="mb-1">No Invoice <small class="text-danger">*</small></label>
                    <select class="form-select" id="id_invoice" name="id_invoice" autocomplete="off" required>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="nominal_mitra_edit_komisi" class="mb-1">Nominal Saldo <small class="text-danger">*</small></label>
                    <input type="number" class="form-control" id="nominal_mitra_edit_komisi" name="nominal_mitra_edit_komisi" placeholder="" autocomplete="off" required>
                </div>
                <div class="form-group mb-3">
                    <label for="notes" class="mb-1">Notes <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="notes" name="notes" placeholder="" autocomplete="off" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary text-white" id="update_komisi" type="submit">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
