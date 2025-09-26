@extends('backend.layouts.app_new')

@section('title', 'Service')

@section('css')
  <link rel="stylesheet" href="https://unpkg.com/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3><i class="ti ti-settings me-1"></i> Service</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid user-management-page">
    <div class="row">
      <div class="col-12 mb-4 rounded p-3">
        <ul class="nav nav-pills justify-content-center" id="data-tab" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link active" id="active-tab" data-bs-toggle="tab"
               href="#active" role="tab" aria-controls="active" aria-selected="true">
              <i class="ti ti-user-check me-1"></i> Active
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="inactive-tab" data-bs-toggle="tab"
               href="#inactive" role="tab" aria-controls="inactive" aria-selected="false">
              <i class="ti ti-user-off me-1"></i> Inactive
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="overdue-tab" data-bs-toggle="tab"
               href="#overdue" role="tab" aria-controls="overdue" aria-selected="false">
              <i class="ti ti-alert-circle me-1"></i> Overdue
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="suspend-tab" data-bs-toggle="tab"
               href="#suspend" role="tab" aria-controls="suspend" aria-selected="false">
              <i class="ti ti-ban me-1"></i> Suspend
            </a>
          </li>
        </ul>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="tab-content" id="active-tabContent">
          @foreach (['active', 'inactive', 'overdue', 'suspend'] as $status)
            <div class="tab-pane fade {{ $status == 'active' ? 'active show' : '' }}" id="{{ $status }}" role="tabpanel" aria-labelledby="{{ $status }}-tab">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive custom-scrollbar">
                        <table id="{{ $status }}Table" class="table-hover display nowrap table" width="100%">
                          <thead>
                            <tr>
                              <th>ID Service</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>Service</th>
                              <th>Due Date</th>
                              <th>Status</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script type="text/javascript">
    $(document).ready(() => {
      const tables = {
        active: '{{ route('admin.account.invoice_dinetkan.order.active') }}',
        inactive: '{{ route('admin.account.invoice_dinetkan.order.inactive') }}',
        overdue: '{{ route('admin.account.invoice_dinetkan.order.overdue') }}',
        suspend: '{{ route('admin.account.invoice_dinetkan.order.suspend') }}'
      };

      for (const [key, url] of Object.entries(tables)) {
        $('#' + key + 'Table').DataTable({
          processing: true,
          serverSide: true,
          ajax: { url },
          columns: [
            { data: 'service_id', name: 'service_id' },
            { data: 'first_name', name: 'first_name' },
            { data: 'last_name', name: 'last_name' },
            { data: 'service', name: 'service' },
            { data: 'due_date', name: 'due_date' },
            { data: 'status', name: 'status' }
          ],
          order: [[1, 'desc']]
        });
      }
    });
  </script>
@endpush
