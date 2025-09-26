<!-- Modal Show -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Edit Mitra</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <input type="hidden" id="reseller_id">
                    <label for="nama_reseller_edit" class="mb-1">Nama Reseller <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="nama_reseller_edit" name="nama_reseller_edit" placeholder=""
                        autocomplete="off" required>
                    <small id="helper" class="form-text text-muted">Isi nama reseller, misalnya JOCONG</small>
                </div>
                <div class="form-group mb-3">
                    <label for="nomor_wa_edit" class="mb-1">Nomor WA </label>
                    <input type="number" class="form-control" id="nomor_wa_edit" name="nomor_wa_edit" placeholder=""
                        autocomplete="off" required>
                    <small id="helper" class="form-text text-muted">Isi format berikut, contoh 081220313001</small>
                </div>
                <hr>
                <div class="form-group mb-3">
                    <label for="id_reseller_edit" class="mb-1">ID Mitra <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="id_reseller_edit" name="id_reseller_edit" placeholder=""
                        autocomplete="off" required disabled>
                        {{-- <small id="helper" class="form-text text-muted">Isi angka minimal 5 digit, maksimal 10 digit</small> --}}

                </div>
                <div class="form-group mb-3">
                    <label for="pass_reseller_edit" class="mb-1">Password Mitra <small class="text-danger">*</small></label>
                    <input type="password" class="form-control" id="pass_reseller_edit" name="pass_reseller_edit" placeholder="Kosongkan jika tidak ingin merubah password"
                        autocomplete="off" required >
                </div>
                <hr>
                <div class="form-group mb-3">
                    <label for="login_edit" class="mb-1">Izin Login Dashboard <small class="text-danger">*</small></label>
                    <select class="form-select" id="login_edit" name="login_edit" autocomplete="off">
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="cetak" class="mb-1">Izin Create Voucher <small class="text-danger">*</small></label>
                    <select class="form-select" id="cetak" name="cetak" autocomplete="off">
                    <option value="1">Ya</option>
                    <option value="0">Tidak</option>
                    </select>
                </div>
                <hr>
                <div class="form-group mb-3">
                    <label for="profile_edit" class="mb-1">Pilihan Paket <small class="text-danger">*</small></label>
                    <select class="form-select" id="profile_edit" name="profile_edit" autocomplete="off" multiple>
                        @forelse ($profiles as $profile)
                            <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                        @empty
                        @endforelse
                    </select>
                    <small id="helper" class="form-text text-muted">Pilih paket yang akan ditampilkan di dashboard Reseller</small>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary text-white" id="update" type="submit">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
