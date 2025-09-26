<!-- Modal Show -->
<div class="modal fade" id="create" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">TAMBAH TICKET GANGGUAN</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="tipe" class="mb-1">Tipe Gangguan</label>
                            <select class="form-select" id="tipe" name="tipe" autocomplete="off">
                                <option value="1">Individual</option>
                                <option value="2">Massal</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr />
                <div id="show_individual">
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="member_id" class="mb-1">Nama Pelanggan <small class="text-danger">*</small></label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-select" id="member_id" name="member_id" autocomplete="off"
                                    data-placeholder="Pilih Nama Pelanggan">
                                    <option value=""></option>
                                    @forelse ($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="jenis" class="mb-1">Jenis Gangguan <small class="text-danger">*</small></label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-select" id="jenis" name="jenis" autocomplete="off" data-placeholder="Pilih Jenis Gangguan">
                                    <option value=""></option>
                                    <option value="Loss Merah">Loss Merah</option>
                                    <option value="Redaman Unspec">Redaman Unspec</option>
                                    <option value="ONT Mati / Rusak">ONT Mati / Rusak</option>
                                    <option value="PON Blinking / Mati">PON Blinking / Mati</option>
                                    <option value="Internet Offline">Internet Offline</option>
                                    <option value="Internet Lag">Internet Lag</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>

                    </div>
                   
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="note" class="mb-1">Note <small class="text-sm">Optional</small></label>
                                <textarea name="note" id="note" style="height: 90px" class="form-control" placeholder="" autocomplete="off" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row px-2">
                        <table class="table table-striped">
                            <input type="hidden" id="ppp_id">
                            <tbody style="font-size:15px">
                                <tr>
                                    <td style="width:30%">Nama Lengkap</td>
                                    <td>: <span id="fill_name"></span></td>
                                </tr>
                                <tr>
                                    <td>Kode Area</td>
                                    <td>: <span id="fill_area"></span></td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>: <span id="fill_alamat"></span></td>
                                </tr>
                                <tr>
                                    <td>Status Internet</td>
                                    <td>: <span id="fill_internet"></span></td>
                                </tr>
                                <tr>
                                    <td>IP Address</td>
                                    <td>: <span id="fill_ip"></span></td>
                                </tr>
                                <tr>
                                    <td>Redaman ONU</td>
                                    @if(session()->has('namaolt'))
                                    <td>: <a href="/olt/device/dashboard" target="_blank">Lihat Redaman</a>
                                    @else
                                    <td>: <a href="/olt" target="_blank" class="text-primary">Lihat Redaman</a>
                                    @endif
                                </tr>
                                <input type="hidden" id="fill_wa">
                                <input type="hidden" id="fill_odp">
                            </tbody>
                        </table>
                        <span>SAAT DISUBMIT DATA TICKET GANGGUAN AKAN MASUK KE GRUP TELEGRAM GANGGUAN<br>HARAP SETTING TERLEBIH DAHULU GROUP CHAT ID DI MENU <a href="/telegram" class="text-primary">TELEGRAM</a></span>

                    </div>

                    
                </div>

                <div id="show_massal" style="display:none">
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="kode_area" class="mb-1">Kode Area <small class="text-danger">*</small></label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-control select2" id="kode_area" name="kode_area" autocomplete="off"
                                    data-placeholder="Pilih Kode Area">
                                    <option value=""></option>
                                    @forelse ($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->kode_area }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="jenis_massal" class="mb-1">Jenis Gangguan <small class="text-danger">*</small></label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-select" id="jenis_massal" name="jenis_massal" autocomplete="off" data-placeholder="Pilih Jenis Gangguan">
                                    <option value=""></option>
                                    <option value="Jalur Optik Putus">Jalur Optik Putus</option>
                                    <option value="Jalur Optik Bending">Jalur Optik Bending</option>
                                    <option value="ODP Loss">ODP Loss</option>
                                    <option value="OLT Mati / Error">OLT Mati / Error</option>
                                    <option value="SFP Mati / Rusak">SFP Mati / Error</option>
                                    <option value="Switch Mati / Rusak">Switch Mati / Error</option>
                                    <option value="HTB Mati / Rusak">HTB Mati / Error</option>
                                    <option value="Radio Mati / Rusak">Radio Disconnect</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="note_massal" class="mb-1">Note <small class="text-sm">Optional</small></label>
                                <textarea name="note_massal" id="note_massal" style="height: 90px" class="form-control" placeholder="" autocomplete="off" required></textarea>
                            </div>
                        </div>
                    </div>
                    <span>SAAT DISUBMIT DATA TICKET GANGGUAN AKAN MASUK KE GRUP TELEGRAM GANGGUAN<br>HARAP SETTING TERLEBIH DAHULU GROUP CHAT ID DI MENU <a href="/telegram" class="text-primary">TELEGRAM</a></span>

                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                    Batal
                </button>
                <button class="btn btn-primary" id="store" type="submit">
                    Submit
                </button>
            </div>
        </div>
    </div>
</div>
