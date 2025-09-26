    <!-- Modal Show -->
    <div class="modal fade" id="show_invoice" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Edit Invoice</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="invoice_id" name="invoice_id">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="no_invoice" class="mb-1">Nomor Invoice</label>
                                <input type="text" class="form-control" id="no_invoice" name="no_invoice">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="full_name" class="mb-1">Nama Lengkap</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="invoice_date" class="mb-1">Tanggal Invoice</label>
                                <input type="date" class="form-control" id="invoice_date" name="invoice_date"
                                    disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="due_date" class="mb-1">Tanggal Jatuh Tempo</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="subscribe" class="mb-1">Periode Langganan</label>
                                <input type="text" class="form-control" id="subscribe" name="subscribe" disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="validity" class="mb-1">Masa Aktif Langganan</label>
                                <select class="form-select" id="validity" name="validity" autocomplete="off" disabled>
                                    <option selected="selected">
                                    </option>
                                </select>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="payment_type" class="mb-1">Tipe Pembayaran</label>
                                <select class="form-select" id="payment_type" name="payment_type" autocomplete="off"
                                    disabled>
                                    <option selected="selected">
                                    </option>
                                </select>

                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="billing_period" class="mb-1">Siklus Tagihan</label>
                                <select class="form-select" id="billing_period" name="billing_period" autocomplete="off"
                                    disabled>
                                    <option selected="selected">
                                    </option>
                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="item" class="mb-1">Items</label>
                                <input type="text" class="form-control" id="item" name="item"
                                    placeholder="" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="amount" class="mb-1">Amount</label>
                                <input type="text" class="form-control" id="amount" name="amount"
                                    placeholder="" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="ppn" class="mb-1">PPN<small> %</small></label>
                                <input type="number" class="form-control" id="ppn" name="ppn"
                                    placeholder="" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="discount" class="mb-1">Discount<small> (Masukkan nominal tanpa titik)</small></label>
                                <input type="number" class="form-control" id="discount" name="discount"
                                    placeholder="" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="payment_total" class="mb-1">Payment Total</label>
                                <input type="text" disabled class="form-control" id="payment_total"
                                    name="payment_total" placeholder="" autocomplete="off">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-link" type="button" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-primary" id="editInvoice" type="submit">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>
