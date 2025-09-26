    <!-- Modal Show -->
    <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Tambah Tiket</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- <span class="fw-bold">
                        <span class="material-symbols-outlined">
                            chat_info
                        </span> Tipe Gangguan &nbsp;&nbsp;</span>
                    <div class="row mt-1 mb-5">
                        <div class="col-md-12">
                            <input id="active_now" required="" type="radio" name="tipe_gangguan" value="individu"
                                checked>
                                <label class="form-check-label me-2" for="on_process">
                                    <span class="material-symbols-outlined">
                                        person
                                    </span> Individu</label>
                            <input id="on_process" required="" type="radio" name="tipe_gangguan" value="massal">
                            <label class="form-check-label" for="on_process">
                                <span class="material-symbols-outlined">
                                    groups
                                </span> Massal</label>

                        </div>
                    </div>
                    <hr> --}}
                    <div id="individu">
                    <div class="form-group mb-3">
                        <label for="id_pelanggan" class="mb-1">ID / Nama Pelanggan <small
                                class="text-danger">*</small></label>
                        <select class="form-select" id="id_pelanggan">
                            @forelse ($pelanggan as $row)
                                <option value="{{ $row->id }}">{{ $row->id_pelanggan }} - {{ $row->full_name }}
                                </option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="jenis_gangguan" class="mb-1">Jenis Gangguan <small
                                class="text-danger">*</small></label>
                        <select class="form-select" id="jenis_gangguan" name="jenis_gangguan" autocomplete="off"
                            required>
                            <option value="">- Pilih Jenis Gangguan -</option>
                            <option value="Loss Merah">Loss Merah</option>
                            <option value="Redaman Tinggi">Redaman Tinggi</option>
                            <option value="SSID Hilang">SSID Hilang</option>
                            <option value="SSID Lemah">SSID Lemah</option>
                            <option value="ONT Mati">ONT Mati</option>
                            <option value="PON Blinking / Mati">PON Blinking</option>
                            <option value="Internet Offline">Internet Offline</option>
                            <option value="Internet Lemot">Internet Lemot</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="teknisi" class="mb-1">Tugaskan Teknisi <small
                                class="text-danger">*</small></label>
                        <select class="form-select" id="teknisi" name="teknisi" autocomplete="off" required>
                            <option value="">- Pilih Teknisi -</option>
                            @forelse ($teknisi as $teknisi)
                                <option value="{{ $teknisi->username }}">{{ $teknisi->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="prioritas" class="mb-1">Prioritas <small class="text-danger">*</small></label>
                        <select class="form-select" id="prioritas" name="prioritas" autocomplete="off" required>
                            <option value="rendah">Rendah</option>
                            <option value="normal" selected>Normal</option>
                            <option value="tinggi">Tinggi</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="note" class="mb-1">Note / Keterangan <small></small></label>
                        <textarea name="note" id="note" style="height: 90px" class="form-control" placeholder="" autocomplete="off"
                            required></textarea>
                    </div>
                    <div class="row px-2">
                        <table class="table table-striped">
                            <input type="hidden" id="ppp_id">
                            <tbody style="font-size:12px">
                                <tr>
                                    <td style="width:30%">Status Internet</td>
                                    <td>: <span id="fill_internet"></span></td>
                                </tr>
                                <tr>
                                    <td>IP Address</td>
                                    <td>: <span id="fill_ip"></span></td>
                                </tr>
                            </tbody>
                        </table>
                        <span><span class="material-symbols-outlined">
                                info
                            </span> Tiket gangguan akan dikirim melalui whatsapp ke nomor pelanggan dan group kerja /
                            group gangguan</span>

                    </div>
                    </div>

                    {{-- <div id="massal">
                        <div class="form-group mb-3">
                            <label for="kode_area" class="mb-1">POP / Area <small
                                    class="text-danger">*</small></label>
                            <select class="form-select" id="kode_area">
                                @forelse ($areas as $row)
                                    <option value="{{ $row->id }}">{{ $row->kode_area }} - {{ $row->deskripsi }}
                                    </option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div> --}}

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="store" type="submit">
                        Submit tiket
                    </button>
                </div>
            </div>
        </div>
    </div>
