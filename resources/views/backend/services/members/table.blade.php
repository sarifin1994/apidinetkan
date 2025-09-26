@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
@endsection
@if (auth()->user()->role === 'Admin')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="bg-light-primary b-r-15">
            <div class="upcoming-box">
              <div class="d-flex gap-2 px-4">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fa fa-cog"></i>
                  {{ __('Action') }}
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <li>
                    <a class="dropdown-item" href="#" id="deleteSelectedMember">
                      <i class="fa fa-trash"></i>
                      {{ __('Delete Selected') }}
                    </a>
                  </li>
                </ul>

                <button id="createMember" class="btn btn-primary" type="button" data-bs-toggle="modal"
                  data-bs-target="#memberFormModal">
                  <i class="fa fa-plus"></i>
                  {{ __('Create') }}
                </button>

                <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#import-member">
                  <i class="fa fa-upload"></i>
                  {{ __('Import') }}
                </button>

                <a href="{{ route('admin.services.member.export') }}" class="btn btn-warning">
                  <i class="fa fa-download"></i>
                  {{ __('Export') }}
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endif

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive custom-scrollbar">
          <table id="memberTable" class="table-hover display nowrap clickable table" width="100%">
            <thead>
              <tr>
                <th>
                  <input class="checkbox_animated" id="checkAllMember" type="checkbox">
                </th>
                <th style="text-align:left!important">ID</th>
                <th>NAME</th>
                <th>EMAIL</th>
                <th>WA</th>
                <th>ADDRESS</th>
                <th>STATUS</th>
                @role('admin')
                  <th>SERVICES</th>
                  <th>INVOICE</th>
                @endrole
                <th>CREATED</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

        @include('services.members.modals.form')
        @role('admin')
          @include('services.members.modals.services')
          @include('services.members.modals.invoices')
        @endrole
        @include('services.members.modals.import')
      </div>
    </div>
  </div>
</div>

@push('script-childs')
  <script type="text/javascript">
    const memberUrl = baseUrl.clone().pop() + '/member';
    const memberModal = new bootstrap.Modal(document.getElementById('memberFormModal'));
    const memberTable = $('#memberTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: memberUrl,
      stateSave: true,
      columns: [{
          data: 'id',
          name: 'id',
          searchable: false,
          sortable: false,
          render: function(data, type, row, meta) {
            return `<input class="checkbox_animated" type="checkbox" name="checkedMemberIds[]" value="${data}">`;
          }
        },
        {
          data: 'id_member_new',
          name: 'id_member',
          render: function(data, type, row) {
            return `<span class="badge badge-sm bg-primary">${data}</span>`;
          },
        },
        {
          data: 'full_name',
          name: 'full_name'
        },
        {
          data: 'email',
          name: 'email'
        },
        {
          data: 'wa',
          name: 'wa'
        },
        {
          data: 'address',
          name: 'address'
        },
        {
          data: 'status',
          name: 'status',
          render: function(data, type, row) {
            return data === 'active' ? `<span class="badge badge-sm bg-success">Active</span>` :
              `<span class="badge badge-sm bg-danger">Inactive</span>`;
          },
        },
        @role('admin')
          {
            data: 'services',
            name: 'services',
            searchable: false,
            orderable: false,
            render: function(data, type, row) {
              return `
            <button
                class="btn btn-sm btn-warning show-member-services"
                data-bs-toggle="modal"
                data-bs-target="#memberServices"
                data-id="${row.id}"
                title="See Member Services">
                <i class="fas fa-wifi"></i> Services
            </button>`;
            }
          }, {
            data: null,
            name: 'invoice',
            searchable: false,
            orderable: false,
            render: function(data, type, row) {
              return `
            <button
                class="btn btn-sm btn-success show-member-invoices"
                data-bs-toggle="modal"
                data-bs-target="#memberInvoices"
                data-id="${row.id}"
                title="See Invoice History">
                <i class="fas fa-file-invoice"></i> Invoice
            </button>`;
            }
          },
        @endrole {
          data: 'created_at',
          name: 'created_at',
          render: function(data, type, row) {
            return moment(data).local().format('DD/MM/YYYY');
          },
        },
      ],
      columnDefs: [{
          targets: 0,
          orderable: false,
          searchable: false,
        },
        {
          targets: 6,
          orderable: false,
          searchable: false,
        },
      ],
    });

    $('#memberTable tbody').on('click', '.show-member-services', function() {
      const id = $(this).data('id');
      $('#memberServices').find('#member_id').val(id);
      $('#memberServices').find('#member_full_name').text($(this).closest('tr').find('td:eq(2)').text());
      memberServicesTable.ajax.url(`${memberUrl}/${id}/services`).load();
    });

    $('#memberTable tbody').on('click', '.show-member-invoices', function() {
      const id = $(this).data('id');
      $('#memberInvoices').find('#member_id').val(id);
      $('#memberInvoices').find('#member_full_name').text($(this).closest('tr').find('td:eq(2)').text());
      memberInvoicesTable.ajax.url(`${memberUrl}/${id}/invoices`).load();
    });

    $('#checkAllMember').on('click', function() {
      $(this).closest('table').find('input:checkbox').prop('checked', this.checked);
    });

    $('#memberTable tbody').on('click', 'input:checkbox', function(e) {
      e.stopPropagation();

      if (!this.checked) {
        $('#checkAllMember').prop('checked', false);
      }
    });

    $('#deleteSelectedMember').on('click', function() {
      const checkedMemberIds = $('input[name="checkedMemberIds[]"]:checked').map(function() {
        return $(this).val();
      }).get();

      if (checkedMemberIds.length === 0) {
        toastr.warning('Please select at least one member to delete.');
        return;
      }

      Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: memberUrl,
            method: 'DELETE',
            data: {
              ids: checkedMemberIds
            },
            success: function(response) {
              memberTable.ajax.reload();
              toastr.success(response.message);
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
        }
      });
    });
  </script>
@endpush
