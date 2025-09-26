    <!-- Modal Show -->
    <div class="modal fade" id="create_expense" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Create Pengeluaran</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="tanggal_ce" class="mb-1">Tanggal <small class="text-danger">*</small></label>
                                <input type="date" class="form-control" id="tanggal_ce" name="tanggal_ce">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="kategori_ce" class="mb-1">Kategori <small class="text-danger">*</small></label>
                                <select type="text" class="form-select" id="kategori_ce" name="kategori_ce">
                                    <option value="">- Pilih Kategori -</option>
                                    @forelse ($kategori_pengeluaran as $kategori)
                                        <option value="{{ $kategori->category }}">{{ $kategori->category }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="deskripsi_ce" class="mb-1">Deskripsi <small class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="deskripsi_ce" name="deskripsi_ce">
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="nominal_ce" class="mb-1">Nominal <small class="text-danger">*</small></label>
                                <input class="form-control" id="nominal_ce" name="nominal_ce" autocomplete="off">

                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="metode_ce" class="mb-1">Metode <small class="text-danger">*</small></label>
                                <select class="form-select" id="metode_ce" name="metode_ce"
                                    autocomplete="off">
                                    <option value="Cash">Cash</option>
                                    <option value="Transfer">Transfer</option>
                                </select>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger me-2" type="button" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-primary" id="store_expense" type="submit">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>
