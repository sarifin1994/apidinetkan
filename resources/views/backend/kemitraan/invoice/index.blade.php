@extends('backend.layouts.app_new')

@section('title', 'Invoice')

@section('css')
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}"> -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">
@endsection

@section('main')
  <div class="container-fluid user-management-page">
    <!-- <div class="row">
      <div class="col-xxl-4 box-col-4">
        <div class="card">
          <div class="card-body">
            <div class="bg-light-primary b-r-15">
              <div class="upcoming-box d-flex align-items-center justify-content-between px-4">
                <div>
                  <div class="upcoming-icon bg-primary">
                    <svg class="stroke-icon">
                      <use href="{{ asset('assets/old/svg/icon-sprite.svg#stroke-form') }}"></use>
                    </svg>
                  </div>
                </div>
                <button type="button" class="btn btn-primary" id="openCreateModal">Add Order Dinetkan</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> -->
    <div class="row">
      <div class="col-sm-6 ps-0">
        <h3>Invoice</h3>
      </div>
    </div>

    <div class="row">
      <!-- <div class="row mb-4"> -->
        <div class="col-12 mb-3 rounded">
          <ul class="nav nav-pills justify-content-center" id="data-tab" role="tablist">
            <li class="nav-item" role="presentation"><a class="nav-link active" id="unpaid-tab" data-bs-toggle="tab"
                href="#unpaid" role="tab" aria-controls="unpaid" aria-selected="true">
                <i class="icofont icofont-ui-user"></i>
                Unpaid
              </a>
            </li>
            <li class="nav-item" role="presentation"><a class="nav-link" id="paid-tab" data-bs-toggle="tab"
                href="#paid" role="tab" aria-controls="paid" aria-selected="false">
                <i class="icofont icofont-man-in-glasses"></i>
                Paid
              </a>
            </li>
            <li class="nav-item" role="presentation"><a class="nav-link" id="expired-tab" data-bs-toggle="tab"
                href="#expired" role="tab" aria-controls="expired" aria-selected="false">
                <i class="icofont icofont-man-in-glasses"></i>
                Cancel / Expired
              </a>
            </li>
          </ul>
        </div>
      <!-- </div> -->

      <div class="row">
        <div class="col-12">
          <div class="tab-content" id="unpaid-tabContent">
            <div class="tab-pane fade active show" id="unpaid" role="tabpanel" aria-labelledby="unpaid-tab">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive custom-scrollbar">
                        <table id="unpaidTable" class="table-hover display nowrap table" width="100%">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>ORDER</th>
                              <th>NAME</th>
                              <th>DATE</th>
                              <th>DUE</th>
                              <th>PERIOD</th>
                              <th>TOTAL</th>
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

            <div class="tab-pane fade" id="paid" role="tabpanel" aria-labelledby="paid-tab">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive custom-scrollbar">
                        <table id="paidTable" class="table-hover display nowrap table" width="100%">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>ORDER</th>
                              <th>NAME</th>
                              <th>DATE</th>
                              <th>DUE</th>
                              <th>PERIOD</th>
                              <th>TOTAL</th>
                              <th>PAID</th>
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
            
            <div class="tab-pane fade" id="expired" role="tabpanel" aria-labelledby="expired-tab">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive custom-scrollbar">
                        <table id="expiredTable" class="table-hover display nowrap table" width="100%">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>ORDER</th>
                              <th>NAME</th>
                              <th>DATE</th>
                              <th>DUE</th>
                              <th>PERIOD</th>
                              <th>TOTAL</th>
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
                  <select class="form-select" id="dinetkan_user_id" name="dinetkan_user_id" required>
                    @foreach ($resellers as $row)
                      <option value="{{ $row->dinetkan_user_id }}">{{ $row->username }} - {{ $row->first_name }} {{ $row->last_name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="mb-3">
                  <label for="next_due" class="form-label">Next Due</label>
                  <input type="date" class="form-control" id="next_due" name="next_due">
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
      const unpaidTable = $('#unpaidTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('kemitraan.invoice.unpaid') }}',
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex'
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
            data: 'no_invoice',
            name: 'no_invoice'
          },
          {
            data: 'item',
            name: 'item'
          },
          {
            data: 'invoice_date',
            name: 'invoice_date',
            render: function(data, type, row) {
              return moment(row.invoice_date).format('DD/MM/YYYY');
            }
          },
          {
            data: 'due_date',
            name: 'due_date',
            render: function(data, type, row) {
              return moment(row.due_date).format('DD/MM/YYYY');
            }
          },
          {
            data: 'subscribe',
            name: 'subscribe'
          },
          {
            data: 'total',
            name: 'total',
            searchable: false,
            orderable: false,
            render: function(data, type, row) {
              const price = parseInt(row.price) || 0;
              const fee = parseInt(row.fee) || 0;
              const ppn = parseInt(row.ppn) || 0;
              const discount = parseInt(row.discount) || 0;
              const discount_coupon = parseInt(row.discount_coupon) || 0;
              
              const price_otc = parseInt(row.price_otc) || 0;
              const ppn_otc = parseInt(row.ppn_otc) || 0;
              let totalppn = 0;
              if(ppn_otc > 0){
                totalppn = price_otc * ppn_otc /100;
              }

              // const ppnAmount = price * ppn / 100;
              const discountAmount = price * discount / 100;
              const ppnAmount = ((price - discountAmount - discount_coupon) * ppn) / 100;

              const total = price + fee + ppnAmount - discountAmount - discount_coupon + price_otc + totalppn;

              return total.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR'
              });
            }
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
        columnDefs: [{
            orderable: false,
            targets: [0, 7]
          },
          {
            className: 'text-center',
            targets: [0, 7]
          },
        ],
      });

      const paidTable = $('#paidTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('kemitraan.invoice.paid') }}',
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex'
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
            data: 'no_invoice',
            name: 'no_invoice'
          },
          {
            data: 'item',
            name: 'item'
          },
          {
            data: 'invoice_date',
            name: 'invoice_date',
            render: function(data, type, row) {
              return moment(row.invoice_date).format('DD/MM/YYYY');
            }
          },
          {
            data: 'due_date',
            name: 'due_date',
            render: function(data, type, row) {
              return moment(row.due_date).format('DD/MM/YYYY');
            }
          },
          {
            data: 'subscribe',
            name: 'subscribe'
          },
          {
            data: 'id',
            name: 'id',
            searchable: false,
            orderable: false,
            render: function(data, type, row) {
              
              const price = parseInt(row.price) || 0;
              const fee = parseInt(row.fee) || 0;
              const ppn = parseInt(row.ppn) || 0;
              const discount = parseInt(row.discount) || 0;
              const discount_coupon = parseInt(row.discount_coupon) || 0;
              
              const price_otc = parseInt(row.price_otc) || 0;
              const ppn_otc = parseInt(row.ppn_otc) || 0;
              let totalppn = 0;
              if(ppn_otc > 0){
                totalppn = price_otc * ppn_otc /100;
              }

              // const ppnAmount = price * ppn / 100;
              const discountAmount = price * discount / 100;
              const ppnAmount = ((price - discountAmount - discount_coupon) * ppn) / 100;

              const total = price + fee + ppnAmount - discountAmount - discount_coupon + price_otc + totalppn;

              return total.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR'
              });
            }
          },
          {
            data: 'paid',
            name: 'paid',
            searchable: false,
            orderable: false,
            render: function(data, type, row) {
              return moment(row.invoice_date).format('DD/MM/YYYY');
            }
          },
        ],
        order: [
          [1, 'desc']
        ],
        columnDefs: [{
            orderable: false,
            targets: [0, 7]
          },
          {
            className: 'text-center',
            targets: [0, 7]
          },
        ],
      });
      
      const expiredTable = $('#expiredTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('kemitraan.invoice.expired') }}',
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex'
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
            data: 'no_invoice',
            name: 'no_invoice'
          },
          {
            data: 'item',
            name: 'item'
          },
          {
            data: 'invoice_date',
            name: 'invoice_date',
            render: function(data, type, row) {
              return moment(row.invoice_date).format('DD/MM/YYYY');
            }
          },
          {
            data: 'due_date',
            name: 'due_date',
            render: function(data, type, row) {
              return moment(row.due_date).format('DD/MM/YYYY');
            }
          },
          {
            data: 'subscribe',
            name: 'subscribe'
          },
          {
            data: 'total',
            name: 'total',
            searchable: false,
            orderable: false,
            render: function(data, type, row) {
              const price = parseInt(row.price) || 0;
              const fee = parseInt(row.fee) || 0;
              const ppn = parseInt(row.ppn) || 0;
              const discount = parseInt(row.discount) || 0;
              const discount_coupon = parseInt(row.discount_coupon) || 0;

              // const ppnAmount = price * ppn / 100;
              const discountAmount = price * discount / 100;
              const ppnAmount = ((price - discountAmount - discount_coupon) * ppn) / 100;

              const total = price + fee + ppnAmount - discountAmount - discount_coupon;

              return total.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR'
              });
            }
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
        columnDefs: [{
            orderable: false,
            targets: [0, 7]
          },
          {
            className: 'text-center',
            targets: [0, 7]
          },
        ],
      });

    });
  </script>
@endpush
