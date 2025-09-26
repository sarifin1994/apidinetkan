<!-- Modal for Displaying PPPoE Session -->
<div class="modal fade" id="show_session" tabindex="-1" aria-labelledby="sessionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sessionModalLabel">PPPoE Session</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive custom-scrollbar">
          <table id="sessionTable" class="table-hover table-bordered table" style="font-size: 12px; width: 100%;">
            <thead class="table-dark">
              <tr>
                <th>Start</th>
                <th>Stop</th>
                <th>IP</th>
                <th>MAC</th>
                <th>Rx</th>
                <th>Tx</th>
                <th>Uptime</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    // Initialize DataTable for sessionTable with better configuration
    let sessionTable = $('#sessionTable').DataTable({
      pageLength: 5,
      lengthMenu: [
        [5, 10, 20, 50],
        [5, 10, 20, 50]
      ],
      scrollX: true,
      destroy: true,
      columns: [{
          data: 'start',
          title: 'Start'
        },
        {
          data: 'stop',
          title: 'Stop',
          render: function(data) {
            return data ? data : '<span class="text-muted">Active</span>';
          }
        },
        {
          data: 'ip',
          title: 'IP'
        },
        {
          data: 'mac',
          title: 'MAC'
        },
        {
          data: 'input',
          title: 'Rx',
          render: formatBytes
        },
        {
          data: 'output',
          title: 'Tx',
          render: formatBytes
        },
        {
          data: 'uptime',
          title: 'Uptime',
          render: formatUptime
        }
      ]
    });

    // Helper function to format bytes
    function formatBytes(bytes) {
      const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
      if (bytes === 0) return 'n/a';
      const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)), 10);
      return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
    }

    // Helper function to format uptime
    function formatUptime(seconds) {
      seconds = Number(seconds);
      const d = Math.floor(seconds / (3600 * 24));
      const h = Math.floor((seconds % (3600 * 24)) / 3600);
      const m = Math.floor((seconds % 3600) / 60);
      const parts = [];

      if (d > 0) parts.push(`${d}d`);
      if (h > 0) parts.push(`${h}h`);
      if (m > 0) parts.push(`${m}m`);

      return parts.join(' ') || '0m';
    }
  });
</script>
