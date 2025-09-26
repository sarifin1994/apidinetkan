@extends('backend.layouts.app_new')
@section(section: 'main')
@section('title', 'Reseller')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->

    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">Reseller</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-heart-handshake f-s-16"></i> Patnership</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Reseller</a>
                </li>
            </ul>
        </div>

        <!-- Buttons -->
        <div class="d-flex flex-wrap justify-content-md-end gap-2">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#create">
                <i class="ti ti-plus"></i> Tambah
            </button>
        </div>
    </div>
    <br />
    @include('backend.partnership.reseller.modal.create')
    @include('backend.partnership.reseller.modal.edit')
    <!-- Page content -->
    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left!important">No</th>
                        <th>ID Reseller</th>
                        <th>Nama Reseller</th>
                        <th>Nomor WA</th>
                        <th>Stok Voucher</th>
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
                className: 'text-center',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'id_reseller',
                name: 'id_reseller',
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'nomor_wa',
                name: 'nomor_wa',
            },
            {
                data: 'jml_plgn',
                name: 'jml_plgn',
                className: 'text-center',
                sortable: false,
                searchable: false,
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

    var multipleCancelButton = new Choices(
        '#profile', {
            allowHTML: true,
            removeItemButton: true,
        }
    );

    $('#create').on('shown.bs.modal', function() {
        var number = 'R' + Math.random().toString().substring(2, 7);
        $('#id_reseller').val(number);
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
                    url: `/partnership/reseller/${id}`,
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
    // Aksi Create (Store)
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
            'nama_reseller': $('#nama_reseller').val(),
            'nomor_wa': $('#nomor_wa').val(),
            'profile': $('#profile').val(),
            'id_reseller': $('#id_reseller').val(),
            'password': $('#password').val(),
            'login': $('#login').val(),
            'cetak': $('#cetak').val(),
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
            'Memproses... <i class="ti ti-loader-2 spinner"></i>');

        // Proses AJAX
        $.ajax({
            url: `/partnership/reseller`,
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
                        $('#kode_area_id').val('').trigger('change');
                        $('#create').modal('hide');
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        // Tambahkan class is-invalid untuk membuat border merah (jika menggunakan Bootstrap)
                        el.addClass('is-invalid');
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

    // Aksi Update
    $('#update').click(function(e) {
        e.preventDefault();

        // Hapus pesan error jika ada
        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }
        let reseller_id = $('#reseller_id').val();

        // Kumpulkan data dari form
        var data = {
            'nama_reseller': $('#nama_reseller_edit').val(),
            'nomor_wa': $('#nomor_wa_edit').val(),
            'profile': $('#profile_edit').val(),
            'id_reseller': $('#id_reseller_edit').val(),
            'password': $('#pass_reseller_edit').val(),
            'login': $('#login_edit').val(),
            'cetak': $('#cetak_edit').val(),
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
            url: `/partnership/reseller/${reseller_id}`,
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
                        // Tambahkan class is-invalid untuk membuat border merah (jika menggunakan Bootstrap)
                        el.addClass('is-invalid');
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


    var multipleCancelButton = new Choices(
        '#profile_edit', {
            allowHTML: true,
            removeItemButton: true,
        }
    );
    $('#myTable').on('click', '#edit', function() {

        let reseller_id = $(this).data('id');

        //fetch detail post with ajax
        $.ajax({
            url: `/partnership/reseller/${reseller_id}`,
            type: "GET",
            cache: false,
            success: function(response) {
                // console.log(response.data.profile)

                //fill data to form
                $('#reseller_id').val(response.data.id),
                    $('#nama_reseller_edit').val(response.data.name),
                    $('#nomor_wa_edit').val(response.data.nomor_wa),
                    $('#id_reseller_edit').val(response.data.id_reseller),
                    $('#login_edit').val(response.data.login),
                    $('#cetak_edit').val(response.data.cetak);
                // $('#profile_edit').val(response.data.profile);
                if (typeof response.data.profile === 'string') {
                    try {
                        response.data.profile = JSON.parse(response.data.profile);
                    } catch (e) {
                        console.error("Gagal parsing string ke array:", e);
                    }
                };
                if (response.data.profile !== null && response.data.profile !== '' && response.data
                    .profile !== undefined) {
                    multipleCancelButton.setChoiceByValue(response.data.profile);
                }


                //open modal
                $('#edit').modal('show');
            }
        });
    });



    $('#myTable').on('click', '#enable', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: "Aktifkan Mitra",
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
                    url: `/partnership/reseller/enable/${id}`,
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
                            setTimeout(
                                function() {
                                    table.ajax.reload();
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

    $('#myTable').on('click', '#disable', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: "Nonaktifkan Mitra",
            icon: 'warning',
            text: 'Apakah Anda yakin ingin menonaktifkan reseller ini?',
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
                    url: `/partnership/reseller/disable/${id}`,
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
                            setTimeout(
                                function() {
                                    table.ajax.reload();
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
</script>
@endpush
