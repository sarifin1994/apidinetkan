<!-- Modal Show -->
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Tambah Area</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <input type="text" class="form-control" id="deskripsi" name="deskripsi" placeholder="deskripsi" autocomplete="off" required>
                    <label for="deskripsi">Nama Area</label>
                </div>
                <div class="form-group mb-3">
                    <input type="text" class="form-control" id="kode_area" name="kode_area" placeholder="kode_area" autocomplete="off" required>
                    <label for="kode_area">Kode Area</label>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                    Batal
                </button>
                <button class="btn btn-success" id="store" type="submit">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
