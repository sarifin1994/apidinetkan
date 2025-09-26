@extends('backend.layouts.app_new')
@section('main')
@section('title', 'PPPoE Profile')
    <!-- Content -->
    <div class="container-fluid">
      <!-- Page header -->
      <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6">
            <h4 class="main-title">PPPoE Profile</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-router f-s-16"></i> PPPoE</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Profile</a>
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

     @include('backend.pppoe.profile.modal.create')
          @include('backend.pppoe.profile.modal.edit')

      <!-- Page content -->
      <div class="row">
            <div class="card-body table-responsive">
                <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                    <thead>
                        <tr>
                            <th style="text-align:left!important">No</th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th style="text-align:left!important">Fee Mitra</th>
                            <th style="text-align:left!important">Rate Limit</th>
                            <th>Group</th>
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
                data: 'name',
                name: 'name'
            },
            {
                data: 'price',
                name: 'price',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            {
                data: 'fee_mitra',
                name: 'fee_mitra',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            {
                data: 'rateLimit',
                name: 'rateLimit'
            },
            {
                data: 'groupProfile',
                name: 'groupProfile'
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
        'name': $('#name').val(),
        'price': $('#price').val(),
        'fee_mitra': $('#fee_mitra').val(),
        'rate': $('#rate').val(),
        'groupProfile': $('#groupProfile').val(),
        'status': $('#status').val(),
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
        .html('Processing&nbsp;<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

    // Proses AJAX
    $.ajax({
        url: `/pppoe_profile`,
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
                    el.after($('<div class="form-text text-danger">' + value[0] + '</div>'));
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
                    url: `/pppoe/profile/${profile_id}`,
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

        let profile_id = $(this).data('id');

        //fetch detail post with ajax
        $.ajax({
            url: `/pppoe/profile/${profile_id}`,
            type: "GET",
            cache: false,
            success: function(response) {
                console.log(response);
                $('#profile_id').val(response.data.id);
                $('#name_edit').val(response.data.name);
                var rp = response.data.price;
                rp = formatRupiah(rp, 2, ',', '.');
                $('#price_edit').val(rp);
                var fee = response.data.fee_mitra;
                fee = formatRupiah(fee, 2, ',', '.');
                $('#fee_mitra_edit').val(fee);
                if (response.data.rateLimit === 'Unlimited') {
                    $('#rate_edit').val(null);
                } else {
                    var rate = response.data.rateLimit;
                    $('#rate_edit').val(rate);
                }
                $('#groupProfile_edit').val(response.data.groupProfile);

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

    let profile_id = $('#profile_id').val();

    // Kumpulkan data dari form
    var data = {
        'name': $('#name_edit').val(),
        'price': $('#price_edit').val(),
        'fee_mitra': $('#fee_mitra_edit').val(),
        'rate': $('#rate_edit').val(),
        'groupProfile': $('#groupProfile_edit').val(),
        'status': $('#status_edit').val(),
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
        .html('Processing&nbsp;<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

    // Proses AJAX
    $.ajax({
        url: `/pppoe/profile/${profile_id}`,
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
                    el.after($('<div class="form-text text-danger">' + value[0] + '</div>'));
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
            title: "Aktifkan Profile",
            icon: 'warning',
            text: 'Apakah Anda yakin ingin mengaktifkan profile ini?',
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
                    url: `/pppoe/profile/enable/${id}`,
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
            title: "Nonaktifkan Profile",
            icon: 'warning',
            text: 'Apakah Anda yakin ingin menonaktifkan profile ini?',
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
                    url: `/pppoe/profile/disable/${id}`,
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
