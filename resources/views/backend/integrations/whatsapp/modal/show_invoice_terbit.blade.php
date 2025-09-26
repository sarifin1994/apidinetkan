    <!-- Modal Show -->
    <div class="modal fade" id="show_invoice_terbit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Invoice Terbit</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">


                    <div class="row">
                        <div class="col-lg-7">
                            <div class="form-group mb-3">
                                <input type="hidden" id="id">
                                <label for="invoice_terbit" class="mb-1">Format Message<small></small></label>
                                <textarea name="invoice_terbit" id="invoice_terbit" style="height: 450px" class="form-control" placeholder="invoice_terbit"
                                    autocomplete="off" required></textarea>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="form-group mb-3">
                                <b class="text-sm">Kode Variabel Invoice Terbit</b><br><br>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[nama_lengkap]</span> Nama Lengkap</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[id_pelanggan]</span> ID Pelanggan</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[username]</span> Username PPPoE</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[password]</span> Password PPPoE</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[paket_internet]</span> Paket Internet</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[no_invoice]</span> Nomor Invoice</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[tgl_invoice]</span> Tanggal Invoice Terbit</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[jumlah]</span> Harga Paket Internet</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[ppn]</span> PPN %</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[discount]</span> Discount %</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[total]</span> Total dengan PPN & Discount</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[periode]</span> Periode Invoice</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[jth_tempo]</span> Jatuh Tempo</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[payment_midtrans]</span> Link Metode Pembayaran Payment Gateway</li><br>
                                <span class="text-sm"> Gunakan *<b>teks</b>* agar text tebal / bold</span><br>
                                <span class="text-sm"> Gunakan _<i>teks</i>_ agar text miring</span>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-primary" id="updateInvoiceTerbit" type="submit">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
