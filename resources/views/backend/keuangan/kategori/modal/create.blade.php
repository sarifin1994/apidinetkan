<!-- Modal Show -->
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Create Kategori</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="category" class="mb-1">Nama Kategori <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="category" name="category" placeholder="" autocomplete="off" required>
                </div>
                <div class="form-group mb-3">
                    <label for="type" class="mb-1">Tipe <small class="text-danger">*</small></label>
                    <select class="form-select" id="type">
                        <option value="Pemasukan">Pemasukan</option>
                        <option value="Pengeluaran">Pengeluaran</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="store" type="submit">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
