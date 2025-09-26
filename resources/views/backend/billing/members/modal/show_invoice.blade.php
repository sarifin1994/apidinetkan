<!-- invoice Modal Show -->
<div class="modal fade" id="show_invoice" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal">DATA INVOICE | <span class="text-sm" id="inv_full_name"></span></h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row table-responsive px-3">
          <table id="invoiceTable" class="table-hover table" width="100%">
            <thead>
              <tr>
                <th>#</th>
                <th>TGL INVOICE</th>
                <th>NO INVOICE</th>
                <th>TIPE</th>
                <th>PERIODE</th>
                <th>TOTAL</th>
                <th>STATUS</th>
                <th>TGL BAYAR</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>

      </div>
      <div class="modal-footer">
        {{-- <button class="btn btn-secondary" type="submit" id="refresh">
                    Refresh
                </button> --}}
        <button class="btn btn-primary" type="button" data-bs-dismiss="modal">
          Close
        </button>
      </div>
    </div>
  </div>
</div>
