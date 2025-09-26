@extends('backend.layouts.app_new')

@section('title', 'Users')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}"> -->

@endsection

@section('main')
  <!-- container-fluid starts-->
  <div class="container-fluid user-management-page">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="modal-body"> 
              <form method="post" id="adminForm">  
                @csrf
                <div class="row">
                  <div class="mb-3 col-sm-12">
                    <h3>Add Users</h3>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="email" class="form-label">Email (as username)</label>
                    <input type="email" class="form-control" id="email" name="email">
                  </div>
                  <div class="mb-3 col-6">
                    <label for="whatsapp" class="form-label">WhatsApp</label>
                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" required>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="company_name" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" >
                  </div>
                  <div class="mb-3 col-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                    <option value="4">NEW</option>
                    </select>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="id_card" class="form-label">No. KTP / No. SIM </label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="id_card" name="id_card" class="form-control'" required>
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="npwp" class="form-label">NPWP</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="npwp" name="npwp" class="form-control" required>
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="provinsi" class="mb-1">Provinsi</label>
                    <div class="form-group mb-3" style="display:grid">
                    <select class="form-control" id="provinsi" name="province_id" autocomplete="off" data-placeholder="Pilih Provinsi">
                        <option value=""></option>
                        @forelse ($provinces as $pp)
                            <option value="{{ $pp->id }}">{{ $pp->name }}</option>
                        @empty
                        @endforelse
                    </select>
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="kabupaten" class="mb-1">Kota / Kabupaten</label>
                    <div class="form-group mb-3" style="display:grid">
                    <select class="form-control" id="kabupaten" name="regency_id" autocomplete="off" data-placeholder="Pilih Kota / Kabupaten">
                        <option value=""></option>
                        @forelse ($regencies as $pp)
                            <option value="{{ $pp->id }}">{{ $pp->name }}</option>
                        @empty
                        @endforelse
                    </select>

                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="kecamatan" class="mb-1">Kecamatan</label>
                    <div class="form-group mb-3" style="display:grid">
                    <select class="form-control" id="kecamatan" name="district_id" autocomplete="off" data-placeholder="Pilih Kecamatan">
                        <option value=""></option>
                        @forelse ($districts as $pp)
                            <option value="{{ $pp->id }}">{{ $pp->name }}</option>
                        @empty
                        @endforelse
                    </select>

                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="desa" class="mb-1">Desa / Kelurahan </label>
                    <div class="form-group mb-3" style="display:grid">
                    <select class="form-control" id="desa" name="village_id" autocomplete="off" data-placeholder="Pilih Desa">
                        <option value=""></option>
                        @forelse ($villages as $pp)
                            <option value="{{ $pp->id }}">{{ $pp->name }}</option>
                        @empty
                        @endforelse
                    </select>

                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <div class="input-group">
                        <textarea required class="form-control" id="address" name="address" rows="3" class="form-control"></textarea>
                    </div>
                  </div>
                  <div style="margin-top:100px"></div>
                  <div class="col-md-12 mb-3">
                      <label for="address" class="form-label">Lokasi</label>
                      <div class="input-group">
                          <div id="map"></div>
                      </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="latitude" class="form-label">Latitude</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="latitude" name="latitude" class="form-control" onchange="set_maps()" required>
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="latitude" class="form-label">Longitude</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="longitude" name="longitude" class="form-control" onchange="set_maps()" required>
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <button type="submit" class="btn btn-primary">Save</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>  
  </div>
  <!-- container-fluid Ends-->

@endsection

@push('scripts')
  <script src="{{ asset('assets/old/radiusqu/dist/js/moment.js') }}"></script>
  <!-- <script src="{{ asset('assets/old/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <!-- <script src="{{ asset('assets/old/js/datatable/datatables/datatable.custom.js') }}"></script> --> -->
  <script>

      const form = $('#adminForm');
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

        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url: '{{route('kemitraan.users.create_users')}}',
          method: "POST",
          data: form.serialize(),
          success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: `${response.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
                // location.reload();
                window.location = `/kemitraan/users`;
          },
          error: function(xhr) {
            const errors = xhr.responseJSON.message;
            console.log(xhr);
            // toastr.error(message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: `${errors}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
          }
        });
      });

    const baseurl = "{{ url('/') }}" //baseUrl.clone().pop().pop().pop().pop();
    $('#provinsi').select2();
    $.ajax({
    url: baseurl + '/settings/master/geo/provinces',
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
          // allowClear: true,
          // dropdownParent: $("#sync .modal-content"),
          width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
          // placeholder: $(this).data('placeholder'),
          // dropdownParent: $("#adminModal .modal-content"),
        });
      }
    });
    

    $('#kabupaten').select2();
    $('#provinsi').on('change', function() {
      $('#kabupaten').empty();
      var id_provinsi = $(this).val();
      console.log(id_provinsi);
      if (id_provinsi) {
        $.ajax({
          url: baseurl + '/settings/master/geo/regencies/' + id_provinsi,
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
            // dropdownParent: $("#adminModal .modal-content"),
            });
          }
        });
      } else {
        // $('#kabupaten').empty();
      }
    });

    $('#kecamatan').select2();
    $('#kabupaten').on('change', function() {
      $('#kecamatan').empty();
    var id_kabupaten = $(this).val();
    if (id_kabupaten) {
        $.ajax({
        url: baseurl + '/settings/master/geo/districts/' + id_kabupaten,
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
            // dropdownParent: $("#adminModal .modal-content"),
            });
        }
        });
    } else {
        // $('#kecamatan').empty();
    }
    });

    
    $('#desa').select2();
    $('#kecamatan').on('change', function() {
    $('#desa').empty();
    var id_kecamatan = $(this).val();
    if (id_kecamatan) {
        $.ajax({
        url: baseurl + '/settings/master/geo/villages/' + id_kecamatan,
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
            // dropdownParent: $("#adminModal .modal-content"),
            });
        }
        });
    } else {
        // $('#desa').empty();
    }
    });
    var lat = -6.200000;
    var lon = 106.816666;
    set_maps(lat,lon);
    var map = null;

  function set_maps(defaultLat = -6.200000, defaultLng = 106.816666) {
    if (map) {
      map.remove();
      map = null;
    }

    // FIX utama di sini
    if (L.DomUtil.get('map') != null) {
      L.DomUtil.get('map')._leaflet_id = null;
    }

    var latInput = document.getElementById("latitude").value.trim();
    var lngInput = document.getElementById("longitude").value.trim();

    var lat = latInput ? parseFloat(latInput) : defaultLat;
    var lng = lngInput ? parseFloat(lngInput) : defaultLng;

    map = L.map('map').setView([lat, lng], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([lat, lng], { draggable: true }).addTo(map);

    marker.on('dragend', function (event) {
      var position = marker.getLatLng();
      document.getElementById('latitude').value = position.lat;
      document.getElementById('longitude').value = position.lng;
    });
  }
  
  $('#whatsapp').on('change', function() {
    Swal.fire({
        title: 'Please wait...',
        text: 'Checking Whatsapp',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    val = $(this).val();
    validate_data(val,'whatsapp')
  })

  $('#email').on('change', function() {
    Swal.fire({
        title: 'Please wait...',
        text: 'Checking Email / Username',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    val = $(this).val();
    validate_data(val,'email')
  })

  function validate_data(data,field){
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      $.ajax({
        url: baseurl + `/kemitraan/validate_data`,
        data: {
          field: field,
          field_data: data
        },
        method: 'POST',
        success: function(data) {
          swal.close();
          Swal.fire({
              icon: 'success',
              title: 'Success',
              text: 'Data bisa digunakan',
              showConfirmButton: false,
              timer: 1000
          });
        },
        error: function(xhr) {
          swal.close();
          Swal.fire({
              icon: 'error',
              title: 'Error',
              text: `${xhr.responseJSON.message}`,
              showConfirmButton: true,
          });
        }
      });
    }
  </script>
  @endpush
