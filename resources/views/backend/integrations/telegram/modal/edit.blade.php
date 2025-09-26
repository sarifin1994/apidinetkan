<!-- Modal Show -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">EDIT GRUP TELEGRAM</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <input type="hidden" id="telegram_id">
                                <input type="hidden" id="tipe_id">
                                <label for="tipe" class="mb-1">Tipe Grup</label>
                                    <input disabled type="text" class="form-control" id="tipe" name="tipe"
                                        autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="chatid" class="mb-1">Chat ID</label>
                                <input type="text" class="form-control" id="chatid" name="chatid" autocomplete="off">
                            </div>
                        </div>
                        
                    </div>
                    

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-primary" id="update" type="submit">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
