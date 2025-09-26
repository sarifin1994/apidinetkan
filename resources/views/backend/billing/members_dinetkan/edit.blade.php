@extends('backend.layouts.app_new')

@section('title', 'Pelanggan')

@section('css')
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}"> -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">
@endsection

@section('main')

  <!-- Container-fluid starts-->
  <div class="container-fluid user-management-page">
    <div class="container-fluid">
      <div class="page-title">
        <div class="row">
          <div class="col-sm-6 ps-0">
            <h3>Edit Pelanggan</h3>
          </div>
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-xl-12 mb-4">
        <div class="card rounded border-0 shadow-sm">
          <div class="card-body">
            <form id="adminForm" method="POST">
              @csrf
              <input type="hidden" id="adminMethod" name="_method" value="POST">
              <input type="hidden" id="id" name="id" value="{{$member->id}}">
              <div class="row">
                <!-- <div class="col-6"> -->
                  <div class="mb-3 col-6">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{$member->first_name}}" required>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{$member->last_name}}" required>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="email" class="form-label">Email (as username)</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{$member->email}}" required>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="wa" class="form-label">wa</label>
                    <input type="text" class="form-control" id="wa" name="wa" value="{{$member->wa}}" required>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="no_ktp" class="form-label">No. KTP / No. SIM </label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="no_ktp" name="no_ktp" :class="errors.no_ktp && 'is-invalid'" value="{{$member->no_ktp}}">
                        <template x-if="errors.no_ktp">
                            <span class="invalid-feedback">
                                <strong x-text="errors.no_ktp"></strong>
                            </span>
                        </template>
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="npwp" class="form-label">NPWP</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="npwp" name="npwp" :class="errors.npwp && 'is-invalid'" value="{{$member->npwp}}">
                      <template x-if="errors.npwp">
                                <span class="invalid-feedback">
                                    <strong x-text="errors.npwp"></strong>
                                </span>
                      </template>
                    </div>
                  </div>
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
                  <div class="mb-3 col-6">
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
                  <div class="mb-3 col-6">
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
                  <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <div class="input-group">
                        <textarea class="form-control" id="address" name="address" rows="3" :class="errors.address && 'is-invalid'">{{$member->address}}</textarea>
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
                        <input type="text" class="form-control" id="latitude" name="latitude" :class="errors.latitude && 'is-invalid'" value="{{$member->latitude}}">
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
                        <input type="text" class="form-control" id="longitude" name="longitude" :class="errors.longitude && 'is-invalid'" value="{{$member->longitude}}">
                        <template x-if="errors.longitude">
                            <span class="invalid-longitude">
                                <strong x-text="errors.longitude"></strong>
                            </span>
                        </template>
                    </div>
                  </div>
                  
                  <div class="mb-3 col-12">
                    <label for="product_dinetkan_id" class="mb-1">Service</label>
                    <div class="form-group mb-3" style="display:grid">
                    <select class="form-control" id="product_dinetkan_id_service" name="product_dinetkan_id" autocomplete="off" data-placeholder="Pilih Service">
                        <div class="row">
                        <option value=""></option>
                        @forelse ($product as $pp)
                            <option value="{{ $pp->id }}" {{$member->product_dinetkan_id == $pp->id ? 'selected' : ''}}>{{ $pp->product_name }}</option>
                        @empty
                        @endforelse
                        </div>
                    </select>

                    </div>
                  </div>
              </div>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid Ends-->

@endsection

@push('scripts')
  <!-- <script src="{{ asset('assets/old/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/old/js/datatable/datatables/jquery.dataTables.min.js') }}"></script> -->
  <!-- <script src="{{ asset('assets/old/js/datatable/datatables/datatable.custom.js') }}"></script> -->

  <script>
      
    // baseurl = baseUrl.clone().pop().pop().pop();
    const baseurl = "{{ url('/') }}"
    $(document).ready(function() {
      const modal = $('#adminModal');
      const form = $('#adminForm');
      const methodField = $('#adminMethod');
      const submitBtn = $('#submitBtn');
      
      const modalService = $('#serviceModal');
      const formService = $('#serviceForm');
      const methodFieldService = $('#serviceMethod');
      set_maps({{$member->latitude}},{{$member->longitude}});

      // Form Submission
      form.on('submit', function(e) {
          Swal.fire({
              title: 'Please wait...',
              text: 'Processing Data ....',
              allowOutsideClick: false,
              showConfirmButton: false,
              didOpen: () => {
                  Swal.showLoading();
              }
          });
        e.preventDefault();

        $.ajax({
          url: '{{ route('admin.billing.member_dinetkan.update',$member->id) }}',
          method: 'PUT',
          data: form.serialize(),
          success: function(response) {
            swal.close();
            Swal.fire(
              'Berhasil!',
              response.message,
              'success'
            );
            window.location = "{{route('admin.billing.member_dinetkan.index')}}";
          },
          error: function(xhr) {
            swal.close();
            const errors = xhr.responseJSON.errors;
            let message = '';

            for (const key in errors) {
              message += errors[key] + '\n';
            }

            // toastr.error(message);
            Swal.fire(
              'Gagal!',
              message,
              'Error'
            );
          }
        });
      });
    });

  </script>
  <script>
    
    $.ajax({
        url: baseurl + '/settings/master/geo/provinces',
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
                placeholder: $('#provinsi').data('placeholder'),
                // dropdownParent: $("#adminModal .modal-content"),
            });

            // Set default value sesuai dengan village_id
            if ({{$member->province_id}}) {
                $('#provinsi').val({{$member->province_id}}).trigger('change');
            }
        }
    });
    
    $.ajax({
        url: baseurl + '/settings/master/geo/regencies/' + {{$member->province_id}},
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
                placeholder: $('#kabupaten').data('placeholder'),
                // dropdownParent: $("#adminModal .modal-content"),
            });

            // Set default value sesuai dengan village_id
            if ({{$member->regency_id}}) {
                $('#kabupaten').val({{$member->regency_id}}).trigger('change');
            }
        }
    });

    $.ajax({
        url: baseurl + '/settings/master/geo/districts/' + {{$member->regency_id}},
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
                placeholder: $('#kecamatan').data('placeholder'),
                // dropdownParent: $("#adminModal .modal-content"),
            });

            // Set default value sesuai dengan village_id
            if ({{$member->district_id}}) {
                $('#kecamatan').val({{$member->district_id}}).trigger('change');
            }
        }
    });

    $.ajax({
        url: baseurl + '/settings/master/geo/villages/' + {{$member->district_id}},
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
                // dropdownParent: $("#adminModal .modal-content"),
            });

            // Set default value sesuai dengan village_id
            if ({{$member->village_id}}) {
                $('#desa').val({{$member->village_id}}).trigger('change');
            }
        }
    });

    var map = null; // Simpan instance peta di luar fungsi agar bisa diakses ulang
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
    };
  </script>
@endpush
