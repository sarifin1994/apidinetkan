<script src="{{ asset('assets/old/js/select2/select2.full.min.js') }}"></script>
<!-- Bootstrap js-->
<script src="{{ asset('assets/old/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
<!-- font awesome icon js-->
<script src="{{ asset('assets/old/js/icons/font-awesome/font-awesome.min.js') }}"></script>
<!-- feather icon js-->
<script src="{{ asset('assets/old/js/icons/feather-icon/feather.min.js') }}"></script>
<script src="{{ asset('assets/old/js/icons/feather-icon/feather-icon.js') }}"></script>
<!-- scrollbar js-->
<script src="{{ asset('assets/old/js/scrollbar/simplebar.js') }}"></script>
<script src="{{ asset('assets/old/js/scrollbar/custom.js') }}"></script>
<!-- Sidebar jquery-->
<script src="{{ asset('assets/old/js/config.js') }}"></script>
<!-- Plugins JS start-->
<script src="{{ asset('assets/old/js/sidebar-menu.js') }}"></script>
<script src="{{ asset('assets/old/js/sidebar-pin.js') }}"></script>
<script src="{{ asset('assets/old/js/slick/slick.min.js') }}"></script>
<script src="{{ asset('assets/old/js/slick/slick.js') }}"></script>
<script src="{{ asset('assets/old/js/header-slick.js') }}"></script>
<!-- <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script> -->
 <!-- Leaflet CSS -->
 <link rel="stylesheet" href="{{ asset('assets/leaflet/leaflet.css') }}">
    <!-- Leaflet JS -->
    <script src="{{ asset('assets/leaflet/leaflet.js') }}"></script>


<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
</script>

@yield('scripts')
@stack('scripts')
@stack('script-childs')
@stack('script-modal')
<!-- Plugins JS Ends-->
<!-- Theme js-->
<script src="{{ asset('assets/old/js/script.js') }}"></script>
<!-- Plugin used-->
<!-- toastr js -->
<script src="{{ asset('assets/old/radiusqu/dist/libs/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/old/js/toastr.min.js') }}"></script>

<script>
  $(document).ready(function() {
    function handleToggle(status, url, confirmation, clickedElement) {

      if (confirmation) {
        Swal.fire({
          title: 'Are you sure?',
          text: 'You are about to change the status of this item',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, change it!'
        }).then((result) => {
          if (result.isConfirmed) {
            updateStatus(status, url, clickedElement);
          } else if (clickedElement.is(':checkbox')) {
            clickedElement.prop('checked', !status);
          }
        });
      } else {
        updateStatus(status, url, clickedElement);
      }
    }

    $(document).on('change', '.toggle-status', function() {
      let status = $(this).prop('checked') ? 1 : 0;
      let url = $(this).data('route');
      let confirmation = $(this).data('confirmation');
      handleToggle(status, url, confirmation, $(this));
    });

    $(document).on('click', '.toggle-status-btn', function() {
      let status = $(this).data('status');
      let url = $(this).data('route');
      let confirmation = $(this).data('confirmation');
      handleToggle(status, url, confirmation, $(this));
    });

    function updateStatus(status, url, clickedElement) {
      $.ajax({
        type: "PUT",
        url: url,
        data: {
          status: status,
          _token: '{{ csrf_token() }}',
        },
        success: function(data) {
          if (clickedElement.is(':checkbox')) {
            clickedElement.prop('checked', status);
          } else {
            clickedElement.prop('disabled', true);
          }

          if ($.fn.DataTable.isDataTable('.dataTable')) {
            $('.dataTable').DataTable().ajax.reload();
          }

          toastr.success("Status Updated Successfully");
        },
        error: function(xhr, status, error) {
          toastr.error("Failed to update status");

          if (clickedElement.is(':checkbox')) {
            clickedElement.prop('checked', !status);
          } else {
            clickedElement.prop('disabled', false);
          }
        }
      });
    }
  });
</script>


