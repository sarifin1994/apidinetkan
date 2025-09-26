    <!-- Modal Show -->
    <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal">Edit ODP</h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="odp_id">
                        <input type="hidden" id="kda_id" name="kode_area_id">
                        <div class="form-group mb-3">
                            <label for="kode_area_id_edit">POP</label>
                            <input disabled type="text" class="form-control" id="kode_area_id_edit"
                                name="kode_area_id_edit" placeholder="kode_area_id" autocomplete="off" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="kode_odp_edit">Kode ODP</label>
                            <input type="text" class="form-control" id="kode_odp_edit" name="kode_odp"
                                value="{{ old('kode_odp') }}" placeholder="kode_odp" autocomplete="off" disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label for="deskripsi_edit" class="mb-1">Deskripsi </label>
                            <input type="text" class="form-control" id="deskripsi_edit" name="deskripsi_edit" placeholder="" autocomplete="off" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="port_odp_edit">Jml Port</label>
                            <input type="text" class="form-control" id="port_odp_edit" name="port_odp" placeholder="" autocomplete="off" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="latitude_edit">Latitude</label>
                            <input type="text" class="form-control" id="latitude_edit" name="latitude_edit"  placeholder="" autocomplete="off" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="longitude_edit">Longitude</label>
                            <input type="text" class="form-control" id="longitude_edit" name="longitude_edit"  placeholder="" autocomplete="off" required>
                        </div>
                        
                        
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger text-white me-2" type="button" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button class="btn btn-primary" id="update" type="submit">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
