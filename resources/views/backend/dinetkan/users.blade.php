@extends('backend.layouts.app_new')

@section('title', 'Users Management')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Users Management</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dinetkan.dashboard') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item active">Users Management</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Container-fluid starts-->
  <div class="container-fluid user-management-page">
    <div class="row">
      <div class="col-xxl-4 box-col-4">
        <div class="card">
          <div class="card-body">
            <div class="bg-light-primary b-r-15">
              <div class="upcoming-box d-flex align-items-center justify-content-between px-4">
                <div>
                  <div class="upcoming-icon bg-primary">
                    <svg class="stroke-icon">
                      <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-form') }}"></use>
                    </svg>
                  </div>
                  <p>{{ $adminCount }} Admin</p>
                </div>
                <button type="button" class="btn btn-primary" id="openCreateModal">Add Admin</button>
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
  <div class="modal fade" id="adminModal" tabindex="-1" aria-labelledby="adminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
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
            <div class="mb-3">
              <label for="full_name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>
            <div class="mb-3">
              <label for="company_name" class="form-label">Company Name</label>
              <input type="text" class="form-control" id="company_name" name="company_name" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="whatsapp" class="form-label">WhatsApp</label>
              <input type="text" class="form-control" id="whatsapp" name="whatsapp" required>
            </div>
            <div class="mb-3">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" id="status" name="status" required>
                <option value="">Select Status</option>
                <option value="0">Inactive</option>
                <option value="1">Active</option>
                <option value="2">New</option>
                <option value="3">Expired</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="license_id" class="form-label">License</label>
              <select class="form-select" id="license_id" name="license_id" required>
                <option value="">Select License</option>
                @foreach ($licenses as $license)
                  <option value="{{ $license->id }}">{{ $license->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label for="next_due" class="form-label">Next Due</label>
              <input type="date" class="form-control" id="next_due" name="next_due">
            </div>
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password">
              <small class="text-muted">Leave blank if you don't want to change the password</small>
            </div>
            <div class="mb-3">
              <label for="is_dinetkan" class="form-label">Mitra</label>
              <select class="form-select" id="is_dinetkan" name="is_dinetkan" required>
                <option value="">Select Status</option>
                <option value="0">Inactive</option>
                <option value="1">Active</option>
              </select>
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
  <div class="modal fade" id="loginHistoryModal" tabindex="-1" aria-labelledby="loginHistoryModalLabel"
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
  </div>

@endsection

@section('scripts')
  <script src="{{ asset('assets/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
  {!! $dataTable->scripts() !!}

  <script>
    $(document).ready(function() {
      const modal = $('#adminModal');
      const form = $('#adminForm');
      const methodField = $('#adminMethod');
      const submitBtn = $('#submitBtn');

      // Open Create Modal
      $('#openCreateModal').on('click', function() {
        form[0].reset();
        modal.find('.modal-title').text('Create New Admin');
        form.attr('action', '{{ route('dinetkan.users.store') }}');
        methodField.val('POST');
        submitBtn.text('Create');
        modal.modal('show');
      });

      // Edit Button Click Handler
      $(document).on('click', '.edit-icon.edit', function() {
        const userId = $(this).data('id');

        $.ajax({
          url: `/dinetkan/users/${userId}`,
          method: 'GET',
          success: function(data) {
            modal.find('.modal-title').text('Edit Admin');
            form.attr('action', `/dinetkan/users/${userId}`);
            methodField.val('PUT');
            submitBtn.text('Update');

            // Populate the form with data
            $('#id').val(data.id);
            $('#full_name').val(data.name);
            $('#company_name').val(data.company_name);
            $('#email').val(data.email);
            $('#whatsapp').val(data.whatsapp);
            $('#status').val(data.status);
            $('#license_id').val(data.license_id);
            $('#next_due').val(data.next_due);
            $('#username').val(data.username);
            $('#is_dinetkan').val(data.is_dinetkan);

            modal.modal('show');
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
            toastr.success(response.message);
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

      // Initialize DataTable
      $('#login-history-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: `/dinetkan/users/${userId}/login-histories`,
          type: 'GET'
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'ip_address',
            name: 'ip_address'
          },
          {
            data: 'user_agent',
            name: 'user_agent'
          },
          {
            data: 'login_at',
            name: 'login_at',
            render: function(data) {
              return moment(data).format('DD/MM/YYYY HH:mm:ss');
            }
          }
        ],
        language: {
          emptyTable: 'No login history available',
          zeroRecords: 'No matching records found'
        }
      });

      $('#loginHistoryModal').modal('show');
    });
  </script>
@endsection
