<!-- Modal Show -->
<div class="modal fade" id="rename" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">ONU Rename</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="port_id_rename" value="{{ $data['base']->port_id }}">
                <input type="hidden" id="onu_id_rename" value="{{ $data['base']->onu_id }}">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group mb-3">
                            <label for="onu_name" class="mb-1">ONU Name</label>
                            <input type="text" class="form-control" id="onu_name" value="{{ $data['base']->onu_name }}">
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button class="btn btn-link" type="button" data-bs-dismiss="modal">
                    Close
                </button>
                <button class="btn btn-primary" id="rename_onu" type="submit">
                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>
