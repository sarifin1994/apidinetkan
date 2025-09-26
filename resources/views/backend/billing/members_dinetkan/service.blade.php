@extends('backend.layouts.app_new')

@section('title', 'Report')
@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Report</h3>
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
      <div class="row mb-4">
        <div class="col-12 mb-4 rounded p-3">
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
          </ul>
        </div>
      </div>

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
                              <th>ID</th>
                              <th>Bulan</th>
                              <th>Tahun</th>
                              <th>Total Member</th>
                              <th>Total Harga</th>
                              <th>Status</th>
                              <td>Action</td>
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
                            <th>ID</th>
                              <th>Bulan</th>
                              <th>Tahun</th>
                              <th>Total Member</th>
                              <th>Total Harga</th>
                              <th>Status</th>
                              <td>Action</td>
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
          url: '{{ route('admin.billing.member_dinetkan.mapping_service_unpaid') }}',
        },
        columns: [
          {
            data: 'id',
            name: 'id'
          },
          {
            data: 'month',
            name: 'month'
          },
          {
            data: 'year',
            name: 'year'
          },
          {
            data: 'total_member',
            name: 'total_member'
          },
          {
            data: 'total_price',
            name: 'total_price'
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
          [0, 'desc']
        ]
      });

      
      const paidTable = $('#paidTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('admin.billing.member_dinetkan.mapping_service_paid') }}',
        },
        columns: [
          {
            data: 'id',
            name: 'id'
          },
          {
            data: 'month',
            name: 'month'
          },
          {
            data: 'year',
            name: 'year'
          },
          {
            data: 'total_member',
            name: 'total_member'
          },
          {
            data: 'total_price',
            name: 'total_price'
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
          [0, 'desc']
        ]
      });
    });
  </script>
@endpush
