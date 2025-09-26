@extends('backend.layouts.app_new')

@section('title', 'Master Metro')

@section('css')
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}">

  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
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
            <h3>Master Metro</h3>
          </div>
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-12">
        <div class="card overview-details-box b-s-3-primary ">
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <div class="d-flex align-items-center gap-1">
                  <div class="flex-grow-1">
                    <button type="button" class="btn btn-light-success h-60" id="openCreateModal">
                      <i class="fa-solid fa-plus"></i> Add Metro
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
        <div class="table-responsive custom-scrollbar">
          <table id="kuponTable" class="table-hover display nowrap clickable table" width="100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>PIC</th>
                <th>PIC Phone</th>
                <th>WA GROUP</th>
                <th>Province</th>
                <th>City</th>
                <th>District</th>
                <th>Village</th>
                <th>Address</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($metro as $row)
                <tr>
                  <td>{{ $row->id}}</td>
                  <td>{{ $row->name}}</td>
                  <td>{{ $row->pic}}</td>
                  <td>{{ $row->pic_phone}}</td>
                  <td>{{ $row->name_wag}}</td>
                  <td>{{ $row->province->name}}</td>
                  <td>{{ $row->regency->name}}</td>
                  <td>{{ $row->district->name}}</td>
                  <td>{{ $row->village->name}}</td>
                  <td>{{ $row->address}}</td>
                  <td>
                    <div class="action-div">
                        <a href="javascript:void(0)" class="edit-icon edit btn btn-light-warning" data-id="{{ $row->id}}">
                          <span class="material-symbols-outlined">edit_square</span>
                        </a>
                        
                        <a href="javascript:void(0)" class="delete-icon delete btn btn-light-danger" data-id="{{ $row->id}}">
                          <span class="material-symbols-outlined">delete</span>
                        </a>
                    </div>
                  </td>
                </tr>
              @empty
              @endforelse
            </tbody>
          </table>
        </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid Ends-->

  <!-- Unified Modal -->
  <div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="couponModalLabel">Manage Coupon</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="couponForm" method="POST">
          @csrf
          <input type="hidden" id="couponMethod" name="_method" value="POST">
          <div class="modal-body">
            <input type="hidden" id="id" name="id">
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
              <label for="pic" class="form-label">PIC</label>
              <input type="text" class="form-control" id="pic" name="pic" required>
            </div> 
            <div class="mb-3">
              <label for="pic_phone" class="form-label">PIC Phone</label>
              <input type="text" class="form-control" id="pic_phone" name="pic_phone" required>
            </div>
            <div class="mb-3">
              <label for="id_wag" class="form-label">ID Whatsapp group</label>
              <!-- <input type="text" class="form-control" id="id_wag" name="id_wag" required> -->
                  <div class="form-group mb-3" style="display:grid">
                  <select class="form-control" id="id_wag" name="id_wag" autocomplete="off" data-placeholder="Pilih WAGroup">
                      <div class="row">
                      <option value=""></option>
                      @forelse ($wag as $row)
                          <option value="{{ $row->group_id }}" >{{ $row->group_name }}</option>
                      @empty
                      @endforelse
                      </div>
                  </select>

                  </div>
            </div> 
                <div class="mb-3">
                  <label for="provinsi" class="mb-1">Provinsi</label>
                  <div class="form-group mb-3" style="display:grid">
                  <select class="form-control" id="provinsi" name="province_id" autocomplete="off" data-placeholder="Pilih Provinsi">
                      
                  </select>

                  </div>
                </div>
                <div class="mb-3">
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
                <div class="mb-3">
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
                <div class="mb-3">
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
  <script src="{{ asset('assets/old/js/datatable/datatables/jquery.dataTables.min.`js') }}"></script>
  <!-- <script src="{{ asset('assets/old/js/datatable/datatables/datatable.custom.js') }}"></script> --> -->

  <script>
    
    $('#id_wag').select2({
        allowClear: true,
        dropdownParent: $("#couponModal .modal-content"),
    });
    const baseurl = "{{ url('/') }}" //baseUrl.clone().pop().pop().pop().pop();
    $(document).ready(function() {
      $('#kuponTable').DataTable();
      const modal = $('#couponModal');
      const form = $('#couponForm');
      const methodField = $('#couponMethod');
      const submitBtn = $('#submitBtn');

      // Open Create Modal
      $('#openCreateModal').on('click', function() {
        form[0].reset();
        modal.find('.modal-title').text('Create New Master Metro');
        form.attr('action', '{{ route('dinetkan.master_metro.store') }}');
        methodField.val('POST');
        submitBtn.text('Create');
        
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
              dropdownParent: $("#couponModal .modal-content"),
            });
          }
        });
        
        modal.modal('show');
      });

      $(document).on('click', '.delete-icon.delete', function() {
        const userId = $(this).data('id');

        $.ajax({
          url: `/dinetkan/master_metro/get_single/${userId}`,
          method: 'GET',
          success: function(data) {
            modal.find('.modal-title').text('Delete Metro');
            form.attr('action', `/dinetkan/master_metro/delete/${userId}`);
            methodField.val('POST');
            submitBtn.text('Delete');

            // Populate the form with data
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#pic').val(data.pic);
            $('#pic_phone').val(data.pic_phone);
            $('#id_wag').val(data.id_wag);
            $('#province_id').val(data.province_id);
            $('#regency_id').val(data.regency_id);
            $('#district_id').val(data.district_id);
            $('#village_id').val(data.village_id);
            $('#address').val(data.address);

            
            $('#name').prop('readonly', true);
            $('#pic').prop('readonly', true);
            $('#pic_phone').prop('readonly', true);
            $('#id_wag').prop('readonly', true);
            $('#province_id').prop('readonly', true);
            $('#regency_id').prop('readonly', true);
            $('#district_id').prop('readonly', true);
            $('#village_id').prop('readonly', true);
            $('#address').prop('readonly', true);

            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/provinces',
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
                        dropdownParent: $("#couponModal .modal-content"),
                    });

                    // Set default value sesuai dengan village_id
                    if (data.province_id) {
                        $('#provinsi').val(data.province_id).trigger('change');
                    }
                }
            });
            
            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/regencies/' + data.province_id,
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
                        dropdownParent: $("#couponModal .modal-content"),
                    });

                    // Set default value sesuai dengan village_id
                    if (data.regency_id) {
                        $('#kabupaten').val(data.regency_id).trigger('change');
                    }
                }
            });

            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/districts/' + data.regency_id,
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
                        dropdownParent: $("#couponModal .modal-content"),
                    });

                    // Set default value sesuai dengan village_id
                    if (data.district_id) {
                        $('#kecamatan').val(data.district_id).trigger('change');
                    }
                }
            });

            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/villages/' + data.district_id,
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
                        dropdownParent: $("#couponModal .modal-content"),
                    });

                    // Set default value sesuai dengan village_id
                    if (data.village_id) {
                        $('#desa').val(data.village_id).trigger('change');
                    }
                }
            });

            modal.modal('show');
          },
          error: function(xhr) {
            alert('Error fetching coupon data: ' + xhr.responseJSON.message);
          }
        });
      });

      // Edit Button Click Handler
      $(document).on('click', '.edit-icon.edit', function() {
        const userId = $(this).data('id');

        $.ajax({
          url: `/dinetkan/master_metro/get_single/${userId}`,
          method: 'GET',
          success: function(data) {
            modal.find('.modal-title').text('EDIT Metro');
            form.attr('action', `/dinetkan/master_metro/${userId}`);
            methodField.val('POST');
            submitBtn.text('Edit');

            // Populate the form with data
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#pic').val(data.pic);
            $('#pic_phone').val(data.pic_phone);
            $('#id_wag').val(data.id_wag);
            $('#province_id').val(data.province_id);
            $('#regency_id').val(data.regency_id);
            $('#district_id').val(data.district_id);
            $('#village_id').val(data.village_id);
            $('#address').val(data.address);
            if (data.id_wag) {
                $('#id_wag').val(data.id_wag).trigger('change');
            }
            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/provinces',
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
                        dropdownParent: $("#couponModal .modal-content"),
                    });

                    // Set default value sesuai dengan village_id
                    if (data.province_id) {
                        $('#provinsi').val(data.province_id).trigger('change');
                    }
                }
            });
            
            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/regencies/' + data.province_id,
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
                        dropdownParent: $("#couponModal .modal-content"),
                    });

                    // Set default value sesuai dengan village_id
                    if (data.regency_id) {
                        $('#kabupaten').val(data.regency_id).trigger('change');
                    }
                }
            });

            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/districts/' + data.regency_id,
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
                        dropdownParent: $("#couponModal .modal-content"),
                    });

                    // Set default value sesuai dengan village_id
                    if (data.district_id) {
                        $('#kecamatan').val(data.district_id).trigger('change');
                    }
                }
            });

            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/villages/' + data.district_id,
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
                        dropdownParent: $("#couponModal .modal-content"),
                    });

                    // Set default value sesuai dengan village_id
                    if (data.village_id) {
                        $('#desa').val(data.village_id).trigger('change');
                    }
                }
            });

            modal.modal('show');
          },
          error: function(xhr) {
            alert('Error fetching coupon data: ' + xhr.responseJSON.message);
          }
        });
      });

      // Form Submission
      form.on('submit', function(e) {
        e.preventDefault();

        $.ajax({
          url: form.attr('action'),
          method: form.find('#couponMethod').val() === 'POST' ? 'POST' : 'PUT',
          data: form.serialize(),
          success: function(response) {
            modal.modal('hide');
            // $('#kuponTable').DataTable().ajax.reload();
            // console.log('suksessss');
            toastr.success(response.message);
            location.reload();
          },
          error: function(xhr) {
            const errors = xhr.responseJSON.errors;
            let message = '';

            for (const key in errors) {
              message += errors[key] + '\n';
            }

            toastr.error(message);
          }
        });
      });
    });

    $(document).on('click', '.view-login-history', function() {
      const userId = $(this).data('id');

      // Destroy existing DataTable if it exists
      if ($.fn.DataTable.isDataTable('#login-history-table')) {
        $('#login-history-table').DataTable().destroy();
      }

      $('#loginHistoryModal').modal('show');
    });

    $('#user_id').select2({
      allowClear: true,
      placeholder: $(this).data('placeholder'),
      dropdownParent: $("#couponModal .modal-content")
    });

  $('#license_id').select2({
    allowClear: true,
    placeholder: $(this).data('placeholder'),
    dropdownParent: $("#couponModal .modal-content")
  });

  function cek_radio(val){
    
    const type_nominal = document.getElementById("type_nominal");
    const type_percent = document.getElementById("type_percent");
    
    const nominal = document.getElementById("nominal");
    const percent = document.getElementById("percent");
    if(val == 'percent'){
      nominal.style.display = 'none';
      percent.style.display = 'block';
    }
    if(val == 'nominal'){
      nominal.style.display = 'block';
      percent.style.display = 'none';
    }
  }
  </script>
  <script>
    
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
          dropdownParent: $("#couponModal .modal-content"),
        });
      }
    });

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
            dropdownParent: $("#couponModal .modal-content"),
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
            dropdownParent: $("#couponModal .modal-content"),
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
            dropdownParent: $("#couponModal .modal-content"),
            });
        }
        });
    } else {
        $('#desa').empty();
    }
    });
    </script>
@endpush
