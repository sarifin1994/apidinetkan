<!-- Modal Show -->
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Create Mitra</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="nama_mitra" class="mb-1">Nama Mitra <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="nama_mitra" name="nama_mitra" placeholder=""
                        autocomplete="off" required>
                    <small id="helper" class="form-text text-muted">Isi nama mitra, misalnya ACENG</small>
                </div>
                <div class="form-group mb-3">
                    <label for="nomor_wa" class="mb-1">Nomor WA <small class="text-danger">*</small></label>
                    <input type="number" class="form-control" id="nomor_wa" name="nomor_wa" placeholder=""
                        autocomplete="off" required>
                    <small id="helper" class="form-text text-muted">Isi format berikut, contoh 081220313001</small>
                </div>
                <hr>
                <div class="form-group mb-3">
                    <label for="id_mitra" class="mb-1">ID Mitra <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="id_mitra" name="id_mitra" placeholder=""
                        autocomplete="off" required disabled>
                        {{-- <small id="helper" class="form-text text-muted">Isi angka minimal 5 digit, maksimal 10 digit</small> --}}

                </div>
                <div class="form-group mb-3">
                    <label for="pass_mitra" class="mb-1">Password Mitra <small class="text-danger">*</small></label>
                    <input type="password" class="form-control" id="pass_mitra" name="pass_mitra" placeholder=""
                        autocomplete="off" required>
                </div>
                {{-- <small>Login akun mitra di dashboard <a href="https://my.frradius.com" target="_blank">my.frradius.com</a></small> --}}
                <hr>
                <div class="form-group mb-3">
                    <label for="login" class="mb-1">Izin Login Dashboard <small class="text-danger">*</small></label>
                    <select class="form-select" id="login" name="login" autocomplete="off">
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="user" class="mb-1">Izin Create Pelanggan Baru <small class="text-danger">*</small></label>
                    <select class="form-select" id="user" name="user" autocomplete="off">
                    <option value="1">Ya</option>
                    <option value="0">Tidak</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="billing" class="mb-1">Izin Menu Billing <small class="text-danger">*</small></label>
                    <select class="form-select" id="billing" name="billing" autocomplete="off">
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
                        
                        @forelse ($licensedinetkan as $lic)
                            <option value="{{ $lic->id_dinetkan }}">{{ $lic->name }}</option>
                        @empty
                        @endforelse
                        
                    </select>
                    <small id="helper" class="form-text text-muted">Pilih paket yang akan ditampilkan di dashboard Mitra</small>
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
