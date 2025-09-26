<!-- Modal Show -->
<div class="modal fade" id="print" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Print Voucher</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <div class="form-group mb-3">
                        <label for="template" class="mb-1">Pilih Template</label>
                        <select class="form-select" id="template" name="template" autocomplete="off"
                            data-placeholder="template" required>
                            <option value="1">Default Radiusqu</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button class="btn btn-primary" id="print_voucher" type="submit">
                    Print
                </button>
            </div>
        </div>
    </div>
</div>
