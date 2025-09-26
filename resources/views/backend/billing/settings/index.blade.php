@extends('backend.layouts.app')

@section('title', 'Billing Settings')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Billing Settings</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Billing</li>
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
          <div class="card-header align-items-center justify-content-between">
            <div class="h5 col-auto">SETTINGS</div>
            {{-- <div class="col-auto">
                        <button class="btn btn-sm btn-primary text-light" type="submit" id="update">
                            <i class="fas fa-circle-check"></i>&nbspApply
                        </button>
                    </div> --}}
          </div>
          <div class="card-body">
            <input type="hidden" id="billing_id">
            <div class="form-group row">
              <label for="payment_gateway" class="col-sm-10">Pilih Payment Gateway yang ingin digunakan</label>
              <div class="col-sm-2">
                <select class="form-select" id="payment_gateway" name="payment_gateway">
                  <option value="manual">Manual</option>
                  <option value="duitku">Duitku</option>
                  <option value="midtrans">Midtrans</option>
                  <option value="tripay">TriPay</option>
                  <option value="xendit">Xendit</option>
                </select>
              </div>
            </div>

            <div class="form-group row" id="manual-wrapper" style="display: none;">
              <label for="manual">Isi pesan yang akan di tampilkan pada invoice jika menggunakan
                metode manual</label>
              <div>
                <textarea class="form-control" id="manual" name="manual" rows="6"></textarea>
              </div>
            </div>
            <hr />

            <div class="form-group row">
              <label for="due_bc" class="col-sm-10">Tanggal jatuh tempo untuk
                pembarayan metode <span class="text-primary">Pascabayar - Billing Cycle</span>
              </label>
              <div class="col-sm-2">
                <input type="text" class="form-control" id="due_bc" name="due_bc" placeholder="20">
              </div>
            </div>
            <hr />

            <div class="form-group row">
              <label for="inv_fd" class="col-sm-10">Minimal berapa hari generate Invoice sebelum jatuh tempo untuk
                billing <span class="text-primary">Fixed Date, isi 7 paling cepat</span>
              </label>
              <div class="col-sm-2">
                <input type="text" class="form-control" id="inv_fd" name="inv_fd" placeholder="1">
              </div>
            </div>

            <hr />

            <div class="form-group row">
              <label for="suspend_date" class="col-sm-10">Batas waktu paling lambat untuk pembayaran invoice setelah jatuh
                tempo sebelum user pppoe di suspend, <span class="text-primary">isi 0 jika tidak pernah</span>
              </label>
              <div class="col-sm-2">
                <input type="text" class="form-control" id="suspend_date" name="suspend_date" placeholder="1">
              </div>
            </div>
            <hr />

            <div class="form-group row">
              <label for="suspend_time" class="col-sm-9">Waktu pelanggan <span class="text-primary">di suspend /
                  isolir</span> oleh sistem jika tagihan belum di bayar
              </label>
              <div class="col-sm-3">
                <input type="text" class="form-control" id="suspend_time" name="suspend_time" placeholder="06:00:00">
              </div>
            </div>
            <hr />

            <div class="form-group row">
              <label for="notif_bi" class="col-sm-10">Berapa hari pelanggan di kirim notifikasi sebelum jatuh tempo, <span
                  class="text-primary">isi 0 jika tidak pernah</span>
              </label>
              <div class="col-sm-2">
                <input type="text" class="form-control" id="notif_bi" name="notif_bi" placeholder="0">
              </div>
            </div>
            <hr />

            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="0" id="notif_it">
              <label class="form-check-label" for="notif_it">
                Kirim notifikasi saat terbit invoice <small><i>Saat invoice terbit baik secara manual ataupun oleh
                    sistem</i></small>
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="0" id="notif_ps">
              <label class="form-check-label" for="notif_ps">
                Kirim notifikasi status pembayaran <small><i>Saat invoice dibayar atau dibatalkan</i></small>
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="0" id="notif_sm">
              <label class="form-check-label" for="notif_sm">
                Kirim notifikasi status member <small><i>Saat pelanggan baru aktif dan saat pelanggan disuspend oleh
                    sistem</i></small>
              </label>
            </div>
            <hr>
            <div class="modal-footer">
              <button class="btn btn-primary" id="update" type="submit">
                Simpan
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card card-header-actions h-100">
          <div class="card-header align-items-center justify-content-between">
            <div class="h5 col-auto">INFORMASI</div>
          </div>
          <div class="card-body">
            <div class="sbp-preview-code">
              <ol>
                <li><span class="text-primary">PASCABAYAR</span> merupakan tipe tagihan pakai dulu baru bayar dan ada 2
                  opsi siklus tagihan yaitu <span class="text-success">BILLING CYCLE</span> dan <span
                    class="text-warning">FIXED DATE</span></li>
                <li><span class="text-primary">PRABAYAR</span> merupakan tipe tagihan bayar dulu baru pakai dan hanya ada
                  1 opsi siklus tagihan yaitu <span class="text-warning">FIXED DATE</span></li>
                <li><span class="text-success">BILLING CYCLE</span> merupakan siklus tagihan dengan tanggal jatuh tempo
                  yang sama setiap bulannya (prorate)</li>
                <li><span class="text-success">BILLING CYCLE</span> menggenerate invoice secara otomatis setiap tanggal 1
                  setiap bulannya</li>
                <li><span class="text-warning">FIXED DATE</span> merupakan siklus tagihan dengan tanggal jatuh tempo
                  berdasarkan tanggal aktif / reg date</li>
                <li><span class="text-warning">FIXED DATE</span> menggenerate invoice paling cepat 7 hari sebelum tanggal
                  jatuh tempo dan bisa diatur dibawah itu </li>
                <li>Jika ada pertanyaan mengenai billing setting, jangan ragu silahkan hubungi kami
                  melalui nomor whatsapp yang telah disediakan</li>
              </ol>
            </div>

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
      //fetch detail post with ajax
      $.ajax({
        url: '{{ url()->current() }}',
        type: "GET",
        cache: false,
        success: function(response) {
          $('#billing_id').val(response.data[0].id);
          $('#due_bc').val(response.data[0].due_bc);
          $('#inv_fd').val(response.data[0].inv_fd);
          $('#suspend_date').val(response.data[0].suspend_date);
          $('#suspend_time').val(response.data[0].suspend_time);
          $('#notif_bi').val(response.data[0].notif_bi);
          $('#payment_gateway').val(response.data[0].payment_gateway);
          $('#manual').val(response.data[0].bank_account);
          // $('#notif_it').val(response.data[0].notif_it);
          // $('#notif_ps').val(response.data[0].notif_ps);
          // $('#notif_sm').val(response.data[0].notif_sm);
          if (response.data[0].notif_it === 1) {
            $('#notif_it').attr('checked', true);
            $('#notif_it').val('1');
          } else {
            $('#notif_it').attr('checked', false);
            $('#notif_it').val('0');
          };
          if (response.data[0].notif_ps === 1) {
            $('#notif_ps').attr('checked', true);
            $('#notif_ps').val('1');
          } else {
            $('#notif_ps').attr('checked', false);
            $('#notif_ps').val('0');
          };
          if (response.data[0].notif_sm === 1) {
            $('#notif_sm').attr('checked', true);
            $('#notif_sm').val('1');
          } else {
            $('#notif_sm').attr('checked', false);
            $('#notif_sm').val('0');
          };

          if (response.data[0].payment_gateway === 'manual') {
            $('#manual-wrapper').show();
          } else {
            $('#manual-wrapper').hide();
          }
        }
      });
    });

    $("#notif_it").click(function() {
      if ($("#notif_it").prop("checked")) {
        $("#notif_it").val(1);
      } else {
        $("#notif_it").val(0);
      }
    });

    $("#notif_ps").click(function() {
      if ($("#notif_ps").prop("checked")) {
        $("#notif_ps").val(1);
      } else {
        $("#notif_ps").val(0);
      }
    });

    $("#notif_sm").click(function() {
      if ($("#notif_sm").prop("checked")) {
        $("#notif_sm").val(1);
      } else {
        $("#notif_sm").val(0);
      }
    });

    $('#update').click(function() {
      let setting = $('#billing_id').val();

      // collect data by id
      var data = {
        'due_bc': $('#due_bc').val(),
        'inv_fd': $('#inv_fd').val(),
        'suspend_date': $('#suspend_date').val(),
        'suspend_time': $('#suspend_time').val(),
        'notif_bi': $('#notif_bi').val(),
        'payment_gateway': $('#payment_gateway').val(),
        'manual': $('#manual').val(),
        'notif_it': $('#notif_it').val(),
        'notif_ps': $('#notif_ps').val(),
        'notif_sm': $('#notif_sm').val(),
      }

      $.ajax({
        url: baseUrl + `/${setting}`,
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
              el.after($('<div class="form-text text-danger">' + value[0] +
                '</div>'));
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });
    });

    $('#payment_gateway').change(function() {
      if ($(this).val() === 'manual') {
        $('#manual-wrapper').show();
      } else {
        $('#manual-wrapper').hide();
      }
    });

    $('#manual').attr('placeholder',
      'No Rekening BCA : 035 300 000 0\nAtas Nama : PT. Karya Anak Bangsa\n\nNo Rekening BNI : 011 000 000 0\nAtas Nama : PT. Karya Anak Bangsa\n\nNo Rekening Mandiri : 900 000 000 0\nAtas Nama : PT. Karya Anak Bangsa'
    );
  </script>
@endsection
