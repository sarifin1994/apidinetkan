<!-- Modal Show -->
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">TAMBAH RESELLER</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="form-group mb-3">
                    <label for="name" class="mb-1">NAMA RESELLER <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="" autocomplete="off" required>
                </div>

                <div class="form-group mb-3">
                    <label for="wa" class="mb-1">NOMOR WA <small class="text-danger">*</small></label>
                    <input type="number" class="form-control" id="wa" name="wa" placeholder="" autocomplete="off" required>
                </div>

                    <label for="kode_area" class="mb-1">KODE AREA</label>
                    <div class="form-group mb-3" style="display:grid">
                        <select class="form-control select2" id="kode_area" name="kode_area" autocomplete="off" required>
                            <option value="">-- Pilih Kode Area --</option>
                            @forelse ($areas as $area)
                                <option value="{{ $area->kode_area }}">{{ $area->kode_area }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="nas" class="mb-1">NAS</label>
                        <select class="form-select" id="nas" name="nas" autocomplete="off"
                            data-placeholder="Pilih Nas">
                            <option value="">all</option>
                            @forelse ($nas as $nas)
                                <option value="{{ $nas->ip_router }}">{{ $nas->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="profile" class="mb-1">PROFILE</label>
                        <select class="form-select" id="profile" name="profile[]" autocomplete="off"
                            multiple="multiple">
                            @forelse ($profiles as $profile)
                                <option value="{{ $profile->name }}">{{ $profile->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="cetak" class="mb-1">GENERATE VOUCHER</label>
                        <select class="form-select" id="cetak" name="cetak" autocomplete="off">
                                <option value="1">YA</option>
                                <option value="0">TIDAK</option>
                        </select>
                    </div>

                    <span class="text-sm">Setelah berhasil membuat data reseller, silakan buat data loginnya di menu <a class="text-primary" href="/user/" target="_blank">Users</a></span>


                    

            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" type="button" data-bs-dismiss="modal">
                    Batal
                </button>
                <button class="btn btn-success" id="store" type="submit">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
