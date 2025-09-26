    {{-- <!-- Modal Show -->
    <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Tambah Transaksi</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <input type="date" class="form-control" id="tanggal_c" name="tanggal_c">
                                <label for="tanggal_c">Tanggal</label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <select type="text" class="form-select" id="type_c" name="type_c">
                                    <option value="3">Income</option>
                                    <option value="6">Expense</option>
                                </select>
                                <label for="type_c">Type</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <select type="text" class="form-select" id="item_c" name="item_c">
                                    <option value="Invoice" disabled>Invoice</option>
                                    <option value="Hotspot" disabled>Hotspot</option>
                                    <option value="Regist PSB">Regist PSB (Income)</option>
                                    <option value="Fee Mitra">Fee Mitra (Expense)</option>
                                    <option value="Operasional">Operasional (Expense)</option>
                                    <option value="Belanja">Belanja (Expense)</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <label for="item_c">Kategori</label>
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <input type="text" class="form-control" id="deskripsi_c" name="deskripsi_c">
                                <label for="deskripsi_c">Deskripsi</label>
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <input class="form-control" id="price_c" name="price_c" autocomplete="off">
                                <label for="price_c">Total</label>

                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <select class="form-select" id="payment_method_c" name="payment_method_c" autocomplete="off">
                                <option value="1">Cash</option>
                                <option value="2">Transfer</option>
                                </select>
                                <label for="payment_method_c">Payment Method</label>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-primary" id="store" type="submit">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div> --}}
