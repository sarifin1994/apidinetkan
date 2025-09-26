@extends('backend.layouts.app_new')

@section('title', 'Master POP')

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
            <h3>Master POP</h3>
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
                      <i class="fa-solid fa-plus"></i> Add POP
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
                <th>Whatsapp</th>
                <th>IP</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($pop as $row)
                <tr>
                  <td>{{ $row->id}}</td>
                  <td>{{ $row->name}}</td>
                  <td>{{ $row->pic_name}}</td>
                  <td>{{ $row->pic_whatsapp}}</td>
                  <td>{{ $row->ip}}</td>
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
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div> 
            <div class="mb-3">
              <label for="pic_name" class="form-label">PIC Name</label>
              <input type="text" class="form-control" id="pic_name" name="pic_name" required>
            </div> 
            <div class="mb-3">
              <label for="pic_whatsapp" class="form-label">PIC Whatsapp</label>
              <input type="text" class="form-control" id="pic_whatsapp" name="pic_whatsapp" required>
            </div>
            <div class="mb-3">
              <label for="ip" class="form-label">IP</label>
              <input type="text" class="form-control" id="ip" name="ip" required>
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
        modal.find('.modal-title').text('Create New Master POP');
        form.attr('action', '{{ route('dinetkan.master_pop.store') }}');
        methodField.val('POST');
        submitBtn.text('Create');
        modal.modal('show');
      });

      // Edit Button Click Handler
      $(document).on('click', '.edit-icon.edit', function() {
        const userId = $(this).data('id');

        $.ajax({
          url: `/dinetkan/master_pop/get_single/${userId}`,
          method: 'GET',
          success: function(data) {
            modal.find('.modal-title').text('Update POP');
            form.attr('action', `/dinetkan/master_pop/${userId}`);
            methodField.val('put');
            submitBtn.text('Update');

            // Populate the form with data
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#pic_name').val(data.pic_name);
            $('#pic_whatsapp').val(data.pic_whatsapp);
            $('#ip').val(data.ip);

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
          url: `/dinetkan/master_pop/get_single/${userId}`,
          method: 'GET',
          success: function(data) {
            modal.find('.modal-title').text('Delete POP');
            form.attr('action', `/dinetkan/master_pop/${userId}`);
            methodField.val('POST');
            submitBtn.text('Delete');

            // Populate the form with data
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#pic_name').val(data.pic_name);
            $('#pic_whatsapp').val(data.pic_whatsapp);
            $('#ip').val(data.ip);

            $('#name').prop('readonly', true);
            $('#pic_name').prop('readonly', true);
            $('#pic_whatsapp').prop('readonly', true);
            $('#ip').prop('readonly', true);

            modal.modal('show');
          },
          error: function(xhr) {
            alert('Error fetching coupon data: ' + xhr.responseJSON.message);
          }
        });
      });

      // Form Submission
      // form.on('submit', function(e) {
      //   e.preventDefault();

      //   $.ajax({
      //     url: form.attr('action'),
      //     method: form.find('#couponMethod').val() === 'POST' ? 'POST' : 'PUT',
      //     data: form.serialize(),
      //     success: function(response) {
      //       modal.modal('hide');
      //       // $('#kuponTable').DataTable().ajax.reload();
      //       // console.log('suksessss');
      //       toastr.success(response.message);
      //       location.reload();
      //     },
      //     error: function(xhr) {
      //       const errors = xhr.responseJSON.errors;
      //       let message = '';

      //       for (const key in errors) {
      //         message += errors[key] + '\n';
      //       }

      //       toastr.error(message);
      //     }
      //   });
      // });
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

@endpush
