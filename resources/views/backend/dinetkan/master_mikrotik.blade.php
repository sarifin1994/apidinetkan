@extends('backend.layouts.app_new')

@section('title', 'Master MIkrotik')

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
            <h3>Master Mikrotik</h3>
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
                      <i class="fa-solid fa-plus"></i> Add Mikrotik
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
                <th>NAME</th>
                <th>IP</th>
                <th>PORT</th>
                <th>USERNAME</th>
                <th>TIMEOUT</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($mikrotik as $row)
                <tr>
                  <td>{{ $row->id}}</td>
                  <td>{{ $row->name}}</td>
                  <td>{{ $row->ip}}</td>
                  <td>{{ $row->port}}</td>
                  <td>{{ $row->username}}</td>
                  <td>{{ $row->timeout}}</td>
                  <td>
                    <div class="action-div">
                        <a href="javascript:void(0)" class="edit-icon edit btn btn-light-warning btn-xs" data-id="{{ $row->id}}">
                          <span class="material-symbols-outlined">edit_square</span>
                        </a>
                        <a href="javascript:void(0)" class="edit-icon delete btn btn-light-danger btn-xs" data-id="{{ $row->id}}">
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
              <label for="name" class="form-label">NAMA</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div> 
            <div class="mb-3">
              <label for="ip" class="form-label">IP</label>
              <input type="text" class="form-control" id="ip" name="ip" required>
            </div> 
            <div class="mb-3">
              <label for="port" class="form-label">port</label>
              <input type="text" class="form-control" id="port" name="port" required>
            </div> 
            <div class="mb-3">
              <label for="username" class="form-label">username</label>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">password</label>
              <input type="text" class="form-control" id="password" name="password" required>
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

  <script>
    $(document).ready(function() {
      $('#kuponTable').DataTable();
      const modal = $('#couponModal');
      const form = $('#couponForm');
      const methodField = $('#couponMethod');
      const submitBtn = $('#submitBtn');

      // Open Create Modal
      $('#openCreateModal').on('click', function() {
        form[0].reset();
        modal.find('.modal-title').text('Create New Master Mikrotik');
        form.attr('action', '{{ route('dinetkan.master_mikrotik.store') }}');
        methodField.val('POST');
        submitBtn.text('Create');
        modal.modal('show');
      });

      // Edit Button Click Handler
      $(document).on('click', '.edit-icon.edit', function() {
        const userId = $(this).data('id');

        $.ajax({
          url: `/dinetkan/master_mikrotik/get_single/${userId}`,
          method: 'GET',
          success: function(data) {
            modal.find('.modal-title').text('Update MIkrotik');
            form.attr('action', `/dinetkan/master_mikrotik/${userId}`);
            methodField.val('put');
            submitBtn.text('Update');

            // Populate the form with data
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#ip').val(data.ip);
            $('#port').val(data.port);
            $('#username').val(data.username);
            $('#password').val(data.password);

            modal.modal('show');
          },
          error: function(xhr) {
            alert('Error fetching coupon data: ' + xhr.responseJSON.message);
          }
        });
      });

      
      $(document).on('click', '.edit-icon.delete', function() {
        const userId = $(this).data('id');

        $.ajax({
          url: `/dinetkan/master_mikrotik/get_single/${userId}`,
          method: 'GET',
          success: function(data) {
            modal.find('.modal-title').text('Delete MIkrotik');
            form.attr('action', `/dinetkan/master_mikrotik/${userId}`);
            methodField.val('POST');
            submitBtn.text('Delete');

            // Populate the form with data
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#ip').val(data.ip);
            $('#port').val(data.port);
            $('#username').val(data.username);
            $('#password').val(data.password);

            $('#ip').prop('readonly', true);
            $('#name').prop('readonly', true);
            $('#port').prop('readonly', true);
            $('#username').prop('readonly', true);
            $('#passowrd').prop('readonly', true);

            modal.modal('show');
          },
          error: function(xhr) {
            alert('Error fetching coupon data: ' + xhr.responseJSON.message);
          }
        });
      });
    });

  </script>

@endpush
