    <!-- payment Modal Show -->
    <div class="modal fade" id="show_payment" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal">DATA PEMBAYARAN | <span class="text-sm" id="payment_full_name"></h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <input type="hidden" id="payment_full_name_edit">
              <input type="hidden" id="member_id" name="member_id">
              <div class="col-lg-6">
                <div class="form-group mb-3">
                  <label for="payment_type" class="mb-1">Tipe Pembayaran</label>
                  <select class="form-select" id="payment_type" name="payment_type" autocomplete="off" disabled>
                    <option selected="selected">
                    </option>
                  </select>

                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group mb-3">
                  <label for="billing_period" class="mb-1">Siklus Tagihan</label>
                  <select class="form-select" id="billing_period" name="billing_period" autocomplete="off" disabled>
                    <option selected="selected">
                    </option>
                  </select>

                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-6">
                <div class="form-group mb-3">
                  <label for="payment_type" class="mb-1">Tanggal Aktif</label>
                  <input type="date" class="form-control" id="reg_date" name="reg_date" disabled>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group mb-3">
                  <label for="payment_type" class="mb-1">Tanggal Jatuh Tempo</label>
                  <input type="date" class="form-control" id="next_due" name="next_due">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-6">
                <div class="form-group mb-3">
                  <label for="profile" class="mb-1">Internet Profile</label>
                  <select class="form-select" id="profile" name="profile" autocomplete="off" disabled>
                    <option selected="selected">
                    </option>
                  </select>

                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group mb-3">
                  <label for="amount" class="mb-1">Amount</label>
                  <input type="text" class="form-control" id="amount" name="amount" disabled>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group mb-3">
                  <label for="ppn" class="mb-1">PPN<small> %</small></label>
                  <input type="number" class="form-control" id="ppn" name="ppn" placeholder=""
                    autocomplete="off" required>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group mb-3">
                  <label for="discount" class="mb-1">Discount<small> %</small></label>
                  <input type="number" class="form-control" id="discount" name="discount" placeholder=""
                    autocomplete="off" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group mb-3">
                  <label for="payment_total" class="mb-1">Payment Total</label>
                  <input type="text" disabled class="form-control" id="payment_total" name="payment_total"
                    placeholder="payment_total" autocomplete="off">
                </div>
              </div>
            </div>

          </div>

          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
              Batal
            </button>
            @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Helpdesk' || auth()->user()->role === 'Kasir')
              <button class="btn btn-primary" id="editPayment" type="submit">
                Simpan
              </button>
            @endif
          </div>
        </div>
      </div>
    </div>
