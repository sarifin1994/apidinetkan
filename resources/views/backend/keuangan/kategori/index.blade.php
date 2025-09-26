@extends('backend.layouts.app')
@section('main')
@section('title', 'Kategori Keuangan')
<!-- Content -->
<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <!-- Avatar -->
            <div class="avatar avatar-xl rounded text-primary">
                <span class="fs-2 material-symbols-outlined">
                    checkbook
                    </span>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">Keuangan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kategori</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">Kategori</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
            <!-- Action -->
            @include('backend.keuangan.kategori.modal.create')
            @include('backend.keuangan.kategori.modal.edit')
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
                            <th style="text-align:left!important">#</th>
                            <th>Nama Kategori</th>
                            <th>Tipe</th>
                            {{-- <th>Status</th> --}}
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
                data: 'category',
                name: 'category'
            },
            {
                data: 'type',
                name: 'type',
                render: function(data) {
                    if (data === 'Pemasukan') {
                        return '<span class="badge bg-success-subtle text-success">Pemasukan</span>'
                    } else {
                        return '<span class="badge bg-danger-subtle text-danger">Pengeluaran</span>'
                    }
                },
            },
            // {
            //     data: 'status',
            //     name: 'status',
            //     render: function(data) {
            //         if (data === 1) {
            //             return '<span class="badge bg-success">Aktif</span>'
            //         } else {
            //             return '<span class="badge bg-danger">Nonaktif</span>'
            //         }

            //     },
            // },
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

    // Simpan data dari form
    var data = {
        'category': $('#category').val(),
        'type': $('#type').val(),
    };

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Simpan referensi tombol dan teks aslinya
    var btn = $(this);
    var originalText = btn.html();

    // Ubah tampilan tombol: nonaktifkan tombol dan tampilkan teks dengan ikon spinner di sebelah kanan
    btn.prop('disabled', true).html('Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

    // Proses AJAX
    $.ajax({
        url: `/keuangan/kategori`,
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

        let kategori = $(this).data('id');

        //fetch detail post with ajax
        $.ajax({
            url: `/keuangan/kategori/${kategori}`,
            type: "GET",
            cache: false,
            success: function(response) {

                //fill data to form
                $('#kategori_id').val(response.data.id),
                    $('#category_edit').val(response.data.category),
                    $('#type_edit').val(response.data.type),

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
    
    let kategori = $('#kategori_id').val();

    // Kumpulkan data berdasarkan id
    var data = {
        'category': $('#category_edit').val(),
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
        url: `/keuangan/kategori/${kategori}`,
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
                    $('input, textarea').val('');
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


    $('#myTable').on('click', '#disable', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: "Nonaktifkan Kategori",
            icon: 'warning',
            text: 'Apakah Anda yakin ingin menonaktifkan kategori ini?',
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
                    url: `/keuangan/kategori/disable/${id}`,
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
            title: "Aktifkan Kategori",
            icon: 'warning',
            text: 'Apakah Anda yakin ingin mengaktifkan kategori ini?',
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
                    url: `/keuangan/kategori/enable/${id}`,
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
                    url: `/keuangan/kategori/${id}`,
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
