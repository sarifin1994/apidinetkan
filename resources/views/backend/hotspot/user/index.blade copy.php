@extends('backend.layouts.app')
@section('main')
@section('title', 'Hotspot User')
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
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">Hotspot</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">Hotspot User</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
            <!-- Action -->
            @include('backend.hotspot.user.modal.create')
            @include('backend.hotspot.user.modal.generate')
            @include('backend.hotspot.user.modal.print')
            @include('backend.hotspot.user.modal.edit')
            @include('backend.hotspot.user.modal.show_session')
            @include('backend.hotspot.user.modal.import')

            @if (multi_auth()->role === 'Admin')
                <div class="btn-group">
                    <button type="button" class="btn btn-primary me-2 mb-2 dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <span class="material-symbols-outlined">
                            add
                        </span> Tambah </span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" type="button" data-bs-toggle="modal"
                                data-bs-target="#create">User</a>
                        </li>
                        <li><a class="dropdown-item" type="button" data-bs-toggle="modal"
                                data-bs-target="#generate">Voucher</a></li>
                    </ul>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-warning text-white me-2 mb-2 dropdown-toggle"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="material-symbols-outlined">
                            edit_square
                        </span> Action<span class="row-count badge bg-dark text-light ms-1"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" id="enable">Enable</a></li>
                        <li><a class="dropdown-item" id="disable">Disable</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" id="reactivate">Re-Activate</a></li>
                    </ul>
                </div>
                <button class="btn btn-success text-light me-2 mb-2" type="button" id="btn-print"
                    data-bs-toggle="modal" data-bs-target="#print" disabled> <span
                        class="material-symbols-outlined me-1">print</span> Print <span
                        class="row-count badge bg-dark text-light"></span></button>
                <button class="btn btn-danger text-light me-2 mb-2" type="button" id="delete" disabled>
                    <span class="material-symbols-outlined">delete</span> Delete <span
                        class="row-count badge bg-dark text-light"></span></button>

                <button class="btn btn-warning text-white mb-2" data-bs-toggle="modal" data-bs-target="#import"> <span
                        class="material-symbols-outlined me-1">file_save</span> Import </button>
            @endif

        </div>
    </div>

    <!-- Page content -->
    <div class="row mb-4">
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <!-- Card 1: User Total -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-xxl-0">
            <div class="card bg-body-tertiary border-transparent">
                <div class="card-body px-6 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="fs-sm fw-normal text-body-secondary mb-1">User Total</h4>
                            <div class="fs-5 fw-semibold" id="totaluser" data-value="{{ $totaluser }}">
                                <span class="material-symbols-outlined spinner">progress_activity</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="avatar avatar-lg bg-body text-primary">
                                <span class="material-symbols-outlined">group</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: User New -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-md-0">
            <div class="card bg-body-tertiary border-transparent">
                <div class="card-body px-6 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="fs-sm fw-normal text-body-secondary mb-1">User New</h4>
                            <div class="fs-5 fw-semibold" id="totaldisabled" data-value="{{ $totalnew }}">
                                <span class="material-symbols-outlined spinner">progress_activity</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="avatar avatar-lg bg-body text-warning">
                                <span class="material-symbols-outlined">group</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: User Active -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-xxl-0">
            <div class="card bg-body-tertiary border-transparent">
                <div class="card-body px-6 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="fs-sm fw-normal text-body-secondary mb-1">User Active</h4>
                            <div class="fs-5 fw-semibold" id="totalactive" data-value="{{ $totalactive }}">
                                <span class="material-symbols-outlined spinner">progress_activity</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="avatar avatar-lg bg-body text-success">
                                <span class="material-symbols-outlined">person_check</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: User Expired -->
        <div class="col-12 col-md-6 col-xxl-3">
            <div class="card bg-body-tertiary border-transparent mb-4">
                <div class="card-body px-6 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="fs-sm fw-normal text-body-secondary mb-1">User Expired</h4>
                            <div class="fs-5 fw-semibold" id="totalsuspend" data-value="{{ $totalexpired }}">
                                <span class="material-symbols-outlined spinner">progress_activity</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="avatar avatar-lg bg-body text-danger">
                                <span class="material-symbols-outlined">person_remove</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        @if (in_array(multi_auth()->role, ['Admin', 'Teknisi']))
            <div class="row mb-3">
                <div class="col-lg-3">
                    <div class="form-group mb-3">
                        <select data-column="7" class="form-select" id="filter_remark" name="filter_remark">
                            <option value="">FILTER CREATED</option>
                            @forelse ($remarks as $remark)
                                <option value="{{ $remark->created_at }}">
                                    {{ Carbon\Carbon::parse($remark->created_at)->format('d/m/Y H:i:s') }}
                                </option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group mb-3">
                        <select data-column="5" class="form-select" id="filter_nas" name="filter_nas">
                            <option value="">FILTER NAS</option>
                            @forelse ($nas as $nas)
                                <option value="{{ $nas->ip_router }}">{{ $nas->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group mb-3">
                        <select data-column="7" class="form-select" id="filter_reseller" name="filter_reseller">
                            <option value="">FILTER RESELLER</option>
                            @forelse ($resellers as $reseller)
                                <option value="{{ $reseller->id }}">{{ $reseller->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group mb-3">
                        <select data-column="11" class="form-select" id="filter_status" name="filter_status">
                            <option value="">FILTER STATUS</option>
                            <option value="1">New</option>
                            <option value="2">Active</option>
                            <option value="3">Expired</option>
                            <option value="0">Disabled</option>
                        </select>
                    </div>
                </div>
            </div>
        @endif
        <div class="card-body table-responsive">

            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="form-check-input" id="head-cb"></th>
                        <th style="text-align:left!important">#</th>
                        <th style="text-align:left!important">Username</th>
                        <th style="text-align:left!important">Password</th>
                        <th>Profile</th>
                        <th>NAS</th>
                        <th>Server</th>
                        <th>Reseller</th>
                        <th>Remark</th>
                        <th>Created</th>
                        <th>Owner</th>
                        <th>Status</th>
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
    document.addEventListener("DOMContentLoaded", function() {
        const delay = 1000; // Delay 1,5 detik
        const elements = document.querySelectorAll('[data-value]');
        elements.forEach(function(el) {
            setTimeout(function() {
                el.innerHTML = el.getAttribute('data-value');
            }, delay);
        });
    });

    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: true,
        scrollX: true,
        ajax: {
            url: '/hotspot/user/datatable',
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        },
        lengthMenu: [10, 100, 500, 1000, 2000],
        order: [
            [9, 'desc']
        ],
        columns: [{
                data: 'checkbox',
                'sortable': false,
                name: 'checkbox',
                // render: function(data, type, row, meta) {
                //     return '<input type="checkbox" class="form-check-input row-cb" value="' + row.id +
                //         '">';
                // },
            },
            {
                data: null,
                'sortable': false,
                className: "text-center",
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'username',
                name: 'username'
            },
            {
                data: 'value',
                name: 'value'
            },
            {
                data: 'profile_name',
                name: 'profile',
                render: function(data) {
                    if (data === null) {
                        return '<i class="text-danger">Unknown</i>'
                    } else {
                        return data
                    }
                },
            },
            {
                data: 'nas_name',
                name: 'nas',
                render: function(data, type, row) {
                    if (row.nas === null) {
                        return 'all'
                    } else if (row.nas_name === null) {
                        return '<i class="text-danger">Unknown</i>'
                    } else {
                        return data
                    }
                },
            },
            {
                data: 'server',
                name: 'server',
                render: function(data, type, row) {
                    if (data === null) {
                        return 'all'
                    } else {
                        return data
                    }
                },
            },
            {
                data: 'reseller_name',
                name: 'reseller_id',
                render: function(data, type, row) {
                    if (row.reseller_id === null) {
                        return '-'
                    } else if (row.id === null) {
                        return '<i class="text-danger">Unknown</i>'
                    } else {
                        return data
                    }
                },
            },

            {
                data: 'remark',
                name: 'remark',
                visiable: false,
            },
            {
                data: 'created_at',
                name: 'created_at',
                render: function(data, type, row, meta) {
                    return moment(data).local().format('DD/MM/YYYY HH:mm:ss');
                },
            },
            {
                data: 'created_by',
                name: 'created_by',
            },
            {
                data: 'status',
                name: 'status',
                render: function(data) {
                    if (data === 1) {
                        return '<span class="text-warning">new</span>'
                    } else if (data === 0) {
                        return '<span class="text-danger">off</span>'
                    } else if (data === 2) {
                        return '<span class="text-success">active</span>'
                    } else if (data === 3) {
                        return '<span class="text-danger">expired</span>'
                    } else {
                        return data;
                    }

                },
            },
        ],

    });

    var id_selected = [];
    table.on('preXhr.dt', function(e, settings, data) {
        data.remark = $('#filter_remark').val();
        data.status = $('#filter_status').val();
        data.nas = $('#filter_nas').val();
        data.reseller = $('#filter_reseller').val();
        data.idsel = id_selected;
    });


    $('#generate_voucher').click(function(e) {
        e.preventDefault();

        // Hapus pesan error sebelumnya
        var error_ele = document.getElementsByClassName('alert text-sm');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        // Kumpulkan data dari form
        var data = {
            'jml_voucher': $('#jml_voucher').val(),
            'model': $('#model').val(),
            'character': $('#character').val(),
            'length': $('#length').val(),
            'prefix': $('#prefix').val(),
            'profile': $('#profile option:selected').text(),
            'profile_id': $('#profile option:selected').val(),
            'nas': $('#nas option:selected').val(),
            'hotspot_server': $('#hotspot_server').val(),
            'payment_status': $('#payment_status option:selected').val(),
            'price': $('#price').val(),
            'total': $('#total').val(),
            'reseller_id': $('#reseller option:selected').val(),
            'wa_reseller': $('#wa_reseller').val(),
        };

        // Simpan konten asli tombol agar bisa dikembalikan nanti
        var originalBtnContent = $('#generate_voucher').html();

        // Nonaktifkan tombol dan tampilkan spinner di dalamnya
        $('#generate_voucher').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
        );

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Proses AJAX
        $.ajax({
            url: `/hotspot/user/generate`,
            type: "POST",
            cache: false,
            data: data,
            dataType: "json",
            success: function(response) {
                is_generate = true;
                // Jika ada error dari server
                if (response.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.error,
                        showConfirmButton: false,
                        timer: 5000
                    });
                    $('#generate_voucher').prop('disabled', false).html(originalBtnContent);
                    return;
                }
                // Jika respons sukses
                if (response.success) {
                    id_selected = response.id.map(item => item.id);
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    // Kembalikan tombol ke kondisi semula
                    $('#generate_voucher').prop('disabled', false).html(originalBtnContent);
                    // Tampilkan spinner di total sebelum update
                    $('#totaluser').html(
                        '<span class="material-symbols-outlined spinner">progress_activity</span>'
                    );
                    $('#totalactive').html(
                        '<span class="material-symbols-outlined spinner">progress_activity</span>'
                    );
                    $('#totalsuspend').html(
                        '<span class="material-symbols-outlined spinner">progress_activity</span>'
                    );
                    $('#totaldisabled').html(
                        '<span class="material-symbols-outlined spinner">progress_activity</span>'
                    );
                    setTimeout(function() {
                        table.ajax.reload();
                        $('#btn-print').prop('disabled', false);
                        $('#delete').prop('disabled', false);
                        $('input, textarea').val('');
                        $('#profile').val('').trigger('change');
                        $('#nas').val('').trigger('change');
                        $('#reseller').val('').trigger('change');
                        // Update nilai dengan efek transisi
                        $('#totaluser').fadeOut(200, function() {
                            $(this).html(response.totaluser).fadeIn(200);
                        });
                        $('#totalactive').fadeOut(200, function() {
                            $(this).html(response.totalactive).fadeIn(200);
                        });
                        $('#totalsuspend').fadeOut(200, function() {
                            $(this).html(response.totalsuspend).fadeIn(200);
                        });
                        $('#totaldisabled').fadeOut(200, function() {
                            $(this).html(response.totaldisabled).fadeIn(200);
                        });
                        $('#generate').modal('hide');
                    }, 1500);
                } else {
                    // Jika response tidak mengandung success, tampilkan error dari validasi
                    $.each(response.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        el.after($('<span class="alert text-sm text-danger">' + value[0] +
                            '</span>'));
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Failed to create voucher, please check your field',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#generate_voucher').prop('disabled', false).html(originalBtnContent);
                }
            },
            error: function(err) {
                $("#message").html("Some Error Occurred!");
                $('#generate_voucher').prop('disabled', false).html(originalBtnContent);
            }
        });
    });


    $('#filter_remark').change(function() {
        table.ajax.reload(function(json) {
            $(".row-count").html(json.data.length);
        });
        $('#head-cb').prop('checked', true);
        $('#btn-print').prop('disabled', false);
        $('#delete').prop('disabled', false);
        $(".row-count").html($('.row-cb:checked').length);
        return false;
    });

    $('#filter_status').change(function() {
        table.ajax.reload(function(json) {
            $(".row-count").html(json.data.length);
        });
        $('#head-cb').prop('checked', true);
        $('#btn-print').prop('disabled', false);
        $('#delete').prop('disabled', false);
        return false;
    });

    $('#filter_nas').change(function() {
        table.ajax.reload()
        return false;
    });
    $('#filter_reseller').change(function() {
        table.ajax.reload()
        return false;
    });

    $('#myTable').on('length.dt', function(e, settings, len) {
        if ($('#filter_remark').val() || $('#filter_status').val()) {
            if (settings._iDisplayLength >= settings._iRecordsTotal) {
                $(".row-count").html(settings._iRecordsTotal);
            } else {
                $(".row-count").html(len);
            }
        } else {
            $('#head-cb').prop('checked', false);
            $(".row-count").html('');
            $('#btn-print').prop('disabled', true);
            $('#delete').prop('disabled', true);
        }
    });


    $('#myTable tbody').on('click', 'tr td:not(:first-child)', function() {

        let user_id = table.row(this).data().id;
        let username = table.row(this).data().username;

        $.ajax({
            url: `/hotspot/user/${user_id}`,
            type: "GET",
            success: function(data) {
                $('#user_id').val(data.data.id),
                    $('#username_edit').val(data.data.username),
                    $('#password_edit').val(data.data.value),
                    $('#profile_edit').val(data.data.rprofile.id);
                $('#nas_edit').val(data.data.nas);
                $('#hotspot_server_edit').val(data.data.server);
                $('#remark_edit').val(data.data.remark);
                if (data.data.status === 1) {
                    $('#fill_status').html('<span class="text-primary">new</span>');
                } else if (data.data.status === 0) {
                    $('#fill_status').html('<span class="text-warning">off</span>');
                } else if (data.data.status === 2) {
                    $('#fill_status').html('<span class="text-success">active</span>');
                } else if (data.data.status === 3) {
                    $('#fill_status').html('<span class="text-danger">expired</span>');
                }
                if (data.data.start_time !== null) {
                    let rawDate = data.data.start_time; // "2025-03-28 13:30:52"
                    let dateObj = new Date(rawDate);

                    let day = String(dateObj.getDate()).padStart(2, '0');
                    let month = String(dateObj.getMonth() + 1).padStart(2,
                    '0'); // bulan dimulai dari 0
                    let year = dateObj.getFullYear();

                    let hours = String(dateObj.getHours()).padStart(2, '0');
                    let minutes = String(dateObj.getMinutes()).padStart(2, '0');
                    let seconds = String(dateObj.getSeconds()).padStart(2, '0');

                    let formatted = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
                    $('#fill_login').html(formatted);
                }
                if (data.data.end_time !== null) {
                    let rawDate = data.data.end_time; // "2025-03-28 13:30:52"
                    let dateObj = new Date(rawDate);

                    let day = String(dateObj.getDate()).padStart(2, '0');
                    let month = String(dateObj.getMonth() + 1).padStart(2,
                    '0'); // bulan dimulai dari 0
                    let year = dateObj.getFullYear();

                    let hours = String(dateObj.getHours()).padStart(2, '0');
                    let minutes = String(dateObj.getMinutes()).padStart(2, '0');
                    let seconds = String(dateObj.getSeconds()).padStart(2, '0');

                    let formatted = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
                    $('#fill_expired').html(formatted);
                }

                if (jQuery.type(data.data.session.input) === 'undefined') {
                    $('#fill_upload').html('-');
                } else {
                    var upload = data.data.session.input
                    var sizes = ['Bytes', 'KB', 'MB', 'GB',
                        'TB'
                    ];
                    if (upload == 0) return 'n/a';
                    var i = parseInt(Math.floor(Math.log(
                        upload) / Math.log(1024)));
                    // if (i == 0) return upload + ' ' + sizes[i];
                    var upload_format = (upload / Math.pow(1024, i)).toFixed(
                        1) + ' ' + sizes[i];
                    $('#fill_upload').html(upload_format);
                }

                if (jQuery.type(data.data.session.output) === 'undefined') {
                    $('#fill_download').html('-');
                } else {
                    var download = data.data.session.output
                    var sizes = ['Bytes', 'KB', 'MB', 'GB',
                        'TB'
                    ];
                    // if (download == 0) return 'n/a';
                    var i = parseInt(Math.floor(Math.log(
                        download) / Math.log(1024)));
                    // if (i == 0) return download + ' ' + sizes[i];
                    var download_format = (download / Math.pow(1024, i)).toFixed(
                        1) + ' ' + sizes[i];
                    $('#fill_download').html(download_format);
                }

                if (jQuery.type(data.data.session.uptime) === 'undefined') {
                    $('#fill_uptime').html('-');
                } else {
                    var seconds = data.data.session.uptime
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
                        parts.push(d + 'd');
                    }

                    if (h > 0) {
                        parts.push(h + 'h');
                    }

                    if (m > 0) {
                        parts.push(m + 'm');
                    }

                    // if (s > 0) {
                    //     parts.push(s + ' second' + (s > 1 ? 's' :
                    //         ''));
                    // }

                    var uptime = parts.join(' ');
                    $('#fill_uptime').html(uptime)
                }




            }
        });

        $('#edit').modal('show');

    });

    $('#head-cb').on('click', function(e) {
        if ($(this).is(':checked', true)) {
            $(".row-cb").prop('checked', true);
            $(".row-count").html($('.row-cb:checked').length);
            $('#btn-print').prop('disabled', false);
            $('#delete').prop('disabled', false);
        } else {
            $(".row-cb").prop('checked', false);
            $(".row-count").html('');
            $('#btn-print').prop('disabled', true);
            $('#delete').prop('disabled', true);
        }
    });

    $('#myTable').on('click', '.row-cb', function() {
        if ($('.row-cb:checked').length == $('.row-cb').length) {
            $('#head-cb').prop('checked', true);
            $(".row-count").html($('.row-cb:checked').length);
            $('#btn-print').prop('disabled', false);
            $('#delete').prop('disabled', false);
        } else if ($('.row-cb:checked').length == 0) {
            $('#head-cb').prop('checked', false);
            $(".row-count").html('');
            $('#btn-print').prop('disabled', true);
            $('#delete').prop('disabled', true);
        } else {
            $('#head-cb').prop('checked', false);
            $(".row-count").html($('.row-cb:checked').length);
            $('#btn-print').prop('disabled', false);
            $('#delete').prop('disabled', false);
        }
    });

    $("#profile,#jml_voucher").on("keyup change", function() {
        var profile = $('#profile').val();
        if (profile) {
            $.ajax({
                url: `/hotspot/profile/${profile}`,
                type: "GET",
                cache: false,
                data: {
                    profile: profile,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.data.reseller_price === '0') {
                        var amount = data.data.price;
                    } else {
                        var amount = data.data.reseller_price;
                    }
                    var jml = $('#jml_voucher').val();
                    var total = (jml * amount).toString();
                    rp_amount = formatRupiah(amount, 2, ',', '.');
                    $('#price').val(rp_amount);
                    rp_total_amount = formatRupiah(total, 2, ',', '.');
                    $('#total').val(rp_total_amount);
                }
            });
        }
    });

    $("#profile_c").on("keyup change", function() {
        var profile = $('#profile_c').val();
        if (profile) {
            $.ajax({
                url: `/hotspot/profile/${profile}`,
                type: "GET",
                cache: false,
                data: {
                    profile: profile,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.data.reseller_price === '0') {
                        var amount = data.data.price;
                    } else {
                        var amount = data.data.reseller_price;
                    }
                    rp_total_amount = formatRupiah(amount, 2, ',', '.');
                    $('#total_c').val(rp_total_amount);
                }
            });
        }
    });

    // $("#reseller").on("change", function() {
    //     var reseller = $(this).val();
    //     $.ajax({
    //         url: `/hotspot/reseller/${reseller}`,
    //         type: "GET",
    //         cache: false,
    //         data: {
    //             reseller: reseller,
    //             '_token': '{{ csrf_token() }}'
    //         },
    //         success: function(data) {

    //             $('#wa_reseller').val(data.data.wa);
    //         }
    //     });
    // });
    // $("#reseller_c").on("change", function() {
    //     var reseller = $(this).val();
    //     $.ajax({
    //         url: `/hotspot/reseller/${reseller}`,
    //         type: "GET",
    //         cache: false,
    //         data: {
    //             reseller: reseller,
    //             '_token': '{{ csrf_token() }}'
    //         },
    //         success: function(data) {

    //             $('#wa_reseller_c').val(data.data.wa);
    //         }
    //     });
    // });

    $('#store').click(function(e) {
        e.preventDefault();
        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        // Kumpulkan data dari form
        var data = {
            'username': $('#username').val(),
            'password': $('#password').val(),
            'profile': $('#profile_c option:selected').text(),
            'nas': $('#nas_c option:selected').val(),
            'hotspot_server': $('#hotspot_server_c').val(),
            'payment_status': $('#payment_status_c option:selected').val(),
            'total': $('#total_c').val(),
            'reseller_id': $('#reseller_c option:selected').val(),
            'wa_reseller': $('#wa_reseller_c').val(),
            'remark': $('#remark_c').val(),
        };

        // Simpan konten asli tombol agar bisa dikembalikan nanti
        var originalButtonContent = $('#store').html();

        // Tampilkan spinner di tombol dan nonaktifkan tombol
        $('#store').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
        );

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Proses AJAX
        $.ajax({
            url: `/hotspot/user`,
            type: "POST",
            cache: false,
            data: data,
            dataType: "json",
            success: function(data) {
                if (data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: `${data.error}`,
                        showConfirmButton: false,
                        timer: 5000
                    });
                    // Kembalikan tombol ke kondisi semula
                    $('#store').prop('disabled', false).html(originalButtonContent);
                }
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `${data.message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    // Tampilkan spinner pada elemen nilai sebelum update
                    $('#totaluser').html(
                        '<span class="material-symbols-outlined spinner">progress_activity</span>'
                    );
                    $('#totalactive').html(
                        '<span class="material-symbols-outlined spinner">progress_activity</span>'
                    );
                    $('#totalsuspend').html(
                        '<span class="material-symbols-outlined spinner">progress_activity</span>'
                    );
                    $('#totaldisabled').html(
                        '<span class="material-symbols-outlined spinner">progress_activity</span>'
                    );

                    setTimeout(function() {
                        table.ajax.reload();
                        $('input, textarea').val('');
                        $('#profile').val('').trigger('change');
                        $('#nas').val('').trigger('change');
                        $('#reseller_c').val('').trigger('change');
                        // Update nilai dengan efek transisi
                        $('#totaluser').fadeOut(200, function() {
                            $(this).html(data.totaluser).fadeIn(200);
                        });
                        $('#totalactive').fadeOut(200, function() {
                            $(this).html(data.totalactive).fadeIn(200);
                        });
                        $('#totalsuspend').fadeOut(200, function() {
                            $(this).html(data.totalsuspend).fadeIn(200);
                        });
                        $('#totaldisabled').fadeOut(200, function() {
                            $(this).html(data.totaldisabled).fadeIn(200);
                        });
                        $('#create').modal('hide');
                        // Kembalikan tombol ke kondisi semula
                        $('#store').prop('disabled', false).html(originalButtonContent);
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
    var el = $(document).find('[name="' + key + '"]');
    // Tambahkan class is-invalid untuk membuat border merah (jika menggunakan Bootstrap)
    el.addClass('is-invalid');
    el.after($('<div class="form-text text-danger">' + value[0] + '</div>'));
});

                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Gagal membuat user, silakan cek data yang diinput',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    // Kembalikan tombol ke kondisi semula
                    $('#store').prop('disabled', false).html(originalButtonContent);
                }
            },

            error: function(err) {
                $("#message").html("Some Error Occurred!");
                // Kembalikan tombol ke kondisi semula
                $('#store').prop('disabled', false).html(originalButtonContent);
            }
        });
    });


    $('#update').click(function(e) {
        e.preventDefault();
        var error_ele = document.getElementsByClassName('alert text-sm');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        let user_id = $('#user_id').val();
        // collect data by id
        var data = {
            'user_id': $('#user_id').val(),
            'username': $('#username_edit').val(),
            'password': $('#password_edit').val(),
            'profile': $('#profile_edit option:selected').text(),
            'nas': $('#nas_edit').val(),
            'hotspot_server': $('#hotspot_server_edit').val(),
            'remark': $('#remark_edit').val(),
        }


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ajax proses
        $.ajax({
            url: `/hotspot/user/${user_id}`,
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
                        $('#totaluser').html(data.totaluser)
                        $('#totalactive').html(data.totalactive)
                        $('#totalsuspend').html(data.totalsuspend)
                        $('#totaldisabled').html(data.totaldisabled)
                        $('#edit').modal('hide')
                    });
                } else {
                    $.each(data.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        el.after($('<span class= "alert text-sm text-danger">' + value[0] +
                            '</div>'));
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Gagal membuat user, silakan cek data yang diinput',
                        showConfirmButton: false,
                        timer: 1500
                    });

                }
            },

            error: function(err) {
                $("#message").html("Some Error Occurred!")
            }

        });
    });

    let is_generate = false;
    $('#print_voucher').click(function(e) {
        var template = $('#template').val();
        e.preventDefault();
        // Simpan konten asli tombol agar bisa dikembalikan nanti
        var originalBtnContent = $('#print_voucher').html();

        // Nonaktifkan tombol dan tampilkan spinner pada tombol
        $('#print_voucher').prop('disabled', true).html(
            'Processing&nbsp;<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
        );

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let checked = $('#myTable tbody .row-cb:checked');
        let ids = [];
        if (is_generate === true) {
            ids = id_selected;
        } else {
            $.each(checked, function(index, elm) {
                ids.push(elm.value);
            });
        }

        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: `Voucher Berhasil Diprint. Harap Tunggu...`,
            showConfirmButton: false,
            timer: 5000
        });

        // Proses AJAX
        $.ajax({
            url: `/hotspot/user/print`,
            type: "POST",
            cache: false,
            data: {
                ids: ids,
                template: template
            },
            dataType: "json",
            success: function(response) {
                $('#print_voucher').prop('disabled', false).html(originalBtnContent);
                var win = window.open('', '_blank');
                win.document.open();
                win.document.write(response.data);
                win.document.close();
                win.focus();
                // Tunggu hingga window selesai load, lalu panggil print
                win.onload = function() {
                    win.print();
                };
                // Jika event onload tidak terpanggil, bisa gunakan setTimeout sebagai fallback
                setTimeout(function() {
                    win.print();
                }, 500);
                $("#print").modal('hide');
            },
            error: function(err) {
                $("#message").html("Some Error Occurred!");
                $('#print_voucher').prop('disabled', false).html(originalBtnContent);
            }
        });
    });


    $('#enable').click(function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let checked = $('#myTable tbody .row-cb:checked');
        let ids = [];

        if (is_generate === true) {
            ids = id_selected;
        } else {
            $.each(checked, function(index, elm) {
                ids.push(elm.value);
            });
        }

        // Tampilkan spinner pada elemen nilai sebelum AJAX diproses
        $('#totaluser').html('<span class="material-symbols-outlined spinner">progress_activity</span>');
        $('#totalactive').html('<span class="material-symbols-outlined spinner">progress_activity</span>');
        $('#totalsuspend').html('<span class="material-symbols-outlined spinner">progress_activity</span>');
        $('#totaldisabled').html('<span class="material-symbols-outlined spinner">progress_activity</span>');

        // Proses AJAX
        $.ajax({
            url: `/hotspot/user/enable`,
            type: "POST",
            cache: false,
            data: {
                ids: ids
            },
            dataType: "json",
            success: function(data) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: `${data.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
                // Update nilai dengan delay 1,5 detik dan efek transisi
                setTimeout(function() {
                    table.ajax.reload();
                    $('#totaluser').fadeOut(200, function() {
                        $(this).html(data.totaluser).fadeIn(200);
                    });
                    $('#totalactive').fadeOut(200, function() {
                        $(this).html(data.totalactive).fadeIn(200);
                    });
                    $('#totalsuspend').fadeOut(200, function() {
                        $(this).html(data.totalsuspend).fadeIn(200);
                    });
                    $('#totaldisabled').fadeOut(200, function() {
                        $(this).html(data.totaldisabled).fadeIn(200);
                    });
                }, 1500);
                $('#head-cb').prop('checked', false);
                $(".row-count").html('');
            },
            error: function(err) {
                $("#message").html("Some Error Occurred!");
            }
        });
    });

    $('#disable').click(function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let checked = $('#myTable tbody .row-cb:checked');
        let ids = [];

        if (is_generate === true) {
            ids = id_selected;
        } else {
            $.each(checked, function(index, elm) {
                ids.push(elm.value);
            });
        }

        // Tampilkan spinner pada elemen nilai sebelum AJAX diproses
        $('#totaluser').html('<span class="material-symbols-outlined spinner">progress_activity</span>');
        $('#totalactive').html('<span class="material-symbols-outlined spinner">progress_activity</span>');
        $('#totalsuspend').html('<span class="material-symbols-outlined spinner">progress_activity</span>');
        $('#totaldisabled').html('<span class="material-symbols-outlined spinner">progress_activity</span>');

        // Proses AJAX
        $.ajax({
            url: `/hotspot/user/disable`,
            type: "POST",
            cache: false,
            data: {
                ids: ids
            },
            dataType: "json",
            success: function(data) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: `${data.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
                // Update nilai setelah delay 1,5 detik dengan efek fadeOut/fadeIn
                setTimeout(function() {
                    table.ajax.reload();
                    $('#totaluser').fadeOut(200, function() {
                        $(this).html(data.totaluser).fadeIn(200);
                    });
                    $('#totalactive').fadeOut(200, function() {
                        $(this).html(data.totalactive).fadeIn(200);
                    });
                    $('#totalsuspend').fadeOut(200, function() {
                        $(this).html(data.totalsuspend).fadeIn(200);
                    });
                    $('#totaldisabled').fadeOut(200, function() {
                        $(this).html(data.totaldisabled).fadeIn(200);
                    });
                }, 1500);
                $('#head-cb').prop('checked', false);
                $(".row-count").html('');
            },
            error: function(err) {
                $("#message").html("Some Error Occurred!");
            }
        });
    });

    $('#reactivate').click(function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let checked = $('#myTable tbody .row-cb:checked');
        let ids = [];

        if (is_generate === true) {
            ids = id_selected;
        } else {
            $.each(checked, function(index, elm) {
                ids.push(elm.value);
            });
        }

        // Tampilkan spinner pada elemen nilai sebelum AJAX diproses
        $('#totaluser').html('<span class="material-symbols-outlined spinner">progress_activity</span>');
        $('#totalactive').html('<span class="material-symbols-outlined spinner">progress_activity</span>');
        $('#totalsuspend').html('<span class="material-symbols-outlined spinner">progress_activity</span>');
        $('#totaldisabled').html('<span class="material-symbols-outlined spinner">progress_activity</span>');

        $.ajax({
            url: `/hotspot/user/reactivate`,
            type: "POST",
            cache: false,
            data: {
                ids: ids
            },
            dataType: "json",
            success: function(data) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: `${data.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
                // Update nilai setelah delay 1,5 detik dengan efek transisi
                setTimeout(function() {
                    table.ajax.reload();
                    $('#totaluser').fadeOut(200, function() {
                        $(this).html(data.totaluser).fadeIn(200);
                    });
                    $('#totalactive').fadeOut(200, function() {
                        $(this).html(data.totalactive).fadeIn(200);
                    });
                    $('#totalsuspend').fadeOut(200, function() {
                        $(this).html(data.totalsuspend).fadeIn(200);
                    });
                    $('#totaldisabled').fadeOut(200, function() {
                        $(this).html(data.totaldisabled).fadeIn(200);
                    });
                }, 1500);
                $('#head-cb').prop('checked', false);
                $(".row-count").html('');
            },
            error: function(err) {
                $("#message").html("Some Error Occurred!");
            }
        });
    });

    $('#delete').on('click', function() {
        Swal.fire({
            title: "Apakah anda yakin?",
            icon: 'warning',
            text: "Data yang sudah dihapus tidak dapat dikembalikan",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Yes, delete!",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#d33",
        }).then(function(result) {
            if (result.isConfirmed) {
                // Simpan konten asli tombol delete
                var originalBtnContent = $('#delete').html();
                // Nonaktifkan tombol dan tampilkan spinner di dalamnya
                $('#delete').prop('disabled', true).html(
                    'Delete&nbsp;<i class="fa fa-refresh fa-spin" id="spinner"></i>');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                let checked = $('#myTable tbody .row-cb:checked');
                let ids = [];
                if (is_generate === true) {
                    ids = id_selected;
                } else {
                    $.each(checked, function(index, elm) {
                        ids.push(elm.value);
                    });
                }

                // Tampilkan spinner pada elemen nilai sebelum AJAX diproses
                $('#totaluser').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');
                $('#totalactive').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');
                $('#totalsuspend').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');
                $('#totaldisabled').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');

                $.ajax({
                    url: `/hotspot/user/delete`,
                    type: "POST",
                    cache: false,
                    data: {
                        _method: "DELETE",
                        ids: ids
                    },
                    dataType: "json",
                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        // Update nilai setelah delay 1,5 detik dengan efek transisi
                        setTimeout(function() {
                            table.ajax.reload();
                            $('#totaluser').fadeOut(200, function() {
                                $(this).html(data.totaluser).fadeIn(200);
                            });
                            $('#totalactive').fadeOut(200, function() {
                                $(this).html(data.totalactive).fadeIn(200);
                            });
                            $('#totalsuspend').fadeOut(200, function() {
                                $(this).html(data.totalsuspend).fadeIn(200);
                            });
                            $('#totaldisabled').fadeOut(200, function() {
                                $(this).html(data.totaldisabled).fadeIn(
                                    200);
                            });
                            // Kembalikan tombol delete ke kondisi semula
                            $('#delete').prop('disabled', false).html(
                                originalBtnContent);
                        }, 1500);
                        $('#head-cb').prop('checked', false);
                        $(".row-count").html('');
                    },
                    error: function(err) {
                        $("#message").html("Some Error Occurred!");
                        $('#delete').prop('disabled', false).html(originalBtnContent);
                    }
                });
            }
        });
    });

    $('#show_session').click(function() {
        let username = $('#username_edit').val();
        $.ajax({
            url: `/hotspot/user/getSession/${username}`,
            type: 'GET',
            data: {
                username: username
            },
            success: function(response) {
                console.log(response);
                $('#sessionTable').DataTable({
                    data: response,
                    scrollX: true,
                    pageLength: 2,
                    lengthMenu: [
                        [2, 5, 10, 20],
                        [2, 5, 10, 20]
                    ],
                    destroy: true,
                    order: [
                        [0, 'desc']
                    ],
                    columns: [{
                            data: 'start',
                        },
                        {
                            data: 'stop',
                            render: function(data) {
                                if (data === null) {
                                    return ''
                                } else {
                                    return data;
                                }

                            },
                        },
                        {
                            data: 'ip'
                        },
                        {
                            data: 'mac'
                        },
                        {
                            data: 'input',
                            render: function bytesToSize(data) {
                                var sizes = ['Bytes', 'KB', 'MB', 'GB',
                                    'TB'
                                ];
                                if (data == 0) return '0 Bytes';
                                var i = parseInt(Math.floor(Math.log(
                                    data) / Math.log(1024)));
                                if (i == 0) return data + ' ' + sizes[i];
                                return (data / Math.pow(1024, i)).toFixed(
                                    1) + ' ' + sizes[i];
                            }
                        },
                        {
                            data: 'output',
                            render: function bytesToSize(data) {
                                var sizes = ['Bytes', 'KB', 'MB', 'GB',
                                    'TB'
                                ];
                                if (data == 0) return '0 Bytes';
                                var i = parseInt(Math.floor(Math.log(
                                    data) / Math.log(1024)));
                                if (i == 0) return data + ' ' + sizes[i];
                                return (data / Math.pow(1024, i)).toFixed(
                                    1) + ' ' + sizes[i];
                            }
                        },
                        {
                            data: 'uptime',
                            render: function convertSecondsToReadableString(
                                seconds) {
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
                                    parts.push(d + 'd');
                                }

                                if (h > 0) {
                                    parts.push(h + 'h');
                                }

                                if (m > 0) {
                                    parts.push(m + 'm');
                                }

                                // if (s > 0) {
                                //     parts.push(s + ' second' + (s > 1 ? 's' :
                                //         ''));
                                // }

                                return parts.join(' ');
                            }

                        }
                    ]
                });

            }
        });
        $(this).toggleClass("active");
        if ($(this).hasClass("active")) {
            $(this).text("Hide session");
        } else {
            $(this).text("Show session");
        }
    });

    $('#btnImport').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        $btn.prop('disabled', true)
            .html('Importing&nbsp;<span class="material-symbols-outlined spinner">autorenew</span>');
        // Lanjutkan dengan submit form atau AJAX sesuai kebutuhan...
        $('#importForm').submit();
    });

    /* Fungsi */
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
    }
</script>
@endpush
