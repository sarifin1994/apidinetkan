<!-- Modal Show -->
<div class="modal fade" id="create" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">TAMBAH USER</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <input type="hidden" id="wa_reseller_c">
                        <div class="form-group mb-3">
                            <label for="username" class="mb-1">Username <small
                                class="text-danger">*</small></label>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="password" class="mb-1">Password <small
                                class="text-danger">*</small></label>
                            <input type="text" class="form-control" id="password" name="password"
                                placeholder="" autocomplete="off" required> 
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="profile_c" class="mb-1">Assign Profile <small
                                class="text-danger">*</small></label>
                            <select class="form-select" id="profile_c" name="profile_c" autocomplete="off"
                                data-placeholder="Pilih Profile" required>
                                <option value="">Pilih Profile</option>
                                @forelse ($profiles as $profile)
                                    <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="nas_c" class="mb-1">Nas</label>
                            <select class="form-select" id="nas_c" name="nas_c" autocomplete="off"
                                data-placeholder="Pilih Nas">
                                <option value="">all</option>
                                @forelse ($nas as $nas)
                                    <option value="{{ $nas->ip_router }}">{{ $nas->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="hotspot_server_c" class="mb-1">Hotspot Server</label>
                            <input type="text" class="form-control" id="hotspot_server_c" name="hotspot_server_c"
                                placeholder="" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="payment_status_c" class="mb-1">Payment Status</label>
                            <select class="form-select" id="payment_status_c" name="payment_status_c" autocomplete="off"
                                data-placeholder="Pilih Payment Status">
                                <option value="2">Paid</option>
                                <option value="1">Unpaid</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="total_c" class="mb-1">Total</label>
                            <input type="text" class="form-control" id="total_c" name="total_c"
                                placeholder="" autocomplete="off" disabled>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="reseller_c" class="mb-1">Reseller</label>
                        <div class="form-group mb-3" style="display:grid">
                            <select class="form-select select2" id="reseller_c" name="reseller_c" autocomplete="off"
                                data-placeholder="Pilih Reseller">
                                <option value=""></option>
                                @forelse ($resellers as $reseller)
                                    <option value="{{ $reseller->id }}">{{ $reseller->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" type="button" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button class="btn btn-success" id="store" type="submit">
                    Create
                </button>
            </div>
        </div>
    </div>
</div>
