@extends('backend.layouts.app_new')

@section('title', 'Mitra Detail')

@section('css')
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}"> -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Mitra Detail</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="">
                <!-- <svg class="stroke-icon">
                  <use href="{{ asset('assets/old/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg> -->
              </a>
            </li>
            <!-- <li class="breadcrumb-item active">Mitra Detail</li> -->
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Container-fluid starts-->
  <div class="container-fluid user-management-page">

    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <form method="post" id="adminForm">
              @csrf
              <input type="hidden" value="{{$userdinetkan->dinetkan_user_id}}" name="dinetkan_user_id"> 
              <div class="mb-3 col-6">
              </div>
              <div class="mb-3 col-6">
                <button type="submit" class="btn btn-primary">Accept</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="modal-body"> 
              <form>  
                @csrf
                <div class="row">
                  <input type="hidden" name="dinetkan_user_id" value="{{$userdinetkan->dinetkan_user_id}}">
                  <div class="mb-3 col-sm-12">
                    <h3>Detail</h3>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{$userdinetkan->first_name}}" disabled>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name"  value="{{$userdinetkan->last_name}}" disabled>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="email" class="form-label">Email (as username)</label>
                    <input type="email" class="form-control" id="email" name="email"  value="{{$userdinetkan->username}}" disabled>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="whatsapp" class="form-label">WhatsApp</label>
                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="{{$userdinetkan->whatsapp}}" disabled>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="company_name" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name"  value="{{isset($userdinetkan->company) ? $userdinetkan->company->name : '' }}" disabled>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" disabled>
                      <option value="">Select Status</option>
                      <option {{$status_id = 0 ? 'selected' : ''}} value="0">Inactive</option>
                      <option {{$status_id = 1 ? 'selected' : ''}} value="1">Active</option>
                      <option {{$status_id = 2 ? 'selected' : ''}} value="2">New</option>
                      <option {{$status_id = 3 ? 'selected' : ''}} value="3">Expired</option>
                    </select>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="id_card" class="form-label">No. KTP / No. SIM </label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="id_card" name="id_card" :class="errors.id_card && 'is-invalid'" value="{{$userdinetkan->id_card}}" disabled>
                        <template x-if="errors.id_card">
                            <span class="invalid-feedback">
                                <strong x-text="errors.id_card"></strong>
                            </span>
                        </template>
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="npwp" class="form-label">NPWP</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="npwp" name="npwp" :class="errors.npwp && 'is-invalid'" value="{{$userdinetkan->npwp}}" disabled>
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
                        <option value=""></option>
                        @forelse ($provinces as $pp)
                            <option value="{{ $pp->id }}" <?php if($userdinetkan->province_id == $pp->id){echo 'selected';}?> >{{ $pp->name }}</option>
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
                            <option value="{{ $pp->id }}" <?php if($userdinetkan->regency_id == $pp->id){echo 'selected';}?> >{{ $pp->name }}</option>
                        @empty
                        @endforelse
                    </select>

                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="kecamatan" class="mb-1">Kecamatan {{$userdinetkan->district_id}}</label>
                    <div class="form-group mb-3" style="display:grid">
                    <select class="form-control" id="kecamatan" name="district_id" autocomplete="off" data-placeholder="Pilih Kecamatan">
                        <option value=""></option>
                        @forelse ($districts as $pp)
                            <option value="{{ $pp->id }}" <?php if($userdinetkan->district_id == $pp->id){echo 'selected';}?> >{{ $pp->name }}</option>
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
                            <option value="{{ $pp->id }}" <?php if($userdinetkan->village_id == $pp->id){echo 'selected';}?> >{{ $pp->name }}</option>
                        @empty
                        @endforelse
                    </select>

                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <div class="input-group">
                        <textarea disabled class="form-control" id="address" name="address" rows="3" :class="errors.address && 'is-invalid'">{{$userdinetkan->address}}</textarea>
                        <template x-if="errors.address">
                            <span class="invalid-feedback">
                                <strong x-text="errors.address"></strong>
                            </span>
                        </template>
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
                        <input type="text" class="form-control" id="latitude" name="latitude" :class="errors.latitude && 'is-invalid'" value="{{$userdinetkan->latitude}}" disabled>
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
                        <input type="text" class="form-control" id="longitude" name="longitude" :class="errors.longitude && 'is-invalid'" value="{{$userdinetkan->longitude}}" disabled>
                        <template x-if="errors.longitude">
                            <span class="invalid-longitude">
                                <strong x-text="errors.longitude"></strong>
                            </span>
                        </template>
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <!-- <button type="submit" class="btn btn-primary">Update</button> -->
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>  

    <div style="margin-top:30px"></div>
    
    <div class="row">
      <!-- Account Information Card -->
      <div class="col-xl-12 mb-4">
        <div class="card rounded border-0 shadow-sm">
          <div class="card-body"> 
            <div class="mb-3 col-sm-12">
              <h3>Informasi Dokumen</h3>
            </div>       
            <form method="post" action="{{ route('dinetkan.users_dinetkan.update_doc_info_dinetkan') }}" enctype="multipart/form-data">
              @csrf
              <input type="hidden" value="{{$userdinetkan->dinetkan_user_id}}" name="doc_dinetkan_user_id"> 
              <div class="row">
                <div class="mb-3 col-6">
                  <label for="doc_id" class="form-label">Dokumen</label>
                  <select class="form-select" id="doc_id" name="doc_id" disabled>
                    <option value="">Pilih Dokumen</option>
                    @if($docType)
                      @foreach($docType as $doc)
                      <option value="{{$doc->id}}">{{$doc->name}}</option>
                      @endforeach
                    @endif
                  </select>
                </div>
                <div class="mb-3 col-6">
                  <label for="doc" class="form-label">Pilih File</label>
                  <input type="file" accept="image/jpg,image/jpeg,image/png, application/pdf" name="doc" id="doc" class="form-control">
                </div>
                <div class="mb-3 col-6">
                  <!-- <button type="submit" class="btn btn-primary">Save</button> -->
                </div>
              </div>
            </form>
            <div class="mb-3 col-12"> 
              <div class="table-responsive custom-scrollbar">
                <table id="myTable" class="table-hover display nowrap table" width="100%">
                  <thead>
                    <th>Dokumen</th>
                    <th>Lihat File</th>
                  </thead>
                  @if($listDoc)
                    @foreach($listDoc as $row)
                      <tr>
                        <td>{{$row->docType->name}}</td>
                        <td>
                          @if($row->file_ext == 'pdf')
                          <a href="{{route('dinetkan.users_dinetkan.show_file', $row->id)}}" target="_blank">Lihat file</a>
                          @endif
                          @if($row->file_ext != 'pdf')
                          <a href="{{route('dinetkan.users_dinetkan.show_file', $row->id)}}" target="_blank">Lihat file</a>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  @endif
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="modal-body">              
              <div class="row">
                <div class="mb-3 col-sm-12">
                  <h3>Data</h3>
                </div>
                <div class="mb-3 col-md-3">
                  <label for="vlan" class="form-label">VLAN</label>
                  <input type="text" class="form-control" id="vlan" name="vlan" value="{{$userdinetkan->vlan}}" disabled>
                </div>
                <div class="mb-3 col-md-3">
                  <label for="metro_id" class="form-label">Metro</label>
                  <select class="form-select" id="metro_id" name="metro_id" disabled>
                    <option value="">Select Metro</option>
                    @if($metro)
                      @foreach($metro as $row)
                        <option value="{{$row->id}}" <?php if($userdinetkan->metro_id == $row->id){echo 'selected';}?> >{{$row->name}}</option>
                      @endforeach
                    @endif
                  </select>
                </div>
                <div class="mb-3 col-md-3">
                  <label for="vendor" class="form-label">ID/CID/SO/SID Vendor</label>
                  <input type="text" class="form-control" id="vendor" name="vendor" value="{{$userdinetkan->vendor}}" disabled>
                </div>
                <div class="mb-3 col-md-3">
                  <label for="ip_prefix" class="form-label">IP Prefix</label>
                  <input type="text" class="form-control" id="ip_prefix" name="ip_prefix" value="{{$userdinetkan->ip_prefix}}" disabled>
                </div>
                <div class="mb-3">
                  <label for="id_wag" class="form-label">ID Whatsapp group</label>
                      <div class="form-group mb-3" style="display:grid">
                      <select class="form-control" id="id_wag" name="id_wag" autocomplete="off" data-placeholder="Pilih WAGroup" disabled>
                          <div class="row">
                          <option value=""></option>
                          @forelse ($wag as $row)
                              <option value="{{ $row->group_id }} " {{ $row->group_id == $userdinetkan->group_id ? 'selected' : ''}}>{{ $row->group_name }}</option>
                          @empty
                          @endforelse
                          </div>
                      </select>

                      </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>     -->

    <!-- <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="mb-3 col-sm-12">
              <h3>MRTG</h3>
            </div>
            <div class="table-responsive custom-scrollbar" id="row_create">
              <table id="myTable" class="table-hover display nowrap table" width="100%">
                <thead>
                  <th>ID</th>
                  <th>Name</th>
                </thead>
                @if($userdinetkanGraph)
                  @foreach($userdinetkanGraph as $row)
                    <tr>
                      <td>{{$row->graph_id}}</td>
                      <td>{{$row->graph_name}}</td>
                    </tr>
                  @endforeach
                @endif
              </table>
            </div>
          </div>
        </div>
      </div>
    </div> -->

  </div>
  <!-- Container-fluid Ends-->

@endsection

@push('scripts')
  <!-- <script src="{{ asset('assets/old/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/old/js/datatable/datatables/jquery.dataTables.min.js') }}"></script> -->
  <!-- <script src="{{ asset('assets/old/js/datatable/datatables/datatable.custom.js') }}"></script> -->
  <script>
    const baseurl = "{{ url('/') }}" //baseUrl.clone().pop().pop().pop().pop();
    
    const formCacti = $("#adminForm");       
    formCacti.on('submit', function(e) {
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
        url: "{{ route('dinetkan.users_dinetkan.accept_new') }}",
        method: 'POST',
        data: formCacti.serialize(),
        success: function(response) {
          swal.close();
          window.location.href = "{{ route('dinetkan.users_dinetkan') }}";
        },
        error: function(xhr) {
          const errors = xhr.responseJSON.errors;
          let message = '';

          for (const key in errors) {
            message += errors[key] + '\n';
          }
                  Swal.fire({
                      icon: 'error',
                      title: 'error',
                      text: `${message}`,
                      showConfirmButton: false,
                      timer: 1500
                  });
        }
      });
    });

    $('#provinsi').select2();
    $.ajax({
    url: baseurl + '/dinetkan/settings/master/geo/provinces',
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
          // dropdownParent: $("#adminModal .modal-content"),
        });
      }
    });

    $('#kabupaten').select2();
    $('#provinsi').on('change', function() {
      $('#kabupaten').empty();
      var id_provinsi = $(this).val();
      if (id_provinsi) {
        $.ajax({
          url: baseurl + '/dinetkan/settings/master/geo/regencies/' + id_provinsi,
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
        url: baseurl + '/dinetkan/settings/master/geo/districts/' + id_kabupaten,
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
        url: baseurl + '/dinetkan/settings/master/geo/villages/' + id_kecamatan,
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
    set_maps('{{$userdinetkan->latitude}}','{{$userdinetkan->longitude}}');
    
    function set_maps(defaultLat = -6.200000,defaultLng = 106.816666){
      var map = null; // Simpan instance peta di luar fungsi agar bisa diakses ulang
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
