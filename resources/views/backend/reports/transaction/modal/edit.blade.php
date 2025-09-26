    {{-- <!-- Modal Show -->
    <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Transaction Detail</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="transaction_id" name="transaction_id">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <input type="date" class="form-control" id="tanggal" name="tanggal" disabled>
                                <label for="tanggal">Tanggal</label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <select type="text" class="form-select" id="type" name="type" disabled>
                                    <option selected></option>
                                </select>
                                <label for="type">Type</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <select type="text" class="form-select" id="item" name="item">
                                    <option value="Invoice" disabled>Invoice</option>
                                    <option value="Hotspot" disabled>Hotspot</option>
                                    <option value="Regist PSB">Regist PSB (Income)</option>
                                    <option value="Fee Mitra">Fee Mitra (Expense)</option>
                                    <option value="Operasional">Operasional (Expense)</option>
                                    <option value="Belanja">Belanja (Expense)</option>
                                    <option value="Lainnya">Lainnya</option>

                                </select>
                                <label for="item">Kategori</label>
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <input type="text" class="form-control" id="deskripsi" name="deskripsi">
                                <label for="deskripsi">Deskripsi</label>
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <input class="form-control" id="price" name="price" autocomplete="off">
                                <label for="price">Total</label>

                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <select class="form-select" id="payment_method" name="payment_method" autocomplete="off">
                                </select>
                                <label for="payment_method">Payment Method</label>

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
    </div> --}}
