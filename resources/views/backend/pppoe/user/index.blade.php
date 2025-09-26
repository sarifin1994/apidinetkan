@extends('backend.layouts.app')
@section('main')
@section('title', 'PPPoE User')
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
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">PPPoE</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">PPPoE User</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
            <!-- Action -->
            @include('backend.pppoe.user.modal.create')
            @include('backend.pppoe.user.modal.edit')
            @include('backend.pppoe.user.modal.import')
            @include('backend.pppoe.user.modal.show_session')

            @if (in_array(multi_auth()->role, ['Admin', 'Teknisi']) || (multi_auth()->role === 'Mitra' && multi_auth()->user === 1))
                <button class="btn btn-primary me-2 mb-2" data-bs-toggle="modal" data-bs-target="#create"> <span
                        class="material-symbols-outlined me-1">add</span> Tambah</button>
            @endif
            @if (multi_auth()->role === 'Admin')
                <div class="btn-group">
                    <button type="button" class="btn btn-danger me-2 mb-2 dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <span class="material-symbols-outlined">
                            edit_square
                        </span> Action<span class="row-count badge bg-dark text-white ms-1"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" id="enableMassal">Aktifkan</a></li>
                        <li><a class="dropdown-item" id="disableMassal">Suspend</a></li>
                        <li><a class="dropdown-item" id="registMassal">Proses Registrasi</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" id="deleteMassal">Hapus</a></li>
                    </ul>
                </div>
                <button class="btn btn-warning me-2 mb-2 text-white" data-bs-toggle="modal" data-bs-target="#import">
                    <span class="material-symbols-outlined me-1">file_save</span> Import </button>
                <button onclick="downloadExcel()" id="downloadExcel" class="btn btn-success text-white mb-2"
                    data-bs-toggle="modal" data-bs-target="#export"><span
                        class="material-symbols-outlined me-1">file_export</span> Export
                </button>
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
        <!-- User Total -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-xxl-0 mb-4 mb-xxl-0">
            <div class="card bg-body-tertiary border-transparent">
                <div class="card-body px-6 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="fs-sm fw-normal text-body-secondary mb-1">USER TOTAL</h4>
                            <div class="fs-5 fw-semibold" id="totaluser" data-value="{{ $totaluser }}">
                                <span class="material-symbols-outlined spinner">progress_activity</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="fs-1 text-primary">
                                <span class="material-symbols-outlined">group</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Pending -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-xxl-0 mb-4 mb-md-0">
            <div class="card bg-body-tertiary border-transparent">
                <div class="card-body px-6 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="fs-sm fw-normal text-body-secondary mb-1">USER PENDING</h4>
                            <div class="fs-5 fw-semibold" id="totaldisabled" data-value="{{ $totaldisabled }}">
                                <span class="material-symbols-outlined spinner">progress_activity</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="fs-1 text-warning">
                                <span class="material-symbols-outlined">person_alert</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Active -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-xxl-0 mb-4 mb-xxl-0">
            <div class="card bg-body-tertiary border-transparent">
                <div class="card-body px-6 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="fs-sm fw-normal text-body-secondary mb-1">USER ACTIVE</h4>
                            <div class="fs-5 fw-semibold" id="totalactive" data-value="{{ $totalactive }}">
                                <span class="material-symbols-outlined spinner">progress_activity</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="fs-1 text-success">
                                <span class="material-symbols-outlined">person_check</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Suspend -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-xxl-0">
            <div class="card bg-body-tertiary border-transparent mb-4">
                <div class="card-body px-6 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="fs-sm fw-normal text-body-secondary mb-1">USER SUSPEND</h4>
                            <div class="fs-5 fw-semibold" id="totalsuspend" data-value="{{ $totalsuspend }}">
                                <span class="material-symbols-outlined spinner">progress_activity</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="fs-1 text-danger">
                                <span class="material-symbols-outlined">person_cancel</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row mb-3">
        <div class="col-lg-3">
            <div class="form-group mb-3">
                <select data-column="7" class="form-select" id="filter_nas" name="filter_nas">
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
                <select data-column="5" class="form-select" id="filter_area" name="filter_area">
                    <option value="">FILTER POP</option>
                    @forelse ($areas as $area)
                        <option value="{{ $area->kode_area }}">{{ $area->kode_area }} - {{ $area->deskripsi }}
                        </option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group mb-3">
                <select data-column="13" class="form-select" id="filter_mitra" name="filter_mitra">
                    <option value="">FILTER MITRA</option>
                    @forelse ($mitras as $mitra)
                        <option value="{{ $mitra->id }}">{{ $mitra->name }}</option>
                    @empty
                    @endforelse
                </select>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group mb-3">
                <select data-column="9" class="form-select" id="filter_status" name="filter_status">
                    <option value="">FILTER STATUS</option>
                    <option value="0">PENDING</option>
                    <option value="1">ACTIVE</option>
                    <option value="2">SUSPEND</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="form-check-input" id="head-cb"></th>
                        <th>Inet</th>
                        <th>ID Pelanggan</th>
                        <th>Nama Pelanggan</th>
                        <th>Username</th>
                        <th>Profile</th>
                        <th>POP</th>
                        <th>ODP</th>
                        <th>NAS</th>
                        <th>Jatuh Tempo</th>
                        {{-- <th>Status</th> --}}
                        {{-- <th>Tipe Billing</th> --}}
                        <th>Next Invoice</th>
                        <th>Mitra</th>
                        <th>Created</th>
                        <th>Whatsapp</th>
                        {{-- <th>Owner</th> --}}
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
    
    const baseurl = "{{ url('/') }}" //baseUrl.clone().pop().pop().pop().pop();
    document.addEventListener("DOMContentLoaded", function() {
        const delay = 1000; // Delay dalam milidetik (1.5 detik)
        // Pilih semua elemen yang memiliki atribut data-value
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
        scrollX: true,
        order: [
            [12, 'desc']
        ],
        lengthMenu: [10, 100, 500, 1000, 2000],
        ajax: '{{ url()->current() }}',
        columns: [{
                data: 'checkbox',
                'sortable': false,
                name: 'checkbox',
            },

            {
                data: 'inet',
                name: 'inet',
                sortable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    if (row.session.session_id !== null && row.session.status === 1 && row.session
                        .ip !== null && row.status === 1) {
                        return '<a href="javascript:void(0)" title="LIHAT SESSION" class="material-symbols-outlined text-success fw-bold" id="show_session" data-username=' +
                            row.username + '>globe</a>'
                    } else if (row.session.session_id !== null && row.session.status === 2 && row
                        .session
                        .ip !== null) {
                        return '<a href="javascript:void(0)" title="LIHAT SESSION" class="material-symbols-outlined text-danger fw-bold" id="show_session" data-username=' +
                            row.username + '>globe</a>'
                    } else if (row.session.session_id !== null && row.session.status === 1 && row
                        .session
                        .ip !== null && row.status === 2) {
                        return '<a href="javascript:void(0)" title="LIHAT SESSION" class="material-symbols-outlined text-danger fw-bold" id="show_session" data-username=' +
                            row.username + '>block</a>'
                    } else {
                        return '<span class="material-symbols-outlined text-danger fw-bold">help</span>'
                    }
                },
            },
            // {
            //     data: 'id_pelanggan',
            //     name: 'id_pelanggan',
            //     render: function(data, type, row) {
            //         // 1 aktif // 2 isolir // 0 off
            //         if (row.status === 1) {
            //             return `<a data-toggle="popover" title="Detail" data-bs-content="<table class='table table-hover'><tr><td>Data 1</td><td>Data 2</td></tr></table>">Klik untuk melihat detail</a>`
            //             // return '<span class="text-primary fw-bold"><span class="material-symbols-outlined">check_circle</span> ' +
            //             //     data + '</span>'
            //             // isolir
            //         } else if (row.status === 2) {
            //             return '<span class="text-danger fw-bold"><span class="material-symbols-outlined">block</span> ' +
            //                 data + '</span>'
            //             // pending
            //         } else if (row.status === 0) {
            //             return '<span class="text-warning fw-bold"><span class="material-symbols-outlined">timer</span> ' +
            //                 data + '</span>'
            //         }
            //     }
            // },
            {
                data: 'id_pelanggan',
                name: 'id_pelanggan',
                render: function(data, type, row) {
                    // Tentukan icon dan kelas warna berdasarkan status pelanggan
                    var icon, color;
                    if (row.status === 1) {
                        icon = '<span class="material-symbols-outlined">task_alt</span>';
                        // color = 'text-primary';
                    } else if (row.status === 2) {
                        icon = '<span class="material-symbols-outlined">block</span>';
                        color = 'text-danger';
                    } else if (row.status === 0) {
                        icon = '<span class="material-symbols-outlined">pace</span>';
                        color = 'text-warning';
                    }

                    // Siapkan konten HTML untuk popover berupa table
                    var popContent =
                        '<table class="table table-flush table-stripped" style="margin-top:8px;margin-bottom:1px;border:none!important">' +
                        '<tr><th class="m-0 py-1 px-2" style="border:none!important">Server [NAS]</th><td style="border:none!important"class="m-0 py-1 px-2" >' +
                        (row.mnas && row.mnas.name ? row.mnas.name : 'Semua Server [ALL]') +
                        '</td></tr>' +

                        '<tr><th class="m-0 py-1 px-2" style="border:none!important">Username</th><td style="border:none!important"class="m-0 py-1 px-2" >' +
                        row
                        .username + '</td></tr>' +
                        '<tr><th class="m-0 py-1 px-2" style="border:none!important">Password</th><td style="border:none!important" class="m-0 py-1 px-2" >' +
                        row
                        .value + '</td></tr>' +
                        '<tr><th class="m-0 py-1 px-2" style="border:none!important">Nomor HP</th><td style="border:none!important" class="m-0 py-1 px-2" >' +
                        (row
                            .wa || '-') + '</td></tr>' +
                        '<tr><th class="m-0 py-1 px-2" style="border:none!important">Tipe Billing</th><td style="border:none!important" class="m-0 py-1 px-2" >' +
                        (row
                            .payment_type || '-') + '</td></tr>' +
                        '<tr><th class="m-0 py-1 px-2" style="border:none!important">Siklus Tagihan</th><td style="border:none!important" class="m-0 py-1 px-2" >' +
                        (row
                            .billing_period || '-') + '</td></tr>' +
                        '<tr><th class="m-0 py-1 px-2" style="border:none!important">Status</th><td style="border:none!important" class="m-0 py-1 px-2">' +
                        (row.status == 1 ? 'Aktif' : (row.status == 0 ? 'Pending' : (row.status == 2 ?
                            'Suspend' : 'Unknown'))) +
                        '</td></tr>'

                    '</table>';

                    // Kembalikan elemen anchor dengan atribut popover
                    return '<a href="javascript:void(0)"' +
                        'data-toggle="popover" ' +
                        'data-html="true" ' +
                        'data-bs-content=\'' + popContent + '\' ' +
                        'title="<div class=\'bg-body-tertiary p-3\' style=\'margin:-15px;margin-bottom:-5px\'> ' +
                        row.full_name + ' [' + row.id_pelanggan + ']</div>">' +
                        '<span class="' + color + '">' +
                        icon + ' ' + data +
                        '</span></a>';
                }
            },

            {
                data: 'full_name',
                name: 'full_name',
            },
            {
                data: 'username',
                name: 'username',
                visible: false,
            },
            {
                data: 'profile_name',
                name: 'profile_name',
                render: function(data, type, row) {
                    if (data === null) {
                        return '<i class="text-danger">Unknown</i>'
                    } else {
                        return data
                    }
                },
            },
            {
                data: 'kode_area',
                name: 'kode_area',
                render: function(data, type, row) {
                    if (data === null) {
                        return '-'
                    } else {
                        return '<span class="material-symbols-outlined">map</span>' + data;
                    }
                },
            },
            {
                data: 'kode_odp',
                name: 'kode_odp',
                render: function(data, type, row) {
                    if (data === null) {
                        return '-'
                    } else {
                        return '<span class="material-symbols-outlined">distance</span>' + data;
                    }
                },
            },
            {
                data: 'mnas.name',
                name: 'mnas.name',
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    if (row.nas === null) {
                        return 'ALL'
                    } else if (row.nas_name === null) {
                        return '<i class="text-danger">Unknown</i>'
                    } else {
                        return data
                    }
                },
            },

            {
                data: 'next_due',
                name: 'next_due',
                render: function(data, type, row, meta) {
                    if (data === null) {
                        return '-';
                    } else {
                        var formattedDate = moment(data).local().format('DD/MM/YYYY');

                        // Cek apakah tanggal sudah lewat dari hari ini
                        if (moment(data).isBefore(moment(), 'day')) {
                            return '<span class="text-danger">' + formattedDate + '</span>';
                        } else {
                            return formattedDate;
                        }
                    }
                }

            },
            // {
            //     data: 'status',
            //     name: 'status',
            //     render: function(data, type, row) {
            //         // 1 aktif // 2 isolir // 0 off
            //         if (row.status === 1) {
            //             return '<span class="badge bg-success-subtle text-success">AKTIF</span>'
            //             // isolir
            //         } else if (row.status === 2) {
            //             return '<span class="badge bg-danger-subtle text-danger">SUSPEND</span>'
            //             // pending
            //         } else if (row.status === 0) {
            //             return '<span class="badge bg-danger-subtle text-warning">PENDING</span>'
            //         }
            //     }
            // },
            // {
            //     data: 'payment_type',
            //     name: 'payment_type',
            //     render: function(data, type, row) {
            //         if (data === 'Prabayar') {
            //             return '<span class="badge bg-success">PRABAYAR</span>'
            //         } else if (data === 'Pascabayar' && row.billing_period == 'Fixed Date') {
            //             return '<span class="badge bg-warning">PASCABAYAR</span>'
            //         } else if (data === 'Pascabayar' && row.billing_period == 'Billing Cycle') {
            //             return '<span class="badge bg-danger">PASCABAYAR</span>'
            //         } else {
            //             return '-';
            //         }

            //     },
            // },
            {
                data: 'next_invoice',
                name: 'next_invoice',
                render: function(data, type, row, meta) {
                    if (data === null) {
                        return '-';
                    } else {
                        var formattedDate = moment(data).local().format('DD/MM/YYYY');

                        // Cek apakah tanggal sudah lewat dari hari ini
                        if (moment(data).isBefore(moment(), 'day')) {
                            return '<span class="text-danger">' + formattedDate + '</span>';
                        } else {
                            return formattedDate;
                        }
                    }
                }

            },
            {
                data: 'mitra',
                name: 'mitra',
                render: function(data, type, row) {
                    if (row.mitra_id === null || row.mitra_id === 0) {
                        return '-'
                    } else if (row.mitra === null) {
                        return '<i class="text-danger">Unknown</i>'
                    } else {
                        return data
                    }
                },
            },
            {
                data: 'created_at',
                name: 'created_at',
                render: function(data, type, row, meta) {
                    return moment(data).local().format('DD/MM/YYYY HH:mm:ss');
                },
            },

            {
                data: 'wa',
                name: 'wa',
                visible: false,
            },

            {
                data: 'email',
                name: 'email',
                visible: false,
            },
            // {
            //     data: 'created_by',
            //     name: 'created_by',
            // },
        ],
        drawCallback: function(settings) {
            $('[data-toggle="popover"]').popover({
                html: true,
                sanitize: false,
                trigger: 'hover',
            });
        }
    });

    table.on('preXhr.dt', function(e, settings, data) {
        data.area = $('#filter_area').val();
        data.status = $('#filter_status').val();
        data.nas = $('#filter_nas').val();
        data.mitra = $('#filter_mitra').val();
    });

    $('#filter_nas,#filter_area,#filter_status,#filter_mitra').change(function() {
        table.ajax.reload();
        return false;
    });

    // Variabel global untuk menyimpan baris yang terpilih
    var selectedRows = {};

    // Fungsi untuk memperbarui tampilan row count
    function updateRowCount() {
        $(".row-count").html(Object.keys(selectedRows).length);
    }

    // Event listener untuk checkbox per baris
    $('#myTable').on('click', '.row-cb', function() {
        var rowId = $(this).val();

        if ($(this).is(':checked')) {
            selectedRows[rowId] = true;
        } else {
            delete selectedRows[rowId];
        }
        updateRowCount();

        // Update header checkbox (hanya untuk halaman aktif)
        var allChecked = $('.row-cb').length > 0 && $('.row-cb:checked').length === $('.row-cb').length;
        $('#head-cb').prop('checked', allChecked);

        // Update status tombol enable
        $('#action').prop('disabled', Object.keys(selectedRows).length === 0);
    });

    $('#head-cb').on('click', function() {
        var isChecked = $(this).is(':checked');
        $('.row-cb').each(function() {
            var rowId = $(this).val();
            $(this).prop('checked', isChecked);
            if (isChecked) {
                selectedRows[rowId] = true;
            } else {
                delete selectedRows[rowId];
            }
        });

        updateRowCount();
        $('#action').prop('disabled', Object.keys(selectedRows).length === 0);
    });

    // Saat DataTable melakukan redraw (misalnya pada pagination), perbarui checkbox di halaman baru
    table.on('draw', function() {
        $('.row-cb').each(function() {
            var rowId = $(this).val();
            $(this).prop('checked', !!selectedRows[rowId]);
        });

        var allChecked = $('.row-cb').length > 0 && $('.row-cb:checked').length === $('.row-cb').length;
        $('#head-cb').prop('checked', allChecked);
    });

    $('#edit').on('hidden.bs.modal', function() {
        // Hapus centang pada semua checkbox di dalam tabel
        $('#myTable tbody .row-cb').prop('checked', false);
    });
    $('#myTable tbody').on('click', 'tr td:not(:nth-child(1)):not(:nth-child(2)):not(:nth-child(3))', function() {

        let row_user_id = table.row(this).data().id;
        let row_username = table.row(this).data().username;
        let row_kode_area_id = table.row(this).data().rarea.id;
        let row_kode_odp = table.row(this).data().kode_odp;
        let row_profile_id = table.row(this).data().profile_id;

        $.ajax({
            url: `/pppoe/user/${row_user_id}`,
            type: "GET",
            success: function(data) {
                $('#user_id').val(data.data.id),
                $('#kode_area_id').val(row_kode_area_id),
                $('#kode_odp_id').val(row_kode_odp),
                $('#profile_id').val(row_profile_id),
                $('#username_edit').val(data.data.username),
                $('#password_edit').val(data.data.value),
                $('#profile_edit').val(data.data.profile_id).trigger('change'),
                $('#mitra_id_edit').val(data.data.mitra_id),
                $('#nas_edit').val(data.data.nas);
                $('#kode_area_edit').val(data.data.rarea.id).trigger('change');

                $('#nas_secret').val(data.data.mnas.secret);

                if (data.data.payment_type == null) {
                    $('#show_billing_edit').hide();
                    $('#option_billing_edit').val('0').prop('checked', false);
                    $('#show_reg_date_edit').hide();
                } else {
                    $('#show_billing_edit').show();
                    $('#option_billing_edit').val('1').prop('checked', true);
                    $('#show_reg_date_edit').show();
                }
                if (data.data.payment_type === 'Prabayar') {
                    const $select = $('#billing_period_edit');

                    // Kosongkan dulu semua opsi
                    $select.empty();

                    // Tambahkan hanya opsi yang dibutuhkan
                    $select.append(new Option('Fixed Date', 'Fixed Date'));
                    $select.append(new Option('Renewable', 'Renewable'));
                }else if(data.data.payment_type === 'Pascabayar') {
                    const $select = $('#billing_period_edit');

                    // Kosongkan dulu semua opsi
                    $select.empty();

                    // Tambahkan hanya opsi yang dibutuhkan
                    $select.append(new Option('Fixed Date', 'Fixed Date'));
                    $select.append(new Option('Billing Cycle', 'Billing Cycle'));
                }
                
                $("#option_billing_edit").click(function() {
                    if ($("#option_billing_edit").prop("checked")) {
                        $("#option_billing_edit").val(1);
                        $("#show_billing_edit").show();
                    } else {
                        $("#option_billing_edit").val(0);
                        $("#show_billing_edit").hide();
                    }
                });
                if (data.data.status === 2) {
                    $('#enable').removeClass('disabled');
                    $('#disable').addClass('disabled');
                    $('#regist').addClass('disabled');
                } else if (data.data.status === 1) {
                    $('#enable').addClass('disabled');
                    $('#disable').removeClass('disabled');
                    $('#regist').addClass('disabled');
                } else if (data.data.status === 0) {
                    $('#enable').addClass('disabled');
                    $('#disable').addClass('disabled');
                    $('#regist').removeClass('disabled');
                }
                if (data.data.lock_mac === 1) {
                    $('#show_mac_edit').show();
                } else {
                    $('#show_mac_edit').hide();
                };
                $('#lock_mac_edit').change(function() {
                    let lock_mac_edit = $(this).val(); //Get selected option value
                    if (lock_mac_edit == '1') {
                        $('#show_mac_edit').show()
                    } else {
                        $('#show_mac_edit').hide()
                    }
                });
                $('#lock_mac_edit').val(data.data.lock_mac),
                    $('#mac_edit').val(data.data.mac);

                $('#full_name_edit').val(data.data.full_name);
                $('#id_pelanggan_edit').val(data.data.id_pelanggan);
                $('#wa_edit').val(data.data.wa);
                $('#address_edit').val(data.data.address);
                $('#payment_type_edit').val(data.data.payment_type);
                $('#reg_date_edit').val(data.data.reg_date);
                $('#next_due_edit').val(data.data.next_due);
                // $('#next_invoice').val(data.data.next_invoice);
                $('#billing_period_edit').val(data.data.billing_period);
                $('#ppn_edit').val(data.data.ppn);
                $('#discount_edit').val(data.data.discount);
                $('#ktp_edit').val(data.data.ktp);
                $('#npwp_edit').val(data.data.npwp);
                $('#email_edit').val(data.data.email);
                $('#latitude_edit').val(data.data.latitude);
                $('#longitude_edit').val(data.data.longitude);
                set_maps_edit(data.latitude,data.longitude);

                $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/provinces',
                type: 'GET',
                dataType: "json",
                success: function(response) {
                    let provinceOptions = response.map((item) => {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    }).sort((a, b) => a.text.localeCompare(b.text));

                    $('#provinsi_edit').select2({
                        data: provinceOptions,
                        allowClear: true,
                        placeholder: $('#provinsi_edit').data('placeholder'),
                        dropdownParent: $("#edit .modal-content"),
                    });

                    // Set default value sesuai dengan village_id
                    if (data.data.province_id) {
                        $('#provinsi_edit').val(data.data.province_id).trigger('change');
                    }
                }
            });
            
            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/regencies/' + data.data.province_id,
                type: 'GET',
                dataType: "json",
                success: function(response) {
                    let regencyOptions = response.map((item) => {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    }).sort((a, b) => a.text.localeCompare(b.text));

                    $('#kabupaten_edit').select2({
                        data: regencyOptions,
                        allowClear: true,
                        placeholder: $('#kabupaten_edit').data('placeholder'),
                        dropdownParent: $("#edit .modal-content"),
                    });

                    // Set default value sesuai dengan village_id
                    if (data.data.regency_id) {
                        $('#kabupaten_edit').val(data.data.regency_id).trigger('change');
                    }
                }
            });

            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/districts/' + data.data.regency_id,
                type: 'GET',
                dataType: "json",
                success: function(response) {
                    let districtOptions = response.map((item) => {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    }).sort((a, b) => a.text.localeCompare(b.text));

                    $('#kecamatan_edit').select2({
                        data: districtOptions,
                        allowClear: true,
                        placeholder: $('#kecamatan_edit').data('placeholder'),
                        dropdownParent: $("#edit .modal-content"),
                    });

                    // Set default value sesuai dengan village_id
                    if (data.data.district_id) {
                        $('#kecamatan_edit').val(data.data.district_id).trigger('change');
                    }
                }
            });

            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/villages/' + data.data.district_id,
                type: 'GET',
                dataType: "json",
                success: function(response) {
                    let villageOptions = response.map((item) => {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    }).sort((a, b) => a.text.localeCompare(b.text));

                    $('#desa_edit').select2({
                        data: villageOptions,
                        allowClear: true,
                        placeholder: $('#desa_edit').data('placeholder'),
                        dropdownParent: $("#edit .modal-content"),
                    });

                    // Set default value sesuai dengan village_id
                    if (data.data.village_id) {
                        $('#desa_edit').val(data.data.village_id).trigger('change');
                    }
                }
            });
            }
        });

        $('#edit').modal('show');

    });

    $('#kode_area').on('change', function() {
        var kode_area = $(this).val();
        if (kode_area) {
            $.ajax({
                url: `/pppoe/user/getKodeOdp/${kode_area}`,
                type: 'GET',
                data: {
                    kode_area_id: kode_area,
                    '_token': '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $('kode_odp').empty();
                        $('#kode_odp').attr('disabled', false);
                        $('#kode_odp').html(
                            '<option value="">Pilih Kode ODP</option>');
                        $.each(data.odp, function(key, value) {
                            $("#kode_odp").append('<option value="' + value
                                .kode_odp + '">' + value.kode_odp +
                                '</option>');
                        });

                    } else {
                        $('kode_odp').empty();
                    }
                }
            });
        } else {
            $('kode_odp').empty();
        }
    });

    $('#kode_area_edit').on('change', function() {
        var kode_area = $(this).val();
        if (kode_area) {
            $.ajax({
                url: `/pppoe/user/getKodeOdp/${kode_area}`,
                type: 'GET',
                data: {
                    kode_area_id: kode_area,
                    '_token': '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $('#kode_odp_edit').attr(
                            'disabled',
                            false);
                        $('#kode_odp_edit').html(
                            '<option value="">Pilih Kode ODP</option>'
                        );
                        $.each(data.odp, function(key,
                            value) {
                            $("#kode_odp_edit")
                                .append(
                                    '<option value="' +
                                    value
                                    .kode_odp +
                                    '">' + value
                                    .kode_odp +
                                    '</option>'
                                );
                        });

                    } else {
                        $('kode_odp_edit').empty();
                    }
                }
            });
        } else {
            $('kode_odp_edit').empty();
        }
    });

    $("#profile").on("change", function() {
        var profile_id = $(this).val();
        $.ajax({
            url: `/pppoe/user/getPrice/${profile_id}`,
            type: "GET",
            cache: false,
            data: {
                id: profile_id,
                '_token': '{{ csrf_token() }}'
            },
            success: function(data) {
                var amount = data[0].price.toString();;
                rp_amount = formatRupiah(amount, 2, ',', '.');
                $('#amount').val(rp_amount);
                rp_total_amount = formatRupiah(amount, 2, ',', '.');
                $('#payment_total').val(rp_total_amount);

                if(data[0].ppn){
                    $('#ppn').val(data[0].ppn);
                    var ppn = $('#ppn').val();
                    var discount = $('#discount').val();
                    var amount_ppn = amount * ppn / 100;
                    var amount_discount = discount;
                    if (discount === '') {
                        var total_with_ppn = parseInt(amount) + parseInt(amount_ppn);
                        var total_plus_ppn = total_with_ppn.toString();
                        rp_total_plus_ppn = formatRupiah(total_plus_ppn, 2, ',', '.');
                        $('#payment_total').val(rp_total_plus_ppn);
                    } else {
                        var total_with_ppn_discount = parseInt(amount) + parseInt(
                            amount_ppn) - parseInt(amount_discount);
                        var total_plus_ppn_discount = total_with_ppn_discount.toString();
                        rp_total_plus_ppn_discount = formatRupiah(total_plus_ppn_discount,
                            2, ',', '.');
                        $('#payment_total').val(rp_total_plus_ppn_discount);
                    }
                }
                $("#ppn,#discount").on("keyup change", function() {
                    var ppn = $('#ppn').val();
                    var discount = $('#discount').val();
                    var amount_ppn = amount * ppn / 100;
                    var amount_discount = discount;
                    if (discount === '') {
                        var total_with_ppn = parseInt(amount) + parseInt(amount_ppn);
                        var total_plus_ppn = total_with_ppn.toString();
                        rp_total_plus_ppn = formatRupiah(total_plus_ppn, 2, ',', '.');
                        $('#payment_total').val(rp_total_plus_ppn);
                    } else {
                        var total_with_ppn_discount = parseInt(amount) + parseInt(
                            amount_ppn) - parseInt(amount_discount);
                        var total_plus_ppn_discount = total_with_ppn_discount.toString();
                        rp_total_plus_ppn_discount = formatRupiah(total_plus_ppn_discount,
                            2, ',', '.');
                        $('#payment_total').val(rp_total_plus_ppn_discount);
                    }
                });

            }
        });
    });

    $("#profile_edit").on("change", function() {
        var profile_idx = $(this).val();
        $.ajax({
            url: `/pppoe/user/getPrice/${encodeURIComponent(profile_idx)}`,
            type: "GET",
            cache: false,
            data: {
                id: profile_idx,
                '_token': '{{ csrf_token() }}'
            },
            success: function(data) {
                var amount = data[0].price.toString();
                rp_amount = formatRupiah(amount, 2, ',', '.');
                $('#amount_edit').val(rp_amount);
                rp_total_amount = formatRupiah(amount, 2, ',', '.');
                $('#payment_total_edit').val(rp_total_amount);
            }
        });
    });

    $('#lock_mac').change(function() {
        let lock_mac = $(this).val(); //Get selected option value
        if (lock_mac == '1') {
            $('#show_mac').show()
        } else {
            $('#show_mac').hide()
        }
    });
    $("#option_member").click(function() {
        if ($("#option_member").prop("checked")) {
            $("#option_member").val(1);
            $("#show_member").show();
        } else {
            $("#option_member").val(0);
            $("#show_member").hide();
        }
    });
    $('#payment_type,#payment_type_edit').change(function() {
        let payment_type = $(this).val(); //Get selected option value
        if (payment_type == 'Prabayar') {
            $('#show_payment_status').show()
            $('#billing_period,#billing_period_edit').html(
                "<option value='Fixed Date'>Fixed Date</option><option value='Renewable'>Renewable</option>"
            );
        } else if (payment_type == 'Pascabayar') {
            $('#show_payment_status').hide()
            $('#billing_period,#billing_period_edit').html(
                "<option value='Fixed Date'>Fixed Date</option><option value='Billing Cycle'>Billing Cycle</option>"
            );
        }
    });

    $('#myTable').on('click', '#show_session', function() {
        let username = $(this).data("username");
        $("#session_username").val(username);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: `/pppoe/user/getSession/${username}`,
            type: 'GET',
            data: {
                username: username
            },
            success: function(response) {
                $('#sessionTable').DataTable({
                    data: response,
                    scrollX: true,
                    pageLength: 5,
                    lengthMenu: [
                        [5, 10, 20],
                        [5, 10, 20]
                    ],
                    destroy: true,
                    order: [
                        [0, 'desc']
                    ],
                    columns: [

                        {
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
                            data: 'username',
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
                                if (data == 0) return 'n/a';
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
                                if (data == 0) return 'n/a';
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
                $('#show_session').modal('show')

            }
        });

    });

    $('#edit').on('shown.bs.modal', function() {

        let kode_area_id = $('#kode_area_id').val();
        let kode_odp = $('#kode_odp_id').val();
        if (kode_area_id) {
            $.ajax({
                url: `/pppoe/user/getKodeOdp/${kode_area_id}`,
                type: 'GET',
                data: {
                    kode_area_id: kode_area_id,
                    '_token': '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $('#kode_odp_edit').attr(
                            'disabled',
                            false);
                        $('#kode_odp_edit').val(kode_odp).trigger('change');
                    } else {
                        $('kode_odp_edit').empty();
                    }
                }
            });
        } else {
            $('kode_odp_edit').empty();
        };
        var profile_id = $('#profile_id').val();
        $.ajax({
            url: `/pppoe/user/getPrice/${profile_id}`,
            type: "GET",
            cache: false,
            data: {
                id: profile_id,
                '_token': '{{ csrf_token() }}'
            },
            success: function(data) {
                var amount = data[0].price;
                var ppn = $('#ppn_edit').val();
                var discount = $('#discount_edit').val();
                var amount_ppn = amount * ppn / 100;
                var amount_discount = discount;
                $('#amount_edit').val(rp_amount);

                if (discount === '') {
                    var total_with_ppn = parseInt(amount) + parseInt(amount_ppn);
                    var total_plus_ppn = total_with_ppn.toString();
                    rp_total_plus_ppn = formatRupiah(total_plus_ppn, 2, ',', '.');
                    $('#payment_total_edit').val(rp_total_plus_ppn);
                } else {
                    var total_with_ppn_discount = parseInt(amount) + parseInt(
                        amount_ppn) - parseInt(amount_discount);
                    var total_plus_ppn_discount = total_with_ppn_discount.toString();
                    rp_total_plus_ppn_discount = formatRupiah(total_plus_ppn_discount,
                        2, ',', '.');
                    $('#payment_total_edit').val(rp_total_plus_ppn_discount);
                }

                // rp_amount = formatRupiah(amount, 2, ',', '.');
                // rp_total_amount = formatRupiah(amount, 2, ',', '.');
                // $('#payment_total_edit').val(rp_total_amount);

                $("#ppn_edit,#discount_edit").on("keyup change", function() {
                    var ppn = $('#ppn_edit').val();
                    var discount = $('#discount_edit').val();
                    var amount_ppn = amount * ppn / 100;
                    var amount_discount = discount ;
                    if (discount === '') {
                        var total_with_ppn = parseInt(amount) + parseInt(amount_ppn);
                        var total_plus_ppn = total_with_ppn.toString();
                        rp_total_plus_ppn = formatRupiah(total_plus_ppn, 2, ',', '.');
                        $('#payment_total_edit').val(rp_total_plus_ppn);
                    } else {
                        var total_with_ppn_discount = parseInt(amount) + parseInt(
                            amount_ppn) - parseInt(amount_discount);
                        var total_plus_ppn_discount = total_with_ppn_discount.toString();
                        rp_total_plus_ppn_discount = formatRupiah(total_plus_ppn_discount,
                            2, ',', '.');
                        $('#payment_total_edit').val(rp_total_plus_ppn_discount);
                    }
                });

            }
        });
    });

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
            'option_billing': $('#option_billing').val(),
            'username': $('#username').val(),
            'password': $('#password').val(),
            'profile': $('#profile option:selected').text(),
            'nas': $('#nas').val(),
            'kode_area': $('#kode_area option:selected').text(),
            'kode_odp': $('#kode_odp option:selected').val(),
            'lock_mac': $('#lock_mac').val(),
            'mac': $('#mac').val(),
            'profile_id': $('#profile option:selected').val(),
            'id_pelanggan': $('#id_pelanggan').val(),
            'full_name': $('#full_name').val(),
            'wa': $('#wa').val(),
            'email': $('#email').val(),
            'ktp': $('#ktp').val(),
            'npwp': $('#npwp').val(),
            'mitra_id': $('#mitra_id').val(),
            'address': $('#address').val(),
            'payment_type': $('#payment_type').val(),
            'payment_status': $('#payment_status').val(),
            'billing_period': $('#billing_period').val(),
            'reg_date': $('#reg_date').val(),
            'ppn': $('#ppn').val(),
            'discount': $('#discount').val(),
            'amount': $('#amount').val(),
            'payment_total': $('#payment_total').val(),
            'status': $("input[name='reg_status']:checked").val(),
            'province_id': $('#provinsi').val(),
            'regency_id': $('#kabupaten').val(),
            'district_id': $('#kecamatan').val(),
            'village_id': $('#desa').val(),
            'longitude': $('#longitude').val(),
            'latitude': $('#latitude').val(),
        };

        // Setup CSRF token untuk AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Simpan konten asli tombol untuk dikembalikan nanti
        var originalBtnContent = $('#store').html();

        // Suspend tombol dan tampilkan spinner
        $('#store').prop('disabled', true).html(
            'Processing&nbsp;<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
        );

        // Lakukan request AJAX
        $.ajax({
            url: `/pppoe/user`,
            type: "POST",
            cache: false,
            data: data,
            dataType: "json",
            success: function(data) {
                if (data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: data.error,
                        showConfirmButton: false,
                        timer: 5000
                    });
                    $('#store').prop('disabled', false).html(originalBtnContent);
                }
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
                        // Reset form fields
                        $('#username').val('');
                        $('#password').val('');
                        $('#full_name').val('');
                        $('#wa').val('');
                        $('#ppn').val('');
                        $('#discount').val('');
                        $('#amount').val('');
                        $('#payment_total').val('');
                        $('textarea').val('');
                        $('#profile').val('');
                        $('#nas').val('');
                        $('#kode_area').val('').trigger('change');
                        $('#kode_odp').val('').trigger('change');
                        // Update nilai total di halaman
                        $('#totaluser').html(data.totaluser);
                        $('#totalactive').html(data.totalactive);
                        $('#totalsuspend').html(data.totalsuspend);
                        $('#totaldisabled').html(data.totaldisabled);
                        $('#create').modal('hide');
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

                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Gagal membuat user, silakan cek data yang diinput',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#store').prop('disabled', false).html(originalBtnContent);
                }
            },
            error: function(err) {
                $("#message").html("Some Error Occurred!");
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Gagal membuat user, silakan cek data yang diinput',
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#store').prop('disabled', false).html(originalBtnContent);
            }
        });
    });

    $('#update').click(function(e) {
        e.preventDefault();

        // Hapus pesan error yang ada
        var error_ele = document.getElementsByClassName('alert text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        let user_id = $('#user_id').val();

        // Kumpulkan data dari form
        var data = {
            'option_billing': $('#option_billing_edit').val(),
            'username': $('#username_edit').val(),
            'password': $('#password_edit').val(),
            'profile': $('#profile_edit option:selected').text(),
            'nas': $('#nas_edit').val(),
            'kode_area': $('#kode_area_edit option:selected').text(),
            'kode_odp': $('#kode_odp_edit option:selected').val(),
            'lock_mac': $('#lock_mac_edit').val(),
            'mac': $('#mac_edit').val(),
            'profile_id': $('#profile_edit option:selected').val(),
            'full_name': $('#full_name_edit').val(),
            'wa': $('#wa_edit').val(),
            'email': $('#email_edit').val(),
            'ktp': $('#ktp_edit').val(),
            'npwp': $('#npwp_edit').val(),
            'mitra_id': $('#mitra_id_edit').val(),
            'address': $('#address_edit').val(),
            'ppn': $('#ppn_edit').val(),
            'discount': $('#discount_edit').val(),
            'amount': $('#amount_edit').val(),
            'payment_total': $('#payment_total_edit').val(),
            'billing_period': $('#billing_period_edit').val(),
            'next_due': $('#next_due_edit').val(),
            'payment_type': $('#payment_type_edit').val(),
            'reg_date': $('#reg_date_edit').val(),
            'province_id': $('#provinsi_edit').val(),
            'regency_id': $('#kabupaten_edit').val(),
            'district_id': $('#kecamatan_edit').val(),
            'village_id': $('#desa_edit').val(),
            'longitude': $('#longitude_edit').val(),
            'latitude': $('#latitude_edit').val(),
        };
        console.log(data);

        // Setup CSRF token untuk AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Simpan konten asli tombol update
        var originalBtnContent = $('#update').html();

        // Suspend tombol dan tampilkan spinner
        $('#update').prop('disabled', true).html(
            'Processing&nbsp;<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
        );

        $.ajax({
            url: `/pppoe/user/${user_id}`,
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
                        $('#totaluser').html(data.totaluser);
                        $('#totalactive').html(data.totalactive);
                        $('#totalsuspend').html(data.totalsuspend);
                        $('#totaldisabled').html(data.totaldisabled);
                        $('#edit').modal('hide');
                        $('#update').prop('disabled', false).html(originalBtnContent);
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        el.after($('<span class="alert text-sm text-danger">' + value[0] +
                            '</span>'));
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Failed to edit user, please check your field',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#update').prop('disabled', false).html(originalBtnContent);
                }
            },
            error: function(err) {
                $("#message").html("Some Error Occurred!");
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Data Gagal Disimpan!',
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#update').prop('disabled', false).html(originalBtnContent);
            }
        });
    });

    $('#kode_area').select2({
        allowClear: true,
        dropdownParent: $("#create .modal-content"),
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
    });

    $('#kode_area_edit').select2({
        allowClear: true,
        dropdownParent: $("#edit .modal-content"),
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
    });
    $('#kode_odp').select2({
        allowClear: true,
        dropdownParent: $("#create .modal-content"),
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
    });
    $('#kode_odp_edit').select2({
        allowClear: true,
        dropdownParent: $("#edit .modal-content"),
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
    });

    // var multipleCancelButton = new Choices(
    //     '#profile', {
    //         allowHTML: true,
    //         removeItemButton: true,
    //     }
    // );
    $('#profile').select2({
        allowClear: true,
        dropdownParent: $("#create .modal-content"),
        width: '100%',
        placeholder: $(this).data('placeholder'),
    });
    $('#create').on('shown.bs.modal', function() {
        document.getElementById('reg_date').valueAsDate = new Date();
        var prefix = '1';
        var rand = Math.round(Math.random() * 999999999);
        var number = prefix + rand;
        $('#id_pelanggan').val(number);
    });

    function kapital() {
        let x = document.getElementById("full_name");
        let y = document.getElementById("address");
        x.value = x.value.toUpperCase();
        y.value = y.value.toUpperCase();
    }

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

    $('#enable,#enableMassal').on('click', function() {
        Swal.fire({
            title: "Aktifkan User?",
            icon: 'warning',
            text: "User yang dipilih akan diaktifkan",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Ya, Aktifkan",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                // Tampilkan spinner pada elemen total
                $('#totaluser').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');
                $('#totalactive').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');
                $('#totalsuspend').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');
                $('#totaldisabled').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');

                let ids = Object.keys(selectedRows);
                if (ids.length === 0) {
                    let id = $("#user_id").val(); // Ganti dengan nilai atau logika yang sesuai
                    ids = [id]; // Menetapkan nilai fallback ke ids
                }

                // Setup CSRF token untuk AJAX
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Jalankan AJAX dan kembalikan promise-nya
                return $.ajax({
                    url: `/pppoe/user/enable`,
                    type: "POST",
                    cache: false,
                    data: {
                        ids: ids
                    },
                    dataType: "json"
                }).then(response => {
                    if (!response.success) {
                        return Promise.reject(response.error || 'Unknown error');
                    }
                    return response;
                }).catch(error => {
                    // Tampilkan pesan error yang lebih informatif
                    let errMsg = error.responseText || error.statusText || JSON.stringify(
                        error) || "Unknown error";
                    Swal.showValidationMessage(`Request failed: ${errMsg}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: result.value.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(function() {
                    table.ajax.reload();
                    $('#totaluser').html(result.value.totaluser);
                    $('#totalactive').html(result.value.totalactive);
                    $('#totalsuspend').html(result.value.totalsuspend);
                    $('#totaldisabled').html(result.value.totaldisabled);
                    $('#edit').modal('hide');
                }, 1500);
                // Reset data global dan tampilan
                selectedRows = {};
                updateRowCount();
                $('#head-cb').prop('checked', false);
            }
        });
    });

    $('#disable, #disableMassal').on('click', function() {
        Swal.fire({
            title: "Suspend User?",
            icon: 'warning',
            text: "User yang dipilih akan dinonaktifkan",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Ya, Suspend",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                // Tampilkan spinner pada elemen total
                $('#totaluser').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');
                $('#totalactive').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');
                $('#totalsuspend').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');
                $('#totaldisabled').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');

                let ids = Object.keys(selectedRows);
                if (ids.length === 0) {
                    let id = $("#user_id").val(); // Ganti dengan nilai atau logika yang sesuai
                    ids = [id]; // Menetapkan nilai fallback ke ids
                }

                // Setup CSRF token
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Jalankan AJAX dan kembalikan promise-nya
                return $.ajax({
                    url: `/pppoe/user/disable`,
                    type: "POST",
                    cache: false,
                    data: {
                        ids: ids
                    },
                    dataType: "json"
                }).then(data => {
                    if (!data.success) {
                        throw new Error(data.error || "Unknown error");
                    }
                    return data;
                }).catch(error => {
                    let errMsg = error.responseText || error.statusText || JSON.stringify(
                        error) || "Unknown error";
                    Swal.showValidationMessage(`Request failed: ${errMsg}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(result => {
            if (result.isConfirmed && result.value) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: result.value.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(function() {
                    table.ajax.reload();
                    $('#totaluser').html(result.value.totaluser);
                    $('#totalactive').html(result.value.totalactive);
                    $('#totalsuspend').html(result.value.totalsuspend);
                    $('#totaldisabled').html(result.value.totaldisabled);
                    $('#edit').modal('hide');
                }, 1500);
                // Reset data global dan tampilan
                selectedRows = {};
                updateRowCount();
                $('#head-cb').prop('checked', false);
            }
        });
    });

    $('#regist, #registMassal').on('click', function() {
        Swal.fire({
            title: "Proses Registrasi?",
            icon: 'warning',
            text: "User yang dipilih akan diaktifkan",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Ya, Aktifkan",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                // Setup CSRF token untuk AJAX
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                let ids = Object.keys(selectedRows);
                if (ids.length === 0) {
                    let id = $("#user_id").val(); // Ganti dengan nilai atau logika yang sesuai
                    ids = [id]; // Menetapkan nilai fallback ke ids
                }
                // Tampilkan spinner pada elemen totaluser, totalactive, totalsuspend, dan totaldisabled
                $('#totaluser').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');
                $('#totalactive').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');
                $('#totalsuspend').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');
                $('#totaldisabled').html(
                    '<span class="material-symbols-outlined spinner">progress_activity</span>');
                // Jalankan AJAX dan kembalikan promise-nya
                return $.ajax({
                    url: `/pppoe/user/regist`,
                    type: "POST",
                    cache: false,
                    data: {
                        ids: ids
                    },
                    dataType: "json"
                }).then(response => {
                    if (response.success) {
                        return response;
                    } else {
                        Swal.showValidationMessage(`Error: ${response.error}`);
                    }
                }).catch(error => {
                    let errMsg = error.responseText || error.statusText || JSON.stringify(
                        error) || "Unknown error";
                    Swal.showValidationMessage(`Request failed: ${errMsg}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: result.value.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(function() {
                    table.ajax.reload();
                    $('#totaluser').html(result.value.totaluser);
                    $('#totalactive').html(result.value.totalactive);
                    $('#totalsuspend').html(result.value.totalsuspend);
                    $('#totaldisabled').html(result.value.totaldisabled);
                    $('#edit').modal('hide');
                }, 1500);
                selectedRows = {};
                updateRowCount();
                $('#head-cb').prop('checked', false);
            }
        });
    });

    $('#delete, #deleteMassal').on('click', function() {
        let id = $('#user_id').val();
        Swal.fire({
            title: "Hapus User?",
            icon: 'warning',
            text: "User yang dipilih akan dihapus",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Ya, Hapus",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#d33",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                let ids = Object.keys(selectedRows);
                if (ids.length === 0) {
                    let id = $("#user_id").val(); // Ganti dengan nilai atau logika yang sesuai
                    ids = [id]; // Menetapkan nilai fallback ke ids
                }

                // Setup CSRF token untuk AJAX
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Jalankan AJAX dan kembalikan promise-nya
                return $.ajax({
                    url: `/pppoe/user/delete`,
                    type: "POST",
                    cache: false,
                    data: {
                        ids: ids
                    },
                    dataType: "json"
                }).then(response => {
                    if (!response.success) {
                        return Promise.reject(response.error);
                    }
                    return response;
                }).catch(error => {
                    let errMsg = error.responseText || error.statusText || JSON.stringify(
                        error) || "Unknown error";
                    Swal.showValidationMessage(`Request failed: ${errMsg}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(result => {
            if (result.isConfirmed && result.value) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: result.value.message,
                    showConfirmButton: false,
                    timer: 1500 // Akan menutup otomatis setelah 1,5 detik
                });
                // Perbarui tabel dan elemen total
                table.ajax.reload();
                $('#totaluser').html(result.value.totaluser);
                $('#totalactive').html(result.value.totalactive);
                $('#totalsuspend').html(result.value.totalsuspend);
                $('#totaldisabled').html(result.value.totaldisabled);
                $('#edit').modal('hide');
                selectedRows = {};
                updateRowCount();
                $('#head-cb').prop('checked', false);
            }
        });
    });

    $("#option_billing").click(function() {
        if ($("#option_billing").prop("checked")) {
            $("#option_billing").val(1);
            $("#show_billing").show();
        } else {
            $("#option_billing").val(0);
            $("#show_billing").hide();
        }
    });


    function downloadExcel() {
        // Ambil tombol download (pastikan id-nya sesuai dengan tombol yang digunakan)
        var $btn = $("#downloadExcel");
        // Simpan konten asli tombol untuk dikembalikan nanti
        var originalBtnContent = $btn.html();
        // Suspend tombol dan tampilkan spinner
        $btn.prop('disabled', true).html(
            'Downloading&nbsp;<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
        );

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            xhrFields: {
                responseType: 'blob'
            },
            type: 'POST',
            url: `/pppoe/user/export`,
            data: {
                type: 'pppoe'
            },
            success: function(result, status, xhr) {
                var disposition = xhr.getResponseHeader('content-disposition');
                var matches = /"([^"]*)"/.exec(disposition);
                var filename = (matches != null && matches[1] ? matches[1] : 'pppoe_users.xlsx');

                var blob = new Blob([result], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename;

                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Kembalikan tombol ke kondisi semula
                $btn.prop('disabled', false).html(originalBtnContent);
            },
            error: function(err) {
                $("#message").html("Some Error Occurred!");
                // Kembalikan tombol ke kondisi semula jika terjadi error
                $btn.prop('disabled', false).html(originalBtnContent);
            }
        });
    }

    $('#btnImport').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        $btn.prop('disabled', true)
            .html('Importing&nbsp;<span class="material-symbols-outlined spinner">autorenew</span>');
        // Lanjutkan dengan submit form atau AJAX sesuai kebutuhan...
        $('#importForm').submit();
    });

    $(document).on('input', '.form-control', function() {
        if ($(this).val().trim() !== '') {
            $(this).removeClass('is-invalid');
            // Jika ingin menghapus pesan error (jika ada) yang muncul setelah input
            $(this).next('.form-text.text-danger').remove();
        }
    });

    $('#clear_session').on('click', function() {
        let username = $("#session_username").val();

        Swal.fire({
            title: 'Apakah anda yakin?',
            text: `Semua session "${username}" akan dihapus`,
            icon: 'warning',
            showCancelButton: true,
            showCancelButton: !0,
            reverseButtons: !0,
            confirmButtonText: "Ya, hapus",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#d33",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: `/pppoe/user/session/clear`,
                    type: "POST",
                    cache: false,
                    data: {
                        username: username
                    },
                    dataType: "json",

                    success: function(data) {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: `${data.message}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            setTimeout(function() {
                                location.reload()
                            }, 1500);
                        } else {
                            $.each(data.error, function(key, value) {
                                var el = $(document).find('[name="' + key + '"]');
                                el.after($('<span class= "text-sm text-danger">' +
                                    value[0] +
                                    '</span>'));
                            });
                        }
                    },

                    error: function(err) {
                        $("#message").html("Some Error Occurred!")
                    }
                });
            }
        });
    });

    $.ajax({
        url: baseurl + '/dinetkan/settings/master/geo/provinces',
        type: 'GET',
        dataType: "json",
        success: function(response) {
            let provinceOptions = response.map((item) => {
                return {
                    id: item.id,
                    text: item.name
                };
            }).sort((a, b) => a.text.localeCompare(b.text));

            // Tambahkan opsi pertama "--- silahkan pilih ---"
            provinceOptions.unshift({
                id: '',
                text: '--- silahkan pilih ---'
            });

            $('#provinsi').select2({
                data: provinceOptions,
                allowClear: true,
                placeholder: $('#provinsi').data('placeholder') || '--- silahkan pilih ---',
                dropdownParent: $("#create .modal-content"),
            });
        }
    });

    $('#provinsi').on('change', function() {
        $('#kabupaten').empty();
        var id_provinsi = $(this).val();
        if (id_provinsi) {
            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/regencies/' + id_provinsi,
                type: 'GET',
                dataType: "json",
                success: function(response) {
                    let regencyOptions = response.map((item) => {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    }).sort((a, b) => a.text.localeCompare(b.text));

                    // Tambahkan opsi pertama "--- silahkan pilih ---"
                    regencyOptions.unshift({
                        id: '',
                        text: '--- silahkan pilih ---'
                    });

                    $('#kabupaten').select2({
                        data: regencyOptions,
                        allowClear: true,
                        placeholder: $('#kabupaten').data('placeholder') || '--- silahkan pilih ---',
                        dropdownParent: $("#create .modal-content"),
                    });
                }
            });
        } else {
            $('#kabupaten').empty();
        }
    });

    $('#kabupaten').on('change', function() {
        $('#kecamatan').empty();
        var id_kabupaten = $(this).val();
        if (id_kabupaten) {
            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/districts/' + id_kabupaten,
                type: 'GET',
                dataType: "json",
                success: function(response) {
                    let districtOptions = response.map((item) => {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    }).sort((a, b) => a.text.localeCompare(b.text));

                    // Tambahkan opsi pertama "--- silahkan pilih ---"
                    districtOptions.unshift({
                        id: '',
                        text: '--- silahkan pilih ---'
                    });

                    $('#kecamatan').select2({
                        data: districtOptions,
                        allowClear: true,
                        placeholder: $('#kecamatan').data('placeholder') || '--- silahkan pilih ---',
                        dropdownParent: $("#create .modal-content"),
                    });
                }
            });
        } else {
            $('#kecamatan').empty();
        }
    });

    $('#kecamatan').on('change', function() {
        $('#desa').empty();
        var id_kecamatan = $(this).val();
        if (id_kecamatan) {
            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/villages/' + id_kecamatan,
                type: 'GET',
                dataType: "json",
                success: function(response) {
                    let villageOptions = response.map((item) => {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    }).sort((a, b) => a.text.localeCompare(b.text));

                    // Tambahkan opsi pertama "--- silahkan pilih ---"
                    villageOptions.unshift({
                        id: '',
                        text: '--- silahkan pilih ---'
                    });

                    $('#desa').select2({
                        data: villageOptions,
                        allowClear: true,
                        placeholder: $('#desa').data('placeholder') || '--- silahkan pilih ---',
                        dropdownParent: $("#create .modal-content"),
                    });
                }
            });
        } else {
            $('#desa').empty();
        }
    });

    set_maps();
     // Simpan instance peta di luar fungsi agar bisa diakses ulang
    var map = null;
    function set_maps(defaultLat = -6.200000,defaultLng = 106.816666){
          if (map !== null && map !== undefined) {
              map.remove(); // Hapus instance peta sebelumnya
          }
           // Default ke Jakarta jika data kosong
           var defaultLat = -6.200000;
           var defaultLng = 106.816666;
  
           // Ambil data dari input hidden (diisi dari database)
           var latInput = document.getElementById("latitude").value.trim();
           var lngInput = document.getElementById("longitude").value.trim();
  
           // Gunakan nilai database jika ada, jika tidak pakai default
           var lat = latInput ? parseFloat(latInput) : defaultLat;
           var lng = lngInput ? parseFloat(lngInput) : defaultLng;
  
           // Inisialisasi Peta
           map = L.map('map').setView([lat, lng], 10);
  
           // Tambahkan Tile Layer
           L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
               attribution: '&copy; OpenStreetMap contributors'
           }).addTo(map);
  
           // Tambahkan Marker
           var marker = L.marker([lat, lng], { draggable: true }).addTo(map);
  
           // Update Latitude & Longitude saat marker dipindahkan
           marker.on('dragend', function (event) {
               var position = marker.getLatLng();
               document.getElementById('latitude').value = position.lat;
               document.getElementById('longitude').value = position.lng;
           });
    }

    var map_edit = null;
    function set_maps_edit(defaultLat = -6.200000,defaultLng = 106.816666){
          if (map_edit !== null && map_edit !== undefined) {
            map_edit.remove(); // Hapus instance peta sebelumnya
          }
           // Default ke Jakarta jika data kosong
           var defaultLat = -6.200000;
           var defaultLng = 106.816666;
  
           // Ambil data dari input hidden (diisi dari database)
           var latInput = document.getElementById("latitude_edit").value.trim();
           var lngInput = document.getElementById("longitude_edit").value.trim();
  
           // Gunakan nilai database jika ada, jika tidak pakai default
           var lat = latInput ? parseFloat(latInput) : defaultLat;
           var lng = lngInput ? parseFloat(lngInput) : defaultLng;
  
           // Inisialisasi Peta
           map = L.map('map_edit').setView([lat, lng], 10);
  
           // Tambahkan Tile Layer
           L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
               attribution: '&copy; OpenStreetMap contributors'
           }).addTo(map);
  
           // Tambahkan Marker
           var marker = L.marker([lat, lng], { draggable: true }).addTo(map);
  
           // Update Latitude & Longitude saat marker dipindahkan
           marker.on('dragend', function (event) {
               var position = marker.getLatLng();
               document.getElementById('latitude_edit').value = position.lat;
               document.getElementById('longitude_edit').value = position.lng;
           });
    }

    

    $('#provinsi_edit').on('change', function() {
        $('#kabupaten_edit').empty();
        var id_provinsi = $(this).val();
        if (id_provinsi) {
            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/regencies/' + id_provinsi,
                type: 'GET',
                dataType: "json",
                success: function(response) {
                    let regencyOptions = response.map((item) => {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    }).sort((a, b) => a.text.localeCompare(b.text));

                    // Tambahkan opsi pertama "--- silahkan pilih ---"
                    regencyOptions.unshift({
                        id: '',
                        text: '--- silahkan pilih ---'
                    });

                    $('#kabupaten_edit').select2({
                        data: regencyOptions,
                        allowClear: true,
                        placeholder: $('#kabupaten_edit').data('placeholder') || '--- silahkan pilih ---',
                        dropdownParent: $("#edit .modal-content"),
                    });
                }
            });
        } else {
            $('#kabupaten_edit').empty();
        }
    });

    $('#kabupaten_edit').on('change', function() {
        $('#kecamatan_edit').empty();
        var id_kabupaten = $(this).val();
        if (id_kabupaten) {
            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/districts/' + id_kabupaten,
                type: 'GET',
                dataType: "json",
                success: function(response) {
                    let districtOptions = response.map((item) => {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    }).sort((a, b) => a.text.localeCompare(b.text));

                    // Tambahkan opsi pertama "--- silahkan pilih ---"
                    districtOptions.unshift({
                        id: '',
                        text: '--- silahkan pilih ---'
                    });

                    $('#kecamatan_edit').select2({
                        data: districtOptions,
                        allowClear: true,
                        placeholder: $('#kecamatan_edit').data('placeholder') || '--- silahkan pilih ---',
                        dropdownParent: $("#edit .modal-content"),
                    });
                }
            });
        } else {
            $('#kecamatan_edit').empty();
        }
    });

    $('#kecamatan_edit').on('change', function() {
        $('#desa_edit').empty();
        var id_kecamatan = $(this).val();
        if (id_kecamatan) {
            $.ajax({
                url: baseurl + '/dinetkan/settings/master/geo/villages/' + id_kecamatan,
                type: 'GET',
                dataType: "json",
                success: function(response) {
                    let villageOptions = response.map((item) => {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    }).sort((a, b) => a.text.localeCompare(b.text));

                    // Tambahkan opsi pertama "--- silahkan pilih ---"
                    villageOptions.unshift({
                        id: '',
                        text: '--- silahkan pilih ---'
                    });

                    $('#desa_edit').select2({
                        data: villageOptions,
                        allowClear: true,
                        placeholder: $('#desa_edit').data('placeholder') || '--- silahkan pilih ---',
                        dropdownParent: $("#edit .modal-content"),
                    });
                }
            });
        } else {
            $('#desa_edit').empty();
        }
    });
</script>
@endpush
