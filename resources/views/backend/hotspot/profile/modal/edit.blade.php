<!-- Modal Show -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">EDIT PROFILE</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <input type="hidden" id="profile_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="name_edit" class="mb-1">Nama Profile <small
                                        class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="name_edit" name="name_edit"
                                    placeholder="name_edit" autocomplete="off" disabled>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="shared_edit" class="mb-1">Shared User <small
                                    class="text-danger">*</small></label>
                                <input type="number" class="form-control" id="shared_edit" name="shared_edit"
                                    autocomplete="off" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="price_edit" class="mb-1">Harga Jual (Rp)</label>
                                    <input type="text" class="form-control" id="price_edit" name="price_edit"
                                        placeholder="price_edit" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="reseller_price_edit" class="mb-1">Harga Reseller (Rp)</label>
                                    <input type="text" class="form-control" id="reseller_price_edit"
                                        name="reseller_price_edit" placeholder="reseller_price_edit" autocomplete="off">
                            </div>
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="rate_edit" class="mb-1">Rate Limit</label>
                                <input type="text" class="form-control" id="rate_edit" name="rate_edit"
                                    placeholder="5M/10M 0/0 0/0 0/0 8 0/0" autocomplete="off" required>
                                    <small id="helper" class="form-text text-muted">Jika dikosongkan akan mengikuti ratelimit profile</small>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="groupProfile_edit" class="mb-1">Group Profile</label>
                                <input type="text" class="form-control"
                                    id="groupProfile_edit" name="groupProfile_edit" value=""
                                    placeholder="" autocomplete="off">
                                    <small id="helper" class="form-text text-muted">Jika dikosongkan akan menggunakan profile default</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <label for="uptime" class="mb-1">Uptime</label>
                            <div class="input-group mb-3">
                                <div class="form-group me-2">
                                    <input type="number" class="form-control" id="uptime_edit" name="uptime_edit" placeholder="" autocomplete="off">
                                </div>
                                <select class="form-select" id="satuan_uptime_edit" name="satuan_uptime_edit" autocomplete="off">
                                    <option value="Jam">Jam</option>
                                    <option value="Hari">Hari</option>
                                    <option value="Bulan">Bulan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="validity" class="mb-1">Validity</label>
                            <div class="input-group mb-3">
                                <div class="form-group me-2">
                                    <input type="number" class="form-control" id="validity_edit" name="validity_edit" placeholder="" autocomplete="off">
                                </div>
                                <select class="form-select" id="satuan_validity_edit" name="satuan_validity_edit" autocomplete="off">
                                    <option value="Jam">Jam</option>
                                    <option value="Hari">Hari</option>
                                    <option value="Bulan">Bulan</option>
                                </select>
                            </div>
                        </div>
                        

                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <label for="quota" class="mb-1">Quota</label>
                            <div class="input-group mb-3">
                                <div class="form-group me-2">
                                    <input type="number" class="form-control" id="quota_edit" name="quota_edit" placeholder="" autocomplete="off">
                                </div>
                                <select class="form-select" id="satuan_quota_edit" name="satuan_quota_edit" autocomplete="off">
                                    <option value="MB">MB</option>
                                    <option value="GB">GB</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label for="lock_mac_edit" class="mb-1">Lock Mac On First Login</small></label>
                            <div class="form-group mb-3">
                                <select class="form-select" id="lock_mac_edit" name="lock_mac_edit" autocomplete="off">
                                    <option value="0">Disabled</option>
                                    <option value="1">Enabled</option>
                                </select>
                            </div>
                        </div>

                        @if(multi_auth()->license_id == 10)
                        <div class="col-lg-6">
                            <label for="is_billing" class="mb-1">Tampil Billing</small></label>
                            <div class="form-group mb-3">
                                <select class="form-select" id="is_billing" name="is_billing" autocomplete="off">
                                    <option value="0">Disabled</option>
                                    <option value="1">Enabled</option>
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary text-white" id="update" type="submit">
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    /* Tanpa Rupiah */
    var price_edit = document.getElementById('price_edit');
    price_edit.addEventListener('keyup', function(e) {
        price_edit.value = formatRupiah(this.value);
    });

    /* Tanpa Rupiah */
    var reseller_price_edit = document.getElementById('reseller_price_edit');
    reseller_price_edit.addEventListener('keyup', function(e) {
        reseller_price_edit.value = formatRupiah(this.value);
    });

    /* Fungsi */
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
    }
</script>
