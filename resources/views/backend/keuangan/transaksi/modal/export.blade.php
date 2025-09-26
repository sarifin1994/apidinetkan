<!-- Modal Export -->
<div class="modal fade" id="export" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal">Export Transaksi</h5>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ url('/keuangan/transaksi/export') }}" method="POST" enctype="multipart/form-data" id="exportForm">
            @csrf
            <div class="form-group mb-3">
              <label for="periode" class="mb-1">Pilih Periode <small class="text-danger">*</small></label>
              @php
                $now = \Carbon\Carbon::now()->format('F-Y');
              @endphp
              <input type="text" readonly class="form-select" id="periode" name="periode" value="{{ $now }}">
            </div>
            <div class="form-group mb-3">
              <label for="format" class="mb-1">Pilih Format <small class="text-danger">*</small></label>
              <select class="form-select" name="format">
                <option value="pdf">PDF</option>
                <option value="excel">Excel</option>
              </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
          <button class="btn btn-primary text-white" type="submit" id="exportBtn">
            Export data
          </button>
        </div>
          </form>
      </div>
    </div>
  </div>
  