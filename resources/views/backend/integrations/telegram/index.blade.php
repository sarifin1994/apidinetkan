@extends('backend.layouts.app')

@section('title', 'Telegram Integration')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Telegram Integration</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Integration</li>
            <li class="breadcrumb-item active">Telegram</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Container-fluid starts-->
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card card-header-actions h-100">
          <div class="card-header">
            <div class="h5 col-auto">TELEGRAM</div>
          </div>
          <div class="card-body table-responsive">

            <table id="myTable" class="table-hover display nowrap table" width="100%">
              <thead>
                <tr>
                  <th style="text-align:left!important">TIPE GRUP</th>
                  <th style="text-align:left!important">CHAT ID</th>
                  <th>AKSI</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($telegram as $item)
                  <tr>
                    @if ($item->tipe === 1)
                      <td><span class="badge bg-primary">PSB / Pasang Baru</span></td>
                    @else
                      <td><span class="badge bg-secondary">GGN / Gangguan</span></td>
                    @endif
                    <td style="text-align:left!important"><?= $item->chatid ?></td>
                    <td>
                      <a href="https://api.telegram.org/bot{{ $bot_token }}/sendMessage?chat_id={{ $item->chatid }}&text=Hii,+i'm+radiusqu_bot"
                        target="_blank" class="badge b-ln-height badge-primary">
                        <i class="fab fa-telegram"></i>&nbspCheck Bot
                      </a>
                      <a href="javascript:void(0)" id="show" data-id="{{ $item->id }}"
                        class="badge b-ln-height badge-secondary">
                        <i class="fas fa-edit"></i>
                      </a>
                    </td>
                  </tr>
                @endforeach

              </tbody>
            </table>
            @include('integrations.telegram.modal.edit')
          </div>
          <div class="px-5 pb-5">
            <span>Untuk mendapatkan chatid group telegram silakan
              ikuti
              tutorial berikut <a href="#" target="blank_" class="text-primary">#</a></span><br>
            <span class="text-danger">Pastikan bot kami bisa mengirim pesan ke grup telegram PSB & GGN yang sudah anda
              buat</span>
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
    let table = $('#myTable').DataTable({
      // scrollX: true,
      paginate: false,
      searching: false,
      info: false,
      order: [
        [0, 'desc']
      ]
    });

    // $(document).ready(function() {
    // $.ajax({
    // url: '{{ url()->current() }}',
    // type: "GET",
    // cache: false,
    // success: function(response) {
    // $('#wablas_id').val(response.data[0].id);
    // $('#sender').val(response.data[0].sender),
    // $('#token').val(response.data[0].token);
    // }
    // });
    // });

    $('#myTable').on('click', '#show', function() {

      let telegram = $(this).data('id');

      $.ajax({
        url: baseUrl + `/${telegram}`,
        type: "GET",
        cache: false,
        success: function(response) {
          $('#telegram_id').val(response.data.id);
          if (response.data.tipe === 1) {
            $('#tipe').val('PSB / PASANG BARU');
          } else {
            $('#tipe').val('GGN / GANGGUAN');
          }
          $('#tipe_id').val(response.data.tipe);
          $('#chatid').val(response.data.chatid);
          //open modal
          $('#edit').modal('show');
        }
      });
    });

    $('#update').click(function(e) {
      e.preventDefault();
      var error_ele = document.getElementsByClassName('form-text text-danger');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }
      let telegram = $('#telegram_id').val();

      // collect data by id
      var data = {
        'tipe_id': $('#tipe_id').val(),
        'chatid': $('#chatid').val(),
      }

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        url: baseUrl + `/${telegram}`,
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
              $('#edit').modal('hide')
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
  </script>
@endsection
