@extends('backend.layouts.app_new')

@section('title', 'Users Management')

@section('css')
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}"> -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">
@endsection

@section('main')
  <!-- container-fluid starts-->
  <div class="container-fluid user-management-page">
    <div class="container-fluid">
      <div class="page-title">
        <div class="row">
          <div class="col-sm-6 ps-0">
            <h3>Mitra Management</h3>
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
                  <span class="bg-light-primary h-60 w-120 d-flex-center flex-column rounded-3">
                    <span class="f-w-500"> Mitra</span>
                    <span>{{ $adminCount }}</span>
                  </span>
                  <div class="flex-grow-1">
                    <a href="{{ route('dinetkan.users_dinetkan.create') }}" type="button" class="btn btn-light-success h-60" ><i class="fa-solid fa-plus"></i> Add Mitra</a>
                    <a type="button" class="btn btn-light-warning h-60" id="openImportModal"><i class="fa-solid fa-plus"></i> Add Mitra Import</a> 
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div style="margin-top:10px"></div>

    <div class="row">

      <div class="col-12 mb-3 rounded">
        <ul class="nav nav-pills justify-content-center" id="data-tab" role="tablist">
          <li class="nav-item" role="presentation"><a class="nav-link active" id="new-tab" data-bs-toggle="tab"
              href="#new" role="tab" aria-controls="new" aria-selected="true">
              <i class="icofont icofont-ui-user"></i>
              New
            </a>
          </li>
          <li class="nav-item" role="presentation"><a class="nav-link" id="active-tab" data-bs-toggle="tab"
              href="#active" role="tab" aria-controls="active" aria-selected="true">
              <i class="icofont icofont-ui-user"></i>
              Active
            </a>
          </li>
        </ul>
      </div>

      <div class="col-sm-12">
        
        <div class="tab-content" id="active-tabContent">
            <div class="tab-pane fade active show" id="new" role="tabpanel" aria-labelledby="new-tab">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body table-responsive">
                      <table id="newmyTable" class="table table-responsive table-hover display nowrap" width="100%">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>User Id</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Whatsapp</th>
                            <th>Username</th>
                            <!-- <th>MRC</th> -->
                            <th>Joined</th>
                            <th>Company</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="active" role="tabpanel" aria-labelledby="active-tab">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body table-responsive">
                      <table id="activemyTable" class="table table-hover display nowrap" width="100%">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>User Id</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Whatsapp</th>
                            <th>Username</th>
                            <!-- <th>MRC</th> -->
                            <th>Joined</th>
                            <th>Company</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
  <!-- container-fluid Ends-->

  <div class="modal fade" id="adminImportModal" tabindex="-1" aria-label="adminImportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="adminModalLabel">Manage Admin</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="adminImportForm" method="POST">
          <div class="modal-body">
            @csrf
            <input type="hidden" id="adminImportMethod" name="_method" value="POST">
            <div class="row">
                <div class="mb-3 col-12">
                  <label for="import_admin" class="mb-1">User Admin</label>
                  <select class="form-control" id="import_admin" name="import_admin[]" data-placeholder="--- Pilih User ---"></select>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modalImport">Close</button>
              <button type="submit" class="btn btn-primary" id="submitImportBtn">Save</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

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
            <!-- <input type="hidden" id="id" name="id"> -->
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
                  <label for="whatsapp" class="form-label">WhatsApp</label>
                  <input type="text" class="form-control" id="whatsapp" name="whatsapp" required>
                </div>
                <div class="mb-3 col-6">
                  <label for="company_name" class="form-label">Company Name</label>
                  <input type="text" class="form-control" id="company_name" name="company_name" required>
                </div>
                <div class="mb-3 col-6">
                  <label for="status" class="form-label">Status</label>
                  <select class="form-select" id="status" name="status" required>
                    <!-- <option value="">Select Status</option>
                    <option value="0">Inactive</option>
                    <option value="1">Active</option> -->
                    <option value="2">New</option>
                    <!-- <option value="3">Expired</option> -->
                  </select>
                </div>
                <div class="mb-3 col-6">
                  <label for="id_card" class="form-label">No. KTP / No. SIM </label>
                  <div class="input-group">
                      <input type="text" class="form-control" id="id_card" name="id_card" :class="errors.id_card && 'is-invalid'">
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


  <!-- edit cacti -->
  
  <div class="modal fade" id="adminCactiModal" tabindex="-1" aria-label="adminCactiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="adminCactiModalLabel">Manage Admin</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="adminCactiForm" method="POST">
          @csrf
          <input type="hidden" id="adminCactiMethod" name="_method" value="POST">
          <div class="modal-body">
            <input type="hidden" id="id" name="id">
            <input type="hidden" id="dinetkan_user_id" name="dinetkan_user_id">
            
            <div class="row">
              <div class="mb-3">
                  <label for="vlan" class="form-label">VLAN</label>
                  <input type="text" class="form-control" id="vlan" name="vlan" required>
                </div>
                <div class="mb-3">
                  <label for="metro" class="form-label">Metro</label>
                  <input type="text" class="form-control" id="metro" name="metro" required>
                </div>
                <div class="mb-3">
                  <label for="vendor" class="form-label">ID/CID/SO/SID Vendor</label>
                  <input type="text" class="form-control" id="vendor" name="vendor" required>
                </div>
                <div class="mb-3">
                  <label for="trafic_mrtg" class="form-label">Trafic MRTG</label>
                  <label for="trafic_mrtg_tree" class="form-label">Tree</label>
                  <select class="form-select" id="trafic_mrtg_tree" name="trafic_mrtg_tree" required>
                    <option value="">Select Tree</option>
                    
                  </select>
                </div>
                <div class="mb-3">
                  <label for="trafic_mrtg_tree_node" class="form-label">Tree Node</label>
                  <select class="form-control" id="trafic_mrtg_tree_node" name="trafic_mrtg_tree_node" required>
                    <option value="">Select Node</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="graph_name" class="form-label">Graph Name</label>
                  <input type="text" class="form-control" id="graph_name" name="graph_name" required placeholder="graph name">
                </div>
                <div class="mb-3">
                  <label for="trafic_mrtg_graph" class="form-label">Graph</label>
                  <select class="form-control" id="trafic_mrtg_graph" name="trafic_mrtg_graph" required>
                    <option value="">Select Graph</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="ip_prefix" class="form-label">IP Prefix</label>
                  <input type="text" class="form-control" id="ip_prefix" name="ip_prefix" required>
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

  <!-- Login History Modal -->
  <!-- <div class="modal fade" id="loginHistoryModal" tabindex="-1" aria-label="loginHistoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="loginHistoryModalLabel">Login History</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive custom-scrollbar">
            <table id="login-history-table" class="table-bordered table-striped datatable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>IP Address</th>
                  <th>User Agent</th>
                  <th>Login Date</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div> -->
  

@endsection

@push('scripts')
  <script type="text/javascript">
      
    const baseurl = "{{ url('/') }}" //baseUrl.clone().pop().pop().pop().pop();
    $(document).on('click', '.delete-icon', function() {
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
        url: baseurl + `/dinetkan/users_dinetkan/get_single/${userId}`,
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
                url: baseurl + '/dinetkan/users_dinetkan/delete/' + data.id, // Ganti dengan URL endpoint sesuai kebutuhan
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
          alert('Error fetching admin data: ' + xhr.responseJSON.message);
        }
      });
    });

    let table = $('#newmyTable').DataTable({
      processing: true,
      serverSide: true,
      scrollX: true,
      ajax: {
        url: `{{ route('dinetkan.users_dinetkan.getdata') }}`,
        data: {
          status: 4
        }
      },
      columns: [
        {
          data: null,
          'sortable': false,
          className: "text-center",
          render: function(data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {
          data: 'dinetkan_user_id',
          name: 'dinetkan_user_id'
        },
        {
          data: 'first_name',
          name: 'first_name'
        },
        {
          data: 'last_name',
          name: 'last_name',
        },
        {
          data: 'email',
          name: 'email'
        },
        {
          data: 'whatsapp',
          name: 'whatsapp'
        },
        {
          data: 'username',
          name: 'username'
        },
        // {
        //   data: 'mrc_license_dinetkan_id',
        //   name: 'mrc_license_dinetkan_id',
        // },
        {
          data: 'created_at',
          name: 'created_at'
        },
        {
          data: 'company',
          name: 'company.name',
        },
        {
          data: 'action',
          name: 'action'
        }
      ]
    });

    let activetable = $('#activemyTable').DataTable({
      processing: true,
      serverSide: true,
      scrollX: true,
      // ajax: '{{ url()->current() }}',
      ajax: {
        url: `{{ route('dinetkan.users_dinetkan.getdata') }}`,
        data: {
          status: 1
        }
      },
      columns: [
        {
          data: null,
          'sortable': false,
          className: "text-center",
          render: function(data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {
          data: 'dinetkan_user_id',
          name: 'dinetkan_user_id'
        },
        {
          data: 'first_name',
          name: 'first_name'
        },
        {
          data: 'last_name',
          name: 'last_name',
        },
        {
          data: 'email',
          name: 'email'
        },
        {
          data: 'whatsapp',
          name: 'whatsapp'
        },
        {
          data: 'username',
          name: 'username'
        },
        // {
        //   data: 'mrc_license_dinetkan_id',
        //   name: 'mrc_license_dinetkan_id',
        // },
        {
          data: 'created_at',
          name: 'created_at'
        },
        {
          data: 'company',
          name: 'company.name',
        },
        {
          data: 'action',
          name: 'action'
        }
      ]
    });

    $(document).ready(function() {
      const modal = $('#adminModal');
      const form = $('#adminForm');
      const methodField = $('#adminMethod');
      const submitBtn = $('#submitBtn');

      const modalImport = $('#adminImportModal');
      const formImport = $('#adminImportForm');
      const methodImportField = $('#adminImportMethod');
      const submitImportBtn = $('#submitImportBtn');

      // Open Create Modal
      $('#openImportModal').on('click', function() {
        formImport[0].reset();
        modalImport.find('.modal-title').text('Import New Admin');
        formImport.attr('action', '{{ route('dinetkan.users_dinetkan.import') }}');
        methodImportField.val('POST');
        submitImportBtn.text('Import');
        $('#import_admin').select2({
          data: [],
          placeholder: $('#import_admin').data('placeholder'),
          allowClear: true,
          multiple: false, // ini tidak wajib di JS jika sudah di HTML
          dropdownParent: $("#adminImportModal .modal-content"),
        });

        $.ajax({
          url: baseurl + '/dinetkan/users_dinetkan_get_admin_import',
          type: "GET",
          success: function(data) {
              const userData = data.map((item) => ({
                  id: item.id,
                  text: item.name
              })).sort((a, b) => a.text.localeCompare(b.text));

              // Tambahkan pilihan default di awal
              userData.unshift({ id: '', text: '--- Pilih User ---' });

              $('#import_admin').select2({
                  data: userData,
                  placeholder: $('#import_admin').data('placeholder'),
                  allowClear: true,
                  multiple: false, // ini tidak wajib di JS jika sudah di HTML
                  dropdownParent: $("#adminImportModal .modal-content"),
              });
          }
      });

        modalImport.modal('show');
      });

      
      const modalCacti = $('#adminCactiModal');
      const formCacti = $('#adminCactiForm');
      const methodCactiField = $('#adminCactiMethod');

      // Open Create Modal
      $('#openCreateModal').on('click', function() {
        form[0].reset();
        modal.find('.modal-title').text('Create New Admin');
        form.attr('action', '{{ route('dinetkan.users_dinetkan.store') }}');
        methodField.val('POST');
        submitBtn.text('Create');
        set_maps();
    
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
            dropdownParent: $("#adminModal .modal-content"),
            });
        }
        });
        modal.modal('show');
      });
      
      formImport.on('submit', function(e) {
        e.preventDefault();        

        Swal.fire({
            title: 'Please wait...',
            text: 'Processing Data ....',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url: formImport.attr('action'),
          method: 'POST',
          data: formImport.serialize(),
          success: function(response) {
            modal.modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: `${response.message}`,
                    showConfirmButton: false,
                    timer: 10000
                });
            window.location.href = response.redirect;

          },
          error: function(xhr) {
            swal.close();
            const errors = xhr.responseJSON.errors;
            let message = '';

            for (const key in errors) {
              message += errors[key] + '\n';
            }
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `${message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
          }
        });
      });
         
      formCacti.on('submit', function(e) {
        e.preventDefault();

        $.ajax({
          url: formCacti.attr('action'),
          method: formCacti.find('#adminCactiMethod').val() === 'POST' ? 'POST' : 'PUT',
          data: formCacti.serialize(),
          success: function(response) {
            modalCacti.modal('hide');
            // $('#users-table').DataTable().ajax.reload();
            // toastr.success(response.message);
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `${response.message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
          },
          error: function(xhr) {
            const errors = xhr.responseJSON.errors;
            let message = '';

            for (const key in errors) {
              message += errors[key] + '\n';
            }

            // toastr.error(message);
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `${message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
          }
        });
      });
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
    }

    function import_set_maps(defaultLat = -6.200000,defaultLng = 106.816666){
          // var map = null;
          if (map !== null) {
              map.remove(); // Hapus instance peta sebelumnya
          }
           // Default ke Jakarta jika data kosong
           var defaultLat = -6.200000;
           var defaultLng = 106.816666;
  
           // Ambil data dari input hidden (diisi dari database)
           var latInput = document.getElementById("import_latitude").value.trim();
           var lngInput = document.getElementById("import_longitude").value.trim();
  
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
               document.getElementById('import_latitude').value = position.lat;
               document.getElementById('import_longitude').value = position.lng;
           });
    }
  </script>
@endpush
