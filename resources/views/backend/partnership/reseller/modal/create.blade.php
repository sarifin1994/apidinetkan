<!-- Modal Show -->
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Tambah Reseller</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="nama_reseller" class="mb-1">Nama Reseller <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="nama_reseller" name="nama_reseller" placeholder=""
                        autocomplete="off" required>
                    <small id="helper" class="form-text text-muted">Isi nama reseller, misalnya JOCONG</small>
                </div>
                <div class="form-group mb-3">
                    <label for="nomor_wa" class="mb-1">Nomor WA </label>
                    <input type="number" class="form-control" id="nomor_wa" name="nomor_wa" placeholder=""
                        autocomplete="off" required>
                    <small id="helper" class="form-text text-muted">Isi format berikut, contoh 081220313001</small>
                </div>
                <hr>
                <div class="form-group mb-3">
                    <label for="id_reseller" class="mb-1">ID Reseller <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="id_reseller" name="id_reseller" placeholder=""
                        autocomplete="off" required disabled>
                        {{-- <small id="helper" class="form-text text-muted">Isi angka minimal 5 digit, maksimal 10 digit</small> --}}

                </div>
                <div class="form-group mb-3">
                    <label for="password" class="mb-1">Password Reseller <small class="text-danger">*</small></label>
                    <input type="password" class="form-control" id="password" name="password" placeholder=""
                        autocomplete="off" required>
                </div>
                <hr>
                <div class="form-group mb-3">
                    <label for="login" class="mb-1">Izin Login Dashboard <small class="text-danger">*</small></label>
                    <select class="form-select" id="login" name="login" autocomplete="off">
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
                    <label for="profile" class="mb-1">Pilihan Paket <small class="text-danger">*</small></label>
                    <select class="form-select" id="profile" name="profile" autocomplete="off" multiple>
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
                <button class="btn btn-primary text-white" id="store" type="submit">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
