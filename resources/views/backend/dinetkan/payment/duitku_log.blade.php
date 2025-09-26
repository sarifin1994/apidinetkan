@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Duitku LOG')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6">
            <h4 class="main-title">Duitku Log</h4>
        </div>
    </div><br />
    <!-- Page content -->
    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left!important">#</th>
                        <th>Shortname</th>
                        <td>Merchant Code</td>
                        <td>Amount</td>
                        <td>Merchant Order ID</td>
                        <th>Notes</th>
                        <td>Product Detail</td>
                        <td>Payment Code</td>
                        <td>Result Code</td>
                        <td>Reference</td>
                        <td>VA Number</td>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script type="text/javascript">
    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: '{{ url()->current() }}',
        columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex'
        },{
          data: 'shortname',
          name: 'shortname'
        },{
          data: 'merchantCode',
          name: 'merchantCode'
        },{
          data: 'amount',
          name: 'amount'
        },{
          data: 'merchantOrderId',
          name: 'merchantOrderId'
        },{
          data: 'notes',
          name: 'notes'
        },{
          data: 'productDetail',
          name: 'productDetail'
        },{
          data: 'paymentMethod',
          name: 'paymentMethod'
        },{
          data: 'resultCode',
          name: 'resultCode'
        },{
          data: 'reference',
          name: 'reference'
        },{
          data: 'vaNumber',
          name: 'vaNumber'
        },
        ]
    });
</script>
@endpush
