@extends('backend.layouts.app_new')

@section('title', 'Master Group Server')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">

  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Master Group Server</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dinetkan.dashboard') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item active">Master Group Server</li>
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
                </div>
                <button type="button" class="btn btn-primary" id="openCreateModal">Add Group Server</button>
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
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($masterGroupServer as $row)
                <tr>
                  <td>{{ $row->id}}</td>
                  <td>{{ $row->name}}</td>
                  <td>
                    <div class="action-div">
                        <a href="javascript:void(0)" class="edit-icon edit badge badge-warning" data-id="{{ $row->id}}">
                        <i class="fas fa-pencil"></i>
                        </a>
                        <a href="javascript:void(0)" class="edit-icon delete badge badge-danger" data-id="{{ $row->id}}">
                        <i class="fas fa-trash-alt"></i>
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

@section('scripts')
  <script src="{{ asset('assets/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>

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
        modal.find('.modal-title').text('Create New Master Group Server');
        form.attr('action', '{{ route('dinetkan.monitoring.server.group.store') }}');
        methodField.val('POST');
        submitBtn.text('Create');
        modal.modal('show');
      });

      // Edit Button Click Handler
      $(document).on('click', '.edit-icon.edit', function() {
        const userId = $(this).data('id');

        $.ajax({
          url: `/dinetkan/monitoring/server/group/single/${userId}`,
          method: 'GET',
          success: function(data) {
            modal.find('.modal-title').text('Update Group Server');
            form.attr('action', `/dinetkan/monitoring/server/group/update/${userId}`);
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
          url: `/dinetkan/monitoring/server/group/single/${userId}`,
          method: 'GET',
          success: function(data) {
            modal.find('.modal-title').text('Delete Group');
            form.attr('action', `/dinetkan/monitoring/server/group/delete/${userId}`);
            methodField.val('POST');
            submitBtn.text('Delete');

            // Populate the form with data
            $('#id').val(data.id);
            $('#name').val(data.name);

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
@endsection
