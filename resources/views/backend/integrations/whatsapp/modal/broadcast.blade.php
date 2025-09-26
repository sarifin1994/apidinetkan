<!-- Modal Show -->
<div class="modal fade" id="broadcast" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">KIRIM BROADCAST</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="tipe" class="mb-1">Tipe Broadcast</label>
                            <select class="form-select" id="tipe" name="tipe" autocomplete="off">
                                <option value="">-- Pilih Tipe Broadcast --</option>
                                <option value="all">Semua Pelanggan</option>
                                <option value="byarea">Semua Pelanggan BY AREA</option>
                            </select>
                        </div>
                    </div>
                  
                </div>

                {{-- <input type="text" id="fill_wa"> --}}
                <hr />
                <div id="show_all" style="display:none">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="subject_all" class="mb-1">Subject</label>
                                <input type="text" class="form-control" id="subject_all" placeholder="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="message_all" class="mb-1">Message</label>
                                <textarea name="message_all" id="message_all" style="height: 150px" class="form-control" placeholder=""
                                    autocomplete="off" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row px-2">
                        <table class="table table-striped">
                            <tbody style="font-size:15px">
                                {{-- <tr>
                                    <td style="width:30%">Kode Area</td>
                                    <td>: <span id="fill_area"></span></td>
                                </tr> --}}
                                <tr>
                                    <td style="width:30%">Jumlah Area</td>
                                    <td>: <span id="fill_jmlarea"></span></td>
                                </tr>
                                <tr>
                                    <td>Jumlah Pelanggan</td>
                                    <td>: <span id="fill_jmlpelanggan_all"></span></td>
                                </tr>
                                {{-- <tr>
                                    <td>Status Internet</td>
                                    <td>: <span id="fill_internet"></span></td>
                                </tr>
                                <tr>
                                    <td>IP Address</td>
                                    <td>: <span id="fill_ip"></span></td>
                                </tr> --}}
                            </tbody>
                        </table>
                        <small class="text-sm text-danger">Harap gunakan dengan bijak! Kami tidak bertanggung jawab
                            apabila nomor whatsapp anda terblokir</small>
                    </div>


                </div>

                <div id="show_byarea" style="display:none">
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="kode_area" class="mb-1">Area / Wilayah</label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-select" id="kode_area" name="kode_area" autocomplete="off"
                                    data-placeholder="Pilih Kode Area">
                                    <option value=""></option>
                                    @forelse ($areas as $area)
                                        <option value="{{ $area->kode_area }}">{{ $area->kode_area }} - {{ $area->deskripsi }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>    
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="subject_area" class="mb-1">Subject</label>
                                <input type="text" class="form-control" id="subject_area" placeholder="">
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="message_area" class="mb-1">Message</label>
                                <textarea name="message_area" id="message_area" style="height: 150px" class="form-control" placeholder=""
                                    autocomplete="off" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row px-2">
                        <table class="table table-striped">
                            <tbody style="font-size:15px">
                                <tr>
                                    <td style="width:30%">Kode Area</td>
                                    <td>: <span id="fill_area"></span></td>
                                </tr>
                                <tr>
                                    <td>Jumlah Pelanggan</td>
                                    <td>: <span id="fill_jmlpelanggan_area"></span></td>
                                </tr>
                            </tbody>
                        </table>
                        <small class="text-sm text-danger">Harap gunakan dengan bijak! Kami tidak bertanggung jawab
                            apabila nomor whatsapp anda terblokir</small>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                    Batal
                </button>
                <button class="btn btn-primary" id="sendBroadcast" type="submit">
                    Kirim Broadcast
                </button>
            </div>
        </div>
    </div>
</div>
