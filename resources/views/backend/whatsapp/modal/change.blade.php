<!-- Modal Show -->
<div class="modal fade" id="change" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Ganti Nomor</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="mb-3"><small>Harap gunakan nomor whatsapp yang sudah aktif lebih dari 3 bulan agar meminimalisir banned. Kami tidak bertanggung jawab apabila nomor anda dibanned!</small>                
                </label><br>
                <small>Setelah mengubah nomor whatsapp, lakukan scan device agar status whatsapp menjadi <span class="text-success">Connected</span></small><br>
                <hr>
                <div class="form-group mb-3">
                    @php
                    $mpwa = \App\Models\Whatsapp\Mpwa::where('shortname',multi_auth()->shortname)->select('id')->first();
                    @endphp
                    <input type="hidden" id="mpwa_id" value="{{$mpwa->id}}">
                    <label for="no_wa_edit" class="mb-1">Nomor Pengirim <small class="text-danger">*</small></label>
                    <input type="number" class="form-control" id="no_wa_edit" name="no_wa_edit" placeholder="" autocomplete="off" required>
                    <small id="helper" class="form-text text-muted">Masukan nomor whatsapp pengirim dengan format kode negara, contoh <b>62</b>85155112192</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="update" type="submit">
                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>
