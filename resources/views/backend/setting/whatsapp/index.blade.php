@extends('backend.layouts.app')
@section('main')
@section('title', 'WA Server')
    <!-- Content -->
    <div class="container-lg">
      <!-- Page header -->
      <div class="row align-items-center mb-7">
        <div class="col-auto">
          <!-- Avatar -->
          <div class="avatar avatar-xl rounded text-primary">
            <span class="fs-2 material-symbols-outlined">
                map
                </span>
          </div>
        </div>
        <div class="col">
          <!-- Breadcrumb -->
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
              <li class="breadcrumb-item"><a class="text-body-secondary" href="#">Setting</a></li>
              <li class="breadcrumb-item active" aria-current="page">WA Server</li>
            </ol>
          </nav>

          <!-- Heading -->
          <h1 class="fs-4 mb-0">Whatsapp Server</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
          <!-- Action -->
          @include('backend.setting.whatsapp.modal.create')
          @include('backend.setting.whatsapp.modal.edit')
          <button class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#create"> <span class="material-symbols-outlined me-1">add</span> Tambah </button>
        </div>
      </div>

      <!-- Page content -->
      <div class="row">
            <div class="card-body table-responsive">
                <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                    <thead>
                        <tr>
                            <th style="text-align:left!important">No</th>
                            <th>WA URL</th>
                            <th>API Key</th>
                            <th>Sender</th>
                            <th>Server</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
      </div>
      
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: '{{ url()->current() }}',
        columns: [{
                data: null,
                'sortable': false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'wa_url',
                name: 'wa_url'
            },
            {
                data: 'wa_api',
                name: 'wa_api'
            },
            {
                data: 'wa_sender',
                name: 'wa_sender'
            },
            {
                data: 'wa_server',
                name: 'wa_server'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'action',
                name: 'action'
            }
        ]
    });

    $('#myTable').on('click', '#delete', function() {

        let id = $(this).data('id');

        Swal.fire({
            title: "Apakah anda yakin?",
            icon: 'warning',
            text: "Data yang sudah dihapus tidak dapat dikembalikan",
            showCancelButton: !0,
            reverseButtons: !0,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
            // cancelButtonColor: "#d33",
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $(
                                'meta[name="csrf-token"]'
                            )
                            .attr('content')
                    }
                });

                $.ajax({
                    url: `/setting/wa/${id}`,
                    type: "POST",
                    cache: false,
                    data: {
                        _method: "DELETE"
                    },
                    dataType: "json",

                    // tampilkan pesan Success
                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `${data.message}`,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(
                            function() {
                                table.ajax.reload()
                            });
                    },

                    error: function(err) {
                        $("#message").html(
                            "Some Error Occurred!"
                        )
                    }

                });
            }
        });
    });

    // action create
    $('#store').click(function(e) {
    e.preventDefault();
    
    // Hapus pesan error jika ada
    var error_ele = document.getElementsByClassName('form-text text-danger');
    if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
            error_ele[i].remove();
        }
    }
    
    // Ambil data dari form
    var data = {
        'wa_url': $('#wa_url').val(),
        'wa_api': $('#wa_api').val(),
        'wa_sender': $('#wa_sender').val(),
        'wa_server': $('#wa_server').val(),
    };

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Simpan referensi tombol dan teks aslinya
    var btn = $(this);
    var originalText = btn.html();

    // Ubah tampilan tombol: nonaktifkan dan tampilkan teks dengan spinner di sebelah kanan
    btn.prop('disabled', true).html('Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

    // Proses AJAX
    $.ajax({
        url: `/setting/wa`,
        type: "POST",
        cache: false,
        data: data,
        dataType: "json",
        success: function(data) {
            // Kembalikan tombol ke kondisi semula
            btn.prop('disabled', false).html(originalText);
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: `${data.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(function() {
                    table.ajax.reload();
                    $('input, textarea').val('');
                    $('#create').modal('hide');
                }, 1500);
            } else {
                $.each(data.error, function(key, value) {
                    var el = $(document).find('[name="' + key + '"]');
                    el.after($('<div class="form-text text-danger">' + value[0] + '</div>'));
                });
            }
        },
        error: function(err) {
            btn.prop('disabled', false).html(originalText);
            $("#message").html("Some Error Occurred!");
        }
    });
});


    $('#myTable').on('click', '#edit', function() {

        let wa_id = $(this).data('id');

        //fetch detail post with ajax
        $.ajax({
            url: `/setting/wa/${wa_id}`,
            type: "GET",
            cache: false,
            success: function(response) {
                //fill data to form
                $('#wa_id').val(response.data.id),
                $('#wa_url_edit').val(response.data.wa_url),
                $('#wa_api_edit').val(response.data.wa_api),
                $('#wa_sender_edit').val(response.data.wa_sender),
                $('#wa_server_edit').val(response.data.wa_server),
                 
                    //open modal
                    $('#edit').modal('show');
            }
        });
    });

    $('#update').click(function(e) {
    e.preventDefault();
    
    // Hapus pesan error jika ada
    var error_ele = document.getElementsByClassName('form-text text-danger');
    if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
            error_ele[i].remove();
        }
    }
    
    let wa_id = $('#wa_id').val();

    // Kumpulkan data dari form
    var data = {
        // 'wa_url': $('#wa_url').val(),
        'wa_api': $('#wa_api_edit').val(),
        'wa_sender': $('#wa_sender_edit').val(),
        'wa_server': $('#wa_server_edit').val(),
    };

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Simpan referensi tombol dan teks aslinya
    var btn = $(this);
    var originalText = btn.html();
    // Ubah tampilan tombol: nonaktifkan dan tampilkan teks dengan spinner di sebelah kanan
    btn.prop('disabled', true).html('Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

    $.ajax({
        url: `/setting/wa/${wa_id}`,
        type: "PUT",
        cache: false,
        data: data,
        dataType: "json",
        success: function(data) {
            // Kembalikan tombol ke kondisi semula
            btn.prop('disabled', false).html(originalText);
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: `${data.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(function() {
                    table.ajax.reload();
                    $('#edit').modal('hide');
                }, 1500);
            } else {
                $.each(data.error, function(key, value) {
                    var el = $(document).find('[name="' + key + '"]');
                    el.after($('<div class="form-text text-danger">' + value[0] + '</div>'));
                });
            }
        },
        error: function(err) {
            btn.prop('disabled', false).html(originalText);
            $("#message").html("Some Error Occurred!");
        }
    });
});

</script>
@endpush
