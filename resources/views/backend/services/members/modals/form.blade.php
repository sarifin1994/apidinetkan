<div class="modal fade" id="memberFormModal" role="dialog" aria-labelledby="memberFormModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="memberFormModalLabel">Create Member</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form x-load x-data="form" id="memberForm">
              <div class="modal-body">
                  @csrf
                  <input type="hidden" name="member_id" id="member_id">
                  <template x-if="success">
                      <div class="alert alert-success alert-dismissible fade show" role="alert">
                          <strong>{{ __('Success') }}!</strong> {{ __('Member has been saved.') }}
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                  </template>

                  <div class="row">
                      <div class="col-md-6 mb-3">
                          <label for="id_member" class="form-label">Member ID</label>
                          <div class="input-group">
                              <input type="text" class="form-control" id="id_member" name="id_member" :class="errors.id_member && 'is-invalid'">
                              <template x-if="errors.id_member">
                                  <span class="invalid-feedback">
                                      <strong x-text="errors.id_member"></strong>
                                  </span>
                              </template>
                          </div>
                          <small class="text-muted">{{ __('Leave it blank to auto generate') }}</small>
                      </div>
                      <div class="col-md-6 mb-3">
                          <label for="full_name" class="form-label">Full Name</label>
                          <div class="input-group">
                              <input type="text" class="form-control" id="full_name" name="full_name" :class="errors.full_name && 'is-invalid'" required>
                              <template x-if="errors.full_name">
                                  <span class="invalid-feedback">
                                      <strong x-text="errors.full_name"></strong>
                                  </span>
                              </template>
                          </div>
                      </div>
                      <div class="col-md-6 mb-3">
                          <label for="email" class="form-label">Email</label>
                          <div class="input-group">
                              <input type="email" class="form-control" id="email" name="email" :class="errors.email && 'is-invalid'">
                              <template x-if="errors.email">
                                  <span class="invalid-feedback">
                                      <strong x-text="errors.email"></strong>
                                  </span>
                              </template>
                          </div>
                      </div>
                      <div class="col-md-6 mb-3">
                          <label for="wa" class="form-label">WhatsApp Number</label>
                          <div class="input-group">
                              <input type="text" class="form-control" id="wa" name="wa" :class="errors.wa && 'is-invalid'">
                              <template x-if="errors.wa">
                                  <span class="invalid-feedback">
                                      <strong x-text="errors.wa"></strong>
                                  </span>
                              </template>
                          </div>
                      </div>
                      <div class="col-md-6">
                            <label for="provinsi" class="mb-1">Provinsi</label>
                            <div class="form-group mb-3" style="display:grid">
                            <select class="form-control" id="provinsi" name="province_id" autocomplete="off" data-placeholder="Pilih Provinsi">
                                <div class="row">
                                <option value=""></option>
                                @forelse ($provinces as $pp)
                                    <option value="{{ $pp->id }}">{{ $pp->name }}</option>
                                @empty
                                @endforelse
                                </div>
                            </select>

                            </div>
                      </div>
                      <div class="col-md-6">
                            <label for="kabupaten" class="mb-1">Kota / Kabupaten</label>
                            <div class="form-group mb-3" style="display:grid">
                            <select class="form-control" id="kabupaten" name="regency_id" autocomplete="off" data-placeholder="Pilih Kota / Kabupaten">
                                <div class="row">
                                <option value=""></option>
                                @forelse ($regencies as $pp)
                                    <option value="{{ $pp->id }}">{{ $pp->name }}</option>
                                @empty
                                @endforelse
                                </div>
                            </select>

                            </div>
                      </div>
                      <div class="col-md-6">
                            <label for="kecamatan" class="mb-1">Kecamatan</label>
                            <div class="form-group mb-3" style="display:grid">
                            <select class="form-control" id="kecamatan" name="district_id" autocomplete="off" data-placeholder="Pilih Kecamatan">
                                <div class="row">
                                <option value=""></option>
                                @forelse ($districts as $pp)
                                    <option value="{{ $pp->id }}">{{ $pp->name }}</option>
                                @empty
                                @endforelse
                                </div>
                            </select>

                            </div>
                      </div>
                      <div class="col-md-6">
                            <label for="desa" class="mb-1">Desa / Kelurahan</label>
                            <div class="form-group mb-3" style="display:grid">
                            <select class="form-control" id="desa" name="village_id" autocomplete="off" data-placeholder="Pilih Desa">
                                <div class="row">
                                <option value=""></option>
                                @forelse ($villages as $pp)
                                    <option value="{{ $pp->id }}">{{ $pp->name }}</option>
                                @empty
                                @endforelse
                                </div>
                            </select>

                            </div>
                      </div>
                      <div class="col-md-12 mb-3">
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
                          <label for="status" class="form-label">Status</label>
                          <div class="input-group">
                              <select class="form-select" id="status" name="status" :class="errors.status && 'is-invalid'">
                                  <option value="active">Active</option>
                                  <option value="inactive">Inactive</option>
                              </select>
                              <template x-if="errors.status">
                                  <span class="invalid-feedback">
                                      <strong x-text="errors.status"></strong>
                                  </span>
                              </template>
                          </div>
                      </div>
                      <div class="col-md-6 mb-3">
                          <label for="no_ktp" class="form-label">No. KTP / No. SIM </label>
                          <div class="input-group">
                              <input type="text" class="form-control" id="no_ktp" name="no_ktp" :class="errors.no_ktp && 'is-invalid'">
                              <template x-if="errors.no_ktp">
                                  <span class="invalid-feedback">
                                      <strong x-text="errors.no_ktp"></strong>
                                  </span>
                              </template>
                          </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="npwp" class="form-label">NPWP</label>
                        <div class="input-group">
                          <input type="text" class="form-control" id="npwp" name="npwp" :class="errors.npwp && 'is-invalid'">
                          <template x-if="errors.npwp">
                                    <span class="invalid-feedback">
                                        <strong x-text="errors.npwp"></strong>
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
                      <div class="col-md-6 mb-3">
                          <label for="latitude" class="form-label">Latitude</label>
                          <div class="input-group">
                              <input type="text" class="form-control" id="latitude" name="latitude" :class="errors.latitude && 'is-invalid'" readonly>
                              <template x-if="errors.latitude">
                                  <span class="invalid-feedback">
                                      <strong x-text="errors.latitude"></strong>
                                  </span>
                              </template>
                          </div>
                      </div>
                      <div class="col-md-6 mb-3">
                          <label for="latitude" class="form-label">Longitude</label>
                          <div class="input-group">
                              <input type="text" class="form-control" id="longitude" name="longitude" :class="errors.longitude && 'is-invalid'" readonly>
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
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary" x-ref="button">Save Member</button>
              </div>
          </form>
      </div>
  </div>
</div>

@push('script-modal')
<script>
    
const baseurl = baseUrl.clone().pop().pop() + '/settings/master'; 
  $('#createMember').on('click', function() {
      $('#memberForm')[0].reset();
      $('#memberFormModalLabel').text('Create Member');
      $('#memberForm').attr('data-method', 'POST');
      $('#memberForm').attr('data-url', memberUrl);
  });

  $('#memberTable tbody').on('click', 'tr', function(event) {
      if (window.getSelection().toString().length) return;
      if ($(event.target).is('button')) return;
      $('#id_member').prop('readonly', true);

      const data = memberTable.row(this).data();
      $('#memberForm')[0].reset();
      $('#memberFormModalLabel').text('Edit Member');
      $('#memberForm').attr('data-method', 'PUT');
      $('#memberForm').attr('data-url', `${memberUrl}/${data.id}`);
      $('#member_id').val(data.id);
      $('#full_name').val(data.full_name);
    //   $('#id_member').val(data.id_member);
      $('#id_member').val(data.id_member_new);
      $('#email').val(data.email);
      $('#wa').val(data.wa);
      $('#address').val(data.address);
      $('#status').val(data.status);
      $('#no_ktp').val(data.no_ktp);
      $('#npwp').val(data.npwp);
      $('#longitude').val(data.longitude);
      $('#latitude').val(data.latitude);
               
        
        $.ajax({
            url: baseurl + '/geo/provinces',
            type: 'GET',
            dataType: "json",
            success: function(response) {
                let villageOptions = response.map((item) => {
                    return {
                        id: item.id,
                        text: item.name
                    };
                }).sort((a, b) => a.text.localeCompare(b.text));

                $('#provinsi').select2({
                    data: villageOptions,
                    allowClear: true,
                    placeholder: $('#desa').data('placeholder'),
                    dropdownParent: $("#memberFormModal .modal-content"),
                });

                // Set default value sesuai dengan village_id
                if (data.province_id) {
                    $('#provinsi').val(data.province_id).trigger('change');
                }
            }
        });
        
        $.ajax({
            url: baseurl + '/geo/regencies/' + data.province_id,
            type: 'GET',
            dataType: "json",
            success: function(response) {
                let villageOptions = response.map((item) => {
                    return {
                        id: item.id,
                        text: item.name
                    };
                }).sort((a, b) => a.text.localeCompare(b.text));

                $('#kabupaten').select2({
                    data: villageOptions,
                    allowClear: true,
                    placeholder: $('#desa').data('placeholder'),
                    dropdownParent: $("#memberFormModal .modal-content"),
                });

                // Set default value sesuai dengan village_id
                if (data.regency_id) {
                    $('#kabupaten').val(data.regency_id).trigger('change');
                }
            }
        });

        $.ajax({
            url: baseurl + '/geo/districts/' + data.regency_id,
            type: 'GET',
            dataType: "json",
            success: function(response) {
                let villageOptions = response.map((item) => {
                    return {
                        id: item.id,
                        text: item.name
                    };
                }).sort((a, b) => a.text.localeCompare(b.text));

                $('#kecamatan').select2({
                    data: villageOptions,
                    allowClear: true,
                    placeholder: $('#desa').data('placeholder'),
                    dropdownParent: $("#memberFormModal .modal-content"),
                });

                // Set default value sesuai dengan village_id
                if (data.district_id) {
                    $('#kecamatan').val(data.district_id).trigger('change');
                }
            }
        });

        $.ajax({
            url: baseurl + '/geo/villages/' + data.district_id,
            type: 'GET',
            dataType: "json",
            success: function(response) {
                let villageOptions = response.map((item) => {
                    return {
                        id: item.id,
                        text: item.name
                    };
                }).sort((a, b) => a.text.localeCompare(b.text));

                $('#desa').select2({
                    data: villageOptions,
                    allowClear: true,
                    placeholder: $('#desa').data('placeholder'),
                    dropdownParent: $("#memberFormModal .modal-content"),
                });

                // Set default value sesuai dengan village_id
                if (data.village_id) {
                    $('#desa').val(data.village_id).trigger('change');
                }
            }
        });
    //   $('#provinsi').val(data.province_id);
    //   $('#kabupaten').val(data.regency_id);
    //   $('#kecamatan').val(data.district_id);
    //   $('#desa').val(data.village_id);
      memberModal.show();
  });

   
    $.ajax({
    url: baseurl + '/geo/provinces',
      type: "GET",
      success: function(data) {
        $('#provinsi').select2({
          data: (() => {
            return data.map((item) => {
                return {
                  id: item.id,
                  text: item.name
                }
              })
              .sort((a, b) => a.text.localeCompare(b.text));
          })(),
          allowClear: true,
          placeholder: $(this).data('placeholder'),
          dropdownParent: $("#memberFormModal .modal-content"),
        });
      }
    });

    $('#provinsi').on('change', function() {
      var id_provinsi = $(this).val();
      if (id_provinsi) {
        $.ajax({
          url: baseurl + '/geo/regencies/' + id_provinsi,
          type: 'GET',
          dataType: "json",
          success: function(data) {
            $('#kabupaten').select2({
            data: (() => {
                return data.map((item) => {
                    return {
                    id: item.id,
                    text: item.name
                    }
                })
                .sort((a, b) => a.text.localeCompare(b.text));
            })(),
            allowClear: true,
            placeholder: $(this).data('placeholder'),
            dropdownParent: $("#memberFormModal .modal-content"),
            });
          }
        });
      } else {
        $('#kabupaten').empty();
      }
    });

    $('#kabupaten').on('change', function() {
    var id_kabupaten = $(this).val();
    if (id_kabupaten) {
        $.ajax({
        url: baseurl + '/geo/districts/' + id_kabupaten,
        type: 'GET',
        dataType: "json",
        success: function(data) {
            $('#kecamatan').select2({
            data: (() => {
                return data.map((item) => {
                    return {
                    id: item.id,
                    text: item.name
                    }
                })
                .sort((a, b) => a.text.localeCompare(b.text));
            })(),
            allowClear: true,
            placeholder: $(this).data('placeholder'),
            dropdownParent: $("#memberFormModal .modal-content"),
            });
        }
        });
    } else {
        $('#kecamatan').empty();
    }
    });

    $('#kecamatan').on('change', function() {
    var id_kecamatan = $(this).val();
    if (id_kecamatan) {
        $.ajax({
        url: baseurl + '/geo/villages/' + id_kecamatan,
        type: 'GET',
        dataType: "json",
        success: function(data) {
            $('#desa').select2({
            data: (() => {
                return data.map((item) => {
                    return {
                    id: item.id,
                    text: item.name
                    }
                })
                .sort((a, b) => a.text.localeCompare(b.text));
            })(),
            allowClear: true,
            placeholder: $(this).data('placeholder'),
            dropdownParent: $("#memberFormModal .modal-content"),
            });
        }
        });
    } else {
        $('#desa').empty();
    }
    });
</script>

<script>
    $(document).ready(function() {
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
           var map = L.map('map').setView([lat, lng], 10);
  
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
    });
  </script>

@endpush
