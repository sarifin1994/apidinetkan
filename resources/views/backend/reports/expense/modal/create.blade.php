<!-- Modal Show -->
<div class="modal fade" id="create_expense" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal">TAMBAH PENGELUARAN</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="tanggal_ce" class="mb-1">Tanggal</label>
              <input type="date" class="form-control" id="tanggal_ce" name="tanggal_ce">
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="type_ce" class="mb-1">Tipe</label>
              <select type="text" class="form-select" id="type_ce" name="type_ce">
                <option value="2">Pengeluaran</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="category_ce" class="mb-1">Kategori</label>
              <select type="text" class="form-select" id="category_ce" name="category_ce">
                {{-- <option value="Fee Mitra">Fee Mitra</option>
                <option value="Operasional">Operasional</option>
                <option value="Belanja">Belanja</option>
                <option value="Lainnya">Lainnya</option> --}}
                @foreach ($categories as $category)
                  <option value="{{ $category->value }}">{{ $category->label() }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="form-group mb-3">
              <label for="deskripsi_ce" class="mb-1">Deskripsi</label>
              <input type="text" class="form-control" id="deskripsi_ce" name="deskripsi_ce">
            </div>
          </div>

        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="price_ce" class="mb-1">Total</label>
              <input class="form-control" id="price_ce" name="price_ce" autocomplete="off">
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="payment_method_ce" class="mb-1">Metode</label>
              <select class="form-select" id="payment_method_ce" name="payment_method_ce" autocomplete="off">
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
        <button class="btn btn-primary" id="store_expense" type="submit">
          Simpan
        </button>
      </div>
    </div>
  </div>
</div>
