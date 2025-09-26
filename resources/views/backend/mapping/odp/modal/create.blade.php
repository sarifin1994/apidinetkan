    <!-- Modal Show -->
    <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Create ODP</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="errorMsgntainer"></div>
                    <label for="kode_area_id" class="mb-1">POP <small class="text-danger">*</small></label>
                    <div class="form-group mb-3">
                        <select class="form-select" id="kode_area_id" name="kode_area_id"
                            autocomplete="off" required>
                            @forelse ($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->kode_area }} - {{ $area->deskripsi }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="kode_odp" class="mb-1">Kode ODP <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="kode_odp" name="kode_odp" placeholder=""
                            autocomplete="off" required>
                        <small id="helper" class="form-text text-muted">Isi format berikut RCB-001, RCB-002,
                            dst</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="deskripsi" class="mb-1">Deskripsi</label>
                        <input type="text" class="form-control" id="deskripsi" name="deskripsi" placeholder="" autocomplete="off" required>
                        <small id="helper" class="form-text text-muted">Isi deskripsi area, misalnya SAMPING MASJID ALMUKHTAR</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="port_odp" class="mb-1">Jml Port <small class="text-danger">*</small></label>
                        <input type="number" class="form-control" id="port_odp" name="port_odp" placeholder=""
                            autocomplete="off" required>
                        <small id="helper" class="form-text text-muted">Isi port splitter odp, contoh: 8</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="latitude" class="mb-1">Latitude</label>
                        <input type="text" class="form-control" id="latitude" name="latitude" placeholder=""
                            autocomplete="off">
                        <small id="helper" class="form-text text-muted">Opsional. Isi koordinat google maps, contoh:
                            -812381273271</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="longitude" class="mb-1">Longitude</label>
                        <input type="text" class="form-control" id="longitude" name="longitude" placeholder=""
                            autocomplete="off">
                        <small id="helper" class="form-text text-muted">Opsional. Isi koordinat google maps, contoh:
                            -12284717277</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="store" type="submit">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>