<!-- Modal Show -->
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Create Mikrotik</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <div class="form-group mb-3">
                    <label for="name" class="mb-1">Nama <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="" autocomplete="off" required>
                </div>
                <div class="form-group mb-3">
                    <label for="ip_router" class="mb-1">IP Router <small class="text-danger">*</small></label>
                    <select class="form-select" id="ip_router" name="ip_router" autocomplete="off" required>
                            @forelse ($vpns as $vpn)
                                <option value="{{ $vpn->ip_address }}">{{ $vpn->name }} - {{$vpn->ip_address}}</option>
                            @empty
                            @endforelse
                        </select>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="port_api" class="mb-1">Port <small class="text-danger">*</small></label>
                    <input type="number" class="form-control" id="port_api" name="port_api" placeholder="" autocomplete="off" required>
                    <small id="helper" class="form-text text-muted">Jika masih menggunakan port api default, masukan 8728</small>
                </div>
                <div class="form-group mb-3" style="display:none">
                    <label for="secret" class="mb-1">Secret <small class="text-danger">*</small></label>
                    <input type="hidden" class="form-control" id="secret" name="secret" value="radiusqu" autocomplete="off" required>
                </div>


                <div class="form-group mb-3">
                    <label for="ip_router" class="mb-1">Timezone <small class="text-danger">*</small></label>
                    <select class="form-select" id="timezone" name="timezone" autocomplete="off" required>
                        <option value="0">Asia/Jakarta</option>
                        <option value="3600">Asia/Makassar</option>
                        <option value="7200">Asia/Jayapura</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary text-white" id="store" type="submit">
                    Create
                </button>
            </div>
        </div>
    </div>
</div>
