@extends('backend.layouts.app')

@section('title', 'Services Settings')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Services Settings</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg>
              </a>
            </li>
            <li class="breadcrumb-item">Services</li>
            <li class="breadcrumb-item active">Settings</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Container-fluid starts-->
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-6">
        <div class="card card-header-actions">
          <div class="card-header">
            <div class="h5 col-auto">MODE ISOLIR</div>
          </div>
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-auto">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="0" id="isolir">
                  <label class="form-check-label" for="isolir">
                    Aktifkan Mode Isolir
                  </label>
                </div>
              </div>
              <div class="col-auto">
                <button class="btn btn-sm btn-primary text-light" type="button" id="update">
                  <i class="fas fa-circle-check"></i>&nbsp;Simpan
                </button>
              </div>
              <div class="col-auto">
                <select id="versionSelector" class="form-select form-select-sm">
                  <option value="6">RouterOS v6</option>
                  <option value="7" selected>RouterOS v7</option>
                </select>
              </div>
            </div>
            <div class="sbp-preview-code">
              <input type="hidden" id="isolir_id">

              <small class="h6">Aktifkan Web Proxy</small>
              <div class="position-relative">
                <button type="button" class="btn btn-sm btn-transparent-dark position-absolute end-0"
                  onclick="copyToClipboard('copyWebProxy', 'webProxyBtn')">
                  <span id="webProxyBtn">Copy</span>
                </button>
                <textarea class="form-control pt-3" rows="5" readonly id="copyWebProxy"></textarea>
              </div>
              <hr />

              <small class="h6">Redirect User Suspend ke Halaman Isolir</small>
              <div class="position-relative">
                <button type="button" class="btn btn-sm btn-transparent-dark position-absolute end-0"
                  onclick="copyToClipboard('copyRedirect', 'redirectBtn')">
                  <span id="redirectBtn">Copy</span>
                </button>
                <textarea class="form-control pt-3" rows="5" readonly id="copyRedirect"></textarea>
              </div>
              <hr />

              <small class="h6">Blokir Akses Internet User Suspend</small>
              <div class="position-relative">
                <button type="button" class="btn btn-sm btn-transparent-dark position-absolute end-0"
                  onclick="copyToClipboard('copyBlock', 'blockBtn')">
                  <span id="blockBtn">Copy</span>
                </button>
                <textarea class="form-control pt-3" rows="5" readonly id="copyBlock"></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card card-header-actions">
          <div class="card-header">
            <div class="h5 col-auto">INFORMASI</div>
          </div>
          <div class="card-body">
            <ol>
              <li>IP Address Isolir akan di generate secara random oleh sistem dengan Network <small
                  class="text-danger">172.30.0.0/23</small></li>
              <li>Tidak perlu menambahkan IP Pool</li>
              <li>Jika merubah halaman isolir, anda dapat edit pada menu Access di Web Proxy lalu rubah dst-address <small
                  class="text-danger">!35.219.4.234</small> dengan IP web isolir anda</li>
              <li>Setelah mengaktifkan mode isolir, user PPPoE akan konek dengan IP isolir kemudian di redirect ke halaman
                isolir kami</li>
              <li>Jika ada pertanyaan mengenai setting mode isolir, jangan ragu silahkan hubungi kami melalui nomor
                WhatsApp yang telah disediakan</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid Ends-->
@endsection

@section('scripts')
  <script type="text/javascript">
    $(document).ready(function() {
      fetchSettings();
      $("#versionSelector").on('change', fetchSettings);

      $("#isolir").on('change', function() {
        $(this).val($(this).is(":checked") ? '1' : '0');
      });

      $('#update').on('click', updateSettings);
    });

    function fetchSettings() {
      $.ajax({
        url: '{{ url()->current() }}',
        type: "GET",
        cache: false,
        success: function(response) {
          const data = response.data[0];
          $('#isolir_id').val(data.id);
          $('#isolir').prop('checked', data.isolir === 1).val(data.isolir === 1 ? '1' : '0');

          const version = $("#versionSelector").val();
          if (version === '6') {
            // Mikrotik v6 commands
            $('#copyWebProxy').val(`/ip proxy
set enabled=yes parent-proxy=0.0.0.0 port=8080
/ip proxy access
add action=deny redirect-to=http://isolir.radiusqu.com src-address=172.30.0.0/23`);

            $('#copyRedirect').val(`/ip firewall nat
add action=redirect chain=dstnat comment="redirect isolir - by radiusqu" disabled=no \
dst-address=!35.219.4.234 dst-port=80,443 protocol=tcp \
src-address=172.30.0.0/23 to-ports=8080`);

            $('#copyBlock').val(`/ip firewall filter
add action=drop chain=forward comment="drop isolir - by radiusqu" \
dst-address=!35.219.4.234 protocol=tcp src-address=172.30.0.0/23
add action=drop chain=forward comment="drop isolir - by radiusqu" \
dst-address=!35.219.4.234 dst-port=!53,5353 protocol=udp src-address=172.30.0.0/23`);
          } else {
            // Mikrotik v7 commands
            $('#copyWebProxy').val(`/ip proxy
set enabled=yes port=8080
/ip proxy access
add action=deny redirect-to=http://isolir.radiusqu.com src-address=172.30.0.0/23`);

            $('#copyRedirect').val(`/ip firewall nat
add action=redirect chain=dstnat comment="redirect isolir - by radiusqu" disabled=no \
dst-address=!35.219.4.234 dst-port=80,443 protocol=tcp \
src-address=172.30.0.0/23 to-ports=8080`);

            $('#copyBlock').val(`/ip firewall filter
add action=drop chain=forward comment="drop isolir - by radiusqu" \
dst-address=!35.219.4.234 protocol=tcp src-address=172.30.0.0/23
add action=drop chain=forward comment="drop isolir - by radiusqu" \
dst-address=!35.219.4.234 dst-port=!53,5353 protocol=udp src-address=172.30.0.0/23`);
          }
        },
        error: function() {
          console.error('Failed to fetch settings.');
        }
      });
    }

    function updateSettings() {
      const settingId = $('#isolir_id').val();
      const data = {
        'isolir': $('#isolir').val(),
      };

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        url: baseUrl + `/${settingId}`,
        type: "PUT",
        cache: false,
        data: data,
        dataType: "json",
        success: function(response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: response.message,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(() => location.reload(), 1500);
          } else {
            handleValidationErrors(response.error);
          }
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while updating the settings. Please try again.',
          });
        }
      });
    }

    function handleValidationErrors(errors) {
      $.each(errors, function(key, value) {
        const el = $(document).find(`[name="${key}"]`);
        el.next('.form-text.text-danger').remove();
        el.after(`<div class="form-text text-danger">${value[0]}</div>`);
      });
    }

    function copyToClipboard(textareaId, buttonId) {
      const copyText = document.getElementById(textareaId);
      copyText.removeAttribute('readonly');
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      copyText.setAttribute('readonly', '');

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(copyText.value).then(() => {
          Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'The text has been copied to the clipboard.',
            timer: 1500,
            showConfirmButton: false
          });
          const button = document.getElementById(buttonId);
          button.innerHTML = "Copied";
          setTimeout(() => button.innerHTML = "Copy", 1500);
        }).catch(err => {
          fallbackCopyTextToClipboard(copyText.value, buttonId);
        });
      } else {
        fallbackCopyTextToClipboard(copyText.value, buttonId);
      }
    }

    function fallbackCopyTextToClipboard(text, buttonId) {
      const textArea = document.createElement("textarea");
      textArea.value = text;
      document.body.appendChild(textArea);
      textArea.focus();
      textArea.select();
      try {
        document.execCommand('copy');
        Swal.fire({
          icon: 'success',
          title: 'Copied!',
          text: 'The text has been copied to the clipboard.',
          timer: 1500,
          showConfirmButton: false
        });
        const button = document.getElementById(buttonId);
        button.innerHTML = "Copied";
        setTimeout(() => button.innerHTML = "Copy", 1500);
      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Copy failed. Please try again manually.',
        });
        console.error('Failed to copy text:', err);
      }
      document.body.removeChild(textArea);
    }
  </script>
@endsection
