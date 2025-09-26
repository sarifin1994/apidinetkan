<!-- Modal Show -->
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Create Profile</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="name" class="mb-1">Nama Profile <small class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="name" name="name" value=""
                                    placeholder="" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="shared" class="mb-1">Shared User <small
                                    class="text-danger">*</small></small></label>
                                <input type="number" class="form-control" id="shared" name="shared" value="1" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="price" class="mb-1">Harga Jual (Rp)</label>
                                <input type="text" class="form-control" id="price" name="price" value=""
                                    placeholder="" autocomplete="off">
                                {{-- <small id="helper" class="form-text text-muted">Harga jual yang akan ditampilkan ke pelanggan</small>  --}}
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="reseller_price" class="mb-1">Harga Reseller (Rp)</label>
                                <input type="text" class="form-control" id="reseller_price" name="reseller_price" value=""
                                    placeholder="" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="rate" class="mb-1">Rate Limit</label>
                                <input type="text" class="form-control" id="rate" name="rate" autocomplete="off" required>
                                    <small id="helper" class="form-text text-muted">Jika dikosongkan akan mengikuti ratelimit profile</small>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="groupProfile" class="mb-1">Group Profile</label>
                                <input type="text" class="form-control"
                                    id="groupProfile" name="groupProfile" value=""
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
                                    <input type="number" class="form-control" id="uptime" name="uptime" placeholder="" autocomplete="off">
                                </div>
                                <select class="form-select" id="satuan_uptime" name="satuan_uptime" autocomplete="off">
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
                                    <input type="number" class="form-control" id="validity" name="validity" placeholder="" autocomplete="off">
                                </div>
                                <select class="form-select" id="satuan_validity" name="satuan_validity" autocomplete="off">
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
                                    <input type="number" class="form-control" id="quota" name="quota" placeholder="" autocomplete="off">
                                </div>
                                <select class="form-select" id="satuan_quota" name="satuan_quota" autocomplete="off">
                                    <option value="MB">MB</option>
                                    <option value="GB">GB</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label for="lock_mac" class="mb-1">Lock Mac On First Login</small></label>
                            <div class="form-group mb-3">
                                <select class="form-select" id="lock_mac" name="lock_mac" autocomplete="off">
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
                    <button class="btn btn-primary text-white" id="store" type="submit">
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    /* Tanpa Rupiah */
    var price = document.getElementById('price');
    price.addEventListener('keyup', function(e) {
        price.value = formatRupiah(this.value);
    });

    /* Tanpa Rupiah */
    var reseller_price = document.getElementById('reseller_price');
    reseller_price.addEventListener('keyup', function(e) {
        reseller_price.value = formatRupiah(this.value);
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
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }
</script>
