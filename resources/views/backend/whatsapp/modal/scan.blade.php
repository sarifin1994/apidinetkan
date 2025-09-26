<!-- Modal Show -->
<div class="modal fade" id="scan" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Scan Device</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="mb-3">Scan Device Whatsapp<br><small>Apabila status device disconnect, harap klik tombol Generate QR lalu scan ulang agar notifikasi whatsapp bisa tetap terkirim<hr>Harap tunggu hingga proses scan berhasil sebelum Anda mengklik Cek Status</small>                
                </label><br>
                <div id="show_qr" class="text-center">
                </div>
                
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-success text-white me-2" id="action-rescan" type="submit">
                    <span class="material-symbols-outlined">
                        qr_code_2
                        </span> Generate QR
                </button>
                <a href="/whatsapp" class="btn btn-primary me-2">
                    <span class="material-symbols-outlined">
                        refresh
                        </span> Cek Status
                </a>
            </div>
        </div>
    </div>
</div>
