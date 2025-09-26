@extends('backend.layouts.app')
@section('main')
@section('title', 'Hotspot Profile')
<!-- Content -->
<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <!-- Avatar -->
            <div class="avatar avatar-xl rounded text-primary">
                <span class="fs-2 material-symbols-outlined">
                    folder_supervised
                </span>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">Hotspot</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">Hotspot Profile</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
            <!-- Action -->
            @include('backend.hotspot.profile.modal.create')
            @include('backend.hotspot.profile.modal.edit')
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
                            <th style="text-align:left!important">No</th>
                            <th>Nama</th>
                            <th>Harga Jual</th>
                            <th style="text-align:left!important">Harga Reseller</th>
                            <th style="text-align:left!important">Rate Limit</th>
                            <th>Quota</th>
                            <th>Uptime</th>
                            <th>Validity</th>
                            <th>Shared</th>
                            <th>MAC Lock</th>
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
                name: 'name',
            },
            {
                data: 'price',
                name: 'price',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            {
                data: 'reseller_price',
                name: 'reseller_price',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            {
                data: 'rateLimit',
                name: 'rateLimit'
            },
            {
                data: 'quota',
                name: 'quota',
                render: function bytesToSize(data) {
                    var sizes = ['Bytes', 'KB', 'MB', 'GB',
                        'TB'
                    ];
                    if (data == 'Unlimited') return 'Unlimited';
                    var i = parseInt(Math.floor(Math.log(
                        data) / Math.log(1024)));
                    if (i == 0) return data + ' ' + sizes[i];
                    return (data / Math.pow(1024, i)).toFixed(
                        1) + ' ' + sizes[i];
                }
            },
            {
                data: 'uptime',
                name: 'uptime',
                render: function convertSecondsToReadableString(
                    seconds) {
                    if (seconds === 'Unlimited') {
                        return 'Unlimited';
                    }
                    seconds = seconds || 0;
                    seconds = Number(seconds);
                    seconds = Math.abs(seconds);

                    const d = Math.floor(seconds / (3600 * 24));
                    const h = Math.floor(seconds % (3600 * 24) /
                        3600);
                    const m = Math.floor(seconds % 3600 / 60);
                    const s = Math.floor(seconds % 60);
                    const parts = [];

                    if (d > 0) {
                        parts.push(d + ' HARI');
                    }

                    if (h > 0) {
                        parts.push(h + ' JAM');
                    }

                    if (m > 0) {
                        parts.push(m + ' BULAN');
                    }

                    // if (s > 0) {
                    //     parts.push(s + ' second' + (s > 1 ? 's' :
                    //         ''));
                    // }

                    return parts.join(' ');
                }
            },
            {
                data: 'validity',
                name: 'validity',
                render: function convertSecondsToReadableString(
                    seconds) {
                    if (seconds === 'Unlimited') {
                        return 'Unlimited';
                    }
                    seconds = seconds || 0;
                    seconds = Number(seconds);
                    seconds = Math.abs(seconds);

                    const d = Math.floor(seconds / (3600 * 24));
                    const h = Math.floor(seconds % (3600 * 24) /
                        3600);
                    const m = Math.floor(seconds % 3600 / 60);
                    const s = Math.floor(seconds % 60);
                    const parts = [];

                    if (d > 0) {
                        parts.push(d + ' HARI');
                    }

                    if (h > 0) {
                        parts.push(h + ' JAM');
                    }

                    if (m > 0) {
                        parts.push(m + ' BULAN');
                    }

                    // if (s > 0) {
                    //     parts.push(s + ' second' + (s > 1 ? 's' :
                    //         ''));
                    // }

                    return parts.join(' ');
                }
            },
            {
                data: 'shared',
                name: 'shared'
            },
            {
                data: 'mac',
                name: 'mac',
                render: function(data) {
                    if (data === 0) {
                        return 'Disable'
                    }
                    return 'Enable'
                }
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

    $('#store').click(function(e) {
        e.preventDefault();

        // Hapus pesan error sebelumnya
        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        // Kumpulkan data dari form
        var data = {
            'name': $('#name').val(),
            'shared': $('#shared').val(),
            'price': $('#price').val(),
            'reseller_price': $('#reseller_price').val(),
            'rate': $('#rate').val(),
            'groupProfile': $('#groupProfile').val(),
            'uptime': $('#uptime').val(),
            'validity': $('#validity').val(),
            'quota': $('#quota').val(),
            'lock_mac': $('#lock_mac').val(),
            'satuan_uptime': $('#satuan_uptime').val(),
            'satuan_validity': $('#satuan_validity').val(),
            'satuan_quota': $('#satuan_quota').val()
        };

        // Simpan konten asli tombol dan tampilkan spinner
        var originalBtnContent = $('#store').html();
        $('#store').prop('disabled', true).html(
            'Processing&nbsp;<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
        );

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Proses AJAX
        $.ajax({
            url: `/hotspot/profile`,
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
                        $('#create').modal('hide');
                        // Kembalikan tombol ke kondisi semula
                        $('#store').prop('disabled', false).html(originalBtnContent);
                    }, 1500);
                } else {
                    $.each(response.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        el.after($('<div class="form-text text-danger">' + value[0] +
                            '</div>'));
                    });
                    // Kembalikan tombol ke kondisi semula jika terdapat error validasi
                    $('#store').prop('disabled', false).html(originalBtnContent);
                }
            },
            error: function(err) {
                $("#message").html("Some Error Occurred!");
                // Kembalikan tombol ke kondisi semula jika terjadi error AJAX
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
                    url: `/hotspot/profile/${profile_id}`,
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

        // //fetch detail post with ajax
        $.ajax({
            url: `/hotspot/profile/${profile_id}`,
            type: "GET",
            cache: false,
            success: function(response) {

                $('#profile_id').val(response.data.id);
                $('#name_edit').val(response.data.name);
                $('#shared_edit').val(response.data.shared);
                var rp = response.data.price;
                rp = formatRupiah(rp, 2, ',', '.');
                $('#price_edit').val(rp);
                var rp_reseller = response.data.reseller_price;
                rp_reseller = formatRupiah(rp_reseller, 2, ',', '.');
                $('#reseller_price_edit').val(rp_reseller);

                if (response.data.rateLimit === 'Unlimited') {
                    $('#rx_edit').val(null);
                    $('#tx_edit').val(null);
                    $('#priority_edit').val(8);
                } else {
                    var rate = response.data.rateLimit;
                    $('#rate_edit').val(rate);
                };
                $('#groupProfile_edit').val(response.data.groupProfile);

                if (response.data.uptime === 'Unlimited') {
                    $('#uptime_edit').val(null);
                } else {
                    if (response.data.uptime < 86400) {
                        $uptime = response.data.uptime / 3600;
                        $('#uptime_edit').val($uptime);
                        $('#satuan_uptime_edit').val('Jam');
                    } else {
                        $uptime = response.data.uptime / 86400;
                        if ($uptime < 30) {
                            $('#uptime_edit').val($uptime);
                            $('#satuan_uptime_edit').val('Hari');
                        } else {
                            $uptime_month = $uptime / 30;
                            $('#uptime_edit').val($uptime_month);
                            $('#satuan_uptime_edit').val('Bulan');
                        }
                    }
                };

                if (response.data.validity === 'Unlimited') {
                    $('#validity_edit').val(null);
                } else {
                    if (response.data.validity < 86400) {
                        $validity = response.data.validity / 3600;
                        $('#validity_edit').val($validity);
                        $('#satuan_validity_edit').val('Jam');
                    } else {
                        $validity = response.data.validity / 86400;
                        if ($validity < 30) {
                            $('#validity_edit').val($validity);
                            $('#satuan_validity_edit').val('Hari');
                        } else {
                            $validity_month = $validity / 30;
                            $('#validity_edit').val($validity_month);
                            $('#satuan_validity_edit').val('Bulan');
                        }
                    }
                };

                if (response.data.quota === 'Unlimited') {
                    $('#quota_edit').val(null);
                } else {
                    $quota_m = response.data.quota / 1048576;
                    if ($quota_m < 1024) {
                        $('#quota_edit').val($quota_m);
                        $('#satuan_quota_edit').val('MB');
                    } else {
                        $quota_g = $quota_m / 1024;
                        $('#quota_edit').val($quota_g);
                        $('#satuan_quota_edit').val('GB');
                    }
                };

                if (response.data.mac === 1) {
                    $('#lock_mac_edit').val(response.data.mac);
                } else {
                    $('#lock_mac_edit').val(response.data.mac);
                }


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
            'shared': $('#shared_edit').val(),
            'price': $('#price_edit').val(),
            'reseller_price': $('#reseller_price_edit').val(),
            'rate': $('#rate_edit').val(),
            'groupProfile': $('#groupProfile_edit').val(),
            'uptime': $('#uptime_edit').val(),
            'validity': $('#validity_edit').val(),
            'quota': $('#quota_edit').val(),
            'lock_mac': $('#lock_mac_edit').val(),
            'satuan_uptime': $('#satuan_uptime_edit').val(),
            'satuan_validity': $('#satuan_validity_edit').val(),
            'satuan_quota': $('#satuan_quota_edit').val()
        };

        // Simpan konten asli tombol untuk dikembalikan nanti
        var originalBtnContent = $('#update').html();
        // Nonaktifkan tombol dan tampilkan spinner pada tombol
        $('#update').prop('disabled', true).html(
            'Processing&nbsp;<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
        );

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/hotspot/profile/${profile_id}`,
            type: "PUT",
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
                        $('#edit').modal('hide');
                        // Kembalikan tombol ke kondisi semula
                        $('#update').prop('disabled', false).html(originalBtnContent);
                    }, 1500);
                } else {
                    $.each(response.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        el.after($('<div class="form-text text-danger">' + value[0] +
                            '</div>'));
                    });
                    // Kembalikan tombol ke kondisi semula jika terdapat error validasi
                    $('#update').prop('disabled', false).html(originalBtnContent);
                }
            },
            error: function(err) {
                $("#message").html("Some Error Occurred!");
                // Kembalikan tombol ke kondisi semula jika terjadi error AJAX
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
                    url: `/hotspot/profile/enable/${id}`,
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
                    url: `/hotspot/profile/disable/${id}`,
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

    $('#create').on('shown.bs.modal', function(e) {
        $('#shared').val(1);
    });
</script>
@endpush
