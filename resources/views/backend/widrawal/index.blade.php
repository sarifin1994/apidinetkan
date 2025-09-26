@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Widrawal')

<!-- Content -->
<div class="container-fluid">
  <h2 class="fw-bold text-dark">Widrawal</h2>

  <div class="row g-4">
    <!-- Stylish Card Component -->
    @php
      $cards = [
        [
          'title' => number_format(multi_auth()->balance, 0, ',', '.'),
          'subtitle' => 'total komisi',
          'value' => number_format(multi_auth()->balance, 0, ',', '.'),
          'icon' => 'currency-dollar',
          'color' => 'success',
          'link' => '/keuangan/mitra'
        ],
      ];
    @endphp
    <div class="col-6">
      <div class="card position-relative overflow-hidden">
        <div class="card-body p-4 d-flex flex-column justify-content-between">
          <form method="post" id="formBankAccount">
            <div class="row">
              <div class="col-12 mb-3">
                <label for="bank">Nomor Rekening</label>
                  <select class="form-control" id="bank" name="bank" autocomplete="off"
                      data-placeholder="Pilih Bank">
                      @foreach($listBank as $key=>$val)
                      <option value="{{$key}}" <?php if(isset($bank->bank_code)){ if($bank->bank_code == $key) { echo 'selected';} } ?> >{{$val}}</option>
                      @endforeach
                  </select>
              </div>
              <div class="col-12 mb-3">
                <label for="account_number">Nomor Rekening</label>
                <input type="text" class="form-control" name="account_number" id="account_number" value="{{$bank ? $bank->account_number : ''}}">
              </div>
              <div class="col-12 mb-3">
                <label for="account_number">Nama Rekening</label>
                <input type="text" class="form-control" value="{{$bank ? $bank->account_name : ''}}" disabled>
              </div>
              <div class="col-12 mb-3">
                <!-- <label for="account_name">Nama Rekening</label>
                <input type="text" class="form-control" name="account_name" id="account_name" readonly> -->
                <button class="btn btn-primary" id="inq_account">Check Account</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>


    @foreach ($cards as $card)
      <div class="col-6">
        <a class="text-decoration-none">
          <div class="card rounded-4 hover-shadow position-relative overflow-hidden">
            <div class="card-body">
              <div class="mb-3">
                <small class="text-uppercase text-{{ $card['color'] }} fw-semibold">{{ $card['subtitle'] }}</small>
                <h5 class="fw-bold text-dark mb-1">
                  <i class="fa-solid fa-hand-holding-dollar"></i>
                  {{ $card['title'] }}
                </h5>
              </div>
            </div>
            <div class="card-body">
              @if(isset($duitku->status_widrawal))
                @if($duitku->status_widrawal == 1)
                <form method="POST" id="formDisburs">
                  <div class="row">
                    <div class="col-12 mb-3">
                      <label for="nominal">Nominal Tarik </label>
                      <input type="number" class="form-control" id="nominal" name="nominal" min="{{$duitku->minimal_disburs}}" max="{{multi_auth()->balance}}" value="{{multi_auth()->balance}}" autocomplete="off">
                      <span style="color: red">Setiap penarikan data akan di kenakan biaya Rp {{number_format($duitku->fee_disburs, 0, ',', '.')}}</span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12 mb-3">
                      <button class="btn btn-primary" id="btnTarik"> Tarik </button>
                    </div>
                  </div>
                </form>
                @endif
                
                @if($duitku->status_widrawal != 1)
                <div class="alert alert-danger">
                  <span>Saat ini tidak dapat melakukan penarikan komisi</span>
                </div>
                @endif
              @else
                <div class="alert alert-danger">
                  <span>Saat ini tidak dapat melakukan penarikan komisi</span>
                </div>
              @endif
            </div>
          </div>
        </a>
      </div>
    @endforeach
  </div>
</div>
@endsection

@push('scripts')
<script>
  
  $('#inq_account').click(function(e) {
      e.preventDefault();
      var form = $('#formBankAccount');
      // Simpan referensi tombol dan teks aslinya
      var btn = $(this);
      var originalText = btn.html();

      // Ubah tampilan tombol: nonaktifkan dan tampilkan teks dengan spinner di sebelah kanan
      btn.prop('disabled', true).html(
          'Checking Account'
      );

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      $.ajax({
          url: `widrawal/inq_account`,
          type: "POST",
          cache: false,
          dataType: "json",
          data: form.serialize(),
          success: function(data) {
            Swal.fire({
                icon: 'success',
                title: `${data.accountName}`,
                text: 'Account Saved',
                showConfirmButton: false,
                timer: 2500
            });
            setTimeout(function() {
                // Kembalikan tombol ke kondisi semula
                btn.prop('disabled', false).html(originalText);
            }, 2500);
            location.reload()
          },
          error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: `${xhr.responseJSON.message}`,
                text: '',
                showConfirmButton: false,
                timer: 2500
            });
            setTimeout(function() {
                // Kembalikan tombol ke kondisi semula
                btn.prop('disabled', false).html(originalText);
            }, 2500);
            location.reload()
          }
      });
  });

  
  
  $('#btnTarik').click(function(e) {
      e.preventDefault();
      var form = $('#formDisburs');
      // Simpan referensi tombol dan teks aslinya
      var btn = $(this);
      var originalText = btn.html();

      // Ubah tampilan tombol: nonaktifkan dan tampilkan teks dengan spinner di sebelah kanan
      btn.prop('disabled', true).html(
          'Konfirmasi Tarik'
      );

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      $.ajax({
        url: `widrawal/inquiry`,
        type: "POST",
        cache: false,
        dataType: "json",
        data: form.serialize(),
        success: function(data) {
          // Tampilkan SweetAlert dengan tombol konfirmasi
          Swal.fire({
            title: 'Konfirmasi Penarikan',
            html: `Jumlah: <strong>Rp ${data.amountTransfer}</strong>
                  <br>
                  Nama : <strong>${data.accountName}</strong>
                  <br>
                  Rekening : <strong>${data.bankAccount}</strong>`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Lanjutkan',
            cancelButtonText: 'Batal'
          }).then((result) => {
            if (result.isConfirmed) {
              Swal.fire({
                  title: 'Please wait...',
                  text: 'Processing Data ....',
                  allowOutsideClick: false,
                  showConfirmButton: false,
                  didOpen: () => {
                      Swal.showLoading();
                  }
              });
              // Jika user menekan tombol "Lanjutkan", lakukan POST lagi
              $.ajax({
                url: 'widrawal/payment',
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: {
                  nominal: data.amountTransfer,
                  custRefNumber: data.custRefNumber,
                  accountName: data.accountName,
                  disburseId: data.disburseId
                },
                success: function(response) {
                  swal.close();
                  Swal.fire({
                    title: 'Berhasil',
                    text: 'Penarikan berhasil diproses!',
                    icon: 'success'
                  });
                  btn.prop('disabled', false).html(originalText);
                  location.reload();
                },
                error: function(xhr) {
                  swal.close();
                  Swal.fire({
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat memproses penarikan.',
                    icon: 'error'
                  });
                  btn.prop('disabled', false).html(originalText);
                  location.reload();
                }
              });
            }
          });
        },
        error: function(xhr) {
          Swal.fire({
            title: 'Error',
            text: `${xhr.responseJSON.message}`,
            icon: 'error'
          });
          btn.prop('disabled', false).html(originalText);
        }
      });

  });
$('#bank').select2({
    allowClear: true,
    placeholder:'--- silahkan pilih ---'
});
</script>
@endpush