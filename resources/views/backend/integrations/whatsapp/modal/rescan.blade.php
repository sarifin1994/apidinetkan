<!-- Modal Show -->
<div class="modal fade" id="rescan" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal">SCAN DEVICE</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="sender_rescan">
        <label class="mb-3">SCAN DEVICE JIKA DISCONNECT<br><small>Jika anda memindahkan whatsapp ke perangkat lain
            atau menginstal ulang aplikasinya, harap scan ulang device anda agar notifikasi wa bisa tetap
            terkirim</small>
        </label><br>
        <div id="show_qr" class="text-center">
        </div>


      </div>
      <div class="modal-footer">
        <button class="btn btn-warning" type="button" data-bs-dismiss="modal">
          BATAL
        </button>
        <button class="btn btn-success" id="action-rescan" type="submit">
          <i class="fas fa-qrcode me-1"></i>GENERATE QR
        </button>
        <a href="{{ url()->current() }}" class="btn btn-primary">
          <i class="fas fa-repeat me-1"></i>REFRESH PAGE
        </a>
      </div>
    </div>
  </div>
</div>
