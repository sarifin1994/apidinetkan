@extends('backend.layouts.app')

@section('title', 'Midtrans Integration')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Midtrans Integration</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Integration</li>
            <li class="breadcrumb-item active">Midtrans</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Container-fluid starts-->
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header align-items-center justify-content-between">
            <div class="h5 col-auto">MIDTRANS ACCESS KEYS</div>
          </div>
          <div class="card-body">
            <form>
              @csrf
              <input type="hidden" id="midtrans_id" value="{{ $midtrans->id }}">
              <div class="row gx-3 mb-3">
                <div class="col-md-6">
                  <label class="mb-1" for="id_merchant">ID Merchant</label>
                  <input class="form-control" id="id_merchant" type="text" placeholder="G132706530"
                    value="{{ $midtrans->id_merchant }}" />
                </div>
              </div>
              <div class="row gx-3 mb-3">
                <div class="col-md-6">
                  <label class="mb-1" for="client_key">Client Key</label>
                  <input class="form-control" id="client_key" type="text" placeholder="Mid-client-eHid7cD1W-D12dgk"
                    value="{{ $midtrans->client_key }}" />
                </div>

                <div class="col-md-6">
                  <label class="mb-1" for="server_key">Server Key</label>
                  <input class="form-control" id="server_key" type="text"
                    placeholder="Mid-server-5iI_TXQPqwt0aoYBnltvNpjM" value="{{ $midtrans->server_key }}" />
                </div>
              </div>
              <!-- Form Row-->
              <div class="row gx-3 mb-3">
                <!-- Form Group (phone number)-->
                <div class="col-md-6">
                  <label class="mb-1" for="admin_fee">Biaya Admin</label>
                  <input class="form-control" id="admin_fee" type="number" placeholder="0"
                    value="{{ $midtrans->admin_fee }}" />
                  <small>Biaya admin yang akan dibebankan ke pelanggan untuk setiap invoice yang
                    dibayar</small>

                </div>
                <!-- Form Group (birthday)-->
                <div class="col-md-6">
                  <label class="mb-1" for="status">Status</label>
                  <select class="form-select" id="status">
                    @if ($midtrans->status === 1)
                      <option value="1">Production</option>
                      <option value="0">Sandbox</option>
                    @else
                      <option value="0">Sandbox</option>
                      <option value="1">Production</option>
                    @endif
                  </select>
                  <small>Pilih Aktif jika akun midtrans anda sudah siap menerima pembayaran</small>
                </div>
              </div>
              <div class="row mb-3">
                <span>URL Notifikasi Pembayaran Midtrans <small>Copy-Paste di menu PENGATURAN > PAYMENT</small><br><b>
                    {{ config('app.url') }}/notification/midtrans</b></span>
              </div>
              <hr>
              <div class="row">
                <div class="col-lg-8">
                  <span><i>Untuk tutorial integrasi Midtrans silakan ikuti tutorial berikut <a href="#"
                        class="text-danger">#</a></i></span>
                </div>
                <div class="col-lg-4">
                  <button class="btn btn-primary float-end" type="submit" id="update">Save changes</button>
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid Ends-->
@endsection

@section('scripts')
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>

  <script type="text/javascript">
    $('#update').click(function(e) {
      e.preventDefault();
      let midtran = $('#midtrans_id').val();

      // collect data by id
      var data = {
        'id_merchant': $('#id_merchant').val(),
        'client_key': $('#client_key').val(),
        'server_key': $('#server_key').val(),
        'admin_fee': $('#admin_fee').val(),
        'status': $('#status').val(),
      }

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        url: baseUrl + `/${midtran}`,
        type: "PUT",
        cache: false,
        data: data,
        dataType: "json",

        success: function(data) {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: `${data.message}`,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(function() {
              location.reload()
            }, 1500);
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<span class= "text-xs text-danger">' + value[0] +
                '</span>'));
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });

    });
  </script>
@endsection
