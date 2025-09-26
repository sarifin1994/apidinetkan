@extends('backend.layouts.app_new')

@section('title', 'Pelanggan')

@section('css')
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}"> -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">
@endsection

@section('main')
  <div class="container-lg">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Pelanggan</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="/">
                <!-- <svg class="stroke-icon">
                  <use href="{{ asset('assets/old/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg> -->
              </a>
            </li>
            <!-- <li class="breadcrumb-item active">Users Management</li> -->
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Container-fluid starts-->
  <div class="container-lg user-management-page">
    <div class="row">
      <div class="col-xxl-12 box-col-12">
        <div class="card overview-details-box b-s-3-primary ">
          <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex align-items-center gap-1">
                        <span class="bg-light-primary h-60 w-120 d-flex-center flex-column rounded-3">
                            <span class="f-w-500"> Pelanggan</span>
                            <span>{{ $memberCount }}</span>
                        </span>
                        <div class="flex-grow-1">
                        <button type="button" class="btn btn-light-success h-60" id="openCreateModal">Add Pelanggan</button>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div style="margin-top:25px"></div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive custom-scrollbar" id="row_create">
              {!! $dataTable->table() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid Ends-->

  <!-- Unified Modal -->
  <div class="modal fade" id="adminModal" tabindex="-1" aria-label="adminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="adminModalLabel">Manage Admin</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="adminForm" method="POST">
          @csrf
          <input type="hidden" id="adminMethod" name="_method" value="POST">
          <div class="modal-body">
            <input type="hidden" id="id" name="id">
            <div class="row">
              <!-- <div class="col-6"> -->
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
                  <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3 col-6">
                  <label for="wa" class="form-label">wa</label>
                  <input type="text" class="form-control" id="wa" name="wa" required>
                </div>
                <div class="mb-3 col-6">
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
                <div class="mb-3 col-6">
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
                
                <div class="mb-3 col-12">
                  <label for="product_dinetkan_id" class="mb-1">Service</label>
                  <div class="form-group mb-3" style="display:grid">
                  <select class="form-control" id="product_dinetkan_id_service" name="product_dinetkan_id" autocomplete="off" data-placeholder="Pilih Service">
                      <div class="row">
                      <option value=""></option>
                      @forelse ($product as $pp)
                          <option value="{{ $pp->id }}">{{ $pp->product_name }}</option>
                      @empty
                      @endforelse
                      </div>
                  </select>

                  </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  
  <div class="modal fade" id="serviceModal" tabindex="-1" aria-label="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="serviceModalLabel">Manage Service</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="serviceForm" method="POST">
          @csrf
          <input type="hidden" id="serviceMethod" name="_method" value="POST">
          <div class="modal-body">
            <input type="hidden" id="id_service" name="id">
            <div class="row">
              <!-- <div class="col-6"> -->
                <div class="mb-3 col-12">
                  <label for="first_name" class="form-label">First Name</label>
                  <input type="text" class="form-control" id="first_name_service" name="first_name" required>
                </div>
                <div class="mb-3 col-12">
                  <label for="last_name" class="form-label">Last Name</label>
                  <input type="text" class="form-control" id="last_name_service" name="last_name" required>
                </div>
                <div class="mb-3 col-12">
                  <label for="product_dinetkan_id" class="mb-1">Service</label>
                  <div class="form-group mb-3" style="display:grid">
                  <select class="form-control" id="product_dinetkan_id_service" name="product_dinetkan_id" autocomplete="off" data-placeholder="Pilih Service">
                      <div class="row">
                      <option value=""></option>
                      @forelse ($product as $pp)
                          <option value="{{ $pp->id }}">{{ $pp->product_name }}</option>
                      @empty
                      @endforelse
                      </div>
                  </select>

                  </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
  <!-- <script src="{{ asset('assets/old/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/old/js/datatable/datatables/jquery.dataTables.min.js') }}"></script> -->
  <!-- <script src="{{ asset('assets/old/js/datatable/datatables/datatable.custom.js') }}"></script> -->
  {!! $dataTable->scripts() !!}

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

      // Open Create Modal
      $('#openCreateModal').on('click', function() {
        window.location = "{{route('admin.billing.member_dinetkan.add')}}";
      });
      // $('#openCreateModal').on('click', function() {
      //   form[0].reset();
      //   modal.find('.modal-title').text('Create New Pelanggan');
      //   form.attr('action', '{{ route('admin.billing.member_dinetkan.store') }}');
      //   methodField.val('POST');
      //   submitBtn.text('Create');
      //   set_maps();
        
      //   $.ajax({
      //   url: baseurl + '/settings/master/geo/provinces',
      //     type: "GET",
      //     success: function(data) {
      //       $('#provinsi').select2({
      //         data: (() => {
      //           return data.map((item) => {
      //               return {
      //                 id: item.id,
      //                 text: item.name
      //               }
      //             })
      //             .sort((a, b) => a.text.localeCompare(b.text));
      //         })(),
      //         allowClear: true,
      //         placeholder: $(this).data('placeholder'),
      //         dropdownParent: $("#adminModal .modal-content"),
      //       });
      //     }
      //   });
      //   modal.modal('show');
      // });

      // Edit Button Click Handler
      $(document).on('click', '.edit-icon.edit', function() {
        const userId = $(this).data('id');
        window.location =  `/admin/billing/member_dinetkan/edit_pelanggan/${userId}`;
      });

      // $(document).on('click', '.edit-icon.edit', function() {
      //   Swal.fire({
      //       title: 'Please wait...',
      //       text: 'Processing Data ....',
      //       allowOutsideClick: false,
      //       showConfirmButton: false,
      //       didOpen: () => {
      //           Swal.showLoading();
      //       }
      //   });
      //   const userId = $(this).data('id');

      //   $.ajax({
      //     url: `/admin/billing/member_dinetkan/single/${userId}`,
      //     method: 'GET',
      //     success: function(data) {
      //       swal.close();
      //       modal.find('.modal-title').text('Edit Admin');
      //       form.attr('action', `/admin/billing/member_dinetkan/update/${userId}`);
      //       methodField.val('PUT');
      //       submitBtn.text('Update');

      //       // Populate the form with data
      //       $('#id').val(data.id);
      //       $('#full_name').val(data.name);
      //       $('#email').val(data.email);
      //       $('#wa').val(data.wa);
      //       // $('#license_dinetkan_id').val(data.license_dinetkan_id);
      //       $('#next_due').val(data.next_due);
      //       // $('#username').val(data.username);
            
      //       $('#first_name').val(data.first_name);
      //       $('#last_name').val(data.last_name);
      //       $('#no_ktp').val(data.no_ktp);
      //       $('#npwp').val(data.npwp);
      //       $('#address').val(data.address);
      //       $('#province_id').val(data.province_id);
      //       $('#regency_id').val(data.regency_id);
      //       $('#district_id').val(data.district_id);
      //       $('#village_id').val(data.village_id);
      //       $('#latitude').val(data.latitude);
      //       $('#longitude').val(data.longitude);
      //       $('#product_dinetkan_id_service').val(data.product_dinetkan_id);
      //       set_maps(data.latitude,data.longitude);
            
      //       $.ajax({
      //           url: baseurl + '/settings/master/geo/provinces',
      //           type: 'GET',
      //           dataType: "json",
      //           success: function(response) {
      //               let villageOptions = response.map((item) => {
      //                   return {
      //                       id: item.id,
      //                       text: item.name
      //                   };
      //               }).sort((a, b) => a.text.localeCompare(b.text));

      //               $('#provinsi').select2({
      //                   data: villageOptions,
      //                   allowClear: true,
      //                   placeholder: $('#provinsi').data('placeholder'),
      //                   dropdownParent: $("#adminModal .modal-content"),
      //               });

      //               // Set default value sesuai dengan village_id
      //               if (data.province_id) {
      //                   $('#provinsi').val(data.province_id).trigger('change');
      //               }
      //           }
      //       });
            
      //       $.ajax({
      //           url: baseurl + '/settings/master/geo/regencies/' + data.province_id,
      //           type: 'GET',
      //           dataType: "json",
      //           success: function(response) {
      //               let villageOptions = response.map((item) => {
      //                   return {
      //                       id: item.id,
      //                       text: item.name
      //                   };
      //               }).sort((a, b) => a.text.localeCompare(b.text));

      //               $('#kabupaten').select2({
      //                   data: villageOptions,
      //                   allowClear: true,
      //                   placeholder: $('#kabupaten').data('placeholder'),
      //                   dropdownParent: $("#adminModal .modal-content"),
      //               });

      //               // Set default value sesuai dengan village_id
      //               if (data.regency_id) {
      //                   $('#kabupaten').val(data.regency_id).trigger('change');
      //               }
      //           }
      //       });

      //       $.ajax({
      //           url: baseurl + '/settings/master/geo/districts/' + data.regency_id,
      //           type: 'GET',
      //           dataType: "json",
      //           success: function(response) {
      //               let villageOptions = response.map((item) => {
      //                   return {
      //                       id: item.id,
      //                       text: item.name
      //                   };
      //               }).sort((a, b) => a.text.localeCompare(b.text));

      //               $('#kecamatan').select2({
      //                   data: villageOptions,
      //                   allowClear: true,
      //                   placeholder: $('#kecamatan').data('placeholder'),
      //                   dropdownParent: $("#adminModal .modal-content"),
      //               });

      //               // Set default value sesuai dengan village_id
      //               if (data.district_id) {
      //                   $('#kecamatan').val(data.district_id).trigger('change');
      //               }
      //           }
      //       });

      //       $.ajax({
      //           url: baseurl + '/settings/master/geo/villages/' + data.district_id,
      //           type: 'GET',
      //           dataType: "json",
      //           success: function(response) {
      //               let villageOptions = response.map((item) => {
      //                   return {
      //                       id: item.id,
      //                       text: item.name
      //                   };
      //               }).sort((a, b) => a.text.localeCompare(b.text));

      //               $('#desa').select2({
      //                   data: villageOptions,
      //                   allowClear: true,
      //                   placeholder: $('#desa').data('placeholder'),
      //                   dropdownParent: $("#adminModal .modal-content"),
      //               });

      //               // Set default value sesuai dengan village_id
      //               if (data.village_id) {
      //                   $('#desa').val(data.village_id).trigger('change');
      //               }
      //           }
      //       });
            
      //       modal.modal('show');
      //     },
      //     error: function(xhr) {
      //       alert('Error fetching admin data: ' + xhr.responseJSON.message);
      //     }
      //   });
      // });

      
      $(document).on('click', '.edit-icon.edit-service', function() {
        const userId = $(this).data('id');

        $.ajax({
          url: `/admin/billing/member_dinetkan/single/${userId}`,
          method: 'GET',
          success: function(data) {
            modalService.find('.modal-title').text('Edit Service');
            formService.attr('action', `/admin/billing/member_dinetkan/update_product/${userId}`);
            methodFieldService.val('PUT');
            submitBtn.text('Update');

            // Populate the form with data
            $('#id_service').val(data.id);            
            $('#first_name_service').val(data.first_name);
            $('#first_name_service').prop('readonly', true);
            $('#last_name_service').val(data.last_name);
            $('#last_name_service').prop('readonly', true);
            $('#product_dinetkan_id_service').val(data.product_dinetkan_id);
            
            modalService.modal('show');
          },
          error: function(xhr) {
            alert('Error fetching admin data: ' + xhr.responseJSON.message);
          }
        });
      });

      // Form Submission
      form.on('submit', function(e) {
        e.preventDefault();

        $.ajax({
          url: form.attr('action'),
          method: form.find('#adminMethod').val() === 'POST' ? 'POST' : 'PUT',
          data: form.serialize(),
          success: function(response) {
            modal.modal('hide');
            $('#users-table').DataTable().ajax.reload();
            // toastr.success(response.message);
            Swal.fire(
              'Berhasil!',
              response.message,
              'success'
            );
            location.reload();
          },
          error: function(xhr) {
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
            location.reload();
          }
        });
      });
    });

  </script>
  <script>
    
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
          dropdownParent: $("#adminModal .modal-content"),
        });
      }
    });

    $('#provinsi').on('change', function() {
      $('#kabupaten').empty();
      var id_provinsi = $(this).val();
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
            dropdownParent: $("#adminModal .modal-content"),
            });
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
            dropdownParent: $("#adminModal .modal-content"),
            });
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
            dropdownParent: $("#adminModal .modal-content"),
            });
        }
        });
    } else {
        $('#desa').empty();
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

    $(document).on('click', '.edit-icon.delete', function() {
        const userId = $(this).data('id');
        Swal.fire({
            title: 'Please wait...',
            text: 'Processing Data ....',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        $.ajax({
          url: `/admin/billing/member_dinetkan/single/${userId}`,
          method: 'GET',
          success: function(data) {
            swal.close();
            Swal.fire({
              title: 'Yakin ingin menghapus?',
              text: "Data yang dihapus tidak dapat dikembalikan!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Ya, hapus!',
              cancelButtonText: 'Batal'
            }).then((result) => {
              if (result.isConfirmed) {
                // Lakukan AJAX request
                $.ajax({
                  url: baseurl + '/admin/billing/member_dinetkan/single_delete/' + data.id, // Ganti dengan URL endpoint sesuai kebutuhan
                  type: 'POST', // Bisa juga POST tergantung backend-mu
                  data: {
                    _token: '{{ csrf_token() }}' // Jika pakai Laravel atau token lainnya
                  },
                  success: function (response) {
                    location.reload();
                    Swal.fire(
                      'Berhasil!',
                      'Data telah dihapus.',
                      'success'
                    );

                    // Misal: hapus baris dari tabel
                    // $('#row-' + id).remove();
                  },
                  error: function (xhr) {
                    Swal.fire(
                      'Gagal!',
                      'Terjadi kesalahan saat menghapus data.',
                      'error'
                    );
                  }
                });
              }
            });
          },
          error: function(xhr) {
            swal.close();
            alert('Error fetching admin data: ' + xhr.responseJSON.message);
          }
        });
      });
  </script>
@endpush
