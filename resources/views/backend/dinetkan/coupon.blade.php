@extends('backend.layouts.app_new')

@section('title', 'Users Management')

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
          <h3>Coupon</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dinetkan.dashboard') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item active">Coupon</li>
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
                <button type="button" class="btn btn-primary" id="openCreateModal">Add Coupon</button>
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
                <th>
                  <input class="checkbox_animated" id="checkAllMember" type="checkbox">
                </th>
                <th>Kupon</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($coupon as $row)
                <tr>
                  <td></td>
                  <td>{{ $row->coupon_name}}</td>
                  <td>{{ $row->start_date}}</td>
                  <td>{{ $row->end_date}}</td>
                  <td>
                    <div class="action-div">
                        <a href="javascript:void(0)" class="edit-icon edit badge badge-info" data-id="{{ $row->id}}">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path><polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon></svg>
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
              <label for="coupon_name" class="form-label">Coupon Name</label>
              <input type="text" class="form-control" id="coupon_name" name="coupon_name" required>
            </div> 
            <div class="mb-3">
              <label for="user_id" class="form-label">User</label>
              <select class="form-select" id="user_id" name="user_id[]" multiple="multiple">
                @forelse ($resellers as $row)
                  <option value="{{ $row->id }}">{{ $row->shortname }} || {{ $row->name }}</option>
                @empty
                @endforelse
              </select>
              <label for="user_id" class="form-label">For all user, leave it blank</label>
            </div>
            <div class="mb-3">
              <label for="license_id" class="form-label">License</label>
              <select class="form-select" id="license_id" name="license_id[]" multiple="multiple">
                @forelse ($licenses as $row)
                  <option value="{{ $row->id }}">{{ $row->name }}</option>
                @empty
                @endforelse
              </select>
              <p>For all license, leave it blank</p>
            </div>
            <div class="mb-3">
              <label for="used" class="form-label">Used</label>
              <select class="form-select" id="used" name="used" required>
                  <option value="single">Single Use</option>
                  <option value="multiple">Multiple Use</option>
              </select>
              <!-- <p>Single Use : 1 user 1 coupon.</p>
              <div style="margin-top:1px"></div>
              <p>Multiple Use : 1 user multiple coupon</p> -->
            </div>
            <div class="mb-3">
              <label class="form-label">Discount</label>
              <div class="col-6">
                <label for="type_percent" class="form-label">Percent</label>
                <input type="radio" id="type_percent" name="type" value="percent" checked onclick="cek_radio('percent')">
                &nbsp;&nbsp;
                <label for="type_nominal" class="form-label">nominal</label>
                <input type="radio" id="type_nominal" name="type" value="nominal" onclick="cek_radio('nominal')">
              </div>
              <input type="number" class="form-control" id="percent" name="percent" min="1" max="100" placeholder="Pecent">
              <input type="number" class="form-control" id="nominal" name="nominal" style="display:none" min=1 placeholder="nominal">
            </div>
            <div class="mb-3">
              <label for="start_date" class="form-label">Start Date</label>
              <input type="date" class="form-control" id="start_date" name="start_date">
            </div>
            <div class="mb-3">
              <label for="end_date" class="form-label">End Date</label>
              <input type="date" class="form-control" id="end_date" name="end_date">
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
        modal.find('.modal-title').text('Create New Coupon');
        form.attr('action', '{{ route('dinetkan.coupon.create') }}');
        methodField.val('POST');
        submitBtn.text('Create');
        modal.modal('show');
      });

      // Edit Button Click Handler
      $(document).on('click', '.edit-icon.edit', function() {
        const userId = $(this).data('id');

        $.ajax({
          url: `/dinetkan/coupon/get_coupon_single/${userId}`,
          method: 'GET',
          success: function(data) {
            modal.find('.modal-title').text('Edit Coupon');
            form.attr('action', `/dinetkan/coupon/get_coupon_single/${userId}`);
            methodField.val('PUT');
            submitBtn.text('Update');

            // Populate the form with data
            $('#id').val(data.id);
            $('#coupon_name').val(data.coupon_name);
            $('#user_id').val(data.user_id);
            $('#license_id').val(data.license_id);
            $('#start_date').val(data.start_date);
            $('#end_date').val(data.end_date);

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
@endsection
