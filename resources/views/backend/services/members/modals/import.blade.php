<div class="modal fade" id="import-member" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal">Import Members</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="importForm" x-load x-data="form('member')" data-url="{{ route('admin.services.member.import') }}"
          method="POST" enctype="multipart/form-data">
          <template x-if="success">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong>{{ __('Success') }}!</strong> <span x-text="success"></span>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          </template>

          @csrf
          <div class="mb-3">
            <label for="importFile" class="form-label">Import File</label>
            <input class="form-control" type="file" id="importFile" name="file" required>
            <template x-if="errors.file">
              <div class="invalid-feedback" x-text="errors.file[0]"></div>
            </template>
          </div>
          <div class="mb-3">
            <button class="btn btn-primary" type="submit" x-ref="button">Import</button>
            <a href="{{ route('admin.services.member.sample') }}" class="btn btn-warning">
              Download Template
            </a>
          </div>
        </form>

        <div class="table-responsive custom-scrollbar">
          <table id="importMemberTable" class="table-hover table-bordered table" style="font-size: 12px; width: 100%;">
            <thead>
              <tr>
                <th>#</th>
                <th>Message</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
          Close
        </button>
      </div>
    </div>
  </div>
</div>

@push('script-modal')
  <script>
    window.addEventListener('member:success', event => {
      const table = $('#importMemberTable tbody');
      table.html('');

      const {
        message,
        logs
      } = event.detail.data;

      console.log(event.detail.data, message, logs);

      logs.forEach((log, index) => {
        table.append(`
          <tr>
            <td>${index + 1}</td>
            <td>${log}</td>
          </tr>
        `);
      });
    });
  </script>
@endpush
