<!-- Modal Show -->
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Create User</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name" class="mb-1">Nama Lengkap <small class="text-danger">*</small></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="" autocomplete="off" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="mb-1">Alamat Email <small class="text-danger">*</small></label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="" autocomplete="off" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="whatsapp" class="mb-1">Nomor Whatsapp <small class="text-danger">*</small></label>
                            <input type="text" class="form-control" id="whatsapp" name="whatsapp" placeholder="" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="username" class="mb-1">Username <small class="text-danger">*</small></label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="" autocomplete="off" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="mb-1">Password <small class="text-danger">*</small></label>
                            <input type="text" class="form-control" id="password" name="password" placeholder="" autocomplete="off" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="role" class="mb-1">Level User <small class="text-danger">*</small></label>
                            <select class="form-select" id="role">
                                <option value="Admin" disabled>Admin</option>
                                <option value="Teknisi">Teknisi</option>
                                <option value="Kasir">Kasir</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3 col-6">
                        <label for="provinsi" class="mb-1">Provinsi</label>
                        <div class="form-group mb-3" style="display:grid">
                            <select class="form-control" id="provinsi" name="province_id" autocomplete="off" data-placeholder="Pilih Provinsi">
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 col-6">
                        <label for="kabupaten" class="mb-1">Kota / Kabupaten</label>
                        <div class="form-group mb-3" style="display:grid">
                            <select class="form-control" id="kabupaten" name="regency_id" autocomplete="off" data-placeholder="Pilih Kota / Kabupaten">
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 col-6">
                        <label for="kecamatan" class="mb-1">Kecamatan</label>
                        <div class="form-group mb-3" style="display:grid">
                            <select class="form-control" id="kecamatan" name="district_id" autocomplete="off" data-placeholder="Pilih Kecamatan">
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 col-6">
                        <label for="desa" class="mb-1">Desa / Kelurahan</label>
                        <div class="form-group mb-3" style="display:grid">
                            <select class="form-control" id="desa" name="village_id" autocomplete="off" data-placeholder="Pilih Desa">
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <div class="input-group">
                        <textarea class="form-control" id="address" name="address" rows="3" :class="errors.address && 'is-invalid'"></textarea>
                        <template x-if="errors.address">
                            <span class="invalid-feedback">
                                <strong x-text="errors.address"></strong>
                            </span>
                        </template>
                    </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="address" class="form-label">Lokasi</label>
                        <div class="input-group">
                            <div id="map"></div>
                        </div>
                    </div>
                    <div class="mb-3 col-6">
                    <label for="latitude" class="form-label">Latitude</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="latitude" name="latitude" :class="errors.latitude && 'is-invalid'">
                        <template x-if="errors.latitude">
                            <span class="invalid-feedback">
                                <strong x-text="errors.latitude"></strong>
                            </span>
                        </template>
                    </div>
                    </div>
                    <div class="mb-3 col-6">
                    <label for="latitude" class="form-label">Longitude</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="longitude" name="longitude" :class="errors.longitude && 'is-invalid'">
                        <template x-if="errors.longitude">
                            <span class="invalid-longitude">
                                <strong x-text="errors.longitude"></strong>
                            </span>
                        </template>
                    </div>
                    </div>
                    
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
@push('scripts')
{{-- <script src="{{ asset('assets/old/js/datatable/datatables/datatable.custom.js') }}"></script> --}}
<script type="text/javascript">
    
    var map = null; // Simpan instance peta di luar fungsi agar bisa diakses ulang
    set_maps();
    const baseurl = "{{ url('/') }}" //baseUrl.clone().pop().pop().pop().pop();
    $.ajax({
        url: baseurl + '/admin/settings/master/geo/provinces',
        type: "GET",
        success: function(data) {
            const selectData = [{
                id: '',
                text: 'Pilih Provinsi'
            }].concat(
                data.map((item) => ({
                    id: item.id,
                    text: item.name
                })).sort((a, b) => a.text.localeCompare(b.text))
            );

            $('#provinsi').select2({
                data: selectData,
                allowClear: true,
                placeholder: 'Pilih Provinsi',
                dropdownParent: $("#create .modal-content"),
            });

            // Set nilai default kosong agar "Pilih Provinsi" muncul pertama
            $('#provinsi').val('').trigger('change');
        }
    });

    $('#provinsi').on('change', function() {
      $('#kabupaten').empty();
      var id_provinsi = $(this).val();
      if (id_provinsi) {
        $.ajax({
          url: baseurl + '/admin/settings/master/geo/regencies/' + id_provinsi,
          type: 'GET',
          dataType: "json",
          success: function(data) {
            const selectData = [{
                id: '',
                text: 'Pilih Kabupaten'
            }].concat(
                data.map((item) => ({
                    id: item.id,
                    text: item.name
                })).sort((a, b) => a.text.localeCompare(b.text))
            );

            $('#kabupaten').select2({
                data: selectData,
                allowClear: true,
                placeholder: 'Pilih kabupaten',
                dropdownParent: $("#create .modal-content"),
            });

            // Set nilai default kosong agar "Pilih Provinsi" muncul pertama
            $('#kabupaten').val('').trigger('change');
          }
        });
      } else {
        $('#kabupaten').empty();
      }
    });

    $('#kabupaten').on('change', function() {
      $('#kecamatan').empty();
    var id_kabupaten = $(this).val();
    if (id_kabupaten) {
        $.ajax({
        url: baseurl + '/admin/settings/master/geo/districts/' + id_kabupaten,
        type: 'GET',
        dataType: "json",
        success: function(data) {
            const selectData = [{
                id: '',
                text: 'Pilih Kecamatan'
            }].concat(
                data.map((item) => ({
                    id: item.id,
                    text: item.name
                })).sort((a, b) => a.text.localeCompare(b.text))
            );

            $('#kecamatan').select2({
                data: selectData,
                allowClear: true,
                placeholder: 'Pilih Kecamatan',
                dropdownParent: $("#create .modal-content"),
            });

            // Set nilai default kosong agar "Pilih Provinsi" muncul pertama
            $('#kecamatan').val('').trigger('change');
        }
        });
    } else {
        $('#kecamatan').empty();
    }
    });

    $('#kecamatan').on('change', function() {
    $('#desa').empty();
    var id_kecamatan = $(this).val();
    if (id_kecamatan) {
        $.ajax({
        url: baseurl + '/admin/settings/master/geo/villages/' + id_kecamatan,
        type: 'GET',
        dataType: "json",
        success: function(data) {
            const selectData = [{
                id: '',
                text: 'Pilih Desa'
            }].concat(
                data.map((item) => ({
                    id: item.id,
                    text: item.name
                })).sort((a, b) => a.text.localeCompare(b.text))
            );

            $('#desa').select2({
                data: selectData,
                allowClear: true,
                placeholder: 'Pilih Desa',
                dropdownParent: $("#create .modal-content"),
            });

            // Set nilai default kosong agar "Pilih Provinsi" muncul pertama
            $('#desa').val('').trigger('change');
        }
        });
    } else {
        $('#desa').empty();
    }
    });

    
    function set_maps(defaultLat = -6.200000,defaultLng = 106.816666){
          // var map = null;
          if (map !== null) {
              map.remove(); // Hapus instance peta sebelumnya
          }
           // Default ke Jakarta jika data kosong
           var defaultLat = -6.200000;
           var defaultLng = 106.816666;
  
           // Ambil data dari input hidden (diisi dari database)
           var latInput = document.getElementById("latitude").value.trim();
           var lngInput = document.getElementById("longitude").value.trim();
  
           // Gunakan nilai database jika ada, jika tidak pakai default
           var lat = latInput ? parseFloat(latInput) : defaultLat;
           var lng = lngInput ? parseFloat(lngInput) : defaultLng;
  
           // Inisialisasi Peta
           map = L.map('map').setView([lat, lng], 10);
  
           // Tambahkan Tile Layer
           L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
               attribution: '&copy; OpenStreetMap contributors'
           }).addTo(map);
  
           // Tambahkan Marker
           var marker = L.marker([lat, lng], { draggable: true }).addTo(map);
  
           // Update Latitude & Longitude saat marker dipindahkan
           marker.on('dragend', function (event) {
               var position = marker.getLatLng();
               document.getElementById('latitude').value = position.lat;
               document.getElementById('longitude').value = position.lng;
           });
    }
</script>
@endpush