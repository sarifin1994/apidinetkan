@extends('backend.layouts.app_new')
@section('title', 'Order')

@section('css')
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}"> -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">
@endsection

@section('main')
  <div class="container-fluid user-management-page"> 
    <div class="row">
      <div class="col-sm-6 ps-0">
        <h3>Order</h3>
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
                          <a href="{{ route('dinetkan.invoice_dinetkan.order.create') }}" type="button" class="btn btn-light-primary h-60"><i class="fa-solid fa-plus"></i> Add Order</a>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      
      <!-- <div class="row mb-4"> -->
        <div class="col-12 mb-3 rounded">
          <ul class="nav nav-pills justify-content-center" id="data-tab" role="tablist">
            <!-- <li class="nav-item" role="presentation"><a class="nav-link active" id="new-tab" data-bs-toggle="tab"
                href="#new" role="tab" aria-controls="new" aria-selected="true">
                <i class="icofont icofont-ui-user"></i>
                New
              </a>
            </li> -->
            <li class="nav-item" role="presentation"><a class="nav-link active" id="progress-tab" data-bs-toggle="tab"
                href="#progress" role="tab" aria-controls="progress" aria-selected="true">
                <i class="icofont icofont-ui-user"></i>
                Progress / Installation
              </a>
            </li>
            <li class="nav-item" role="presentation"><a class="nav-link" id="active-tab" data-bs-toggle="tab"
                href="#active" role="tab" aria-controls="active" aria-selected="true">
                <i class="icofont icofont-ui-user"></i>
                Active
              </a>
            </li>
            <li class="nav-item" role="presentation"><a class="nav-link" id="inacive-tab" data-bs-toggle="tab"
                href="#inacive" role="tab" aria-controls="inacive" aria-selected="false">
                <i class="icofont icofont-man-in-glasses"></i>
                Inactive
              </a>
            </li>
            <li class="nav-item" role="presentation"><a class="nav-link" id="overdue-tab" data-bs-toggle="tab"
                href="#overdue" role="tab" aria-controls="overdue" aria-selected="false">
                <i class="icofont icofont-man-in-glasses"></i>
                Overdue
              </a>
            </li>
            <li class="nav-item" role="presentation"><a class="nav-link" id="suspend-tab" data-bs-toggle="tab"
                href="#suspend" role="tab" aria-controls="suspend" aria-selected="false">
                <i class="icofont icofont-man-in-glasses"></i>
                Suspend
              </a>
            </li>
            <li class="nav-item" role="presentation"><a class="nav-link" id="cancel-tab" data-bs-toggle="tab"
                href="#cancel" role="tab" aria-controls="cancel" aria-selected="false">
                <i class="icofont icofont-man-in-glasses"></i>
                Cancel
              </a>
            </li>
          </ul>
        </div>
      <!-- </div> -->

      <div class="row">
        <div class="col-12">
          <div class="tab-content" id="active-tabContent">
            <!-- <div class="tab-pane fade active show" id="new" role="tabpanel" aria-labelledby="new-tab">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive">
                        <table id="newTable" class="table table-responsive table-hover display nowrap" width="100%">
                          <thead>
                            <tr>
                              <th>ID Service</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>Mitra</th>
                              <th>Status</th>
                              <th>ACTION</th>
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
            </div> -->

            <div class="tab-pane fade active show" id="progress" role="tabpanel" aria-labelledby="progress-tab">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive">
                        <table id="progressTable" class="table table-responsive table-hover display nowrap" width="100%">
                          <thead>
                            <tr>
                              <th>ID Service</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>Mitra</th>
                              <th>Service</th>
                              <th>Due Date</th>
                              <th>Company</th>
                              <th>Status</th>
                              <th>ACTION</th>
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


            <div class="tab-pane fade" id="active" role="tabpanel" aria-labelledby="active-tab">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive">
                        <table id="activeTable" class="table table-responsive table-hover display nowrap" width="100%">
                          <thead>
                            <tr>
                              <th>ID Service</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>Mitra</th>
                              <th>Service</th>
                              <th>Due Date</th>
                              <th>Company</th>
                              <th>Status</th>
                              <th>ACTION</th>
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

            
            <div class="tab-pane fade" id="inactive" role="tabpanel" aria-labelledby="inactive-tab">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive">
                        <table id="inactiveTable" class="table table-responsive table-hover display nowrap" width="100%">
                          <thead>
                            <tr>
                              <th>ID Service</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>Mitra</th>
                              <th>Service</th>
                              <th>Due Date</th>
                              <th>Company</th>
                              <th>Status</th>
                              <th>ACTION</th>
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

            
            <div class="tab-pane fade" id="overdue" role="tabpanel" aria-labelledby="overdue-tab">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive">
                        <table id="overdueTable" class="table table-responsive table-hover display nowrap" width="100%">
                          <thead>
                            <tr>
                              <th>ID Service</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>Mitra</th>
                              <th>Service</th>
                              <th>Due Date</th>
                              <th>Company</th>
                              <th>Status</th>
                              <th>ACTION</th>
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

            
            <div class="tab-pane fade" id="suspend" role="tabpanel" aria-labelledby="suspend-tab">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive">
                        <table id="suspendTable" class="table table-responsive table-hover display nowrap" width="100%">
                          <thead>
                            <tr>
                              <th>ID Service</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>Mitra</th>
                              <th>Service</th>
                              <th>Due Date</th>
                              <th>Company</th>
                              <th>Status</th>
                              <th>ACTION</th>
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

            
            <div class="tab-pane fade" id="cancel" role="tabpanel" aria-labelledby="cancel-tab">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive custom-scrollbar">
                        <table id="cancelTable" class="table-hover display nowrap table" width="100%">
                          <thead>
                            <tr>
                              <th>ID Service</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>Mitra</th>
                              <th>Service</th>
                              <th>Due Date</th>
                              <th>Company</th>
                              <th>Status</th>
                              <th>ACTION</th>
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
    </div>
  </div>

  
  <!-- Unified Modal -->
  <div class="modal fade" id="adminModal" tabindex="-1" aria-labelledby="adminModalLabel" aria-hidden="true">
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
                <div class="mb-3">
                  <label for="dinetkan_user_id" class="form-label">Mitra</label>
                  <select class="form-select" id="dinetkan_user_id" name="dinetkan_user_id" style="width:100%" required>
                    @foreach ($resellers as $row)
                      <option value="{{ $row->dinetkan_user_id }}">{{ $row->first_name }} {{ $row->last_name }} - {{ isset($row->company) ? $row->company->name : '' }} - {{ $row->username }} </option>
                    @endforeach
                  </select>
                </div>
                <div class="mb-3">
                  <label for="statuses" class="form-label">Statuses</label>
                  <select class="form-select" id="statuses" name="statuses" required>
                    @foreach ($statuses as $key=>$val)
                      <option value="{{ $key }}" <?php echo $key == $progress ? 'selected' : '' ;?>>{{ $val }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="mb-3" id="div_next_due">
                  <label for="next_due" class="form-label">Next Due</label>
                  <input type="date" class="form-control" id="next_due" name="next_due">
                </div>
                <div class="mb-3" id="div_is_otc">
                  <label for="is_otc" class="form-label">is OTC ?</label>
                    <select class="form-select" id="is_otc" name="is_otc">
                      <option value="">Select OTC</option>
                      <option value="1">YES</option>
                      <option value="0">NO</option>
                    </select>
                </div>
                <div class="mb-3">
                  <label for="category_id" class="form-label">Category</label>
                  <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="mb-3">
                  <label for="license_dinetkan_id" class="form-label">Service</label>
                  <select class="form-select" id="license_dinetkan_id" name="license_dinetkan_id" required>
                    <option value="">Select Service</option>
                    @foreach ($licenses as $license)
                      <option value="{{ $license->id }}">{{ $license->name }}</option>
                    @endforeach
                  </select>
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

  <script type="text/javascript">
    $(document).ready(() => {

      // const newTable = $('#newTable').DataTable({
      //   processing: true,
      //   serverSide: true,
      //   ajax: {
      //     url: '{{ route('dinetkan.invoice_dinetkan.order.new') }}',
      //   },
      //   columns: [{
      //       data: 'service_id',
      //       name: 'service_id'
      //     },
      //     {
      //       data: 'first_name',
      //       name: 'first_name'
      //     },
      //     {
      //       data: 'last_name',
      //       name: 'last_name'
      //     },
      //     {
      //       data: 'mitra',
      //       name: 'mitra'
      //     },
      //     {
      //       data: 'status',
      //       name: 'status'
      //     },          
      //     {
      //       data: 'action',
      //       name: 'action',
      //       searchable: false,
      //       orderable: false,
      //     },
      //   ],
      //   order: [
      //     [1, 'desc']
      //   ],
      //   columnDefs: [
      //   ],
      // });

      const progressTable = $('#progressTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('dinetkan.invoice_dinetkan.order.progress') }}',
        },
        columns: [{
            data: 'service_id',
            name: 'service_id'
          },
          {
            data: 'first_name',
            name: 'first_name'
          },
          {
            data: 'last_name',
            name: 'last_name'
          },
          {
            data: 'mitra',
            name: 'mitra'
          },
          {
            data: 'service',
            name: 'service'
          },
          {
            data: 'due_date',
            name: 'due_date'
          },
          {
            data: 'company',
            name: 'company',
          },
          {
            data: 'status',
            name: 'status'
          },          
          {
            data: 'action',
            name: 'action',
            searchable: false,
            orderable: false,
          },
        ],
        order: [
          [1, 'desc']
        ],
        columnDefs: [
        ],
      });

      const activeTable = $('#activeTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('dinetkan.invoice_dinetkan.order.active') }}',
        },
        columns: [{
            data: 'service_id',
            name: 'service_id'
          },
          {
            data: 'first_name',
            name: 'first_name'
          },
          {
            data: 'last_name',
            name: 'last_name'
          },
          {
            data: 'mitra',
            name: 'mitra'
          },
          {
            data: 'service',
            name: 'service'
          },
          {
            data: 'due_date',
            name: 'due_date'
          },
          {
            data: 'company',
            name: 'company',
          },
          {
            data: 'status',
            name: 'status'
          },          
          {
            data: 'action',
            name: 'action',
            searchable: false,
            orderable: false,
          },
        ],
        order: [
          [1, 'desc']
        ],
        columnDefs: [
        ],
      });

      
      const inactiveTable = $('#inactiveTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('dinetkan.invoice_dinetkan.order.inactive') }}',
        },
        columns: [{
            data: 'service_id',
            name: 'service_id'
          },
          {
            data: 'first_name',
            name: 'first_name'
          },
          {
            data: 'last_name',
            name: 'last_name'
          },
          {
            data: 'mitra',
            name: 'mitra'
          },
          {
            data: 'service',
            name: 'service'
          },
          {
            data: 'due_date',
            name: 'due_date'
          },
          {
            data: 'company',
            name: 'company',
          },
          {
            data: 'status',
            name: 'status'
          },          
          {
            data: 'action',
            name: 'action',
            searchable: false,
            orderable: false,
          },
        ],
        order: [
          [1, 'desc']
        ],
        columnDefs: [
        ],
      });

      
      const overdueTable = $('#overdueTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('dinetkan.invoice_dinetkan.order.overdue') }}',
        },
        columns: [{
            data: 'service_id',
            name: 'service_id'
          },
          {
            data: 'first_name',
            name: 'first_name'
          },
          {
            data: 'last_name',
            name: 'last_name'
          },
          {
            data: 'mitra',
            name: 'mitra'
          },
          {
            data: 'service',
            name: 'service'
          },
          {
            data: 'due_date',
            name: 'due_date'
          },
          {
            data: 'company',
            name: 'company',
          },
          {
            data: 'status',
            name: 'status'
          },
          {
            data: 'action',
            name: 'action',
            searchable: false,
            orderable: false,
          },
        ],
        order: [
          [1, 'desc']
        ],
        columnDefs: [
        ],
      });


      const suspendTable = $('#suspendTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('dinetkan.invoice_dinetkan.order.suspend') }}',
        },
        columns: [{
            data: 'service_id',
            name: 'service_id'
          },
          {
            data: 'first_name',
            name: 'first_name'
          },
          {
            data: 'last_name',
            name: 'last_name'
          },
          {
            data: 'mitra',
            name: 'mitra'
          },
          {
            data: 'service',
            name: 'service'
          },
          {
            data: 'due_date',
            name: 'due_date'
          },
          {
            data: 'company',
            name: 'company',
          },
          {
            data: 'status',
            name: 'status'
          },
          {
            data: 'action',
            name: 'action',
            searchable: false,
            orderable: false,
          },
        ],
        order: [
          [1, 'desc']
        ],
        columnDefs: [
        ],
      });

      
      const cancelTable = $('#cancelTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('dinetkan.invoice_dinetkan.order.cancel') }}',
        },
        columns: [{
            data: 'service_id',
            name: 'service_id'
          },
          {
            data: 'first_name',
            name: 'first_name'
          },
          {
            data: 'last_name',
            name: 'last_name'
          },
          {
            data: 'mitra',
            name: 'mitra'
          },
          {
            data: 'service',
            name: 'service'
          },
          {
            data: 'due_date',
            name: 'due_date'
          },
          {
            data: 'company',
            name: 'company',
          },
          {
            data: 'status',
            name: 'status'
          },         
          {
            data: 'action',
            name: 'action',
            searchable: false,
            orderable: false,
          },
        ],
        order: [
          [1, 'desc']
        ],
        columnDefs: [
        ],
      });

    });
  </script>
  <script>
  // const baseurl = baseUrl.clone().pop().pop();
  const baseurl = "{{ url('/') }}"
    $(document).ready(function() {
      $('#div_next_due').hide();
      $('#div_is_otc').hide();
      const modal = $('#adminModal');
      const form = $('#adminForm');
      const methodField = $('#adminMethod');
      const submitBtn = $('#submitBtn');

      // Open Create Modal
      $('#openCreateModal').on('click', function() {
        form[0].reset();
        modal.find('.modal-title').text('Create New Order');
        form.attr('action', '{{ route('dinetkan.invoice_dinetkan.create') }}');
        methodField.val('POST');
        submitBtn.text('Create');
        modal.modal('show');
      });
      
      $('#dinetkan_user_id').select2({
          allowClear: true,
          dropdownParent: $("#adminModal .modal-content"),
      });

      // Edit Button Click Handler
      $(document).on('click', '.edit-icon.edit', function() {
        const userId = $(this).data('id');
        $.ajax({
          url: `/dinetkan/invoice_dinetkan/order/single_order/${userId}`,
          method: 'GET',
          success: function(data) {
            modal.find('.modal-title').text('Edit Order');
            form.attr('action', `/dinetkan/invoice_dinetkan/order/update_mapping/${userId}`);
            methodField.val('PUT');
            submitBtn.text('Update');

            // Populate the form with data
            $('#id').val(data.id);
            $('#dinetkan_user_id').val(data.dinetkan_user_id);
            $('#dinetkan_user_id').prop('readonly', true);
            $('#statuses').val(data.status);
            $('#category_id').val(data.category_id);
            $('#license_dinetkan_id').val(data.license_id);
                       
            modal.modal('show');
          },
          error: function(xhr) {
            alert('Error fetching admin data: ' + xhr.responseJSON.message);
          }
        });
      });

      // Edit Button Click Handler
      $(document).on('click', '.edit-icon.cancel', function() {
        const userId = $(this).data('id');
        $.ajax({
          url: `/dinetkan/invoice_dinetkan/order/single_order/${userId}`,
          method: 'GET',
          success: function(data) {
            modal.find('.modal-title').text('Cancel Order');
            form.attr('action', `/dinetkan/invoice_dinetkan/order/cancel_mapping/${userId}`);
            methodField.val('PUT');
            submitBtn.text('Cancel');

            // Populate the form with data
            $('#id').val(data.id);
            $('#dinetkan_user_id').val(data.dinetkan_user_id);
            $('#statuses').val(data.status);
            $('#category_id').val(data.category_id);
            $('#license_dinetkan_id').val(data.license_id);
            
            $('#dinetkan_user_id').prop('disabled', true);
            $('#statuses').prop('disabled', true);
            $('#category_id').prop('disabled', true);
            $('#license_dinetkan_id').prop('disabled', true);
                      
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
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: `${response.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
            $('#activeTable').DataTable().ajax.reload();
            $('#progressTable').DataTable().ajax.reload();
            // toastr.success(response.message);
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
                    text: `${response.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
          }
        });
      });
    });

    $('#category_id').on('change', function() {
    $('#license_dinetkan_id').empty();
    var category_id = $(this).val();
    if (category_id) {
        $.ajax({
        url:  baseurl + `/dinetkan/license_dinetkan/by_category/`+category_id+`/all`,
        type: 'GET',
        dataType: "json",
        success: function(data) {
              if(data){
                let formattedData = data.map((item) => ({
                id: item.id,
                text: item.name
                })).sort((a, b) => a.text.localeCompare(b.text));

                // Tambahkan opsi default "Silahkan Pilih"
                formattedData.unshift({
                    id: '',
                    text: 'Silahkan Pilih',
                    disabled: false // Jangan disabled agar bisa dipilih
                });

                $('#license_dinetkan_id').select2({
                    data: formattedData,
                    allowClear: true,
                    dropdownParent: $("#adminModal .modal-content"),
                });
            }else{
              $('#license_dinetkan_id').empty();
            }
          }
        });
    } else {
        $('#license_dinetkan_id').empty();
    }
    });
    $('#trafic_mrtg_tree').on('change', function() {
    $('#trafic_mrtg_tree_node').empty();
    var id = $(this).val();
    if (id) {
        $.ajax({
        url:  baseurl + `/dinetkan/invoice_dinetkan/get_tree_node_mrtg/`+id,
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
                    dropdownParent: $("#adminModal .modal-content"),
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
      $('#trafic_mrtg_graph').empty();
      var id = $(this).val();
      if (id) {
          $.ajax({
              url: baseurl + `/dinetkan/invoice_dinetkan/get_graph_mrtg/` + id + `/` + 1,
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
                          dropdownParent: $("#adminModal .modal-content"),
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

    
    $('#statuses').on('change', function() {
      $('#next_due').val("");
      var id = $(this).val();
      if(id == 1){
        $('#div_next_due').show();
        $('#div_is_otc').show();
      } else{
        $('#div_next_due').hide();
        $('#div_is_otc').hide();
      }
    });
  </script>
@endpush
