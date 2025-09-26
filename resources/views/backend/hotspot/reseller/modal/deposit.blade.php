<!-- Modal Show -->
<div class="modal fade" id="deposit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">TAMBAH DEPOSIT</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                    <div class="form-group mb-3">
                        <input id="id_deposit" type="hidden">
                        <input id="group_id" type="hidden">
                        <label for="jml_deposit" class="mb-1">JUMLAH DEPOSIT <small class="text-danger">*</small></label>
                        <input type="number" class="form-control" id="jml_deposit" name="jml_deposit" placeholder="" autocomplete="off" required>
                        <small id="helper" class="form-text text-muted">Isikan nominal deposit, minimal 10.000</small>
                    </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" type="button" data-bs-dismiss="modal">
                    Batal
                </button>
                <button class="btn btn-success" id="depo" type="submit">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    /* Tanpa Rupiah */
    var price = document.getElementById('jml_deposit');
    price.addEventListener('keyup', function(e) {
        price.value = formatRupiah(this.value);
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
