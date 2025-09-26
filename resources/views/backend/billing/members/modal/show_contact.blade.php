<!-- show contact Modal Show -->
<div class="modal fade" id="show_contact" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal">DETAIL MEMBER</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" id="member_idc" name="member_idc">
          <input type="hidden" id="pppoe_idc" name="pppoe_idc">

          <!-- Member ID Field -->
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="id_member" class="mb-1">Member ID</label>
              <input type="text" class="form-control" id="id_member" name="id_member" autocomplete="off">
            </div>
          </div>

          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="full_name" class="mb-1">Nama Lengkap</label>
              <input type="text" class="form-control" id="full_name" name="full_name" onkeyup="kapital()"
                autocomplete="off">
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="wa" class="mb-1">WhatsApp</label>
              <input type="number" class="form-control" id="wa" name="wa" autocomplete="off">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="email" class="mb-1">Email</label>
              <input type="email" class="form-control" id="email" name="email" autocomplete="off">
              <small class="text-danger">* Wajib di isi apabila akan menggunakan payment gateway milik TriPay</small>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="address" class="mb-1">Alamat Lengkap</label>
              <textarea name="address" id="address" style="height: 90px" onkeyup="kapital()" class="form-control" autocomplete="off"
                required></textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
          Batal
        </button>
        <button class="btn btn-primary" id="editContact" type="submit">
          Simpan
        </button>
      </div>
    </div>
  </div>
</div>
