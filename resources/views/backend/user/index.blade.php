@extends('backend.layouts.app')
@section('main')
@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">
@endsection
@section('title', 'Users')
<!-- Content -->
<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <!-- Avatar -->
            <div class="avatar avatar-xl rounded text-primary">
                <span class="fs-2 material-symbols-outlined">
                    group
                </span>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    {{-- <li class="breadcrumb-item"><a class="text-body-secondary" href="#">User</a></li> --}}
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">Users</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
            <!-- Action -->
            @include('backend.user.modal.create')
            @include('backend.user.modal.edit')
            <button class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#create"> <span
                    class="material-symbols-outlined me-1">add</span> Create </button>
        </div>
    </div>

    <!-- Page content -->
    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <!-- <th>#</th> -->
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
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
        columns: [
            // {
            //     data: null,
            //     'sortable': false,
            //     className: "text-center",
            //     render: function(data, type, row, meta) {
            //         return meta.row + meta.settings._iDisplayStart + 1;
            //     }
            // },
            {
                data: 'id',
                name: 'id'
            },
            {
                data: 'username',
                name: 'username'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'role',
                name: 'role',
            },
            {
                data: 'status',
                name: 'status',
                render: function(data) {
                    if (data === 1) {
                        return '<span class="badge bg-success">Aktif</span>'
                    } else {
                        return '<span class="badge bg-danger">Nonaktif</span>'
                    }

                },
            },
            {
                data: 'action',
                name: 'action'
            }
        ]
    });

    $('#store').click(function(e) {
        e.preventDefault();

        // Hapus pesan error jika ada
        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        // Kumpulkan data dari form
        var data = {
            'name': $('#name').val(),
            'role': $('#role').val(),
            'username': $('#username').val(),
            'password': $('#password').val(),
            'email': $('#email').val(),
            'whatsapp': $('#whatsapp').val(),
            'reseller_id': $('#reseller').val(),
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
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        // Proses AJAX
        $.ajax({
            url: `/user`,
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
                        el.after($('<div class="form-text text-danger">' + value[0] +
                            '</div>'));
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

        let user_id = $(this).data('id');

        //fetch detail post with ajax
        $.ajax({
            url: `/user/${user_id}`,
            type: "GET",
            cache: false,
            success: function(response) {

                //fill data to form
                $('#user_id').val(response.data.id),
                    $('#name_edit').val(response.data.name),
                    $('#email_edit').val(response.data.email),
                    $('#whatsapp_edit').val(response.data.whatsapp),
                    $('#username_edit').val(response.data.username),
                    $('#role_edit').val(response.data.role);

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

        let user_id = $('#user_id').val();

        // Kumpulkan data dari form
        var data = {
            'name': $('#name_edit').val(),
            'email': $('#email_edit').val(),
            'username': $('#username_edit').val(),
            'whatsapp': $('#whatsapp_edit').val(),
            'password': $('#password_edit').val(),
            'role': $('#role_edit').val(),
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
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        $.ajax({
            url: `/user/${user_id}`,
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
                        location.reload();
                        // $('input, textarea').val('');
                        // $('#edit').modal('hide');
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
                btn.prop('disabled', false).html(originalText);
                $("#message").html("Some Error Occurred!");
            }
        });
    });


    $('#myTable').on('click', '#disable', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: "Nonaktifkan User",
            icon: 'warning',
            text: 'Apakah Anda yakin ingin menonaktifkan user ini?',
            showCancelButton: !0,
            confirmButtonText: "Ya, Nonaktifkan",
            cancelButtonText: "Batal",
            reverseButtons: !0,
        }).then(function(result) {
            if (result.isConfirmed) {
                var data = {
                    'id': id
                };

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $(
                                'meta[name="csrf-token"]'
                            )
                            .attr('content')
                    }
                });

                // ajax proses
                $.ajax({
                    url: `/user/disable/${id}`,
                    type: "PUT",
                    cache: false,
                    data: data,
                    dataType: "json",

                    // tampilkan pesan Success

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
                                table.ajax.reload()
                            });
                        } else {

                        }
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

    $('#myTable').on('click', '#enable', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: "Aktifkan Reseller",
            icon: 'warning',
            text: 'Apakah Anda yakin ingin mengaktifkan reseller ini?',
            showCancelButton: !0,
            confirmButtonText: "Ya, Aktifkan",
            cancelButtonText: "Batal",
            reverseButtons: !0,
        }).then(function(result) {
            if (result.isConfirmed) {
                var data = {
                    'id': id
                };

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $(
                                'meta[name="csrf-token"]'
                            )
                            .attr('content')
                    }
                });

                // ajax proses
                $.ajax({
                    url: `/user/enable/${id}`,
                    type: "PUT",
                    cache: false,
                    data: data,
                    dataType: "json",

                    // tampilkan pesan Success

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
                                table.ajax.reload()
                            });
                        } else {

                        }
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
                    url: `/user/${id}`,
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
</script>
@endpush
