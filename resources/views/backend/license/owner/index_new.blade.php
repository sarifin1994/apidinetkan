@extends('backend.layouts.app_new')
@section('main')
@section('title', 'License')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6">
            <h4 class="main-title">Radiusqu License</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="active" href="#">
                        <span>
                            <i class="ti ti-file-license f-s-16"></i> Licenses
                        </span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Button -->
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create">
                <i class="ti ti-plus me-1"></i>
                Create
            </button>
        </div>
    </div><br />

    @include('backend.license.owner.modal.create')
    @include('backend.license.owner.modal.edit')

    <!-- Page content -->
    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left!important">ID</th>
                        <th>Nama Lisensi</th>
                        <th>Harga / Bulan (Rp)</th>
                        <th style="text-align:left!important">Limit Hotspot</th>
                        <th style="text-align:left!important">Limit PPPoE</th>
                        <th style="text-align:left!important">Custome</th>
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
                data: 'id',
                name: 'id'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'price',
                name: 'price',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            {
                data: 'limit_hs',
                name: 'limit_hs'
            },
            {
                data: 'limit_pppoe',
                name: 'limit_pppoe'
            },
            {
                data: 'custome',
                name: 'custome'
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

    // action create
    $('#store').click(function(e) {
        e.preventDefault();

        // Hapus pesan error yang ada
        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        // Kumpulkan data dari form
        var data = {
            'name':$('#nama_lisensi').val(),
            'deskripsi':$('#deskripsi').val(),
            'spek':$('#spek').val(),
            'price':$('#price').val(),
            'limit_hs':$('#limit_hs').val(),
            'limit_pppoe':$('#limit_pppoe').val(),
            'midtrans':$('#midtrans').val(),
            'olt':$('#olt').val(),
            'custome':$('#custome').val(),
        };

        // Setup CSRF token untuk AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Simpan konten asli tombol untuk dikembalikan jika diperlukan
        var originalBtnContent = $('#store').html();
        // Nonaktifkan tombol dan tampilkan spinner pada tombol
        $('#store').prop('disabled', true)
            .html(
                'Processing&nbsp;<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
            );

        // Proses AJAX
        $.ajax({
            url: `/license`,
            type: "POST",
            cache: false,
            data: data,
            dataType: "json",
            success: function(data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        table.ajax.reload();
                        $('#name').val('');
                        $('#price').val('');
                        $('#fee_mitra').val('');
                        $('#rate').val('');
                        $('#create').modal('hide');
                        // Kembalikan tombol ke kondisi semula jika diperlukan
                        $('#store').prop('disabled', false).html(originalBtnContent);
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        // Tambahkan class is-invalid untuk membuat border merah (jika menggunakan Bootstrap)
                        el.addClass('is-invalid');
                        el.after($('<div class="form-text text-danger">' + value[0] +
                            '</div>'));
                    });

                    // Kembalikan tombol ke kondisi semula
                    $('#store').prop('disabled', false).html(originalBtnContent);
                }
            },
            error: function(err) {
                $("#message").html("Some Error Occurred!");
                $('#store').prop('disabled', false).html(originalBtnContent);
            }
        });
    });


    $('#myTable').on('click', '#delete', function() {

        let profile_id = $(this).data('id');

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
                    url: `/license/${profile_id}`,
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

    $('#myTable').on('click', '#edit', function() {

        let license = $(this).data('id');

        //fetch detail post with ajax
        $.ajax({
            url: `/license/${license}`,
            type: "GET",
            cache: false,
            success: function(response) {
                $('#license_id').val(response.data.id)
                $('#nama_lisensi_edit').val(response.data.name)
                $('#deskripsi_edit').val(response.data.deskripsi)
                $('#spek_edit').val(response.data.spek)
                $('#price_edit').val(response.data.price)
                $('#limit_hs_edit').val(response.data.limit_hs)
                $('#limit_pppoe_edit').val(response.data.limit_pppoe)
                $('#midtrans_edit').val(response.data.midtrans)
                $('#olt_edit').val(response.data.olt)
                $('#custome_edit').val(response.data.custome)
                //open modal
                $('#edit').modal('show');
            }
        });
    });

    $('#update').click(function(e) {
        e.preventDefault();

        // Hapus pesan error sebelumnya
        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        let license = $('#license_id').val();

        // Kumpulkan data dari form
        var data = {
            'name':$('#nama_lisensi_edit').val(),
            'deskripsi':$('#deskripsi_edit').val(),
            'spek':$('#spek_edit').val(),
            'price':$('#price_edit').val(),
            'limit_hs':$('#limit_hs_edit').val(),
            'limit_pppoe':$('#limit_pppoe_edit').val(),
            'midtrans':$('#midtrans_edit').val(),
            'olt':$('#olt_edit').val(),
            'custome':$('#custome_edit').val(),
        };

        // Setup CSRF token untuk AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Simpan konten asli tombol untuk dikembalikan nanti
        var originalBtnContent = $('#update').html();
        // Nonaktifkan tombol dan tampilkan spinner
        $('#update').prop('disabled', true)
            .html(
                'Processing&nbsp;<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
            );

        // Proses AJAX
        $.ajax({
            url: `/license/${license}`,
            type: "PUT",
            cache: false,
            data: data,
            dataType: "json",
            success: function(data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        table.ajax.reload();
                        $('#edit').modal('hide');
                        // Kembalikan tombol ke kondisi semula
                        $('#update').prop('disabled', false).html(originalBtnContent);
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        // Tambahkan class is-invalid untuk membuat border merah (jika menggunakan Bootstrap)
                        el.addClass('is-invalid');
                        el.after($('<div class="form-text text-danger">' + value[0] +
                            '</div>'));
                    });

                    // Kembalikan tombol ke kondisi semula
                    $('#update').prop('disabled', false).html(originalBtnContent);
                }
            },
            error: function(err) {
                $("#message").html("Some Error Occurred!");
                // Kembalikan tombol ke kondisi semula
                $('#update').prop('disabled', false).html(originalBtnContent);
            }
        });
    });


    $('#myTable').on('click', '#enable', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: "Aktifkan License",
            icon: 'warning',
            text: 'Apakah Anda yakin ingin mengaktifkan license ini?',
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
                    url: `/license/enable/${id}`,
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
            title: "Nonaktifkan License",
            icon: 'warning',
            text: 'Apakah Anda yakin ingin menonaktifkan license ini?',
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
                    url: `/license/disable/${id}`,
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
