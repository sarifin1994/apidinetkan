<!-- Modal Show -->
<div class="modal fade" id="daftar" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Daftar WA Gateway</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="mpwa_server" class="mb-1">Server <small class="text-danger">*</small></label>
                    <select class="form-select" id="mpwa_server" name="mpwa_server" autocomplete="off" required>
                            @forelse ($wa_server as $wa)
                                <option value="{{ $wa->wa_url }}">{{ $wa->wa_url }}</option>
                            @empty
                            @endforelse
                            {{-- <option value="mpwa.frradius.com">mmpwa.frradius.com</option> --}}
                        </select>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="no_wa_daftar" class="mb-1">Nomor Pengirim <small class="text-danger">*</small></label>
                    <input type="number" class="form-control" id="no_wa_daftar" name="no_wa_daftar" placeholder="" autocomplete="off" required>
                    <small id="helper" class="form-text text-muted">Masukan nomor whatsapp pengirim dengan format kode negara, contoh <b>62</b>85155112192</small>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="action_daftar" type="submit">
                    Daftar sekarang
                </button>
            </div>
        </div>
    </div>
</div>
