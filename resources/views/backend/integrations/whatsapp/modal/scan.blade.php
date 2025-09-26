<!-- Modal Show -->
<div class="modal fade" id="scan" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">GANTI NOMOR PENGIRIM</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="mb-3">MASUKKAN NOMOR PENGIRIM<br><small>Harap gunakan nomor whatsapp yang sudah aktif lebih dari 1 bulan agar tidak dianggap spam. Kami tidak bertanggung jawab apabila nomor anda dibanned!</small>                
                </label><br>
                <small>Setelah mengubah nomor whatsapp, lakukan scan device agar status whatsapp menjadi <span class="text-success">CONNECTED</span></small><br>
                    <input type="text" class="mt-3 form-control"
                        id="sender" name="sender"
                        placeholder="08121314xxxx" autocomplete="off" required>

            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" type="button" data-bs-dismiss="modal">
                    BATAL
                </button>
                <button class="btn btn-success" id="update" type="submit">SIMPAN
                </button>
            </div>
        </div>
    </div>
</div>
