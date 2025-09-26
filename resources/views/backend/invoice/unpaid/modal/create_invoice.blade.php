    <!-- Modal Show -->
    <div class="modal fade" id="create_invoice" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Manual Invoice</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="fill_id_pelanggan">
                        <input type="hidden" id="fill_wa">
                        <input type="hidden" id="fill_mitra_id">
                        <input type="hidden" id="fill_komisi">
                        <div class="col-lg-6">
                            <label for="pelanggan_id" class="mb-1">Nama Pelanggan</label>
                            <div class="form-group mb-3">
                                <select class="form-select" id="pelanggan_id">
                                    @forelse ($ppp as $row)
                                        <option value="{{ $row->id }}">{{ $row->id_pelanggan }} -
                                            {{ $row->full_name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="fill_area" class="mb-1">POP</label>
                                <input type="text" class="form-control" id="fill_area" name="fill_area" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="fill_username" class="mb-1">Internet Username</label>
                                <input type="text" class="form-control" id="fill_username" name="fill_username"
                                    disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="fill_profile" class="mb-1">Internet Profile</label>
                                <input type="text" class="form-control" id="fill_profile" name="fill_profile"
                                    disabled>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div id="show_payment" style="display:none">
                        <div class="row mt-3">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="fill_invoice_date" class="mb-1">Tanggal Invoice</label>
                                    <input type="date" class="form-control" id="fill_invoice_date"
                                        name="fill_invoice_date" disabled>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="fill_due_date" class="mb-1">Tanggal Jatuh Tempo</label>
                                    <input type="date" class="form-control" id="fill_due_date" name="fill_due_date"
                                        disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="fill_subscribe" class="mb-1">Periode Langganan</label>
                                    <input type="text" class="form-control" id="fill_subscribe" name="fill_subscribe"
                                        disabled>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="fill_validity" class="mb-1">Masa Aktif Langganan</label>
                                    <select class="form-select" id="fill_validity" name="fill_validity"
                                        autocomplete="off" disabled>
                                        <option selected="selected">
                                        </option>
                                    </select>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="fill_payment_type" class="mb-1">Tipe Pembayaran</label>
                                    <select class="form-select" id="fill_payment_type" name="fill_payment_type"
                                        autocomplete="off" disabled>
                                        <option selected="selected">
                                        </option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="fill_billing_period" class="mb-1">Siklus Tagihan</label>
                                    <select class="form-select" id="fill_billing_period" name="fill_billing_period"
                                        autocomplete="off" disabled>
                                        <option selected="selected">
                                        </option>
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="fill_item" class="mb-1">Items</label>
                                    <input type="text" class="form-control" id="fill_item" name="fill_item"
                                        autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="fill_amount" class="mb-1">Amount</label>
                                    <input type="text" class="form-control" id="fill_amount" name="fill_amount"
                                        autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="fill_ppn" class="mb-1">PPN<small> %</small></label>
                                    <input type="number" class="form-control" id="fill_ppn" name="fill_ppn"
                                        autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="fill_discount" class="mb-1">Discount<small> (Masukkan nominal tanpa
                                            titik)</small></label>
                                    <input type="number" class="form-control" id="fill_discount"
                                        name="fill_discount" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="fill_payment_total" class="mb-1">Payment Total</label>
                                    <input type="text" disabled class="form-control" id="fill_payment_total"
                                        name="fill_payment_total" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-link" type="button" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button class="btn btn-primary" type="submit" id="generate_invoice">
                        <i class="ti ti-notes me-1"></i> Create invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
