<!-- Modal Show -->
<div class="modal fade" id="create_secret" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">TAMBAH PELANGGAN | <span id="nama_psb"></span></h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="id">

        <div class="row" id="member_details">
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="full_name_show" class="mb-1">Nama Lengkap</label>
              <input type="text" class="form-control" id="full_name_show" disabled>
            </div>
          </div>
          <div class="col-lg-6">
            <label for="wa_show" class="mb-1">Nomor WhatsApp</label>
            <input type="text" class="form-control" id="wa_show" disabled>
          </div>
        </div>
        <div class="row" id="member_details_2">
          <div class="col-lg-12">
            <div class="form-group mb-3">
              <label for="address_show" class="mb-1">Alamat</label>
              <textarea class="form-control" id="address_show" style="height:90px;" disabled></textarea>
            </div>
          </div>
        </div>

        <div class="h6 col-auto mt-2">Data Secret</div>
        <hr />
        <div class="row">
          <div class="col-lg-6 mb-3">
            <label>Username</label>
            <input type="text" id="username_add" class="form-control" onkeyup="nonkapital()">
          </div>
          <div class="col-lg-6 mb-3">
            <label>Password</label>
            <input type="text" id="password_add" class="form-control" onkeyup="nonkapital()">
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6 mb-3">
            <label>Assign Profile <small class="text-danger">*</small></label>
            <select class="form-select" id="profile_add" required>
              <option value="">Pilih Profile</option>
              @foreach ($profiles as $prof)
                <option value="{{ $prof->id }}">{{ $prof->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-6 mb-3">
            <label>Nas</label>
            <select class="form-select" id="nas_add">
              <option value="">all</option>
              @foreach ($nas as $n)
                <option value="{{ $n->ip_router }}">{{ $n->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6 mb-3">
            <label>Kode Area</label>
            <select class="form-select" id="kode_area_add">
              <option value=""></option>
              @foreach ($areas as $ar)
                <option value="{{ $ar->id }}">{{ $ar->kode_area }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-6 mb-3">
            <label>Kode ODP</label>
            <select class="form-select" id="kode_odp_add"></select>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6 mb-3">
            <label>Lock Mac</label>
            <select class="form-select" id="lock_mac">
              <option value="0">Disabled</option>
              <option value="1">Enabled</option>
            </select>
          </div>
          <div class="col-lg-6 mb-3" id="show_mac" style="display:none">
            <label>MAC Address</label>
            <input type="text" class="form-control" id="mac" placeholder="8b:fd:55:5a:0b:d4">
          </div>
        </div>

        <div class="h6 col-auto mt-2">Data Pembayaran</div>
        <hr />
        <div class="row">
          <div class="col-lg-6 mb-3">
            <label>Tipe Pembayaran</label>
            <select class="form-select" id="payment_type">
              <option value="Prabayar">Prabayar</option>
              <option value="Pascabayar">Pascabayar</option>
            </select>
          </div>
          <div class="col-lg-6 mb-3" id="show_payment_status">
            <label>Status Pembayaran</label>
            <select class="form-select" id="payment_status">
              <option value="paid">Paid</option>
              <option value="unpaid">Unpaid</option>
            </select>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6 mb-3">
            <label>Siklus Tagihan</label>
            <select class="form-select" id="billing_period">
              <option value="Fixed Date">Fixed Date</option>
              <option value="Billing Cycle" disabled>Billing Cycle</option>
            </select>
          </div>
          <div class="col-lg-6 mb-3">
            <label>Tanggal Aktif</label>
            <input type="date" class="form-control" id="reg_date">
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6 mb-3">
            <label>PPN (%)</label>
            <input type="number" class="form-control" id="ppn" value="0">
          </div>
          <div class="col-lg-6 mb-3">
            <label>Discount (%)</label>
            <input type="number" class="form-control" id="discount" value="0">
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6 mb-3">
            <label>Amount</label>
            <input type="text" disabled class="form-control" id="amount">
          </div>
          <div class="col-lg-6 mb-3">
            <label>Payment Total</label>
            <input type="text" disabled class="form-control" id="payment_total">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-primary" id="store_secret" type="submit">Submit</button>
      </div>
    </div>
  </div>
</div>
