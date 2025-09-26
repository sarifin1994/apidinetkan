<!-- Modal Show -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Edit Profile</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <input type="hidden" id="profile_id">
                                <label for="name_edit" class="mb-1">Nama <small class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="name_edit" name="name_edit" value=""
                                    placeholder="" autocomplete="off" required disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="price_edit" class="mb-1">Harga (Rp)</label>
                                <input type="text" class="form-control" id="price_edit" name="price_edit" value=""
                                    placeholder="" autocomplete="off">
                                {{-- <small id="helper" class="form-text text-muted">Harga jual yang akan ditampilkan ke pelanggan</small>  --}}
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="fee_mitra_edit" class="mb-1">Fee Mitra (Rp)</label>
                                <input type="text" class="form-control" id="fee_mitra_edit" name="fee_mitra_edit" value=""
                                    placeholder="" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="rate_edit" class="mb-1">Rate Limit</label>
                                <input type="text" class="form-control" id="rate_edit" name="rate_edit"
                                    value="" placeholder=""
                                    autocomplete="off" required>
                                <small id="helper" class="form-text text-muted">Isi menggunakan format rate limit standar atau burst limit</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="groupProfile_edit" class="mb-1">Group Profile</label>
                                <input type="text" class="form-control" id="groupProfile_edit" name="groupProfile_edit"
                                    value="Radiusqu" placeholder="Radiusqu" autocomplete="off">
                                <small id="helper" class="form-text text-muted">Jika dikosongkan akan menggunakan
                                    profile default mikrotik</small>
                            </div>
                        </div>
                    </div>
                    {{-- <hr>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="status_edit" class="mb-1">Status</label>
                                <select class="form-select" id="status_edit" name="status_edit">
                                    <option value="1">Aktif</status>
                                    <option value="0">Nonaktif</status>
                                </select>
                            </div>
                        </div>
                    </div> --}}


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
    var fee_mitra_edit = document.getElementById('fee_mitra_edit');
    fee_mitra_edit.addEventListener('keyup', function(e) {
        fee_mitra_edit.value = formatRupiah(this.value);
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
