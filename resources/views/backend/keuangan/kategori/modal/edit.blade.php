<!-- Modal Show -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Edit Kategori</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <input type="hidden" id="kategori_id">
                    <label for="category_edit" class="mb-1">Nama Kategori <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="category_edit" name="category_edit" placeholder="" autocomplete="off" required>
                </div>
                <div class="form-group mb-3">
                    <label for="type_edit" class="mb-1">Tipe <small class="text-danger">*</small></label>
                    <select class="form-select" id="type_edit" disabled>
                        <option value="Pemasukan">Pemasukan</option>
                        <option value="Pengeluaran">Pengeluaran</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="update" type="submit">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
