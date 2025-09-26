@extends('backend.layouts.app_new')
@section('main')
@section('title', 'OLT')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6">
            <h4 class="main-title">OLT</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span>
                            <i class="ti ti-router f-s-16"></i> OLT
                        </span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">List</a>
                </li>
            </ul>
        </div>

        <!-- Button -->
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
          
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus me-1" width="20"
                    height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 5v14" />
                    <path d="M5 12h14" />
                </svg>
                Tambah
            </button>
        </div>
    </div><br />
    <!-- Page content -->
      @include('backend.olt.modal.create')
            @include('backend.olt.modal.edit')
    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left!important">#</th>
                        <th>Nama</th>
                        <th style="text-align:left!important">IP Address</th>
                        <th>Tipe</th>
                        <th style="text-align:left!important">Username</th>
                        <th style="text-align:left!important">Password</th>
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
@if (Session::has('errors'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Failed',
            text: '{{ Session::get('errors') }}',
            showConfirmButton: true,
        });
    </script>
@endif
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
                data: 'name',
                name: 'name',
                render: function(data, type, row) {
                    // Pastikan Material Icons telah diimpor dalam proyek Anda
                    return '<i class="ti ti-brand-openvpn text-success"></i> ' +
                        data;
                }
            },
            {
                data: 'host',
                name: 'host',
                render: function(data, type, row) {
                    // Pastikan Material Icons telah diimpor dalam proyek Anda
                    return '<span class="ti ti-world-www text-primary"></span> ' +
                        data;
                }
            },
            {
                data: 'type',
                name: 'type',
                render: function(data, type, row) {
                    var badgeClass = '';
                    if (data === 'HSGQ EPON') {
                        badgeClass = 'bg-primary'; // ganti sesuai kelas yang diinginkan
                    } else if (data === 'HIOSO 2 PON') {
                        badgeClass = 'bg-success'; // ganti sesuai kelas yang diinginkan
                    } else if (data === 'HIOSO 4 PON') {
                        badgeClass = 'bg-info'; // ganti sesuai kelas yang diinginkan
                    }
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                }
            },

            {
                data: 'username',
                name: 'username',
            },
            {
                data: 'null',
                name: 'null',
                render: function() {
                    return '*******';
                }
            },
            {
                data: 'action',
                name: 'action'
            }
        ]
    });

    $('#myTable').on('click', '#login-hsgq', function() {
        let timerInterval;
        Swal.fire({
            title: "Connecting",
            icon: "info",
            html: "Menghubungkan ke olt, harap tunggu...",
            timer: 10000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                    timer.textContent = `${Swal.getTimerLeft()}`;
                }, 10000);
            },
            willClose: () => {
                clearInterval(timerInterval);
            }
        }).then((result) => {
            /* Read more about handling dismissals below */
            if (result.dismiss === Swal.DismissReason.timer) {
                console.log("Timeout");
            }
        });
        let id = $(this).data('id');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: `/olt/hsgq/auth`,
            type: "POST",
            data: {
                id: id,
            },
            cache: false,
            success: function(response) {
                window.location = 'olt/hsgq/dashboard';
            }
        });


    });

    $('#myTable').on('click', '#login-hioso', function() {
        let timerInterval;
        Swal.fire({
            title: "Connecting",
            icon: "info",
            html: "Menghubungkan ke olt, harap tunggu...",
            timer: 10000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                    timer.textContent = `${Swal.getTimerLeft()}`;
                }, 10000);
            },
            willClose: () => {
                clearInterval(timerInterval);
            }
        }).then((result) => {
            /* Read more about handling dismissals below */
            if (result.dismiss === Swal.DismissReason.timer) {
                console.log("Timeout");
            }
        });
        let id = $(this).data('id');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: `/olt/hioso/auth`,
            type: "POST",
            data: {
                id: id,
            },
            cache: false,
            success: function(response) {
                window.location = 'hioso';
            }
        });


    });

    // action create
    $('#store').click(function(e) {
        e.preventDefault();

        // Hapus error message yang lama
        $('.form-text.text-danger').remove();

        // Kumpulkan data dari form
        var data = {
            'name': $('#name').val(),
            'type': $('#type').val(),
            'host': $('#host').val(),
            'username': $('#username').val(),
            'password': $('#password').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/olt`,
            type: "POST",
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
                    setTimeout(function() {
                        table.ajax.reload();
                        $('input, textarea').val('');
                        $('#kode_area_id').val('').trigger('change');
                        $('#create').modal('hide');
                    }, 1500);
                } else {
                    // Tampilkan error validasi dari server
                    $.each(response.error, function(key, value) {
                        var el = $('[name="' + key + '"]');
                        el.after('<div class="form-text text-danger">' + value[0] +
                            '</div>');
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Jika terdapat response validasi (HTTP 422)
                if (jqXHR.status === 422 && jqXHR.responseJSON) {
                    $.each(jqXHR.responseJSON.errors, function(key, messages) {
                        var el = $('[name="' + key + '"]');
                        el.addClass('is-invalid');
                        el.after('<div class="form-text text-danger">' + messages[0] +
                            '</div>');
                    });
                } else {
                    // Tampilkan pesan error umum menggunakan SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan. Silakan coba lagi nanti.',
                    });
                }
            }
        });
    });


    $('#myTable').on('click', '#edit', function() {

        let id = $(this).data('id');

        //fetch detail post with ajax
        $.ajax({
            url: `/olt/${id}`,
            type: "GET",
            cache: false,
            success: function(response) {
                //fill data to form
                $('#olt_id').val(response.data.id),
                    $('#type_edit').val(response.data.type),
                    $('#name_edit').val(response.data.name),
                    $('#ip_edit').val(response.data.host),
                    $('#username_edit').val(response.data.username),
                    $('#password_edit').val(response.data.password),
                    //open modal
                    $('#edit').modal('show');
            }
        });
    });

    $('#myTable').on('click', '#delete', function() {

        let id = $(this).data('id');

        Swal.fire({
            title: "Apakah anda yakin?",
            icon: 'warning',
            text: "Data yang sudah dihapus tidak dapat dikembalikan",
            showCancelButton: !0,
            reverseButtons: !0,
            confirmButtonText: "Yes, delete!",
            cancelButtonText: "Cancel",
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
                    url: `/olt/${id}`,
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

    $('#update').click(function(e) {
        e.preventDefault();

        // Hapus pesan error yang lama
        $('.form-text.text-danger').remove();

        // Ambil id olt dari input tersembunyi
        var id = $('#olt_id').val();

        // Kumpulkan data dari form edit
        var data = {
            'name': $('#name_edit').val(),
            'host': $('#ip_edit').val(),
            'username': $('#username_edit').val(),
            'password': $('#password_edit').val(),
            'type': $('#type_edit').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/olt/${id}`,
            type: "PUT",
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
                    setTimeout(function() {
                        table.ajax.reload();
                        $('#edit').modal('hide');
                    }, 1500);
                } else {
                    // Tampilkan pesan error jika response.success false (jika dikirim server)
                    $.each(response.errors, function(key, messages) {
                        var el = $('[name="' + key + '"]');
                        el.after('<div class="form-text text-danger">' + messages[0] +
                            '</div>');
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Debug: periksa response error di console
                console.log('Error Response:', jqXHR.responseText);

                // Jika error validasi dengan status 422
                if (jqXHR.status === 422 && jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                    $.each(jqXHR.responseJSON.errors, function(key, messages) {
                        var el = $('[name="' + key + '"]');
                        el.after('<div class="form-text text-danger">' + messages[0] +
                            '</div>');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan. Silakan coba lagi nanti.',
                    });
                }
            }
        });
    });

    $(document).on('input', '.form-control', function() {
        if ($(this).val().trim() !== '') {
            $(this).removeClass('is-invalid');
            // Jika ingin menghapus pesan error (jika ada) yang muncul setelah input
            $(this).next('.form-text.text-danger').remove();
        }
    });
</script>
@endpush
