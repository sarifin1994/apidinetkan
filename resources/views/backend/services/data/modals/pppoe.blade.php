<!-- ppp Modal Show -->
<div class="modal fade" id="showPppoe" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal">DATA INTERNET | <span class="text-sm" id="ppp_full_name"></span></h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-1 px-2">
          <table class="table-striped table">

            <tbody style="font-size:15px">
              <tr>
                <td style="width:30%">Username</td>
                <td>: <span id="username_ppp"></span>
              </tr>
              <tr>
                <td>Password</td>
                <td>: <span id="password_ppp"></span>
              </tr>
              <tr>
                <td>Internet Profile</td>
                <td>: <span id="profile_inet_ppp"></span>
              </tr>
              <tr>
                <td>Internet Status</td>
                <td>: <span id="status_ppp"></span>
              </tr>
              <tr>
                <td>IP Address</td>
                <td>: <span id="fill_ip_ppp"></span>
              </tr>
              </tr>
              <tr>
                <td>Created</td>
                <td>: <span id="created_ppp"></span>
              </tr>
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
    $('#pppoeTable').on('click', '.show-pppoe', function() {
      let ppp_id = $(this).data('id');
      var full_name = $(this).data('name');
      $("#ppp_full_name").html(full_name);

      if (!ppp_id) {
        return;
      }

      $.ajax({
        url: baseUrl + `/${ppp_id}/ppp`,
        type: "GET",
        success: function(data) {
          $("#username_ppp").html(data.username);
          $("#password_ppp").html(data.value);
          $("#profile_inet_ppp").html(data.profile);
          if (data.session !== null && data.session.status === 1 &&
            data.session.ip !== null && data.status === 1) {
            $('#status_ppp').html(
              '<span class="badge bg-success text-light">online</span>');
            $('#fill_ip_ppp').html('<span>' + data.session.ip + '</span>');
          } else if (data.session !== null && data.session.status ===
            1 &&
            data.session.ip !== null && data.status === 2) {
            $('#status_ppp').html(
              '<span class="badge bg-warning text-light">isolir</span>');
            $('#fill_ip_ppp').html('<span>' + data.session.ip + '</span>');
          } else if (data.session !== null && data.session.status ===
            2 && data.session.ip !== null) {
            $('#status_ppp').html(
              '<span class="badge bg-danger text-light">offline</span>');
            $('#fill_ip_ppp').html('-');
          } else {
            $('#status_ppp').html(
              '<span class="badge bg-danger text-light">offline</span>');
            $('#fill_ip_ppp').html('-');
          }
          var created = data.created_at;
          var created_at = moment(created).local().format('DD/MM/YYYY HH:mm:ss');
          $("#created_ppp").html(created_at);
        }
      });
    });
  </script>
@endpush
