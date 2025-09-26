<!-- service Modal Show -->
<div class="modal fade" id="memberServices" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal">DATA SERVICE | <span class="text-sm" id="inv_full_name"></span></h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row table-responsive px-3">
          <table id="memberServicesTable" class="table-hover table" width="100%">
            <thead>
              <tr>
                <th>#</th>
                <th>INET</th>
                <th>SERVICE</th>
                <th>TYPE</th>
                <th>PERIOD</th>
                <th>ACTIVE</th>
                <th>NAS</th>
                <th>AREA</th>
                <th>ODP</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="button" data-bs-dismiss="modal">
          Close
        </button>
      </div>
    </div>
  </div>
</div>

@push('script-modal')
  <script>
    const memberServicesTable = $('#memberServicesTable').DataTable({
      processing: true,
      serverSide: true,
      deferLoading: 0,
      columns: [{
          data: null,
          'sortable': false,
          render: function(data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart +
              1;
          }
        },
        {
          data: 'session_internet',
          name: 'session_internet',
          className: 'text-center',
          sortable: false,
          searchable: false,
          render: function(data, type, row) {
            const session = row.pppoe.session;

            if (session.session_id !== null && session.status === 1 && session.ip !== null && row.pppoe.status ===
              1) {
              return '<i class="fas fa-circle text-success"></i>'
            } else if (session.session_id !== null && session.status === 2 && session.ip !== null) {
              return '<i class="fas fa-circle text-danger"></i>'
            } else if (session.session_id !== null && session.status === 1 && session.ip !== null && row.pppoe
              .status === 2) {
              return '<i class="fas fa-circle text-warning"></i>'
            } else {
              return '<i class="fas fa-circle text-danger"></i>'
            }
          },
        },
        {
          data: 'pppoe_id',
          name: 'pppoe_id',
          render: function(data) {
            return `<span class="badge bg-primary">${data}</span>`;
          }
        },
        {
          data: 'payment_type',
          name: 'payment_type',
          render: function(data) {
            return `<span class="badge bg-info">${data}</span>`;
          }
        },
        {
          data: 'billing_period',
          name: 'billing_period',
        },
        {
          data: 'reg_date',
          name: 'reg_date',
          render: function(data) {
            return moment(data).format('DD/MM/YYYY');
          }
        },
        {
          data: 'pppoe.nas',
          name: 'pppoe.nas',
          render: function(data, type, row) {
            if (row.pppoe.nas === null) {
              return 'all'
            } else if (row.pppoe.nas === null) {
              return '<i class="text-danger">Unknown</i>'
            } else {
              return data
            }
          },
        },
        {
          data: 'pppoe.kode_area',
          name: 'pppoe.kode_area',
          render: function(data) {
            if (data === null) {
              return '-'
            } else {
              return data
            }
          },
        },
        {
          data: 'pppoe.kode_odp',
          name: 'pppoe.kode_odp',
          render: function(data) {
            if (data === null) {
              return '-'
            } else {
              return data
            }
          },
        },
      ],
    });
  </script>
@endpush
