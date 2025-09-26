<!-- Modal Show -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Edit User</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <input type="hidden" id="user_id">
                    <label for="name_edit" class="mb-1">Nama Lengkap <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="name_edit" name="name_edit" placeholder=""
                        autocomplete="off" required>
                </div>
                <div class="form-group mb-3">
                    <label for="email_edit" class="mb-1">Alamat Email <small class="text-danger">*</small></label>
                    <input type="email" class="form-control" id="email_edit" name="email_edit" placeholder=""
                        autocomplete="off" required>
                </div>
                <div class="form-group mb-3">
                    <label for="whatsapp_edit" class="mb-1">Nomor Whatsapp <small
                            class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="whatsapp_edit" name="whatsapp_edit" placeholder=""
                        autocomplete="off" required>
                </div>
                @if(multi_auth()->role === 'Owner')
                <div class="form-group mb-3">
                    <label for="license_edit" class="mb-1">Lisensi <small class="text-danger">*</small></label>
                    <select class="form-select" id="license_edit">
                        @foreach ($license as $row)
                        <option value="{{$row->id}}">{{$row->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="next_due_edit" class="mb-1">Jatuh Tempo <small class="text-danger">*</small></label>
                    <input type="date" class="form-control" id="next_due_edit" name="next_due_edit" placeholder=""
                        autocomplete="off">
                </div>
                <div class="form-group mb-3">
                    <label for="status_edit" class="mb-1">Status <small class="text-danger">*</small></label>
                    <select class="form-select" id="status_edit">
                        <option value="1">Aktif</option>
                        <option value="3">Expired</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="discount_edit" class="mb-1">Discount <small
                            class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="discount_edit" name="discount_edit" placeholder=""
                        autocomplete="off" required>
                </div>
                @endif
                <hr>
                <div class="form-group mb-3">
                    <label for="username_edit" class="mb-1">Username <small class="text-danger">*</small></label>
                    <input type="text" class="form-control" id="username_edit" name="username_edit" placeholder=""
                        autocomplete="off" disabled>
                </div>
                <div class="form-group mb-3">
                    <label for="password_edit" class="mb-1">Password <small class="text-danger">*</small></label>
                    <input type="password" class="form-control" id="password_edit" name="password_edit"
                        placeholder="Kosongkan jika tidak ingin mengubah password" autocomplete="off" required>
                </div>

                {{-- <div class="form-group mb-3">
                    <label for="role_edit" class="mb-1">Level User <small class="text-danger">*</small></label>
                    <select class="form-select" id="role_edit">
                        <option value="Admin" disabled>Admin</option>
                        <option value="Teknisi">Teknisi</option>
                        <option value="Kasir">Kasir</option>
                    </select>
                </div> --}}

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="update" type="submit">
                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>
