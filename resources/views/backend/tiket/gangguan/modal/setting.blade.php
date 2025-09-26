<!-- Modal Show -->
<div class="modal fade" id="settings" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Pilih Group Whatsapp</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group mb-3">
                            <label for="group_select" class="mb-1">Group Gangguan</label>
                            <select id="group_select" class="form-select">
                                <option value="">-- Pilih Group --</option>
                            </select>
                        </div>
                        <small>Notifikasi whatsapp terkait tiket gangguan (saat tiket dibuat/diclose) akan dikirimkan ke grup yang dipilih</small>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button class="btn btn-link" type="button" data-bs-dismiss="modal">
                    Close
                </button>
                <button class="btn btn-primary" id="save" type="submit">
                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>
