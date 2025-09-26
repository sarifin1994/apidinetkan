<!-- Modal Show -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">EDIT RESELLER</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id">
                <div class="form-group mb-3">
                    <label for="name_edit" class="mb-1">NAMA RESELLER</label>
                    <input type="text" class="form-control" id="name_edit" name="name_edit" placeholder=""
                        autocomplete="off" required>
                </div>
                <div class="form-group mb-3">
                    <label for="wa_edit" class="mb-1">NOMOR WA</label>
                    <input type="text" class="form-control" id="wa_edit" name="wa_edit" placeholder=""
                        autocomplete="off" required>
                </div>
                
                <label for="kode_area_edit" class="mb-1">KODE AREA</label>
                <div class="form-group mb-3">
                    <select class="form-select" id="kode_area_edit" name="kode_area" placeholder=""
                        autocomplete="off" required>
                        @forelse ($areas as $area)
                            <option value="{{ $area->kode_area }}">{{ $area->kode_area }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="nas_edit" class="mb-1">NAS</label>
                    <select class="form-select" id="nas_edit" name="nas_edit" autocomplete="off"
                        data-placeholder="Pilih Nas">
                        <option value="">all</option>
                        @forelse ($nas as $nas)
                            <option value="{{ $nas->ip_router }}">{{ $nas->name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="profile_edit" class="mb-1">PROFILE</label>
                    <select class="form-select" id="profile_edit" name="profile_edit[]" autocomplete="off"
                        multiple="multiple">
                        @forelse ($profiles as $profile)
                            <option value="{{ $profile->name }}">{{ $profile->name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="cetak_edit" class="mb-1">GENERATE VOUCHER</label>
                    <select class="form-select" id="cetak_edit" name="cetak_edit" autocomplete="off">
                            <option value="1">YA</option>
                            <option value="0">TIDAK</option>

                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" type="button" data-bs-dismiss="modal">
                    Batal
                </button>
                <button class="btn btn-success" id="update" type="submit">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
