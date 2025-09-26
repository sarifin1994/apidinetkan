<!-- Modal Show -->
<div class="modal fade" id="withdraw" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Tarik Saldo</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="nominal_wd" class="mb-1">Nominal (Input tanpa titik) <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="nominal_wd" name="nominal_wd" placeholder=""
                            autocomplete="off" required>
                            <small><a class="mt-2" href="javascript:void(0)" id="getnominal">Tarik semua saldo</a></small>
                    </div>
                    <hr>
                    
                    Dana akan ditransfer ke rekening dibawah ini, jika belum mengisi data nomor rekening. silakan lengkapi di menu Setting > Perusahaan<hr>
                    @php
                        $company = \App\Models\Setting\Company::where('shortname',multi_auth()->shortname)->first();
                    @endphp
                    <div class="form-group mb-3">
                        <label for="norek_wd" class="mb-1">Nomor Rekening <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="norek_wd" value="{{$company->bank}}"name="norek_wd" placeholder=""
                            autocomplete="off" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label for="atas_nama" class="mb-1">Atas Nama <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="atas_nama" value="{{$company->holder}}" name="atas_nama" placeholder=""
                            autocomplete="off" readonly>
                    </div>
                    <hr>
                    <small>Penarikan akan diproses di hari kerja maksimal 1 x 24 jam<br>Biaya Rp. 5.000 / penarikan</small>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary text-white" id="tarik_saldo" type="submit">
                       Tarik saldo
                    </button>
            </div>
        </div>
    </div>
</div>
