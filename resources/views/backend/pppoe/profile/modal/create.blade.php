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
                                <label for="name" class="mb-1">Nama <small class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="name" name="name" value=""
                                    placeholder="" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="price" class="mb-1">Harga (Rp)</label>
                                <input type="text" class="form-control" id="price" name="price" value=""
                                    placeholder="" autocomplete="off">
                                {{-- <small id="helper" class="form-text text-muted">Harga jual yang akan ditampilkan ke pelanggan</small>  --}}
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="fee_mitra" class="mb-1">Fee Mitra (Rp)</label>
                                <input type="text" class="form-control" id="fee_mitra" name="fee_mitra" value=""
                                    placeholder="" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="rate" class="mb-1">Rate Limit</label>
                                <input type="text" class="form-control" id="rate" name="rate"
                                    value="" placeholder=""
                                    autocomplete="off" required>
                                <small id="helper" class="form-text text-muted">Isi menggunakan format rate limit standar atau burst limit</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="groupProfile" class="mb-1">Group Profile</label>
                                <input type="text" class="form-control" id="groupProfile" name="groupProfile"
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
                                <label for="status" class="mb-1">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="1">Aktif</status>
                                    <option value="0">Nonaktif</status>
                                </select>
                            </div>
                        </div>
                    </div> --}}


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
    var fee_mitra = document.getElementById('fee_mitra');
    fee_mitra.addEventListener('keyup', function(e) {
        fee_mitra.value = formatRupiah(this.value);
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
