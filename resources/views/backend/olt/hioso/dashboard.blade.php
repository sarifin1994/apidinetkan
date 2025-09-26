@extends('backend.layouts.app')
@section('main')
@section('title', 'OLT Hioso')


<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <!-- Avatar -->
            <div class="avatar avatar-xl rounded text-primary">
                <i class="fs-2" data-duoicon="airplay"></i>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">OLT</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">HIOSO </h1>
        </div>

        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
            <div class="col-auto mt-4">
                <button class="btn btn-success text-light me-2 mb-2" id="save" data-olt="{{ $data['name'] }}">
                    <span class="material-symbols-outlined">
                        save
                    </span> Save
                </button>
                <button class="btn btn-danger text-light me-2 mb-2" id="reboot" data-olt="{{ $data['name'] }}">
                    <span class="material-symbols-outlined">
                        restart_alt
                    </span> Reboot
                </button>
                <a class="btn btn-warning text-light me-2 mb-2  " href="/olt">
                    <span class="material-symbols-outlined">
                        Logout
                    </span> Logout
                </a>
            </div>
        </div>

    </div>

    <div class="card bg-body border-0 mb-4">
        <div class="card-body px-4 py-2">
            <div class="row gy-3 gx-4 align-items-center fs-base text-muted">

                {{-- Kolom 1 --}}
                <div class="col-md-auto me-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined text-primary">router</span>
                        <span>Nama</span>
                        <span class="fw-semibold text-dark ms-2">{{ $data['name'] ?? '-' }}</span>
                    </div>
                </div>

                {{-- Kolom 2 --}}
                <div class="col-md-auto me-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined text-primary">location_on</span>
                        <span>Lokasi</span>
                        <span class="fw-semibold text-dark ms-2">{{ $data['location'] ?? '-' }}</span>
                    </div>
                </div>

                {{-- Kolom 3 --}}
                <div class="col-md-auto me-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined text-primary">memory</span>
                        <span>Model</span>
                        <span class="fw-semibold text-dark ms-2">{{ $data['model'] ?? '-' }}</span>
                    </div>
                </div>

                {{-- Kolom 4 --}}
                <div class="col-md-auto me-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined text-primary">lan</span>
                        <span>IP</span>
                        <span class="fw-semibold text-dark ms-2">{{ $data['ip_address'] ?? '-' }}</span>
                    </div>
                </div>

                {{-- Kolom 5 --}}
                <div class="col-md-auto me-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined text-primary">code</span>
                        <span>Software</span>
                        <span class="fw-semibold text-dark ms-2">{{ $data['software'] ?? '-' }}</span>
                    </div>
                </div>

                {{-- Kolom 6 --}}
                <div class="col-md-auto">
                    <div class="d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined text-primary">schedule</span>
                        <span>Uptime</span>
                        <span class="fw-semibold text-dark ms-2">{{ $data['uptime'] ?? '-' }}</span>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="row mb-4">
        <!-- ONU Total -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-xxl-0">
            <div class="card bg-body-tertiary border-transparent">
                <div class="card-body px-6 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="fs-sm fw-normal text-body-secondary mb-1">ONU Total</h4>
                            <div class="fs-5 fw-semibold" id="onuTotalCount"></div>
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

        <!-- ONU Online -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-xxl-0">
            <div class="card bg-body-tertiary border-transparent">
                <div class="card-body px-6 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="fs-sm fw-normal text-body-secondary mb-1">ONU Online</h4>
                            <div class="fs-5 fw-semibold text-success" id="onuUpCount">
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="avatar avatar-lg bg-body text-success">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ONU Loss -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-xxl-0">
            <div class="card bg-body-tertiary border-transparent">
                <div class="card-body px-6 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="fs-sm fw-normal text-body-secondary mb-1">ONU Loss</h4>
                            <div class="fs-5 fw-semibold text-danger" id="onuDown0Count">
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="avatar avatar-lg bg-body text-danger">
                                <span class="material-symbols-outlined">person_off</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ONU Loss -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-xxl-0">
            <div class="card bg-body-tertiary border-transparent">
                <div class="card-body px-6 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="fs-sm fw-normal text-body-secondary mb-1">ONU PwrOff</h4>
                            <div class="fs-5 fw-semibold text-danger" id="onuDown1Count">
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="avatar avatar-lg bg-body text-danger">
                                <span class="material-symbols-outlined">person_off</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">

            <div class="card-body table-responsive">

                <table id="myTable" class="table table-hover display nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>ONU ID</th>
                            <th>Name</th>
                            <th>MAC</th>
                            <th>RX Power</th>
                            <th>Status</th>
                            <th>Last Logout</th>
                            <th>Reason</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ url()->current() }}',
        columns: [{
                data: 'id',
                name: 'id',
            },
            {
                data: 'name',
                name: 'name',
            },
            {
                data: 'mac'
            },

            {
                data: 'rx_power',
                render: function(data) {
                    if (data > -8) {
                        return '<span class="material-symbols-outlined text-success">signal_cellular_4_bar</span> ' +
                            data;
                    } else if (data < -8 && data > -25) {
                        return '<span class="material-symbols-outlined text-success">signal_cellular_3_bar</span> ' +
                            data;
                    } else if (data <= -25) {
                        return '<span class="material-symbols-outlined text-warning">signal_cellular_1_bar</span> ' +
                            data;
                    } else {
                        return '<span class="material-symbols-outlined text-danger">signal_cellular_0_bar</span> ' +
                            data;
                    }
                },

            },
            {
                data: 'status',
                name: 'status',
                render: function(data, type, row) {
                    if (data === 'Up') {
                        return '<span class="badge bg-success-subtle text-success">Up</span>'
                    } else if (data === 'Down' && row.offline_reason == 'Dying_gasp') {
                        return '<span class="badge bg-danger-subtle text-danger">Pwr Down</span>'
                    } else if (data === 'Down') {
                        return '<span class="badge bg-danger-subtle text-danger">Down</span>'
                     }else if (data === 'PwrDown') {
                        return '<span class="badge bg-danger-subtle text-danger">Pwr Down</span>'
                    } else if (data === 'Initial') {
                        return '<span class="badge bg-warning-subtle text-warning">Initial</span>'
                    } else {
                        return data
                    }
                }
            },
            {
                data: 'offline_time',
                name: 'offline_time',
                defaultContent: ''
            },
            {
                data: 'offline_reason',
                name: 'offline_reason',
                defaultContent: ''
            },
            {
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
            <button class="btn btn-sm btn-primary btn-rename" data-onuid="${row.id}" data-ponid="${row.id}" data-ponname="${row.name}">Rename</button>
            <button class="btn btn-sm btn-secondary btn-reboot" data-onuid="${row.id}" data-ponid="${row.id}" data-ponname="${row.name}">Reboot</button>
            <button class="btn btn-sm btn-danger btn-delete" data-onuid="${row.id}" data-ponid="${row.id}" data-ponname="${row.name}">Delete</button>
        `;
                }
            }
        ]
    });

    table.on('xhr.dt', function(e, settings, json) {
        if (json.aggregate) {
            $('#onuTotalCount').text(json.aggregate.countTotal);
            $('#onuUpCount').text(json.aggregate.countUp);
            $('#onuDown0Count').text(json.aggregate.countDown0);
            $('#onuDown1Count').text(json.aggregate.countDown1);
        }
    });


    // $('#myTableOnu tbody').on('click', 'tr', function() {
    //     const rowData = table.row(this).data();
    //     const segments = window.location.pathname.split('/');
    //     const oltId = segments[3]; // misalnya: 9

    //     if (rowData && rowData.pon_id) {
    //         const ponNumber = rowData.pon_id.split('/').pop(); // ambil '1' dari '0/1'
    //         window.location.href = `/olt/hioso/pon/${ponNumber}`;
    //     }
    // });


    $('#reboot').on('click', function() {

        let olt = $(this).data('olt');

        Swal.fire({
            title: "Reboot",
            icon: 'warning',
            text: "Are you sure to Reboot OLT? ",
            showCancelButton: !0,
            reverseButtons: !0,
            confirmButtonText: "Yes, reboot!",
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
                    url: `/olt/hioso/reboot/${olt}}`,
                    type: "POST",
                    cache: false,
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
                                window.location.href = '/olt'
                            }, 1500);
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

    $('#save').on('click', function() {
        Swal.fire({
            title: "Save",
            icon: 'warning',
            text: "Are you sure to Save Configuration?",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Yes, save!",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#d33",
        }).then(function(result) {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Saving...',
                    icon: 'info',
                    html: 'Please wait while saving configuration.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                // Setup CSRF
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Kirim AJAX
                $.ajax({
                    url: `/olt/hioso/save`,
                    type: "POST",
                    dataType: "json",
                    cache: false,

                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },

                    error: function(err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menyimpan konfigurasi.',
                        });
                    }
                });
            }
        });
    });

    // AJAX untuk Rename ONU
    $(document).on('click', '.btn-rename', function() {
        const onuId = $(this).data('onuid');
        const ponId = $(this).data('ponid');
        const newName = prompt("Masukkan nama baru untuk ONU:");

        if (newName) {
            $.ajax({
                url: '/olt/hioso/onu/rename',
                type: 'POST',
                data: {
                    onuid: onuId,
                    ponid: ponId,
                    new_name: newName,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert(response.message);
                    $('#myTable').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    alert('Gagal mengganti nama ONU.');
                }
            });
        }
    });

    // AJAX untuk Reboot ONU
    $(document).on('click', '.btn-reboot', function() {
        const onuId = $(this).data('onuid');
        const ponId = $(this).data('ponid');
        const oldName = $(this).data('ponname');

        if (confirm("Yakin ingin me-reboot ONU ini?")) {
            $.ajax({
                url: '/olt/hioso/onu/reboot',
                type: 'POST',
                data: {
                    onuid: onuId,
                    ponid: ponId,
                    old_name: oldName,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert(response.message);
                    $('#myTable').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    alert('Gagal reboot ONU.');
                }
            });
        }
    });

    // AJAX untuk Delete ONU
    $(document).on('click', '.btn-delete', function() {
        const onuId = $(this).data('onuid');
        const ponId = $(this).data('ponid');

        if (confirm("Yakin ingin menghapus ONU ini?")) {
            $.ajax({
                url: '/olt/hioso/onu/delete',
                type: 'POST',
                data: {
                    onuid: onuId,
                    ponid: ponId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert(response.message);
                    $('#myTable').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    alert('Gagal menghapus ONU.');
                }
            });
        }
    });
</script>
@endpush
