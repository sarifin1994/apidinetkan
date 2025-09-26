<!-- Modal Show -->
<div class="modal fade" id="create_income" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal">TAMBAH PEMASUKAN</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="tanggal_ci" class="mb-1">Tanggal</label>
              <input type="date" class="form-control" id="tanggal_ci" name="tanggal_ci">
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="type_ci" class="mb-1">Tipe</label>
              <select type="text" class="form-select" id="type_ci" name="type_ci">
                <option value="1">Pemasukan</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="category_ci" class="mb-1">Kategori</label>
              <select type="text" class="form-select" id="category_ci" name="category_ci">
                {{-- <option value="Invoice" disabled>Invoice</option>
                <option value="Hotspot" disabled>Hotspot</option>
                <option value="Regist PSB">Regist PSB</option>
                <option value="Lainnya">Lainnya</option> --}}
                @foreach ($categories as $index => $category)
                  <option value="{{ $category->value }}" {{ $index < 2 ? 'disabled' : '' }}>{{ $category->label() }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="form-group mb-3">
              <label for="deskripsi_ci" class="mb-1">Deskripsi</label>
              <input type="text" class="form-control" id="deskripsi_ci" name="deskripsi_ci">
            </div>
          </div>

        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="price_ci" class="mb-1">Total</label>
              <input class="form-control" id="price_ci" name="price_ci" autocomplete="off">

            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="payment_method_ci" class="mb-1">Metode</label>
              <select class="form-select" id="payment_method_ci" name="payment_method_ci" autocomplete="off">
                <option value="1">Cash</option>
                <option value="2">Transfer</option>
              </select>

            </div>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
          Batal
        </button>
        <button class="btn btn-primary" id="store_income" type="submit">
          Simpan
        </button>
      </div>
    </div>
  </div>
</div>
