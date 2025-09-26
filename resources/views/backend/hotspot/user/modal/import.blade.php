<!-- Modal Show -->
<div class="modal fade" id="import" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Import User</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form id="importForm" action="{{ route('import.hotspot')}}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
            <div class="form-group mb-3">
                        <label for="select_file" class="mb-1">File Import<small class="text-danger"> *</small></label>
                        <input type="file" class="form-control" id="select_file" name="select_file" placeholder="" autocomplete="off" required>
                        <hr>
                        <small id="helper" class="form-text text-muted">Download format file import <a href="https://docs.google.com/spreadsheets/d/1f7mB-V1zbcgSPEhw9Rqzs5WqpAto7gPLRAni6yewkUc/export?format=xlsx" target="_blank" class="text-danger">disini</a></small>
                    </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-link" type="button" data-bs-dismiss="modal">
                    Close
                </button>
                <button class="btn btn-primary" id="btnImport" type="submit">
                    Import
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
  
  