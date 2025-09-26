    <!-- Modal Show -->
    <div class="modal fade" id="show_data" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">DATA SECRET</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">

                        <tbody style="font-size:15px">
                            <tr>
                                <td style="width:30%">Username</td>
                                <td>: <span id="username"></span>
                            </tr>
                            <tr>
                                <td>Password</td>
                                <td>: <span id="password"></span>
                            </tr>
                            <tr>
                                <td>Internet Profile</td>
                                <td>: <span id="profile_inet"></span>
                            </tr>
                            <tr>
                                <td>Internet Status</td>
                                <td>: <span id="status"></span>
                            </tr>
                            <tr>
                                <td>IP Address</td>
                                <td>: <span id="fill_ip"></span>
                            </tr>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <input type="hidden" id="member_idc" name="member_idc">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="full_name" class="mb-1">Nama Lengkap</label>
                                <input disabled type="text" class="form-control" id="full_name" name="ful_name" onkeyup="kapital()" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="wa" class="mb-1">Nomor WhatsApp</label>
                                <input disabled type="number" class="form-control" id="wa" name="wa" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="kode_area" class="mb-1">Kode Area</label>
                                <select disabled class="form-select" id="kode_area" name="kode_area" autocomplete="off">
                                    <option selected="selected">
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="kode_odp" class="mb-1">Kode ODP</label>
                            <div class="form-group mb-3">
                                <select disabled class="form-select" id="kode_odp" name="kode_odp" autocomplete="off">
                                    <option selected="selected">
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="address" class="mb-1">Alamat Lengkap <small></small></label>
                                <textarea disabled name="address" id="address" style="height: 90px" onkeyup="kapital()" class="form-control"
                                    placeholder="address" autocomplete="off" required></textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
