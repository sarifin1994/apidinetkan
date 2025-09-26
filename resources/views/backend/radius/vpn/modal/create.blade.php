<!-- Modal Show -->
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Create VPN</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="name" class="mb-1">Nama <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="" autocomplete="off" required>
                </div>
                <div class="form-group mb-3">
                    <label for="vpn_server" class="mb-1">Server <small class="text-danger">*</small></label>
                    <select class="form-select" id="vpn_server" name="vpn_server" autocomplete="off" required>
                            @forelse ($vpnserver as $vpn)
                                <option value="{{ $vpn->host }}">{{ $vpn->lokasi }} - {{$vpn->host}}</option>
                            @empty
                            @endforelse
                        </select>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="user" class="mb-1">User <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="user" name="user" placeholder="" autocomplete="off" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password" class="mb-1">Password <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="password" name="password" placeholder="" autocomplete="off" required>
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
