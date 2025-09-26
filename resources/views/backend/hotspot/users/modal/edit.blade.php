<!-- Modal Show -->
<div class="modal fade" id="edit" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">EDIT USER</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <input type="hidden" id="user_id">
                        <label for="username_edit" class="mb-1">Username <small
                            class="text-danger">*</small></label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username_edit" name="username"
                                    placeholder="Username" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="password_edit" class="mb-1">Password <small
                            class="text-danger">*</small></label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="text" class="form-control" id="password_edit" name="password"
                                    placeholder="Password" autocomplete="off" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label for="profile_edit" class="mb-1">Assign Profile <small
                            class="text-danger">*</small></label>
                        <div class="form-group mb-3">
                            <select class="form-select" id="profile_edit" name="profile_edit" autocomplete="off"
                                data-placeholder="Pilih Profile" required>
                                @forelse ($profiles as $profile)
                                    <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="nas_edit" class="mb-1">Nas</label>
                            <select class="form-select" id="nas_edit" name="nas" autocomplete="off"
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
                            <label for="hotspot_server_edit" class="mb-1">Hotspot Server</label>
                            <input type="text" class="form-control" id="hotspot_server_edit" name="hotspot_server_edit"
                                placeholder="" autocomplete="off" required>
                        </div>
                    </div>

                </div>
                <hr>
                <div class="row px-2">
                    <table class="table table-hover">
                        <input type="hidden" id="ppp_id">
                        <tbody style="font-size:15px">
                            <tr>
                                <td style="width:30%">Status</td>
                                <td>: <span id="fill_status"></span></td>
                            </tr>
                            <tr>
                                <td>First Login Time</td>
                                <td>: <span id="fill_login"></span></td>
                            </tr>
                            <tr>
                                <td>Expired Time</td>
                                <td>: <span id="fill_expired"></span></td>
                            </tr>
                            <tr>
                                <td>Total Upload Used</td>
                                <td>: <span id="fill_upload"></span></td>
                            </tr>
                            <tr>
                                <td>Total Download Used</td>
                                <td>: <span id="fill_download"></span></td>
                            </tr>
                            <tr>
                                <td>Total Session Time</td>
                                <td>: <span id="fill_uptime"></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>


                <div class="collapse table-responsive" id="collapseExample">
                    <table id="sessionTable" style="font-size:12px" class="table table-hover display nowrap"
                        width="100%">
                        <thead>
                            <tr>
                                <th>Start</th>
                                <th>Stop</th>
                                <th>IP</th>
                                <th>MAC</th>
                                <th>Rx</th>
                                <th>Tx</th>
                                <th>Uptime</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" type="button" data-bs-dismiss="modal">
                    Close
                </button>
                <div class="btn-group dropup">
                    <button id="show_session" type="button" class="btn btn-info dropdown-toggle"
                        data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false"
                        aria-controls="collapseExample">
                        Show Session
                    </button>
                </div>
                {{-- <div class="btn-group dropup">
                    <a class="btn btn-orange dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-edit"></i>&nbsp Status
                    </a>

                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <li><button class="dropdown-item" id="enabled">Enabled</button></li>
                        <li><button class="dropdown-item" id="disabled">Disabled</button></li>
                        <li><button class="dropdown-item" id="suspend">Suspend</button></li>
                    </ul>
                </div> --}}
                @if(auth()->user()->role !== 'Reseller')
                <button class="btn btn-success" id="update" type="submit">
                    Simpan
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
