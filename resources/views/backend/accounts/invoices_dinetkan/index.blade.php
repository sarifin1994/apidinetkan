@extends('backend.layouts.app_new')

@section('title', 'ORDER')

@section('css')
  <!-- Select2 -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">
  <!-- Tabler Icons CDN -->
  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title mb-3">
      <div class="row align-items-center">
        <div class="col-sm-6 ps-0">
          <h3 class="d-flex align-items-center">
            <i class="ti ti-file-invoice me-2 text-primary"></i>
            Tagihan Pemakaian Bandwidth
          </h3>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid user-management-page">
    <div class="row">
      <div class="col-12 mb-4">
  <ul class="nav nav-pills justify-content-center" id="data-tab" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link active" id="unpaid-tab" data-bs-toggle="tab" href="#unpaid" role="tab"
         aria-controls="unpaid" aria-selected="true">
        <i class="ti ti-receipt me-1"></i> Unpaid
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="paid-tab" data-bs-toggle="tab" href="#paid" role="tab"
         aria-controls="paid" aria-selected="false">
        <i class="ti ti-cash me-1"></i> Paid
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="expired-tab" data-bs-toggle="tab" href="#expired" role="tab"
         aria-controls="expired" aria-selected="false">
        <i class="ti ti-ban me-1"></i> Cancel / Expired
      </a>
    </li>
  </ul>
</div>


      <div class="col-12">
        <div class="tab-content" id="unpaid-tabContent">
          {{-- Tab Unpaid --}}
          <div class="tab-pane fade show active" id="unpaid" role="tabpanel" aria-labelledby="unpaid-tab">
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="table-responsive custom-scrollbar">
                  <table id="unpaidTable" class="table table-hover display nowrap w-100">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>NO INVOICE</th>
                        <th>NAME</th>
                        <th>DATE</th>
                        <th>DUE</th>
                        <th>PERIOD</th>
                        <th>TOTAL</th>
                        <th>ACTION</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          {{-- Tab Paid --}}
          <div class="tab-pane fade" id="paid" role="tabpanel" aria-labelledby="paid-tab">
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="table-responsive custom-scrollbar">
                  <table id="paidTable" class="table table-hover display nowrap w-100">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>NO INVOICE</th>
                        <th>NAME</th>
                        <th>DATE</th>
                        <th>DUE</th>
                        <th>PERIOD</th>
                        <th>TOTAL</th>
                        <th>PAID</th>
                        <th>ACTION</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          {{-- Tab Expired --}}
          <div class="tab-pane fade" id="expired" role="tabpanel" aria-labelledby="expired-tab">
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="table-responsive custom-scrollbar">
                  <table id="expiredTable" class="table table-hover display nowrap w-100">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>NO INVOICE</th>
                        <th>NAME</th>
                        <th>DATE</th>
                        <th>DUE</th>
                        <th>PERIOD</th>
                        <th>TOTAL</th>
                        <th>ACTION</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div> <!-- .tab-content -->
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
          url: '{{ route('admin.account.invoice_dinetkan.unpaid') }}',
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex'
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

              // const total = price + fee + ppnAmount - discountAmount - discount_coupon;
              const total = parseInt(row.total_all) - discountAmount - discount_coupon;

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
          url: '{{ route('admin.account.invoice_dinetkan.paid') }}',
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex'
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

              // const ppnAmount = price * ppn / 100;
              const discountAmount = price * discount / 100;
              const ppnAmount = ((price - discountAmount - discount_coupon) * ppn) / 100;

              // const total = price + fee + ppnAmount - discountAmount - discount_coupon;
              const total = parseInt(row.total_all) - discountAmount - discount_coupon;

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
      
      const expiredTable = $('#expiredTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('admin.account.invoice_dinetkan.expired') }}',
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex'
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

              // const total = price + fee + ppnAmount - discountAmount - discount_coupon;
              const total = parseInt(row.total_all) - discountAmount - discount_coupon;

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
