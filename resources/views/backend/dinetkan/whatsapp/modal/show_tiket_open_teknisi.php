    <!-- Modal Show -->
    <div class="modal fade" id="show_tiket_open_teknisi" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Tiket Gangguan Open (Untuk Teknisi)</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">


                    <div class="row">
                        <div class="col-lg-7">
                            <div class="form-group mb-3">
                                <input type="hidden" id="id">
                                <textarea name="tiket_open_teknisi" id="tiket_open_teknisi" style="height: 450px" class="form-control" placeholder=""
                                    autocomplete="off" required></textarea>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="form-group mb-3">
                                <b class="text-sm">Tiket Gangguan Open (Untuk Teknisi)</b><br><br>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[tanggal_laporan]</span> Tanggal Tiket Dibuat</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[tanggal_update]</span> Tanggal Update Tiket</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[nomor_tiket]</span> Nomor Tiket Gangguan</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[jenis_gangguan]</span> Jenis Gangguan</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[prioritas]</span> Prioritas Gangguan</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[status_internet]</span> Status Internet</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[ip]</span> IP Address</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[teknisi]</span> Nama Teknisi</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[nomor_teknisi]</span> Nomor Teknisi</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[nama_lengkap]</span> Nama Lengkap</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[id_pelanggan]</span> ID Pelanggan</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[nomor_wa]</span> Nomor Pelanggan</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[pop]</span> POP Pelanggan</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[odp]</span> ODP Pelanggan</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[alamat]</span> Alamat Pelanggan</li>
                                <li class="text-sm" style="margin-left:10px"><span class="text-danger">[note]</span> Keterangan</li><br>
                                <span class="text-sm"> Gunakan *<b>teks</b>* agar text tebal / bold</span><br>
                                <span class="text-sm"> Gunakan _<i>teks</i>_ agar text miring</span>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="updateOpenTeknisi" type="submit">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>
