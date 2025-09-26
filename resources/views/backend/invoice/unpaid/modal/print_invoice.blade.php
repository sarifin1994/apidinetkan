<!-- Modal Show -->
<div class="modal fade" id="print_invoice" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Print Invoice</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <div class="form-group mb-3">
                        <label for="template" class="mb-1">Pilih Kertas</label>
                        <select class="form-select" id="template" name="template" autocomplete="off"
                            data-placeholder="template" required>
                            <option value="a4">A4</option>
                            <option value="a5">Dot Matrix</option>
                            <option value="thermal">Thermal</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary text-white" id="printMassal1" type="submit">
                        Print
                    </button>
            </div>
        </div>
    </div>
</div>
