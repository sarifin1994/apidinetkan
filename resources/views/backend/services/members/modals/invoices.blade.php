<!-- invoice Modal Show -->
<div class="modal fade" id="memberInvoices" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal">DATA INVOICE | <span class="text-sm" id="inv_full_name"></span></h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row table-responsive px-3">
          <table id="memberInvoicesTable" class="table-hover table" width="100%">
            <thead>
              <tr>
                <th>#</th>
                <th>SERVICE</th>
                <th>INV ID</th>
                <th>TYPE</th>
                <th>PERIOD</th>
                <th>TOTAL</th>
                <th>STATUS</th>
                <th>DATE</th>
                <th>PAID</th>
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
    const memberInvoicesTable = $('#memberInvoicesTable').DataTable({
      processing: true,
      serverSide: true,
      deferLoading: 0,
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex'
        },
        {
          data: 'pppoe_id',
          name: 'pppoe_id',
          render: function(data) {
            return `<span class="badge bg-primary">${data}</span>`;
          }
        },
        {
          data: 'id',
          name: 'id',
          render: function(data) {
            return `<span class="badge bg-warning">${data}</span>`;
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
          data: 'price',
          name: 'price',
          render: function(data) {
            return `Rp ${data.toLocaleString()}`;
          }
        },
        {
          data: 'status',
          name: 'status',
          render: function(data) {
            if (data === 1) {
              return '<i class="fas fa-circle-check text-success"></i>'
            } else {
              return '<i class="fas fa-circle-xmark text-danger"></i>'
            }
          }
        },
        {
          data: 'invoice_date',
          name: 'invoice_date',
          render: function(data) {
            return moment(data).local().format('DD/MM/YYYY');
          }
        },
        {
          data: 'paid_date',
          name: 'paid_date',
          render: function(data) {
            return moment(data).local().format('DD/MM/YYYY');
          }
        },
      ],
      order: [
        [8, 'desc']
      ]
    });
  </script>
@endpush
