@extends('backend.layouts.app_new')

@section('title', 'Service Detail')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}"> -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">

@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Service Detail</h3>
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
                    <input type="hidden" name="service_id" value="{{ $mapping->service_id }}">
                    <h3>Service ID {{ $mapping->service_id }}</h3>
                  </div>
                  <div class="mb-3 col-12">
                    <label for="service" class="form-label">Service</label>
                    <input type="text" class="form-control" id="service" name="service" value="{{ $mapping->service->name }}" disbaled readonly>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $service_detail ? $service_detail->first_name : ''}}" disabled="disabled">
                  </div>
                  <div class="mb-3 col-6">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $service_detail ? $service_detail->last_name : ''}}" disabled="disabled">
                  </div>
                  <div class="mb-3 col-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $service_detail ? $service_detail->email : ''}}" disabled="disabled">
                  </div>
                  <div class="mb-3 col-6">
                    <label for="whatsapp" class="form-label">WhatsApp</label>
                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="{{ $service_detail ? $service_detail->whatsapp : ''}}" disabled="disabled">
                  </div>
                  <!-- <div class="mb-3 col-6">
                    <label for="company_name" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" >
                  </div> -->
                  <!-- <div class="mb-3 col-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" disabled="disabled">
                    <option value="4">NEW</option>
                    </select>
                  </div> -->
                  <div class="mb-3 col-6">
                    <label for="id_card" class="form-label">No. KTP / No. SIM </label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="id_card" name="id_card" class="form-control'" value="{{ $service_detail ? $service_detail->id_card : ''}}" disabled="disabled">
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="npwp" class="form-label">NPWP</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="npwp" name="npwp" class="form-control" value="{{ $service_detail ? $service_detail->npwp : ''}}" disabled="disabled">
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="provinsi" class="mb-1">Provinsi</label>
                    <div class="form-group mb-3" style="display:grid">
                    <select class="form-control" id="provinsi" name="province_id" autocomplete="off" data-placeholder="Pilih Provinsi">
                        <option value=""></option>
                        @forelse ($provinces as $pp)
                            <option value="{{ $pp->id }}" <?php if($service_detail->province_id == $pp->id){echo 'selected';}?> >{{ $pp->name }}</option>
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
                            <option value="{{ $pp->id }}" <?php if($service_detail->regency_id == $pp->id){echo 'selected';}?> >{{ $pp->name }}</option>
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
                            <option value="{{ $pp->id }}" <?php if($service_detail->district_id == $pp->id){echo 'selected';}?> >{{ $pp->name }}</option>
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
                            <option value="{{ $pp->id }}" <?php if($service_detail->village_id == $pp->id){echo 'selected';}?> >{{ $pp->name }}</option>
                        @empty
                        @endforelse
                    </select>

                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <div class="input-group">
                        <textarea class="form-control" id="address" name="address" rows="3" class="form-control">{{ $service_detail->address ? $service_detail->address : ''}}</textarea>
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
                        <input type="text" class="form-control" id="latitude" name="latitude" class="form-control" onchange="set_maps()" value="{{ $service_detail->latitude ? $service_detail->latitude : ''}}" >
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="latitude" class="form-label">Longitude</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="longitude" name="longitude" class="form-control" onchange="set_maps()" value="{{ $service_detail->longitude ? $service_detail->longitude : ''}}" >
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    @if($edited)
                    <button type="submit" class="btn btn-light-primary">Save</button>
                    @endif
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
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">   
            <div class="mb-3 col-sm-12">
                <h3>Informasi Dokumen</h3>
            </div>     
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
                          <a class="btn btn-light-primary" href="{{route('dinetkan.users_dinetkan.show_file', $row->id)}}" target="_blank">Lihat file</a>
                          @endif
                          @if($row->file_ext != 'pdf')
                          <a class="btn btn-light-primary" href="{{route('dinetkan.users_dinetkan.show_file', $row->id)}}" target="_blank">Lihat file</a>
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

        
      <div style="margin-top:30px"></div>
      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-body">
              <div class="mb-3 col-sm-12">
                <h3>Graph</h3>
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
      </div>

      

      <div style="margin-top:30px"></div>

      <div class="row">
        <!-- Account Information Card -->
        <!-- <div class="col-sm-12">
          <div class="card">
            <div class="card-body">   
              <div class="mb-3 col-sm-12">
                <h3>Api Mikrotik</h3>
              </div>   
              <div class="mb-3 col-12"> 
                <div class="table-responsive custom-scrollbar">
                  <table id="myTable" class="table-hover display nowrap table" width="100%">
                    <thead>
                      <th>Mikrotik</th>
                      <th>Vlan</th>
                      <th>Status VLAN</th>
                    </thead>
                    <tbody>
                      @if($mikrotik_detail)
                      <tr>
                        <td>{{$mikrotik_detail->name}} - {{$mikrotik_detail->ip}}</td>
                        <td>{{$service_detail->vlan_name}}</td>
                        <td><span id="status_vlan"></span></td>
                      </tr>
                      @endif
                    </tbody>
                    
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div> -->
      </div>

      
    
      <div style="margin-top:30px"></div>
  </div>
  <!-- container-fluid Ends-->

@endsection

@push('scripts')
  <script src="{{ asset('assets/old/radiusqu/dist/js/moment.js') }}"></script>
  <!-- <script src="{{ asset('assets/old/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/old/js/datatable/datatables/datatable.custom.js') }}"></script> -->
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
          url: '{{route('admin.account.invoice_dinetkan.order.update_service_detail')}}',
          method: "POST",
          data: form.serialize(),
          success: function(response) {
            // modal.modal('hide');
            // toastr.success(response.message);
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: `${response.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
                location.reload();
          },
          error: function(xhr) {
            const errors = xhr.responseJSON.errors;
            let message = '';

            for (const key in errors) {
              message += errors[key] + '\n';
            }
            // toastr.error(message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: `${message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
          }
        });
      });

    const baseurl = "{{ url('/') }}" //baseUrl.clone().pop().pop().pop().pop();
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
    // var marker = L.marker([lat, lng]).addTo(map);

    marker.on('dragend', function (event) {
      var position = marker.getLatLng();
      document.getElementById('latitude').value = position.lat;
      document.getElementById('longitude').value = position.lng;
    });
  }

  </script>
  
  <script>  

    $('#trafic_mrtg_tree').on('change', function() {
    $('#trafic_mrtg_tree_node').empty();
    var id = $(this).val();
    if (id) {
        $.ajax({
        url:  baseurl + `/dinetkan/users_dinetkan/get_tree_node_mrtg/`+id,
        type: 'GET',
        dataType: "json",
        success: function(data) {
              if(data){
                let formattedData = data.map((item) => ({
                  id: item.value,
                  text: item.label
                })).sort((a, b) => a.text.localeCompare(b.text));

                // Tambahkan opsi default "Silahkan Pilih"
                formattedData.unshift({
                    id: '',
                    text: 'Silahkan Pilih',
                    disabled: false // Jangan disabled agar bisa dipilih
                });

                $('#trafic_mrtg_tree_node').select2({
                    data: formattedData,
                    allowClear: true,
                    dropdownParent: $("#adminCactiForm .modal-body"),
                });
            }else{
              $('#trafic_mrtg_tree_node').empty();
            }
          }
        });
    } else {
        $('#trafic_mrtg_tree_node').empty();
    }
    });

    $('#trafic_mrtg_tree_node').on('change', function() {
        let page_graph = document.getElementById("page_graph");
        let paging = page_graph.value;
        $('#trafic_mrtg_graph').empty();
        var id = $(this).val();
        if (id) {
            $.ajax({
                url: baseurl + `/dinetkan/users_dinetkan/get_graph_mrtg/` + id + `/` + paging,
                type: 'GET',
                dataType: "json",
                success: function(data) {
                    if (data) {
                        let formattedData = data.map((item) => ({
                            id: item.local_graph_id,
                            text: item.Title
                        })).sort((a, b) => a.text.localeCompare(b.text));

                        // Tambahkan opsi default "Silahkan Pilih"
                        formattedData.unshift({
                            id: '',
                            text: 'Silahkan Pilih',
                            disabled: false // Jangan disabled agar bisa dipilih
                        });

                        $('#trafic_mrtg_graph').select2({
                            data: formattedData,
                            allowClear: true,
                            dropdownParent: $("#adminCactiForm .modal-body"),
                        });
                    } else {
                        $('#trafic_mrtg_graph').empty();
                    }
                }
            });
        } else {
            $('#trafic_mrtg_graph').empty();
        }
    });

    $('#page_graph').on('change', function() {
        let trafic_mrtg_tree_node = document.getElementById("trafic_mrtg_tree_node");
        $('#trafic_mrtg_graph').empty();
        var paging = $(this).val();
        var id = trafic_mrtg_tree_node.value;
        if (id) {
            $.ajax({
                url: baseurl + `/dinetkan/users_dinetkan/get_graph_mrtg/` + id + `/` + paging,
                type: 'GET',
                dataType: "json",
                success: function(data) {
                    if (data) {
                        let formattedData = data.map((item) => ({
                            id: item.local_graph_id,
                            text: item.Title
                        })).sort((a, b) => a.text.localeCompare(b.text));

                        // Tambahkan opsi default "Silahkan Pilih"
                        formattedData.unshift({
                            id: '',
                            text: 'Silahkan Pilih',
                            disabled: false // Jangan disabled agar bisa dipilih
                        });

                        $('#trafic_mrtg_graph').select2({
                            data: formattedData,
                            allowClear: true,
                            dropdownParent: $("#adminCactiForm .modal-body"),
                        });
                    } else {
                        $('#trafic_mrtg_graph').empty();
                    }
                }
            });
        } else {
            $('#trafic_mrtg_graph').empty();
        }
    });
    
    $('#trafic_mrtg_graph').on('change', function() {
        var selectedOption = this.options[this.selectedIndex].text;
        document.getElementById("selected_value").value = selectedOption;
      });

      
    get_vlan_single('{{ $mapping->service_id }}');
    function get_vlan_single(service_id){
      document.getElementById("status_vlan").innerHTML  = '<button class="btn btn-light-danger btn-sm">VLAN Disabled</button>';
      $.ajax({
          url: baseurl + '/dinetkan/master_mikrotik/get_vlan_single/' + service_id,
          type: 'GET',
          dataType: "json",
          success: function(data) {
            if(data.disabled == 'true'){
              document.getElementById("status_vlan").innerHTML  = '<button class="btn btn-light-danger btn-sm">VLAN Disabled</button>';
            }
            if(data.disabled == 'false'){
              document.getElementById("status_vlan").innerHTML  = '<button class="btn btn-light-primary btn-sm">VLAN Enabled</button>';
            }
          }
        });
    }

  </script>
    
    <?php if($service_detail->province_id){ ?>
      <!-- <script>
      $(document).ready(function() {
          // set dulu value provinsi sesuai data dari backend
          $('#provinsi').val("<?= $service_detail->province_id ?>");
          // lalu trigger change supaya event .on('change') jalan
          $('#provinsi').trigger('change');

          
          // set dulu value provinsi sesuai data dari backend
          $('#kabupaten').val("<?= $service_detail->regency_id ?>");
          // lalu trigger change supaya event .on('change') jalan
          $('#kabupaten').trigger('change');
          
          // set dulu value provinsi sesuai data dari backend
          $('#kecamatan').val("<?= $service_detail->district_id ?>");
          // lalu trigger change supaya event .on('change') jalan
          $('#kecamatan').trigger('change');
      });
      </script> -->
      <?php } ?>
  @endpush
