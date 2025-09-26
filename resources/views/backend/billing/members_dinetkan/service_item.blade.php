@extends('backend.layouts.app')

@section('title', 'Report')

@section('css')
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Report {{$month}} / {{$year}}</h3>
        </div>
        <!-- <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/old/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Account</li>
            <li class="breadcrumb-item active">Tagihan Pemakaian Bandwith</li>
          </ol>
        </div> -->
      </div>
    </div>
  </div>

  <div class="container-fluid user-management-page">
    <div class="row">
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
                              <th>ID Member</th>
                              <th>Total Harga</th>
                              <th>Total PPN</th>
                              <th>Total BHP</th>
                              <th>Total USO</th>
                              <th>Bulan</th>
                              <th>Tahun</th>
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
@endsection

@push('scripts')
  <script type="text/javascript">
    $(document).ready(() => {
      const unpaidTable = $('#unpaidTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "{{ route('admin.billing.member_dinetkan.mapping_service_item', ['month' => $month, 'year' => $year]) }}",
        },
        columns: [
          {
            data: 'id_member',
            name: 'id_member'
          },
          {
            data: 'total_price',
            name: 'total_price'
          },
          {
            data: 'total_ppn',
            name: 'total_ppn'
          },
          {
            data: 'total_bhp',
            name: 'total_bhp'
          },
          {
            data: 'total_uso',
            name: 'total_uso'
          },
          {
            data: 'month',
            name: 'month'
          },
          {
            data: 'year',
            name: 'year'
          },
        ],
        order: [
          [1, 'desc']
        ],
      });

      // const paidTable = $('#paidTable').DataTable({
      //   processing: true,
      //   serverSide: true,
      //   ajax: {
      //     url: '{{ route('dinetkan.invoice_dinetkan.paid') }}',
      //   },
      //   columns: [{
      //       data: 'DT_RowIndex',
      //       name: 'DT_RowIndex'
      //     },
      //     {
      //       data: 'no_invoice',
      //       name: 'no_invoice'
      //     },
      //     {
      //       data: 'item',
      //       name: 'item'
      //     },
      //     {
      //       data: 'invoice_date',
      //       name: 'invoice_date',
      //       render: function(data, type, row) {
      //         return moment(row.invoice_date).format('DD/MM/YYYY');
      //       }
      //     },
      //     {
      //       data: 'due_date',
      //       name: 'due_date',
      //       render: function(data, type, row) {
      //         return moment(row.due_date).format('DD/MM/YYYY');
      //       }
      //     },
      //     {
      //       data: 'subscribe',
      //       name: 'subscribe'
      //     },
      //     {
      //       data: 'id',
      //       name: 'id',
      //       searchable: false,
      //       orderable: false,
      //       render: function(data, type, row) {
              
      //         const price = parseInt(row.price) || 0;
      //         const fee = parseInt(row.fee) || 0;
      //         const ppn = parseInt(row.ppn) || 0;
      //         const discount = parseInt(row.discount) || 0;
      //         const discount_coupon = parseInt(row.discount_coupon) || 0;
              
      //         const price_otc = parseInt(row.price_otc) || 0;
      //         const ppn_otc = parseInt(row.ppn_otc) || 0;
      //         let totalppn = 0;
      //         if(ppn_otc > 0){
      //           totalppn = price_otc * ppn_otc /100;
      //         }

      //         // const ppnAmount = price * ppn / 100;
      //         const discountAmount = price * discount / 100;
      //         const ppnAmount = ((price - discountAmount - discount_coupon) * ppn) / 100;

      //         const total = price + fee + ppnAmount - discountAmount - discount_coupon + price_otc + totalppn;

      //         return total.toLocaleString('id-ID', {
      //           style: 'currency',
      //           currency: 'IDR'
      //         });
      //       }
      //     },
      //     {
      //       data: 'paid',
      //       name: 'paid',
      //       searchable: false,
      //       orderable: false,
      //       render: function(data, type, row) {
      //         return moment(row.invoice_date).format('DD/MM/YYYY');
      //       }
      //     },
      //   ],
      //   order: [
      //     [1, 'desc']
      //   ],
      //   columnDefs: [{
      //       orderable: false,
      //       targets: [0, 7]
      //     },
      //     {
      //       className: 'text-center',
      //       targets: [0, 7]
      //     },
      //   ],
      // });

    });
  </script>
  <script>
  </script>
@endpush
