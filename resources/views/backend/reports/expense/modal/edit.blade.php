    <!-- Modal Show -->
    <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal">EDIT PENGELUARAN</h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <input type="hidden" id="transaction_id" name="transaction_id">
              <div class="col-lg-6">
                <div class="form-group mb-3">
                  <label for="tanggal" class="mb-1">Tanggal</label>
                  <input type="datetime" class="form-control" id="tanggal" name="tanggal" disabled>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group mb-3">
                  <label for="type" class="mb-1">Tipe</label>
                  <select type="text" class="form-select" id="type" name="type" disabled>
                    <option value="2">Pengeluaran</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-6">
                <div class="form-group mb-3">
                  <label for="category" class="mb-1">Kategori</label>
                  <select type="text" class="form-select" id="category" name="category">
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
                  <label for="deskripsi" class="mb-1">Deskripsi</label>
                  <input type="text" class="form-control" id="deskripsi" name="deskripsi">
                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-lg-6">
                <div class="form-group mb-3">
                  <label for="price" class="mb-1">Total</label>
                  <input class="form-control" id="price" name="price" autocomplete="off">

                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group mb-3">
                  <label for="payment_method" class="mb-1">Metode</label>
                  <select class="form-select" id="payment_method" name="payment_method" autocomplete="off">
                  </select>

                </div>
              </div>
            </div>

          </div>

          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
              Batal
            </button>
            <button class="btn btn-primary" id="update" type="submit">
              Simpan
            </button>
          </div>
        </div>
      </div>
    </div>
