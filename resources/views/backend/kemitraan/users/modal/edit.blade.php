<!-- Modal Show -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Edit Pelanggan</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="user_id">
                    <input type="hidden" id="kode_area_id">
                    <input type="hidden" id="kode_odp_id">
                    <input type="hidden" id="profile_id">
                    <span class="fw-bold">
                        <i class="ti ti-circle-number-1"></i> Data Secret</span>
                    <hr>
                    <!-- ... potongan kode lainnya tidak diubah ... -->
                    <div class="mt-4">
                        <span class="fw-bold">
                            <i class="ti ti-circle-number-2"></i> Data Pelanggan</span>
                    </div>
                    <hr>
                    <!-- ... potongan kode lainnya tidak diubah ... -->
                    <div class="mt-4">
                        <span class="fw-bold">
                            <i class="ti ti-circle-number-3"></i> Data Alamat</span>
                    </div>
                    <hr>
                    <!-- ... potongan kode lainnya tidak diubah ... -->
                    @if (multi_auth()->role === 'Admin')
                        <div class="mt-4">
                            <span class="fw-bold">
                                <i class="ti ti-circle-number-4"></i> Data Pembayaran</span>
                        </div>
                    <!-- ... potongan billing ... -->
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    @if(multi_auth()->role === 'Admin')
                    <div class="btn-group dropup">
                        <a class="btn btn-danger me-2 dropdown-toggle text-white" href="#" role="button" id="dropdownMenuLink"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-edit"></i> Action
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" id="enable">Aktifkan</a></li>
                            <li><a class="dropdown-item" id="disable">Suspend</a></li>
                            <li><a class="dropdown-item" id="regist">Proses Registrasi</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" id="delete">Hapus</a></li>
                        </ul>
                    </div>
                    @endif
                    <button class="btn btn-primary text-white" id="update" type="submit">
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
